<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();

// Get today's appointments for this doctor only
try {
    $today = date('Y-m-d');
    
    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'appointment_time';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['appointment_time', 'appointment_id'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'appointment_time';
    }
    
    $order_by = "a.$sort_column $sort_order";
    
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
        WHERE a.doc_id = :doctor_id AND a.appointment_date = :today
        ORDER BY $order_by
    ");
    $stmt->execute([
        'doctor_id' => $doctor_id,
        'today' => $today
    ]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments WHERE doc_id = :doctor_id AND appointment_date = :today");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $today_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments WHERE doc_id = :doctor_id AND appointment_date < :today");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $past_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments WHERE doc_id = :doctor_id AND appointment_date > :today");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $future_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
} catch (PDOException $e) {
    error_log("Doctor appointments error: " . $e->getMessage());
    $appointments = [];
    $today_count = 0;
    $past_count = 0;
    $future_count = 0;
}

require_once __DIR__ . '/../../views/doctor/appointments-today.view.php';
