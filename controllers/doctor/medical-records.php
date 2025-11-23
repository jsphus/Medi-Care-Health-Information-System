<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $appointment_id = sanitize($_POST['appointment_id'] ?? '');
        $med_rec_visit_date = $_POST['med_rec_visit_date'];
        $med_rec_diagnosis = sanitize($_POST['med_rec_diagnosis']);
        $med_rec_prescription = sanitize($_POST['med_rec_prescription'] ?? '');
        
        if (empty($appointment_id) || empty($med_rec_visit_date) || empty($med_rec_diagnosis)) {
            $error = 'Appointment, visit date, and diagnosis are required';
        } else {
            try {
                // Verify appointment belongs to this doctor
                $appt = $db->fetchOne(
                    "SELECT appointment_id, doc_id FROM appointments WHERE appointment_id = :appt_id",
                    ['appt_id' => $appointment_id]
                );
                
                if (!$appt || $appt['doc_id'] != $doctor_id) {
                    $error = 'Invalid appointment or appointment does not belong to you';
                } else {
                    $stmt = $db->prepare("
                        INSERT INTO medical_records (appt_id, med_rec_diagnosis, med_rec_prescription, med_rec_visit_date) 
                        VALUES (:appt_id, :med_rec_diagnosis, :med_rec_prescription, :med_rec_visit_date)
                    ");
                    $stmt->execute([
                        'appt_id' => $appointment_id,
                        'med_rec_diagnosis' => $med_rec_diagnosis,
                        'med_rec_prescription' => $med_rec_prescription,
                        'med_rec_visit_date' => $med_rec_visit_date
                    ]);
                    $success = 'Medical record created successfully';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $med_rec_diagnosis = sanitize($_POST['med_rec_diagnosis']);
        $med_rec_prescription = sanitize($_POST['med_rec_prescription'] ?? '');
        
        try {
            // Verify this record belongs to this doctor via appointment
            $stmt = $db->prepare("
                UPDATE medical_records mr
                SET med_rec_diagnosis = :med_rec_diagnosis, 
                    med_rec_prescription = :med_rec_prescription,
                    med_rec_updated_at = NOW()
                FROM appointments a
                WHERE mr.med_rec_id = :id 
                AND mr.appt_id = a.appointment_id
                AND a.doc_id = :doc_id
            ");
            $stmt->execute([
                'med_rec_diagnosis' => $med_rec_diagnosis,
                'med_rec_prescription' => $med_rec_prescription,
                'id' => $id,
                'doc_id' => $doctor_id
            ]);
            if ($stmt->rowCount() > 0) {
                $success = 'Medical record updated successfully';
            } else {
                $error = 'Medical record not found or you do not have permission to update it';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Handle filters from URL parameters
$filter_patient = isset($_GET['filter_patient']) ? sanitize($_GET['filter_patient']) : '';
$filter_diagnosis = isset($_GET['filter_diagnosis']) ? sanitize($_GET['filter_diagnosis']) : '';
$filter_date_from = isset($_GET['filter_date_from']) ? sanitize($_GET['filter_date_from']) : '';
$filter_date_to = isset($_GET['filter_date_to']) ? sanitize($_GET['filter_date_to']) : '';

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = 25;
$offset = ($page - 1) * $items_per_page;

// Handle sorting
$sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'med_rec_visit_date';
$sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
$allowed_columns = ['med_rec_visit_date', 'med_rec_id', 'med_rec_created_at'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'med_rec_visit_date';
}
$sort_order = $sort_order === 'ASC' ? 'ASC' : 'DESC';

// Fetch medical records with filters
try {
    $where_conditions = ['a.doc_id = :doctor_id'];
    $params = ['doctor_id' => $doctor_id];

    if (!empty($filter_patient)) {
        $where_conditions[] = "(LOWER(p.pat_first_name) LIKE LOWER(:filter_patient) OR LOWER(p.pat_middle_initial) LIKE LOWER(:filter_patient) OR LOWER(p.pat_last_name) LIKE LOWER(:filter_patient) OR LOWER(CONCAT(p.pat_first_name, ' ', COALESCE(p.pat_middle_initial, ''), ' ', p.pat_last_name)) LIKE LOWER(:filter_patient))";
        $params['filter_patient'] = '%' . $filter_patient . '%';
    }

    if (!empty($filter_diagnosis)) {
        $where_conditions[] = "LOWER(mr.med_rec_diagnosis) LIKE LOWER(:filter_diagnosis)";
        $params['filter_diagnosis'] = '%' . $filter_diagnosis . '%';
    }

    if (!empty($filter_date_from)) {
        $where_conditions[] = "DATE(mr.med_rec_visit_date) >= :filter_date_from";
        $params['filter_date_from'] = $filter_date_from;
    }

    if (!empty($filter_date_to)) {
        $where_conditions[] = "DATE(mr.med_rec_visit_date) <= :filter_date_to";
        $params['filter_date_to'] = $filter_date_to;
    }

    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    $order_by = "mr.$sort_column $sort_order";

    // Get total count for pagination
    $count_stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM medical_records mr
        JOIN appointments a ON mr.appt_id = a.appointment_id
        JOIN patients p ON a.pat_id = p.pat_id
        $where_clause
    ");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

    // Fetch paginated results
    $stmt = $db->prepare("
        SELECT mr.*, 
               a.pat_id, a.doc_id, a.appointment_date, a.appointment_time, a.appointment_id,
               a.appointment_notes, a.created_at as appointment_created_at,
               p.pat_first_name, p.pat_last_name, p.pat_middle_initial,
               s.status_name, s.status_color,
               sv.service_name, sv.service_price,
               up.profile_picture_url as patient_profile_picture
        FROM medical_records mr
        JOIN appointments a ON mr.appt_id = a.appointment_id
        JOIN patients p ON a.pat_id = p.pat_id
        LEFT JOIN appointment_statuses s ON a.status_id = s.status_id
        LEFT JOIN services sv ON a.service_id = sv.service_id
        LEFT JOIN users up ON up.pat_id = p.pat_id
        $where_clause
        ORDER BY $order_by
        LIMIT :limit OFFSET :offset
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch medical records: ' . $e->getMessage();
    $records = [];
    $total_items = 0;
    $total_pages = 0;
}

// Fetch filter data from database
$filter_patients = [];
try {
    // Get unique patients from this doctor's medical records (via appointments)
    $stmt = $db->prepare("SELECT DISTINCT p.pat_id, p.pat_first_name, p.pat_last_name FROM medical_records mr JOIN appointments a ON mr.appt_id = a.appointment_id JOIN patients p ON a.pat_id = p.pat_id WHERE a.doc_id = :doctor_id ORDER BY p.pat_first_name");
    $stmt->execute(['doctor_id' => $doctor_id]);
    $filter_patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $filter_patients = [];
}

// Fetch appointments for dropdown (all appointments without medical records for this doctor)
try {
    $stmt = $db->prepare("
        SELECT a.appointment_id, a.appointment_date, a.appointment_time,
               p.pat_id, p.pat_first_name, p.pat_last_name, p.pat_middle_initial,
               s.status_name, s.status_color,
               sv.service_name
        FROM appointments a
        JOIN patients p ON a.pat_id = p.pat_id
        JOIN appointment_statuses s ON a.status_id = s.status_id
        LEFT JOIN services sv ON a.service_id = sv.service_id
        LEFT JOIN medical_records mr ON a.appointment_id = mr.appt_id
        WHERE a.doc_id = :doctor_id
        AND mr.med_rec_id IS NULL
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $stmt->execute(['doctor_id' => $doctor_id]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $appointments = [];
}

// Calculate useful statistics for summary cards
$stats = [
    'records_this_month' => 0,
    'pending_followups' => 0,
    'unique_patients' => 0,
    'records_today' => 0
];

try {
    $today = date('Y-m-d');
    $month_start = date('Y-m-01');
    
    // Records this month
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM medical_records mr
        JOIN appointments a ON mr.appt_id = a.appointment_id
        WHERE a.doc_id = :doctor_id 
        AND mr.med_rec_visit_date >= :month_start
    ");
    $stmt->execute(['doctor_id' => $doctor_id, 'month_start' => $month_start]);
    $stats['records_this_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending follow-ups removed (field no longer exists)
    $stats['pending_followups'] = 0;
    
    // Unique patients with records
    $stmt = $db->prepare("
        SELECT COUNT(DISTINCT a.pat_id) as count 
        FROM medical_records mr
        JOIN appointments a ON mr.appt_id = a.appointment_id
        WHERE a.doc_id = :doctor_id
    ");
    $stmt->execute(['doctor_id' => $doctor_id]);
    $stats['unique_patients'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Records created today
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM medical_records mr
        JOIN appointments a ON mr.appt_id = a.appointment_id
        WHERE a.doc_id = :doctor_id 
        AND DATE(mr.med_rec_created_at) = :today
    ");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $stats['records_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/doctor/medical-records.view.php';
