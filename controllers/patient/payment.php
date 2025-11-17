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

// Initialize profile picture for consistent display across the system
$profile_picture_url = initializeProfilePicture($auth, $db);

// Get appointment ID from URL
$appointment_id = isset($_GET['appointment_id']) ? sanitize($_GET['appointment_id']) : '';

if (empty($appointment_id)) {
    header('Location: /patient/appointments');
    exit;
}

// Fetch appointment details
try {
    $stmt = $db->prepare("
        SELECT a.*, 
               d.doc_first_name, d.doc_last_name, d.doc_consultation_fee,
               s.service_name, s.service_price,
               st.status_name
        FROM appointments a
        LEFT JOIN doctors d ON a.doc_id = d.doc_id
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
        WHERE a.appointment_id = :appointment_id AND a.pat_id = :patient_id
    ");
    $stmt->execute(['appointment_id' => $appointment_id, 'patient_id' => $patient_id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$appointment) {
        $error = 'Appointment not found or does not belong to you';
        $appointment = null;
    } else {
        // Check if payment already exists
        $stmt = $db->prepare("SELECT payment_id FROM payments WHERE appointment_id = :appointment_id");
        $stmt->execute(['appointment_id' => $appointment_id]);
        $existing_payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing_payment) {
            // Payment already exists, redirect to payments page
            header('Location: /patient/payments?payment_id=' . $existing_payment['payment_id']);
            exit;
        }
        
        // Calculate payment amount
        $payment_amount = 0;
        if (!empty($appointment['service_price'])) {
            $payment_amount = floatval($appointment['service_price']);
        } elseif (!empty($appointment['doc_consultation_fee'])) {
            $payment_amount = floatval($appointment['doc_consultation_fee']);
        }
    }
} catch (PDOException $e) {
    $error = 'Failed to fetch appointment: ' . $e->getMessage();
    $appointment = null;
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'process_payment') {
    $payment_method_id = isset($_POST['payment_method_id']) ? (int)$_POST['payment_method_id'] : 0;
    $payment_reference = sanitize($_POST['payment_reference'] ?? '');
    $payment_notes = sanitize($_POST['payment_notes'] ?? '');
    
    if (empty($payment_method_id)) {
        $error = 'Payment method is required';
    } else {
        try {
            // Get pending status ID
            $stmt = $db->prepare("SELECT payment_status_id FROM payment_statuses WHERE LOWER(status_name) = 'pending' LIMIT 1");
            $stmt->execute();
            $pending_status = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($pending_status) {
                // Create payment record
                $stmt = $db->prepare("
                    INSERT INTO payments (appointment_id, payment_amount, payment_method_id, payment_status_id, 
                                         payment_date, payment_reference, payment_notes, created_at) 
                    VALUES (:appointment_id, :payment_amount, :payment_method_id, :payment_status_id, 
                           NOW(), :payment_reference, :payment_notes, NOW())
                ");
                $stmt->execute([
                    'appointment_id' => $appointment_id,
                    'payment_amount' => $payment_amount,
                    'payment_method_id' => $payment_method_id,
                    'payment_status_id' => $pending_status['payment_status_id'],
                    'payment_reference' => $payment_reference ?: null,
                    'payment_notes' => $payment_notes ?: null
                ]);
                
                // Redirect to payment confirmation
                header('Location: /patient/payment-confirmation?appointment_id=' . urlencode($appointment_id));
                exit;
            } else {
                $error = 'Payment status not found in system';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch payment methods
try {
    $payment_methods = $db->query("SELECT * FROM payment_methods WHERE is_active = true ORDER BY method_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $payment_methods = [];
}

require_once __DIR__ . '/../../views/patient/payment.view.php';

