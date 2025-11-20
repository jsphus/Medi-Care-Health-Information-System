<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Patient.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$patient_id = $auth->getPatientId();
$error = '';
$success = '';
$patientModel = new Patient();

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name']);
    $middle_initial = sanitize($_POST['middle_initial'] ?? '');
    $last_name = sanitize($_POST['last_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone'] ?? '');
    $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
    $gender = sanitize($_POST['gender'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $emergency_contact = sanitize($_POST['emergency_contact'] ?? '');
    $emergency_phone = sanitize($_POST['emergency_phone'] ?? '');
    
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'First name, last name, and email are required';
    } elseif (!isValidEmail($email)) {
        $error = 'Invalid email format';
    } else {
        $updateResult = $patientModel->update($patient_id, [
            'pat_first_name' => $first_name,
            'pat_middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
            'pat_last_name' => $last_name,
            'pat_email' => $email,
            'pat_phone' => $phone,
            'pat_date_of_birth' => $date_of_birth,
            'pat_gender' => $gender,
            'pat_address' => $address,
            'pat_emergency_contact' => $emergency_contact,
            'pat_emergency_phone' => $emergency_phone
        ]);

        if ($updateResult['success']) {
            $success = 'Profile updated successfully';
        } else {
            $error = implode(', ', $updateResult['errors'] ?? ['Failed to update profile.']);
        }
    }
}

// Fetch patient profile
$patient = $patientModel->getById($patient_id);
if (!$patient) {
    $error = 'Patient profile not found';
    $patient = [];
}

require_once __DIR__ . '/../../views/patient/profile.view.php';
