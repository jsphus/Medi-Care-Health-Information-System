<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Appointment.php';
require_once __DIR__ . '/../../classes/Payment.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$patient_id = $auth->getPatientId();
$error = '';
$appointmentModel = new Appointment();
$paymentModel = new Payment();

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Get appointment ID from URL
$appointment_id = isset($_GET['appointment_id']) ? sanitize($_GET['appointment_id']) : '';

if (empty($appointment_id)) {
    header('Location: /patient/appointments');
    exit;
}

$appointment = $appointmentModel->getForPatientById($appointment_id, $patient_id);
if ($appointment) {
    $payment = $paymentModel->getLatestDetailsByAppointment($appointment_id);
    if ($payment) {
        $appointment = array_merge($appointment, $payment);
    } else {
        $error = 'Payment not found for this appointment.';
    }
} else {
    $error = 'Appointment not found or does not belong to you';
}

require_once __DIR__ . '/../../views/patient/payment-confirmation.view.php';

