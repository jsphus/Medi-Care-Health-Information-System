<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Patient.php';
require_once __DIR__ . '/../../classes/Appointment.php';
require_once __DIR__ . '/../../classes/MedicalRecord.php';
require_once __DIR__ . '/../../classes/Payment.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$patient_id = $auth->getPatientId();
$error = '';
$patientModel = new Patient();
$appointmentModel = new Appointment();
$medicalRecordModel = new MedicalRecord();
$paymentModel = new Payment();

// Get patient info
$patient = $patientModel->getById($patient_id);
$profile_picture_url = User::initializeProfilePicture($auth);
if (!$patient) {
    $error = 'Failed to fetch patient info.';
}

// Get upcoming appointments (next 5)
$upcoming_appointments = $appointmentModel->getUpcomingForPatient($patient_id, 5);

// Get recent medical records (last 5)
$recent_records = $medicalRecordModel->getRecentByPatient($patient_id, 5);

// Get recent payments (last 5)
$recent_payments = $paymentModel->getRecentForPatient($patient_id, 5);

// Get statistics
$stats = [
    'total_appointments' => $appointmentModel->countTotalForPatient($patient_id),
    'upcoming_appointments' => $appointmentModel->countUpcomingForPatient($patient_id),
    'completed_appointments' => $appointmentModel->countCompletedForPatient($patient_id),
    'total_payments' => $paymentModel->getTotalAmountForPatient($patient_id),
    'pending_payments' => $paymentModel->getPendingCountForPatient($patient_id)
];

require_once __DIR__ . '/../../views/patient/dashboard.view.php';

