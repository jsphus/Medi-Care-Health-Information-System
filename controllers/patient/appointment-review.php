<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requirePatient();

$db = Database::getInstance();
$patient_id = $auth->getPatientId();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = initializeProfilePicture($auth, $db);

// Get appointment data from session or POST
$appointment_data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'review') {
    // Store appointment data in session for review
    $_SESSION['appointment_review'] = [
        'doctor_id' => (int)$_POST['doctor_id'],
        'service_id' => !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null,
        'appointment_date' => $_POST['appointment_date'],
        'appointment_time' => $_POST['appointment_time'],
        'notes' => sanitize($_POST['notes'] ?? '')
    ];
    
    $appointment_data = $_SESSION['appointment_review'];
} elseif (isset($_SESSION['appointment_review'])) {
    $appointment_data = $_SESSION['appointment_review'];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If POST but no action, try to get data from POST
    if (isset($_POST['doctor_id'])) {
        $_SESSION['appointment_review'] = [
            'doctor_id' => (int)$_POST['doctor_id'],
            'service_id' => !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null,
            'appointment_date' => $_POST['appointment_date'] ?? '',
            'appointment_time' => $_POST['appointment_time'] ?? '',
            'notes' => sanitize($_POST['notes'] ?? '')
        ];
        $appointment_data = $_SESSION['appointment_review'];
    } else {
        // No appointment data, redirect to book page
        header('Location: /patient/book');
        exit;
    }
} else {
    // No appointment data, redirect to book page
    header('Location: /patient/book');
    exit;
}

// Fetch doctor details
$doctor = null;
if ($appointment_data && isset($appointment_data['doctor_id'])) {
    try {
        $stmt = $db->prepare("
            SELECT d.*, s.spec_name 
            FROM doctors d
            LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
            WHERE d.doc_id = :doctor_id AND d.doc_status = 'active'
        ");
        $stmt->execute(['doctor_id' => $appointment_data['doctor_id']]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$doctor) {
            $error = 'Doctor not found';
            unset($_SESSION['appointment_review']);
        }
    } catch (PDOException $e) {
        $error = 'Failed to fetch doctor details: ' . $e->getMessage();
        $doctor = null;
    }
} else {
    $error = 'Invalid appointment data';
    unset($_SESSION['appointment_review']);
}

// Fetch service details if selected
$service = null;
if ($appointment_data && isset($appointment_data['service_id']) && $appointment_data['service_id']) {
    try {
        $stmt = $db->prepare("SELECT * FROM services WHERE service_id = :service_id");
        $stmt->execute(['service_id' => $appointment_data['service_id']]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Service not found, continue without it
    }
}

// Calculate total amount
$total_amount = 0;
if ($service && !empty($service['service_price'])) {
    $total_amount = floatval($service['service_price']);
} elseif ($doctor && !empty($doctor['doc_consultation_fee'])) {
    $total_amount = floatval($doctor['doc_consultation_fee']);
}

require_once __DIR__ . '/../../views/patient/appointment-review.view.php';

