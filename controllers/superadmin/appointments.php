<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Appointment.php';

$auth = new Auth();
$auth->requireSuperAdmin();

$db = Database::getInstance();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $patient_id = (int)$_POST['patient_id'];
        $doctor_id = (int)$_POST['doctor_id'];
        $service_id = !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null;
        $status_id = !empty($_POST['status_id']) ? (int)$_POST['status_id'] : 1; // Default to first status
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $duration = !empty($_POST['duration']) ? (int)$_POST['duration'] : 30;
        $notes = sanitize($_POST['notes'] ?? '');
        
        if (empty($patient_id) || empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
            $error = 'Patient, doctor, date, and time are required';
        } else {
            try {
                // Use Appointment class to ensure validation (including schedule validation)
                $appointmentModel = new Appointment();
                $result = $appointmentModel->create([
                    'pat_id' => $patient_id,
                    'doc_id' => $doctor_id,
                    'service_id' => $service_id,
                    'status_id' => $status_id,
                    'appointment_date' => $appointment_date,
                    'appointment_time' => $appointment_time,
                    'appointment_duration' => $duration,
                    'appointment_notes' => $notes
                ]);
                
                if ($result['success']) {
                    $success = 'Appointment created successfully with ID: ' . $result['id'];
                } else {
                    $error = implode(', ', $result['errors'] ?? ['Failed to create appointment.']);
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'update') {
        $id = sanitize($_POST['id']);
        $patient_id = (int)$_POST['patient_id'];
        $doctor_id = (int)$_POST['doctor_id'];
        $service_id = !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null;
        $status_id = !empty($_POST['status_id']) ? (int)$_POST['status_id'] : 1;
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $duration = !empty($_POST['duration']) ? (int)$_POST['duration'] : 30;
        $notes = sanitize($_POST['notes'] ?? '');
        
        if (empty($appointment_date) || empty($appointment_time)) {
            $error = 'Date and time are required';
        } else {
            try {
                // Use Appointment class to ensure validation (including schedule validation)
                $appointmentModel = new Appointment();
                $result = $appointmentModel->updateAppointment([
                    'appointment_id' => $id,
                    'pat_id' => $patient_id,
                    'doc_id' => $doctor_id,
                    'service_id' => $service_id,
                    'status_id' => $status_id,
                    'appointment_date' => $appointment_date,
                    'appointment_time' => $appointment_time,
                    'appointment_duration' => $duration,
                    'appointment_notes' => $notes
                ]);
                
                if ($result['success']) {
                    $success = 'Appointment updated successfully';
                } else {
                    $error = implode(', ', $result['errors'] ?? ['Failed to update appointment.']);
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'update_status') {
        $id = sanitize($_POST['id']);
        $status_id = (int)$_POST['status_id'];
        
        try {
            $stmt = $db->prepare("
                UPDATE appointments 
                SET status_id = :status_id, updated_at = NOW()
                WHERE appointment_id = :id
            ");
            $stmt->execute([
                'status_id' => $status_id,
                'id' => $id
            ]);
            $success = 'Appointment status updated successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
    
    if ($action === 'delete') {
        $id = sanitize($_POST['id']);
        try {
            $stmt = $db->prepare("DELETE FROM appointments WHERE appointment_id = :id");
            $stmt->execute(['id' => $id]);
            $success = 'Appointment deleted successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Handle filters from URL
$filter_patient = isset($_GET['filter_patient']) ? sanitize($_GET['filter_patient']) : '';
$filter_doctor = isset($_GET['filter_doctor']) ? sanitize($_GET['filter_doctor']) : '';
$filter_service = isset($_GET['filter_service']) ? sanitize($_GET['filter_service']) : '';
$filter_status = isset($_GET['filter_status']) ? (int)$_GET['filter_status'] : null;
$filter_date_from = isset($_GET['filter_date_from']) ? sanitize($_GET['filter_date_from']) : '';
$filter_date_to = isset($_GET['filter_date_to']) ? sanitize($_GET['filter_date_to']) : '';

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = 25;
$offset = (($page - 1) * $items_per_page);

// Fetch appointments with filters
try {
    $where_conditions = [];
    $params = [];
    
    if (!empty($filter_patient)) {
        // Case-insensitive search in patient name
        $where_conditions[] = "(LOWER(p.pat_first_name) LIKE :patient OR LOWER(p.pat_last_name) LIKE :patient OR LOWER(TRIM(CONCAT(COALESCE(p.pat_first_name, ''), ' ', COALESCE(p.pat_last_name, '')))) LIKE :patient)";
        $params['patient'] = '%' . strtolower($filter_patient) . '%';
    }
    
    if (!empty($filter_doctor)) {
        // Case-insensitive search in doctor name
        $where_conditions[] = "(LOWER(d.doc_first_name) LIKE :doctor OR LOWER(d.doc_last_name) LIKE :doctor OR LOWER(TRIM(CONCAT(COALESCE(d.doc_first_name, ''), ' ', COALESCE(d.doc_last_name, '')))) LIKE :doctor)";
        $params['doctor'] = '%' . strtolower($filter_doctor) . '%';
    }
    
    if (!empty($filter_service)) {
        // Case-insensitive search in service name
        $where_conditions[] = "LOWER(s.service_name) LIKE :service";
        $params['service'] = '%' . strtolower($filter_service) . '%';
    }
    
    if ($filter_status) {
        $where_conditions[] = "a.status_id = :status";
        $params['status'] = $filter_status;
    }
    
    if (!empty($filter_date_from)) {
        $where_conditions[] = "DATE(a.appointment_date) >= :date_from";
        $params['date_from'] = $filter_date_from;
    }
    
    if (!empty($filter_date_to)) {
        $where_conditions[] = "DATE(a.appointment_date) <= :date_to";
        $params['date_to'] = $filter_date_to;
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Get total count for pagination
    $count_stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM appointments a
        LEFT JOIN patients p ON a.pat_id = p.pat_id
        LEFT JOIN doctors d ON a.doc_id = d.doc_id
        $where_clause
    ");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);
    
    // Handle sorting - default to showing newest appointments first by creation date
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'created_at';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['appointment_date', 'appointment_time', 'appointment_id', 'created_at'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'created_at';
    }
    
    // Special handling for date sorting (also sort by time)
    if ($sort_column === 'appointment_date') {
        $order_by = "a.appointment_date $sort_order, a.appointment_time $sort_order";
    } elseif ($sort_column === 'created_at') {
        $order_by = "COALESCE(a.created_at, '1970-01-01'::timestamp) $sort_order";
    } else {
        $order_by = "a.$sort_column $sort_order";
    }
    
    // Fetch paginated results
    $stmt = $db->prepare("
        SELECT a.*, 
               p.pat_first_name, p.pat_last_name,
               d.doc_first_name, d.doc_last_name,
               s.service_name,
               st.status_name, st.status_color,
               up.profile_picture_url as patient_profile_picture,
               ud.profile_picture_url as doctor_profile_picture
        FROM appointments a
        LEFT JOIN patients p ON a.pat_id = p.pat_id
        LEFT JOIN doctors d ON a.doc_id = d.doc_id
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
        LEFT JOIN users up ON up.pat_id = p.pat_id
        LEFT JOIN users ud ON ud.doc_id = d.doc_id
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
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch appointments: ' . $e->getMessage();
    $appointments = [];
    $total_items = 0;
    $total_pages = 0;
}

// Fetch patients, doctors, services, and statuses for dropdowns
try {
    $patients = $db->query("SELECT pat_id, pat_first_name, pat_last_name FROM patients ORDER BY pat_first_name")->fetchAll(PDO::FETCH_ASSOC);
    $doctors = $db->query("SELECT doc_id, doc_first_name, doc_last_name FROM doctors ORDER BY doc_first_name")->fetchAll(PDO::FETCH_ASSOC);
    $services = $db->query("SELECT service_id, service_name FROM services ORDER BY service_name")->fetchAll(PDO::FETCH_ASSOC);
    $statuses = $db->query("SELECT status_id, status_name, status_color FROM appointment_statuses ORDER BY status_id")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $patients = [];
    $doctors = [];
    $services = [];
    $statuses = [];
}

// Fetch filter data from database
$filter_doctors = [];
$filter_patients = [];
$filter_statuses = [];
try {
    // Get unique doctors from appointments
    $stmt = $db->query("SELECT DISTINCT d.doc_id, d.doc_first_name, d.doc_last_name FROM appointments a JOIN doctors d ON a.doc_id = d.doc_id ORDER BY d.doc_first_name");
    $filter_doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get unique patients from appointments
    $stmt = $db->query("SELECT DISTINCT p.pat_id, p.pat_first_name, p.pat_last_name FROM appointments a JOIN patients p ON a.pat_id = p.pat_id ORDER BY p.pat_first_name");
    $filter_patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all appointment statuses
    $stmt = $db->query("SELECT status_id, status_name, status_color FROM appointment_statuses ORDER BY status_name");
    $filter_statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $filter_doctors = [];
    $filter_patients = [];
    $filter_statuses = [];
}

// Calculate statistics for summary cards
$stats = [
    'upcoming' => 0,
    'completed' => 0,
    'cancelled' => 0
];

try {
    // Upcoming appointments (future dates)
    $stmt = $db->query("SELECT COUNT(*) as count FROM appointments WHERE appointment_date > CURRENT_DATE OR (appointment_date = CURRENT_DATE AND appointment_time > CURRENT_TIME)");
    $stats['upcoming'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Completed appointments
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM appointments a
        JOIN appointment_statuses s ON a.status_id = s.status_id
        WHERE LOWER(s.status_name) = 'completed'
    ");
    $stats['completed'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Cancelled appointments
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM appointments a
        JOIN appointment_statuses s ON a.status_id = s.status_id
        WHERE LOWER(s.status_name) IN ('cancelled', 'canceled')
    ");
    $stats['cancelled'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/superadmin/appointments.view.php';
