<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Appointment.php';
require_once __DIR__ . '/../../classes/AppointmentStatus.php';
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

// Handle payment creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_payment') {
    $appointment_id = sanitize($_POST['appointment_id'] ?? '');
    $payment_method_id = isset($_POST['payment_method_id']) ? (int)$_POST['payment_method_id'] : 0;
    $payment_reference = sanitize($_POST['payment_reference'] ?? '');
    $payment_notes = sanitize($_POST['payment_notes'] ?? '');
    
    if (empty($appointment_id) || empty($payment_method_id)) {
        $error = 'Appointment and payment method are required';
    } else {
        $appointment = $appointmentModel->getForPatientById($appointment_id, $patient_id);
        if (!$appointment) {
            $error = 'Appointment not found or does not belong to you';
        } elseif ($paymentModel->hasPaymentForAppointment($appointment_id)) {
            $error = 'Payment already exists for this appointment';
        } else {
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
            
            if ($payment_amount <= 0) {
                $error = 'Cannot determine payment amount. Please contact the clinic.';
            }

            if (empty($error) && $payment_amount > 0) {
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
                        header('Location: /patient/payments?success=created');
                        exit;
                    }

                    $error = 'Failed to submit payment. Please try again.';
                } else {
                    $error = 'Payment status not found in system';
                }
            }
        }
    }
}

// Check for success message from redirect
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'created') {
        $success = 'Payment submitted successfully! It will be reviewed and confirmed by the clinic.';
    }
}

// Handle search
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

// Get all payments for this patient
$payments = $paymentModel->getForPatient($patient_id, $search_query);

// Calculate totals
$total_paid = 0;
$total_pending = 0;
foreach ($payments as $payment) {
    if (strtolower($payment['status_name']) === 'paid') {
        $total_paid += $payment['payment_amount'];
    } elseif (strtolower($payment['status_name']) === 'pending') {
        $total_pending += $payment['payment_amount'];
    }
}

// Calculate statistics for summary cards
$stats = $paymentModel->getStatsForPatient($patient_id);

// Get unpaid appointments (appointments without payments)
$unpaid_appointments = $appointmentModel->getUnpaidForPatient($patient_id);

// Fetch payment methods for dropdown
$payment_methods = $paymentMethodModel->getAllActive();

require_once __DIR__ . '/../../views/patient/payments.view.php';

