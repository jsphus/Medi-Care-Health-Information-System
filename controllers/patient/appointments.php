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
            $redirectUrl = '/patient/appointments';
            if (isset($_GET['tab'])) {
                $redirectUrl .= '/' . sanitize($_GET['tab']);
            }
            header('Location: ' . $redirectUrl . '?success=cancelled');
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
$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? (int)$_GET['status'] : null;

// Get all appointments data
$appointmentData = $appointmentModel->getForPatient($patient_id, [
    'search' => $search_query,
    'status' => $filter_status
]);

$all_appointments = $appointmentData['all'];
$upcoming_appointments = $appointmentData['upcoming'];
$past_appointments = $appointmentData['past'];

// Get today's appointments
$today = date('Y-m-d');
$currentDateTime = date('Y-m-d H:i:s');
$today_appointments = array_filter($all_appointments, function($apt) use ($today, $currentDateTime) {
    $apptDate = $apt['appointment_date'] ?? '';
    $apptTime = $apt['appointment_time'] ?? '00:00:00';
    $apptDateTime = $apptDate . ' ' . $apptTime;
    return $apptDate === $today && $apptDateTime >= $currentDateTime;
});
$today_appointments = array_values($today_appointments);

// Sort today's appointments by time ascending (nearest first)
usort($today_appointments, function($a, $b) {
    $timeA = isset($a['appointment_time']) ? strtotime($a['appointment_time']) : 0;
    $timeB = isset($b['appointment_time']) ? strtotime($b['appointment_time']) : 0;
    return $timeA <=> $timeB;
});

// Get next appointment - the nearest upcoming one (first non-cancelled/non-completed in sorted upcoming array)
$next_appointment = null;
if (!empty($upcoming_appointments)) {
    // Find the nearest appointment that is not cancelled or completed
    // Since upcoming_appointments is already sorted nearest first, we just need to find the first active one
    foreach ($upcoming_appointments as $apt) {
        $statusName = strtolower($apt['status_name'] ?? '');
        // Skip cancelled or completed appointments
        if (in_array($statusName, ['cancelled', 'completed'])) {
            continue;
        }
        
        // This is the nearest active appointment
        $next_appointment = $apt;
        break;
    }
    
    // Fallback to first appointment if all are cancelled/completed (shouldn't happen but safe fallback)
    if (!$next_appointment && !empty($upcoming_appointments)) {
        $next_appointment = $upcoming_appointments[0];
    }
}

// Fetch filter data
$filter_statuses = $appointmentModel->getPatientStatusFilters($patient_id);

// Calculate comprehensive statistics
$stats = [
    'total' => count($all_appointments),
    'today' => count($today_appointments),
    'upcoming' => count($upcoming_appointments),
    'past' => count($past_appointments),
    'completed' => count(array_filter($past_appointments, function($apt) {
        return strtolower($apt['status_name'] ?? '') === 'completed';
    })),
    'this_week' => 0,
    'this_month' => 0
];

// Calculate this week and month stats
$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekEnd = date('Y-m-d', strtotime('sunday this week'));
$monthStart = date('Y-m-01');
$monthEnd = date('Y-m-t');

$stats['this_week'] = count(array_filter($all_appointments, function($apt) use ($weekStart, $weekEnd) {
    return $apt['appointment_date'] >= $weekStart && $apt['appointment_date'] <= $weekEnd;
}));

$stats['this_month'] = count(array_filter($all_appointments, function($apt) use ($monthStart, $monthEnd) {
    return $apt['appointment_date'] >= $monthStart && $apt['appointment_date'] <= $monthEnd;
}));

require_once __DIR__ . '/../../views/patient/appointments.view.php';
