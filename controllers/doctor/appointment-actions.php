<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Appointment.php';
require_once __DIR__ . '/../../classes/services/DoctorService.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$auth = new Auth();
$auth->requireDoctor();

$doctor_id = $auth->getDoctorId();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_details':
            $appointment_id = sanitize($_GET['appointment_id'] ?? '');
            if (empty($appointment_id)) {
                echo json_encode(['success' => false, 'error' => 'Appointment ID required']);
                exit;
            }
            
            $appointmentModel = new Appointment();
            $db = Database::getInstance();
            
            // Get appointment details
            $appointment = $appointmentModel->getById($appointment_id);
            
            if (!$appointment) {
                echo json_encode(['success' => false, 'error' => 'Appointment not found']);
                exit;
            }
            
            // Verify doctor owns this appointment
            if ($appointment['doc_id'] != $doctor_id) {
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                exit;
            }
            
            // Get patient medical records (via appointments)
            try {
                $stmt = $db->prepare("
                    SELECT mr.*, 
                           a.pat_id, a.doc_id, a.appointment_date, a.appointment_time,
                           p.pat_first_name, p.pat_last_name
                    FROM medical_records mr
                    JOIN appointments a ON mr.appt_id = a.appointment_id
                    JOIN patients p ON a.pat_id = p.pat_id
                    WHERE a.pat_id = :pat_id
                    ORDER BY mr.med_rec_visit_date DESC
                    LIMIT 5
                ");
                $stmt->execute(['pat_id' => $appointment['pat_id']]);
                $patientRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $patientRecords = [];
            }
            
            echo json_encode([
                'success' => true,
                'appointment' => $appointment,
                'recent_records' => $patientRecords
            ]);
            break;
            
        case 'update_status':
            $appointment_id = sanitize($_POST['appointment_id'] ?? '');
            $status_id = (int)($_POST['status_id'] ?? 0);
            $notes = sanitize($_POST['notes'] ?? '');
            
            if (empty($appointment_id) || $status_id <= 0) {
                echo json_encode(['success' => false, 'error' => 'Appointment ID and Status ID required']);
                exit;
            }
            
            $appointmentModel = new Appointment();
            $appointment = $appointmentModel->getById($appointment_id);
            
            if (!$appointment) {
                echo json_encode(['success' => false, 'error' => 'Appointment not found']);
                exit;
            }
            
            // Verify doctor owns this appointment
            if ($appointment['doc_id'] != $doctor_id) {
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                exit;
            }
            
            // Update appointment
            $updateData = [
                'appointment_id' => $appointment_id,
                'status_id' => $status_id
            ];
            
            if (!empty($notes)) {
                $updateData['appointment_notes'] = $notes;
            }
            
            $result = $appointmentModel->updateAppointment($updateData);
            
            if ($result['success'] ?? false) {
                // Get updated appointment
                $updated = $appointmentModel->getById($appointment_id);
                echo json_encode([
                    'success' => true,
                    'message' => 'Appointment status updated successfully',
                    'appointment' => $updated
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => implode(', ', $result['errors'] ?? ['Failed to update appointment'])
                ]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

