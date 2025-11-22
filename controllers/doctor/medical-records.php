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

// Handle search and filters
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

$filter_patient = isset($_GET['patient']) ? (int)$_GET['patient'] : null;

// Fetch medical records with filters
try {
    $where_conditions = ['a.doc_id = :doctor_id'];
    $params = ['doctor_id' => $doctor_id];

    if (!empty($search_query)) {
        $where_conditions[] = "(p.pat_first_name LIKE :search OR p.pat_last_name LIKE :search OR mr.med_rec_diagnosis LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }

    if ($filter_patient) {
        $where_conditions[] = "a.pat_id = :patient";
        $params['patient'] = $filter_patient;
    }

    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'med_rec_visit_date';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['med_rec_visit_date', 'med_rec_id', 'med_rec_created_at'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'med_rec_visit_date';
    }
    
    $order_by = "mr.$sort_column $sort_order";

    $stmt = $db->prepare("
        SELECT mr.*, 
               a.pat_id, a.doc_id, a.appointment_date, a.appointment_time, a.appointment_id,
               a.appointment_notes, a.appointment_duration, a.created_at as appointment_created_at,
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
    ");
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch medical records: ' . $e->getMessage();
    $records = [];
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

// Fetch appointments for dropdown (completed appointments without medical records)
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
        AND LOWER(s.status_name) = 'completed'
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
