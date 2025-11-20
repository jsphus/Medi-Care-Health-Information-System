<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Appointment.php';
require_once __DIR__ . '/../../classes/Schedule.php';
require_once __DIR__ . '/../../classes/AppointmentStatus.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$patient_id = $auth->getPatientId();
$error = '';
$success = '';
$appointment = null;
$available_schedules = [];

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Get appointment ID from query parameter
$appointment_id = isset($_GET['id']) ? sanitize($_GET['id']) : null;

if (!$appointment_id) {
    $error = 'Invalid appointment ID';
} else {
    // Use Appointment class to get appointment details
    $appointmentModel = new Appointment();
    $appointment = $appointmentModel->getDetailedForPatient($appointment_id, $patient_id);
    
    if (!$appointment) {
        $error = 'Appointment not found or you do not have permission to reschedule it';
    } else {
        // Check if appointment can be rescheduled
        $statusModel = new AppointmentStatus();
        $status = $statusModel->getById($appointment['status_id']);
        
        if ($status && in_array(strtolower($status['status_name']), ['cancelled', 'completed'])) {
            $error = 'This appointment cannot be rescheduled. Only pending or scheduled appointments can be rescheduled.';
            $appointment = null;
        } else {
            // Get available schedules for the doctor
            $scheduleModel = new Schedule();
            $available_schedules = $scheduleModel->getByDoctor($appointment['doc_id']);
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $appointment) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'reschedule') {
        $appointment_date = $_POST['appointment_date'] ?? '';
        $appointment_time = $_POST['appointment_time'] ?? '';
        $notes = sanitize($_POST['notes'] ?? '');
        
        if (empty($appointment_date) || empty($appointment_time)) {
            $error = 'Date and time are required';
        } else {
            // Use Appointment class method for rescheduling
            $rescheduleResult = $appointmentModel->rescheduleForPatient($appointment_id, $patient_id, [
                'appointment_date' => $appointment_date,
                'appointment_time' => $appointment_time,
                'appointment_notes' => $notes
            ]);

            if ($rescheduleResult['success']) {
                header('Location: /patient/appointments?success=rescheduled&id=' . $appointment_id);
                exit;
            }

            $error = $rescheduleResult['error'] ?? 'Failed to reschedule appointment. Please try again.';
        }
    }
}

require_once __DIR__ . '/../../views/patient/reschedule-appointment.view.php';

