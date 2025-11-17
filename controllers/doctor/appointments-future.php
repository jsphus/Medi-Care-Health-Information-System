<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();

// Get future appointments for this doctor only
try {
    $today = date('Y-m-d');
    
    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'appointment_date';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['appointment_date', 'appointment_time', 'appointment_id'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'appointment_date';
    }
    
    // Special handling for date/time sorting
    if ($sort_column === 'appointment_date') {
        $order_by = "a.appointment_date $sort_order, a.appointment_time $sort_order";
    } else {
        $order_by = "a.$sort_column $sort_order";
    }
    
    $stmt = $db->prepare("
        SELECT a.*, 
               p.pat_first_name, p.pat_last_name, p.pat_phone,
               s.service_name,
               st.status_name, st.status_color,
               up.profile_picture_url as patient_profile_picture
        FROM appointments a
        LEFT JOIN patients p ON a.pat_id = p.pat_id
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
        LEFT JOIN users up ON up.pat_id = p.pat_id
        WHERE a.doc_id = :doctor_id AND a.appointment_date > :today
        ORDER BY $order_by
    ");
    $stmt->execute([
        'doctor_id' => $doctor_id,
        'today' => $today
    ]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Doctor future appointments error: " . $e->getMessage());
    $appointments = [];
    $error = 'Failed to fetch appointments: ' . $e->getMessage();
}

require_once __DIR__ . '/../../views/doctor/appointments-future.view.php';
