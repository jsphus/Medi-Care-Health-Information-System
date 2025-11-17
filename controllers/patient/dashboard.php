<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requirePatient();

$db = Database::getInstance();
$patient_id = $auth->getPatientId();
$error = '';

// Get patient info
try {
    $stmt = $db->prepare("SELECT * FROM patients WHERE pat_id = :patient_id");
    $stmt->execute(['patient_id' => $patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Initialize profile picture for consistent display across the system
    $profile_picture_url = initializeProfilePicture($auth, $db);
} catch (PDOException $e) {
    $error = 'Failed to fetch patient info: ' . $e->getMessage();
    $patient = null;
    $profile_picture_url = null;
}

// Get upcoming appointments (next 5)
try {
    $today = date('Y-m-d');
    $stmt = $db->prepare("
        SELECT a.*, 
               d.doc_first_name, d.doc_last_name, d.doc_specialization_id,
               s.service_name, s.service_price,
               st.status_name, st.status_color,
               sp.spec_name,
               ud.profile_picture_url as doctor_profile_picture
        FROM appointments a
        LEFT JOIN doctors d ON a.doc_id = d.doc_id
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        LEFT JOIN users ud ON ud.doc_id = d.doc_id
        WHERE a.pat_id = :patient_id AND a.appointment_date >= :today
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
        LIMIT 5
    ");
    $stmt->execute(['patient_id' => $patient_id, 'today' => $today]);
    $upcoming_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $upcoming_appointments = [];
}

// Get recent medical records (last 5)
try {
    $stmt = $db->prepare("
        SELECT mr.*, 
               d.doc_first_name, d.doc_last_name,
               a.appointment_date, a.appointment_id,
               ud.profile_picture_url as doctor_profile_picture
        FROM medical_records mr
        LEFT JOIN doctors d ON mr.doc_id = d.doc_id
        LEFT JOIN appointments a ON mr.appointment_id = a.appointment_id
        LEFT JOIN users ud ON ud.doc_id = d.doc_id
        WHERE mr.pat_id = :patient_id
        ORDER BY mr.record_date DESC
        LIMIT 5
    ");
    $stmt->execute(['patient_id' => $patient_id]);
    $recent_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $recent_records = [];
}

// Get recent payments (last 5)
try {
    $stmt = $db->prepare("
        SELECT p.*, 
               a.appointment_id, a.appointment_date,
               pm.method_name,
               ps.status_name, ps.status_color
        FROM payments p
        LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
        LEFT JOIN payment_methods pm ON p.payment_method_id = pm.method_id
        LEFT JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE a.pat_id = :patient_id
        ORDER BY p.payment_date DESC
        LIMIT 5
    ");
    $stmt->execute(['patient_id' => $patient_id]);
    $recent_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $recent_payments = [];
}

// Get statistics
$stats = [
    'total_appointments' => 0,
    'upcoming_appointments' => 0,
    'completed_appointments' => 0,
    'total_payments' => 0,
    'pending_payments' => 0
];

try {
    // Total appointments
    $stmt = $db->prepare("SELECT COUNT(*) FROM appointments WHERE pat_id = :patient_id");
    $stmt->execute(['patient_id' => $patient_id]);
    $stats['total_appointments'] = $stmt->fetchColumn();
    
    // Upcoming appointments
    $stmt = $db->prepare("SELECT COUNT(*) FROM appointments WHERE pat_id = :patient_id AND appointment_date >= :today");
    $stmt->execute(['patient_id' => $patient_id, 'today' => date('Y-m-d')]);
    $stats['upcoming_appointments'] = $stmt->fetchColumn();
    
    // Completed appointments
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM appointments a
        JOIN appointment_statuses st ON a.status_id = st.status_id
        WHERE a.pat_id = :patient_id AND LOWER(st.status_name) = 'completed'
    ");
    $stmt->execute(['patient_id' => $patient_id]);
    $stats['completed_appointments'] = $stmt->fetchColumn();
    
    // Total payments
    $stmt = $db->prepare("
        SELECT COALESCE(SUM(p.payment_amount), 0) FROM payments p
        JOIN appointments a ON p.appointment_id = a.appointment_id
        WHERE a.pat_id = :patient_id
    ");
    $stmt->execute(['patient_id' => $patient_id]);
    $stats['total_payments'] = $stmt->fetchColumn();
    
    // Pending payments
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM payments p
        JOIN appointments a ON p.appointment_id = a.appointment_id
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE a.pat_id = :patient_id AND LOWER(ps.status_name) = 'pending'
    ");
    $stmt->execute(['patient_id' => $patient_id]);
    $stats['pending_payments'] = $stmt->fetchColumn();
} catch (PDOException $e) {
    // Stats will remain at defaults
}

require_once __DIR__ . '/../../views/patient/dashboard.view.php';

