<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requirePatient();

$db = Database::getInstance();
$patient_id = $auth->getPatientId();
$error = '';

// Get appointment ID from URL
$appointment_id = isset($_GET['appointment_id']) ? sanitize($_GET['appointment_id']) : '';

if (empty($appointment_id)) {
    header('Location: /patient/appointments');
    exit;
}

// Fetch appointment and payment details
try {
    $stmt = $db->prepare("
        SELECT a.*, 
               d.doc_first_name, d.doc_last_name,
               s.service_name,
               p.payment_id, p.payment_amount, p.payment_date,
               pm.method_name,
               ps.status_name, ps.status_color
        FROM appointments a
        LEFT JOIN doctors d ON a.doc_id = d.doc_id
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN payments p ON a.appointment_id = p.appointment_id
        LEFT JOIN payment_methods pm ON p.payment_method_id = pm.method_id
        LEFT JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE a.appointment_id = :appointment_id AND a.pat_id = :patient_id
        ORDER BY p.created_at DESC
        LIMIT 1
    ");
    $stmt->execute(['appointment_id' => $appointment_id, 'patient_id' => $patient_id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$appointment) {
        $error = 'Appointment not found or does not belong to you';
        $appointment = null;
    }
} catch (PDOException $e) {
    $error = 'Failed to fetch appointment: ' . $e->getMessage();
    $appointment = null;
}

require_once __DIR__ . '/../../views/patient/payment-confirmation.view.php';

