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
                header('Location: /login');
                exit;
            }
        } catch (Exception $e) {
            header('Location: /login');
            exit;
        }
    } else {
        header('Location: /login');
        exit;
    }
}

$patient_id = (int)$patient_id;

$error = '';
$success = '';
$appointmentModel = new Appointment();
$patientModel = new Patient();

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'cancelled') {
        $success = 'Appointment cancelled successfully';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $appointment_id = sanitize($_POST['appointment_id'] ?? '');
    
    if (empty($appointment_id)) {
        $error = 'Invalid appointment ID';
    } else {
        $cancelResult = $appointmentModel->cancelForPatient($appointment_id, $patient_id);

        if ($cancelResult['success']) {
            header('Location: /patient/appointments/upcoming?success=cancelled');
            exit;
        }

        $error = $cancelResult['error'] ?? 'Unable to cancel appointment';
    }
}

$patient = $patientModel->getById($patient_id);
if (!$patient) {
    $error = 'Failed to fetch patient info.';
}

$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? (int)$_GET['status'] : null;

$appointmentData = $appointmentModel->getForPatient($patient_id, [
    'search' => $search_query,
    'status' => $filter_status
]);

$today = date('Y-m-d');
$today_appointments = array_filter($appointmentData['all'], function($apt) use ($today) {
    return $apt['appointment_date'] === $today;
});
$today_appointments = array_values($today_appointments);

$all_appointments = $appointmentData['all'];
$upcoming_appointments = $appointmentData['upcoming'];
$past_appointments = $appointmentData['past'];

$filter_statuses = $appointmentModel->getPatientStatusFilters($patient_id);

$stats = [
    'total' => count($all_appointments),
    'today' => count($today_appointments),
    'upcoming' => count($upcoming_appointments),
    'past' => count($past_appointments),
    'completed' => count(array_filter($past_appointments, function($apt) {
        return strtolower($apt['status_name'] ?? '') === 'completed';
    }))
];

$next_appointment = !empty($upcoming_appointments) ? $upcoming_appointments[0] : null;

require_once __DIR__ . '/../../views/patient/appointments.view.php';

