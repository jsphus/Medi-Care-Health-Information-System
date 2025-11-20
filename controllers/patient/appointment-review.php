<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Doctor.php';
require_once __DIR__ . '/../../classes/Service.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$patient_id = $auth->getPatientId();
$error = '';
$success = '';
$doctorModel = new Doctor();
$serviceModel = new Service();

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

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
    $doctor = $doctorModel->getDetailsById($appointment_data['doctor_id']);
    if (!$doctor || $doctor['doc_status'] !== 'active') {
        $error = 'Doctor not found';
        unset($_SESSION['appointment_review']);
    }
} else {
    $error = 'Invalid appointment data';
    unset($_SESSION['appointment_review']);
}

// Fetch service details if selected
$service = null;
if ($appointment_data && !empty($appointment_data['service_id'])) {
    $service = $serviceModel->getById($appointment_data['service_id']);
}

// Calculate total amount
$total_amount = 0;
if ($service && !empty($service['service_price'])) {
    $total_amount = floatval($service['service_price']);
} elseif ($doctor && !empty($doctor['doc_consultation_fee'])) {
    $total_amount = floatval($doctor['doc_consultation_fee']);
}

require_once __DIR__ . '/../../views/patient/appointment-review.view.php';

