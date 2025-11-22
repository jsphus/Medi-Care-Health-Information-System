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
        // Fetch service details if service_id exists
        if (!empty($appointment['service_id'])) {
            require_once __DIR__ . '/../../classes/Service.php';
            $serviceModel = new Service();
            $service_detail = $serviceModel->getById($appointment['service_id']);
            if ($service_detail) {
                $appointment['service_name'] = $service_detail['service_name'];
                $appointment['service_price'] = $service_detail['service_price'];
            }
        }
        
        // Fetch doctor consultation fee
        require_once __DIR__ . '/../../config/Database.php';
        $db = Database::getInstance();
        $doctor_info = $db->fetchOne(
            "SELECT doc_consultation_fee FROM doctors WHERE doc_id = :doc_id",
            ['doc_id' => $appointment['doc_id']]
        );
        if ($doctor_info) {
            $appointment['doc_consultation_fee'] = $doctor_info['doc_consultation_fee'];
        }
        
        // Check if appointment can be rescheduled
        $statusModel = new AppointmentStatus();
        $status = $statusModel->getById($appointment['status_id']);
        
        if ($status && in_array(strtolower($status['status_name']), ['cancelled', 'completed'])) {
            $error = 'This appointment cannot be rescheduled. Only pending or scheduled appointments can be rescheduled.';
            $appointment = null;
        } else {
            // Get available schedules for the doctor (same way as create-appointment)
            
            $today = date('Y-m-d');
            
            // Get all future available schedules for the selected doctor
            $doctor_schedules = $db->fetchAll(
                "SELECT * FROM schedules 
                 WHERE doc_id = :doc_id 
                 AND schedule_date >= :today 
                 ORDER BY schedule_date ASC, start_time ASC",
                ['doc_id' => $appointment['doc_id'], 'today' => $today]
            );
            
            // Get appointment counts for each schedule
            $schedule_appointment_counts = [];
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
                    $schedule_ids_placeholder = implode(',', array_map('intval', $schedule_ids));
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
                            AND a.appointment_id != :exclude_appointment_id
                        )
                        WHERE s.schedule_id IN ($schedule_ids_placeholder)
                        GROUP BY s.schedule_id, s.schedule_date, s.start_time, s.end_time
                    ";
                    
                    $count_results = $db->fetchAll($counts_query, ['exclude_appointment_id' => $appointment_id]);
                    
                    // Build lookup array
                    foreach ($count_results as $count_result) {
                        $key = $count_result['schedule_id'];
                        $schedule_appointment_counts[$key] = (int)$count_result['appointment_count'];
                    }
                }
            }
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

