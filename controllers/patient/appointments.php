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
        try {
            // Verify the appointment belongs to this patient
            $stmt = $db->prepare("SELECT pat_id, appointment_date, status_id FROM appointments WHERE appointment_id = :appointment_id");
            $stmt->execute(['appointment_id' => $appointment_id]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$appointment) {
                $error = 'Appointment not found';
            } elseif ($appointment['pat_id'] != $patient_id) {
                $error = 'You do not have permission to cancel this appointment';
            } else {
                // Check if appointment is already cancelled or completed
                $stmt = $db->prepare("SELECT status_name FROM appointment_statuses WHERE status_id = :status_id");
                $stmt->execute(['status_id' => $appointment['status_id']]);
                $status = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($status && (strtolower($status['status_name']) === 'cancelled' || strtolower($status['status_name']) === 'completed')) {
                    $error = 'This appointment cannot be cancelled';
                } else {
                    // Get cancelled status ID
                    $stmt = $db->prepare("SELECT status_id FROM appointment_statuses WHERE LOWER(status_name) = 'cancelled' LIMIT 1");
                    $stmt->execute();
                    $cancelled_status = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($cancelled_status) {
                        // Update appointment status to cancelled
                        $stmt = $db->prepare("
                            UPDATE appointments 
                            SET status_id = :status_id, updated_at = NOW() 
                            WHERE appointment_id = :appointment_id
                        ");
                        $stmt->execute([
                            'status_id' => $cancelled_status['status_id'],
                            'appointment_id' => $appointment_id
                        ]);
                        $success = 'Appointment cancelled successfully';
                        // Redirect to prevent form resubmission
                        header('Location: /patient/appointments?success=cancelled');
                        exit;
                    } else {
                        $error = 'Cancelled status not found in system';
                    }
                }
            }
        } catch (PDOException $e) {
            $error = 'Failed to cancel appointment: ' . $e->getMessage();
        }
    }
}

// Get patient info
try {
    $stmt = $db->prepare("SELECT * FROM patients WHERE pat_id = :patient_id");
    $stmt->execute(['patient_id' => $patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch patient info: ' . $e->getMessage();
    $patient = null;
}

// Handle search and filters
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

$filter_status = isset($_GET['status']) ? (int)$_GET['status'] : null;
$filter_category = isset($_GET['category']) ? sanitize($_GET['category']) : '';

// Get all appointments for this patient with filters
try {
    $where_conditions = ['a.pat_id = :patient_id'];
    $params = ['patient_id' => $patient_id];

    if (!empty($search_query)) {
        $where_conditions[] = "(d.doc_first_name LIKE :search OR d.doc_last_name LIKE :search OR s.service_name LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }

    if ($filter_status) {
        $where_conditions[] = "a.status_id = :status";
        $params['status'] = $filter_status;
    }

    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

    $stmt = $db->prepare("
        SELECT a.*, 
               d.doc_first_name, d.doc_last_name, d.doc_specialization_id,
               s.service_name, s.service_price,
               st.status_name, st.status_color,
               sp.spec_name,
               ud.profile_picture_url as doctor_profile_picture
        FROM appointments a
        LEFT JOIN doctors d ON a.doc_id = d.doc_id
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        LEFT JOIN users ud ON ud.doc_id = d.doc_id
        $where_clause
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $stmt->execute($params);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Separate into upcoming and past based on filter_category
    $today = date('Y-m-d');
    if ($filter_category === 'upcoming') {
        $upcoming_appointments = array_filter($appointments, function($apt) use ($today) {
            return $apt['appointment_date'] >= $today;
        });
        $past_appointments = [];
    } elseif ($filter_category === 'past') {
        $upcoming_appointments = [];
        $past_appointments = array_filter($appointments, function($apt) use ($today) {
            return $apt['appointment_date'] < $today;
        });
    } else {
        $upcoming_appointments = array_filter($appointments, function($apt) use ($today) {
            return $apt['appointment_date'] >= $today;
        });
        $past_appointments = array_filter($appointments, function($apt) use ($today) {
            return $apt['appointment_date'] < $today;
        });
    }
    
} catch (PDOException $e) {
    $error = 'Failed to fetch appointments: ' . $e->getMessage();
    $appointments = [];
    $upcoming_appointments = [];
    $past_appointments = [];
}

// Fetch filter data from database
$filter_statuses = [];
try {
    // Get unique statuses from this patient's appointments
    $stmt = $db->prepare("SELECT DISTINCT st.status_id, st.status_name FROM appointments a JOIN appointment_statuses st ON a.status_id = st.status_id WHERE a.pat_id = :patient_id ORDER BY st.status_name");
    $stmt->execute(['patient_id' => $patient_id]);
    $filter_statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $filter_statuses = [];
}

// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'upcoming' => 0,
    'completed' => 0
];

try {
    // Total appointments for this patient
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments WHERE pat_id = :patient_id");
    $stmt->execute(['patient_id' => $patient_id]);
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Upcoming appointments
    $stats['upcoming'] = count($upcoming_appointments);
    
    // Completed appointments
    $stats['completed'] = count($past_appointments);
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/patient/appointments.view.php';
