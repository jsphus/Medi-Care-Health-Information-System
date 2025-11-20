<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Appointment.php';
require_once __DIR__ . '/../../classes/Doctor.php';
require_once __DIR__ . '/../../classes/Service.php';
require_once __DIR__ . '/../../classes/AppointmentStatus.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$patient_id = $auth->getPatientId();
$error = '';
$success = '';
$appointment_id = '';
$reschedule_id = isset($_GET['reschedule']) ? sanitize($_GET['reschedule']) : null;
$selected_doctor_id = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : null;
$existing_appointment = null;

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);
$appointmentModel = new Appointment();
$doctorModel = new Doctor();
$serviceModel = new Service();
$statusModel = new AppointmentStatus();

// Check for session data from review page (when user clicks "Edit Details")
$session_appointment_data = null;
if (isset($_SESSION['appointment_review']) && !$reschedule_id) {
    $session_appointment_data = $_SESSION['appointment_review'];
    // Use doctor_id from session if not already set from URL
    if (!$selected_doctor_id && isset($session_appointment_data['doctor_id'])) {
        $selected_doctor_id = (int)$session_appointment_data['doctor_id'];
    }
}

// If rescheduling, get the existing appointment details
if ($reschedule_id) {
    $existing_appointment = $appointmentModel->getDetailedForPatient($reschedule_id, $patient_id);

    if (!$existing_appointment) {
        $error = 'Appointment not found or you do not have permission to reschedule it';
        $reschedule_id = null;
    } else {
        $status = $statusModel->getById($existing_appointment['status_id']);
        if ($status && in_array(strtolower($status['status_name']), ['cancelled', 'completed'])) {
            $error = 'This appointment cannot be rescheduled';
            $reschedule_id = null;
            $existing_appointment = null;
        }
    }
}

// Handle appointment creation/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // If action is 'reschedule', directly update the appointment without review/payment
    if ($action === 'reschedule') {
        $reschedule_appointment_id = isset($_POST['reschedule_id']) ? sanitize($_POST['reschedule_id']) : $reschedule_id;
        
        if (!$reschedule_appointment_id) {
            $error = 'Invalid reschedule request';
        } else {
            $appointment_date = $_POST['appointment_date'] ?? '';
            $appointment_time = $_POST['appointment_time'] ?? '';
            $notes = sanitize($_POST['notes'] ?? '');
            
            if (empty($appointment_date) || empty($appointment_time)) {
                $error = 'Date and time are required';
            } else {
                $rescheduleResult = $appointmentModel->rescheduleForPatient($reschedule_appointment_id, $patient_id, [
                    'appointment_date' => $appointment_date,
                    'appointment_time' => $appointment_time,
                    'appointment_notes' => $notes
                ]);

                if ($rescheduleResult['success']) {
                    header('Location: /patient/appointments?success=rescheduled&id=' . $reschedule_appointment_id);
                    exit;
                }

                $error = $rescheduleResult['error'] ?? 'Failed to reschedule appointment. Please try again.';
            }
        }
    }
    
    // If action is 'review', store data in session and redirect to review page
    if ($action === 'review') {
        // Store appointment data in session for review
        $_SESSION['appointment_review'] = [
            'doctor_id' => (int)$_POST['doctor_id'],
            'service_id' => !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null,
            'appointment_date' => $_POST['appointment_date'] ?? '',
            'appointment_time' => $_POST['appointment_time'] ?? '',
            'notes' => sanitize($_POST['notes'] ?? '')
        ];
        header('Location: /patient/appointment-review');
        exit;
    }
    
    // If action is 'confirm', proceed with appointment creation (ONLY for new appointments)
    if ($action === 'confirm') {
        // Prevent rescheduling through confirm action - rescheduling should use 'reschedule' action
        $reschedule_appointment_id = isset($_POST['reschedule_id']) ? sanitize($_POST['reschedule_id']) : null;
        if ($reschedule_appointment_id) {
            $error = 'Invalid request. Please use the reschedule function to reschedule appointments.';
        } else {
            // For new appointments only, get from POST
            $doctor_id = (int)$_POST['doctor_id'];
            $service_id = !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null;
            $appointment_date = $_POST['appointment_date'];
            $appointment_time = $_POST['appointment_time'];
            $notes = sanitize($_POST['notes'] ?? '');
            
            if (empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
                $error = 'Doctor, date, and time are required';
            }
            
            if (empty($error)) {
                $createResult = $appointmentModel->bookForPatient([
                    'pat_id' => $patient_id,
                    'doc_id' => $doctor_id,
                    'service_id' => $service_id,
                    'appointment_date' => $appointment_date,
                    'appointment_time' => $appointment_time,
                    'appointment_notes' => $notes
                ]);

                if ($createResult['success']) {
                    unset($_SESSION['appointment_review']);
                    header('Location: /patient/payment?appointment_id=' . urlencode($createResult['id']));
                    exit;
                }

                $error = implode(', ', $createResult['errors'] ?? ['Failed to create appointment.']);
            }
        }
    }
}

// Fetch doctors with specializations and profile pictures
$doctors = $doctorModel->searchDoctors();

// Fetch services
$services = $serviceModel->getAll();

require_once __DIR__ . '/../../views/patient/create-appointment.view.php';
