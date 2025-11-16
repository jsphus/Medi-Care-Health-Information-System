<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doc_id = $_SESSION['doc_id'];

// Get doctor info
try {
    $stmt = $db->prepare("
        SELECT d.*, s.spec_name 
        FROM doctors d
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
        WHERE d.doc_id = :doc_id
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get profile picture URL
    $user_id = $auth->getUserId();
    $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_picture_url = $user['profile_picture_url'] ?? null;
} catch (PDOException $e) {
    $doctor = null;
    $profile_picture_url = null;
}

// Get statistics
$stats = [
    'total_appointments' => 0,
    'today_appointments' => 0,
    'upcoming_appointments' => 0,
    'completed_appointments' => 0,
    'total_patients' => 0,
    'my_schedules' => 0,
    'all_schedules' => 0,
    'pending_lab_results' => 0,
    'active_doctors' => 0
];

try {
    // Total appointments for this doctor
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments WHERE doc_id = :doc_id");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['total_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Today's appointments
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM appointments 
        WHERE doc_id = :doc_id AND appointment_date = CURRENT_DATE
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['today_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Upcoming appointments
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM appointments 
        WHERE doc_id = :doc_id AND appointment_date > CURRENT_DATE
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['upcoming_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Completed appointments
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM appointments a
        JOIN appointment_statuses s ON a.status_id = s.status_id
        WHERE a.doc_id = :doc_id AND s.status_name = 'Completed'
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['completed_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total unique patients
    $stmt = $db->prepare("
        SELECT COUNT(DISTINCT pat_id) as count 
        FROM appointments 
        WHERE doc_id = :doc_id
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['total_patients'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // My schedules count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM schedules WHERE doc_id = :doc_id");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['my_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // All schedules count
    $stmt = $db->query("SELECT COUNT(*) as count FROM schedules");
    $stats['all_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending lab results (medical records that need follow-up or are pending)
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM medical_records 
        WHERE doc_id = :doc_id 
        AND (follow_up_date IS NOT NULL AND follow_up_date >= CURRENT_DATE)
        AND diagnosis IS NOT NULL
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['pending_lab_results'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Active doctors count (total active doctors in system)
    $stmt = $db->query("SELECT COUNT(*) as count FROM doctors WHERE doc_status = 'active'");
    $stats['active_doctors'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
} catch (PDOException $e) {
    // Keep default values
}

// Get recent appointments (today and upcoming) with patient and doctor details
try {
    $stmt = $db->prepare("
        SELECT a.*, 
               p.pat_first_name, p.pat_last_name, p.pat_date_of_birth, p.pat_email, p.pat_phone,
               d.doc_first_name, d.doc_last_name,
               s.status_name, s.status_color,
               sv.service_name
        FROM appointments a
        JOIN patients p ON a.pat_id = p.pat_id
        JOIN doctors d ON a.doc_id = d.doc_id
        JOIN appointment_statuses s ON a.status_id = s.status_id
        LEFT JOIN services sv ON a.service_id = sv.service_id
        WHERE a.doc_id = :doc_id 
        AND (a.appointment_date = CURRENT_DATE OR a.appointment_date > CURRENT_DATE)
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
        LIMIT 6
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $recent_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $recent_appointments = [];
}

// Get today's appointments with patient details
try {
    $stmt = $db->prepare("
        SELECT a.*, 
               p.pat_first_name, p.pat_last_name, p.pat_date_of_birth, p.pat_email, p.pat_phone,
               s.status_name, s.status_color
        FROM appointments a
        JOIN patients p ON a.pat_id = p.pat_id
        JOIN appointment_statuses s ON a.status_id = s.status_id
        WHERE a.doc_id = :doc_id AND a.appointment_date = CURRENT_DATE
        ORDER BY a.appointment_time ASC
        LIMIT 10
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $today_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $today_appointments = [];
}

// Get today's schedule
try {
    $stmt = $db->prepare("
        SELECT * FROM schedules 
        WHERE doc_id = :doc_id AND schedule_date = CURRENT_DATE
        ORDER BY start_time ASC
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $today_schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $today_schedule = [];
}

// Get upcoming appointments
try {
    $stmt = $db->prepare("
        SELECT a.*, 
               p.pat_first_name, p.pat_last_name, p.pat_date_of_birth,
               s.status_name, s.status_color
        FROM appointments a
        JOIN patients p ON a.pat_id = p.pat_id
        JOIN appointment_statuses s ON a.status_id = s.status_id
        WHERE a.doc_id = :doc_id AND a.appointment_date > CURRENT_DATE
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
        LIMIT 10
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $upcoming_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $upcoming_appointments = [];
}

// Chart data for appointments
$chart_data = [
    'appointments' => [10, 15, 20, 18, 25, 30, 28],
    'completed' => [8, 12, 18, 15, 22, 25, 24]
];

// Include the view
require_once __DIR__ . '/../../views/doctor/dashboard.view.php';
