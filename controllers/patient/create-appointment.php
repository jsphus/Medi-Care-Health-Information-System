<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requirePatient();

$db = Database::getInstance();
$patient_id = $auth->getPatientId();
$error = '';
$success = '';
$appointment_id = '';
$reschedule_id = isset($_GET['reschedule']) ? sanitize($_GET['reschedule']) : null;
$selected_doctor_id = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : null;
$existing_appointment = null;

// If rescheduling, get the existing appointment details
if ($reschedule_id) {
    try {
        $stmt = $db->prepare("
            SELECT a.*, d.doc_first_name, d.doc_last_name, sp.spec_name
            FROM appointments a
            LEFT JOIN doctors d ON a.doc_id = d.doc_id
            LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
            WHERE a.appointment_id = :appointment_id AND a.pat_id = :patient_id
        ");
        $stmt->execute(['appointment_id' => $reschedule_id, 'patient_id' => $patient_id]);
        $existing_appointment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existing_appointment) {
            $error = 'Appointment not found or you do not have permission to reschedule it';
            $reschedule_id = null;
        } else {
            // Check if appointment can be rescheduled
            $stmt = $db->prepare("SELECT status_name FROM appointment_statuses WHERE status_id = :status_id");
            $stmt->execute(['status_id' => $existing_appointment['status_id']]);
            $status = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($status && (strtolower($status['status_name']) === 'cancelled' || strtolower($status['status_name']) === 'completed')) {
                $error = 'This appointment cannot be rescheduled';
                $reschedule_id = null;
                $existing_appointment = null;
            }
        }
    } catch (PDOException $e) {
        $error = 'Failed to load appointment: ' . $e->getMessage();
        $reschedule_id = null;
        $existing_appointment = null;
    }
}

// Handle appointment creation/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
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
    
    // If action is 'confirm', proceed with appointment creation
    if ($action === 'confirm') {
        $reschedule_appointment_id = isset($_POST['reschedule_id']) ? sanitize($_POST['reschedule_id']) : null;
        
        // If rescheduling, get doctor_id and service_id from existing appointment (not from POST)
        if ($reschedule_appointment_id) {
            try {
                $stmt = $db->prepare("SELECT doc_id, service_id FROM appointments WHERE appointment_id = :appointment_id AND pat_id = :patient_id");
                $stmt->execute(['appointment_id' => $reschedule_appointment_id, 'patient_id' => $patient_id]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$existing) {
                    $error = 'You do not have permission to reschedule this appointment';
                } else {
                    $doctor_id = $existing['doc_id'];
                    $service_id = $existing['service_id'];
                }
            } catch (PDOException $e) {
                $error = 'Failed to load appointment: ' . $e->getMessage();
            }
        } else {
            // For new appointments, get from POST
            $doctor_id = (int)$_POST['doctor_id'];
            $service_id = !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null;
        }
        
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $notes = sanitize($_POST['notes'] ?? '');
        
        if (empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
            $error = 'Doctor, date, and time are required';
        }
        
        if (empty($error)) {
            try {
            // Get service duration or default to 30
            $duration = 30;
            if ($service_id) {
                $stmt = $db->prepare("SELECT service_duration_minutes FROM services WHERE service_id = :service_id");
                $stmt->execute(['service_id' => $service_id]);
                $service = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($service) {
                    $duration = $service['service_duration_minutes'] ?? 30;
                }
            }
            
            // If rescheduling, update existing appointment (only date and time)
            if ($reschedule_appointment_id) {
                // Verify the appointment belongs to this patient
                $stmt = $db->prepare("SELECT pat_id, status_id FROM appointments WHERE appointment_id = :appointment_id");
                $stmt->execute(['appointment_id' => $reschedule_appointment_id]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$existing || $existing['pat_id'] != $patient_id) {
                    $error = 'You do not have permission to reschedule this appointment';
                } else {
                    // Update only date, time, duration, and notes (keep original doctor and service)
                    $stmt = $db->prepare("
                        UPDATE appointments 
                        SET appointment_date = :appointment_date, appointment_time = :appointment_time,
                            appointment_duration = :duration, appointment_notes = :notes,
                            updated_at = NOW()
                        WHERE appointment_id = :appointment_id
                    ");
                    $stmt->execute([
                        'appointment_id' => $reschedule_appointment_id,
                        'appointment_date' => $appointment_date,
                        'appointment_time' => $appointment_time,
                        'duration' => $duration,
                        'notes' => $notes
                    ]);
                    
                    // Redirect to appointments page with success message
                    header('Location: /patient/appointments?success=rescheduled&id=' . $reschedule_appointment_id);
                    exit;
                }
            } else {
                // Create new appointment
                $appointment_id = generateAppointmentId($db);
                
                // Default status is usually 1 (Scheduled/Pending)
                $status_id = 1;
                
                $stmt = $db->prepare("
                    INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, 
                                             appointment_date, appointment_time, appointment_duration, 
                                             appointment_notes, created_at) 
                    VALUES (:appointment_id, :patient_id, :doctor_id, :service_id, :status_id,
                           :appointment_date, :appointment_time, :duration, :notes, NOW())
                ");
                $stmt->execute([
                    'appointment_id' => $appointment_id,
                    'patient_id' => $patient_id,
                    'doctor_id' => $doctor_id,
                    'service_id' => $service_id,
                    'status_id' => $status_id,
                    'appointment_date' => $appointment_date,
                    'appointment_time' => $appointment_time,
                    'duration' => $duration,
                    'notes' => $notes
                ]);
                
                // Clear review session
                unset($_SESSION['appointment_review']);
                
                // Redirect to payment page with appointment ID
                header('Location: /patient/payment?appointment_id=' . urlencode($appointment_id));
                exit;
            }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// Fetch doctors with specializations
try {
    $stmt = $db->query("
        SELECT d.*, s.spec_name 
        FROM doctors d
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
        WHERE d.doc_status = 'active'
        ORDER BY d.doc_first_name, d.doc_last_name
    ");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $doctors = [];
}

// Fetch services
try {
    $stmt = $db->query("SELECT * FROM services ORDER BY service_name");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $services = [];
}

require_once __DIR__ . '/../../views/patient/create-appointment.view.php';
