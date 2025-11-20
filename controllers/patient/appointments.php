<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Appointment.php';
require_once __DIR__ . '/../../classes/Patient.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$patient_id = $auth->getPatientId();
if (!$patient_id || !is_numeric($patient_id)) {
    // If patient ID is not set, try to get it from the user record
    $user_id = $auth->getUserId();
    if ($user_id) {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT pat_id FROM users WHERE user_id = :user_id AND pat_id IS NOT NULL");
            $stmt->execute(['user_id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && $user['pat_id']) {
                $patient_id = (int)$user['pat_id'];
                $_SESSION['pat_id'] = $patient_id;
            } else {
                // No patient ID found, redirect to login
                header('Location: /login');
                exit;
            }
        } catch (Exception $e) {
            // Database error, redirect to login
            header('Location: /login');
            exit;
        }
    } else {
        // No user ID, redirect to login
        header('Location: /login');
        exit;
    }
}

$patient_id = (int)$patient_id; // Ensure it's an integer

$error = '';
$success = '';
$appointmentModel = new Appointment();
$patientModel = new Patient();

// Check for success message from redirect
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'cancelled') {
        $success = 'Appointment cancelled successfully';
    } elseif ($_GET['success'] === 'rescheduled') {
        $appointment_id = isset($_GET['id']) ? sanitize($_GET['id']) : '';
        $success = "Appointment rescheduled successfully! Your appointment ID is: <strong>$appointment_id</strong>.";
    }
}

// Handle appointment cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $appointment_id = sanitize($_POST['appointment_id'] ?? '');
    
    if (empty($appointment_id)) {
        $error = 'Invalid appointment ID';
    } else {
        $cancelResult = $appointmentModel->cancelForPatient($appointment_id, $patient_id);

        if ($cancelResult['success']) {
            header('Location: /patient/appointments?success=cancelled');
            exit;
        }

        $error = $cancelResult['error'] ?? 'Unable to cancel appointment';
    }
}

// Get patient info
$patient = $patientModel->getById($patient_id);
if (!$patient) {
    $error = 'Failed to fetch patient info.';
}

// Handle search and filters
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

$filter_status = isset($_GET['status']) ? (int)$_GET['status'] : null;
$filter_category = isset($_GET['category']) ? sanitize($_GET['category']) : '';

$appointmentData = $appointmentModel->getForPatient($patient_id, [
    'search' => $search_query,
    'status' => $filter_status
]);

$appointments = $appointmentData['all'];
$upcoming_appointments = $appointmentData['upcoming'];
$past_appointments = $appointmentData['past'];

if ($filter_category === 'upcoming') {
    $past_appointments = [];
} elseif ($filter_category === 'past') {
    $upcoming_appointments = [];
}

// Fetch filter data from database
$filter_statuses = $appointmentModel->getPatientStatusFilters($patient_id);

// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'upcoming' => 0,
    'completed' => 0
];

$stats['total'] = count($appointments);
$stats['upcoming'] = count($upcoming_appointments);
$stats['completed'] = count($past_appointments);

require_once __DIR__ . '/../../views/patient/appointments.view.php';
