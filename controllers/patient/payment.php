<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Appointment.php';
require_once __DIR__ . '/../../classes/Payment.php';
require_once __DIR__ . '/../../classes/PaymentMethod.php';
require_once __DIR__ . '/../../classes/PaymentStatus.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$patient_id = $auth->getPatientId();
$error = '';
$success = '';
$appointmentModel = new Appointment();
$paymentModel = new Payment();
$paymentMethodModel = new PaymentMethod();
$paymentStatusModel = new PaymentStatus();

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Get appointment ID from URL
$appointment_id = isset($_GET['appointment_id']) ? sanitize($_GET['appointment_id']) : '';

if (empty($appointment_id)) {
    header('Location: /patient/appointments');
    exit;
}

$appointment = $appointmentModel->getForPatientById($appointment_id, $patient_id);
if (!$appointment) {
    $error = 'Appointment not found or does not belong to you';
    $appointment = null;
} else {
    $existing_payment = $paymentModel->getLatestByAppointment($appointment_id);
    if ($existing_payment) {
        header('Location: /patient/payments?payment_id=' . $existing_payment['payment_id']);
        exit;
    }

    // Calculate payment amount: consultation fee + service fee (if any)
    $consultation_fee = 0;
    if (!empty($appointment['doc_consultation_fee'])) {
        $consultation_fee = (float)$appointment['doc_consultation_fee'];
    }
    
    $service_fee = 0;
    if (!empty($appointment['service_price'])) {
        $service_fee = (float)$appointment['service_price'];
    }
    
    $payment_amount = $consultation_fee + $service_fee;
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'process_payment') {
    $payment_method_id = isset($_POST['payment_method_id']) ? (int)$_POST['payment_method_id'] : 0;
    $payment_reference = sanitize($_POST['payment_reference'] ?? '');
    $payment_notes = sanitize($_POST['payment_notes'] ?? '');
    
    if (empty($payment_method_id)) {
        $error = 'Payment method is required';
    } else {
        $pending_status = $paymentStatusModel->getByName('pending');

        if ($pending_status) {
            $createResult = $paymentModel->createPayment([
                'appointment_id' => $appointment_id,
                'payment_amount' => $payment_amount,
                'payment_method_id' => $payment_method_id,
                'payment_status_id' => $pending_status['payment_status_id'],
                'payment_reference' => $payment_reference,
                'payment_notes' => $payment_notes
            ]);

            if ($createResult['success']) {
                header('Location: /patient/payment-confirmation?appointment_id=' . urlencode($appointment_id));
                exit;
            }

            $error = 'Failed to process payment.';
        } else {
            $error = 'Payment status not found in system';
        }
    }
}

// Fetch payment methods
$payment_methods = $paymentMethodModel->getAllActive();

require_once __DIR__ . '/../../views/patient/payment.view.php';

