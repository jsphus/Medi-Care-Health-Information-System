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
                    // Clear filter session variables
                    unset($_SESSION['filter_date']);
                    unset($_SESSION['filter_time']);
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
        // Clear filter session variables
        unset($_SESSION['filter_date']);
        unset($_SESSION['filter_time']);
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
                    // Clear filter session variables
                    unset($_SESSION['filter_date']);
                    unset($_SESSION['filter_time']);
                    header('Location: /patient/payment?appointment_id=' . urlencode($createResult['id']));
                    exit;
                }

                $error = implode(', ', $createResult['errors'] ?? ['Failed to create appointment.']);
            }
        }
    }
}

// Get appointment date and time for filtering doctors by availability
$appointment_date = null;
$appointment_time = null;

// Check if this is a filter request (action = 'filter')
$isFilterRequest = isset($_POST['action']) && $_POST['action'] === 'filter';

// Check POST data (for filtering or form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['appointment_date']) && isset($_POST['appointment_time'])) {
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        // Store in session for persistence
        $_SESSION['filter_date'] = $appointment_date;
        $_SESSION['filter_time'] = $appointment_time;
    }
}
// Check session data (persisted filter)
elseif (isset($_SESSION['filter_date']) && isset($_SESSION['filter_time'])) {
    $appointment_date = $_SESSION['filter_date'];
    $appointment_time = $_SESSION['filter_time'];
}
// Check session appointment data
elseif ($session_appointment_data && isset($session_appointment_data['appointment_date']) && isset($session_appointment_data['appointment_time'])) {
    $appointment_date = $session_appointment_data['appointment_date'];
    $appointment_time = $session_appointment_data['appointment_time'];
}
// Check existing appointment (for rescheduling)
elseif ($existing_appointment && isset($existing_appointment['appointment_date']) && isset($existing_appointment['appointment_time'])) {
    $appointment_date = $existing_appointment['appointment_date'];
    $appointment_time = $existing_appointment['appointment_time'];
}

// Fetch doctors with specializations and profile pictures, filtered by availability if date/time provided
$doctorOptions = [];
if ($appointment_date && $appointment_time) {
    $doctorOptions['appointment_date'] = $appointment_date;
    $doctorOptions['appointment_time'] = $appointment_time;
}
$doctors = $doctorModel->searchDoctors($doctorOptions);

// If this was just a filter request, preserve selected doctor if still available
if ($isFilterRequest && isset($_POST['doctor_id']) && !empty($_POST['doctor_id'])) {
    $selected_doctor_id = (int)$_POST['doctor_id'];
    // Verify the selected doctor is still in the filtered list
    $doctorStillAvailable = false;
    foreach ($doctors as $doctor) {
        if ($doctor['doc_id'] == $selected_doctor_id) {
            $doctorStillAvailable = true;
            break;
        }
    }
    // If doctor is no longer available, clear selection
    if (!$doctorStillAvailable) {
        $selected_doctor_id = null;
    }
}

// Fetch services
$services = $serviceModel->getAll();

// Fetch doctor schedules if a doctor is selected or if rescheduling
$doctor_schedules = [];
$schedule_appointment_counts = [];
$doctor_id_for_schedules = $selected_doctor_id;
if ($reschedule_id && $existing_appointment) {
    $doctor_id_for_schedules = $existing_appointment['doc_id'];
}

if ($doctor_id_for_schedules) {
    require_once __DIR__ . '/../../classes/Schedule.php';
    require_once __DIR__ . '/../../config/Database.php';
    $scheduleModel = new Schedule();
    $db = Database::getInstance();
    
    $today = date('Y-m-d');
    
    // Get all future available schedules for the selected doctor
    $doctor_schedules = $db->fetchAll(
        "SELECT * FROM schedules 
         WHERE doc_id = :doc_id 
         AND schedule_date >= :today 
         ORDER BY schedule_date ASC, start_time ASC",
        ['doc_id' => $doctor_id_for_schedules, 'today' => $today]
    );
    
    // Get appointment counts for each schedule
    if (!empty($doctor_schedules)) {
        // Pre-fetch excluded status IDs
        $excluded_status_ids = $db->fetchAll(
            "SELECT status_id FROM appointment_statuses WHERE LOWER(status_name) IN ('cancelled', 'completed')"
        );
        $excluded_status_ids_array = array_column($excluded_status_ids, 'status_id');
        $excluded_status_ids_placeholder = !empty($excluded_status_ids_array) 
            ? implode(',', array_map('intval', $excluded_status_ids_array)) 
            : '0';
        
        $schedule_ids = array_column($doctor_schedules, 'schedule_id');
        if (!empty($schedule_ids)) {
            $counts_query = "
                SELECT 
                    s.schedule_id,
                    s.schedule_date,
                    s.start_time,
                    s.end_time,
                    COALESCE(COUNT(a.appointment_id), 0) as appointment_count
                FROM schedules s
                LEFT JOIN appointments a ON (
                    a.doc_id = s.doc_id
                    AND a.appointment_date = s.schedule_date
                    AND a.appointment_time >= s.start_time
                    AND a.appointment_time < s.end_time
                    AND a.status_id NOT IN ($excluded_status_ids_placeholder)
                )
                WHERE s.schedule_id IN (" . implode(',', array_map('intval', $schedule_ids)) . ")
                GROUP BY s.schedule_id, s.schedule_date, s.start_time, s.end_time
            ";
            
            $count_results = $db->fetchAll($counts_query);
            
            // Build lookup array
            foreach ($count_results as $count_result) {
                $key = $count_result['schedule_id'];
                $schedule_appointment_counts[$key] = (int)$count_result['appointment_count'];
            }
        }
    }
}

require_once __DIR__ . '/../../views/patient/create-appointment.view.php';
