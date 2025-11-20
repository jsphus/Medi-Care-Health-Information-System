<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Doctor.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$error = '';
$doctorModel = new Doctor();

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Get doctor ID from URL
$doctor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($doctor_id === 0) {
    header('Location: /patient/book');
    exit;
}

$doctor = $doctorModel->getDetailsById($doctor_id);
if (!$doctor || $doctor['doc_status'] !== 'active') {
    $error = 'Doctor not found or not available';
    $doctor = null;
}

require_once __DIR__ . '/../../views/patient/doctor-detail.view.php';

