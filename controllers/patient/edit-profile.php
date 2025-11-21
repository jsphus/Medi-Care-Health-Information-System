<?php
// Set headers to prevent caching - must be before any output
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Patient.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$patient_id = $auth->getPatientId();
$user_id = $auth->getUserId();
$error = '';
$success = '';
$patientModel = new Patient();
$userModel = new User();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $first_name = sanitize($_POST['first_name'] ?? '');
        $middle_initial = sanitize($_POST['middle_initial'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        if (!empty($phone)) {
            $phone = formatPhoneNumber($phone);
        }
        $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $gender = sanitize($_POST['gender'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $emergency_contact = sanitize($_POST['emergency_contact'] ?? '');
        $emergency_phone = sanitize($_POST['emergency_phone'] ?? '');
        if (!empty($emergency_phone)) {
            $emergency_phone = formatPhoneNumber($emergency_phone);
        }
        
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
                'pat_gender' => $gender ?: null,
                'pat_address' => $address ?: null,
                'pat_emergency_contact' => $emergency_contact ?: null,
                'pat_emergency_phone' => $emergency_phone ?: null
            ]);

            if ($updateResult['success']) {
                // Redirect to account page with success message
                header('Location: /patient/account?success=updated');
                exit;
            } else {
                $error = implode(', ', $updateResult['errors'] ?? ['Failed to update account.']);
            }
        }
    }
}

// Check for success message from redirect
if (isset($_GET['success']) && $_GET['success'] === 'updated') {
    $success = 'Account information updated successfully';
}

// Fetch patient data using Patient class
try {
    if ($patient_id === null) {
        // Try to get patient_id from users table
        $user_data = $userModel->getById($user_id);
        if ($user_data && !empty($user_data['pat_id'])) {
            $patient_id = $user_data['pat_id'];
            $_SESSION['pat_id'] = $patient_id;
        } else {
            throw new Exception('Patient ID is null. Cannot fetch patient data.');
        }
    }
    
    $patient = $patientModel->getById($patient_id);
    
    if (!$patient || empty($patient)) {
        throw new Exception('Patient data not found for patient_id: ' . $patient_id);
    }
    
    // Ensure all required keys exist with defaults
    $defaults = [
        'pat_id' => $patient_id,
        'pat_first_name' => null,
        'pat_middle_initial' => null,
        'pat_last_name' => null,
        'pat_email' => null,
        'pat_phone' => null,
        'pat_date_of_birth' => null,
        'pat_gender' => null,
        'pat_address' => null,
        'pat_emergency_contact' => null,
        'pat_emergency_phone' => null,
        'pat_medical_history' => null,
        'pat_allergies' => null,
        'pat_insurance_provider' => null,
        'pat_insurance_number' => null,
        'created_at' => null,
        'updated_at' => null
    ];
    $patient = array_merge($defaults, $patient);
    
    $profile_picture_url = $userModel->getProfilePictureByUserId($user_id);
    
    // Debug: Log what we're passing to the view
    error_log("Patient Edit Profile - Data fetched successfully");
    error_log("Patient Edit Profile - Patient array keys: " . implode(', ', array_keys($patient)));
    error_log("Patient Edit Profile - Email: " . ($patient['pat_email'] ?? 'NULL'));
} catch (Exception $e) {
    error_log("Patient Edit Profile Fetch Error: " . $e->getMessage());
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    // Don't set patient to null - try to fetch with defaults
    $patient = [
        'pat_id' => $patient_id,
        'pat_first_name' => null,
        'pat_middle_initial' => null,
        'pat_last_name' => null,
        'pat_email' => null,
        'pat_phone' => null,
        'pat_date_of_birth' => null,
        'pat_gender' => null,
        'pat_address' => null,
        'pat_emergency_contact' => null,
        'pat_emergency_phone' => null,
        'pat_medical_history' => null,
        'pat_allergies' => null,
        'pat_insurance_provider' => null,
        'pat_insurance_number' => null,
        'created_at' => null,
        'updated_at' => null
    ];
    $profile_picture_url = null;
}

// Ensure patient is always set before view
if (!isset($patient) || !is_array($patient)) {
    error_log("Patient Edit Profile - CRITICAL: Patient not set before view!");
    $patient = [
        'pat_id' => $patient_id ?? null,
        'pat_first_name' => null,
        'pat_middle_initial' => null,
        'pat_last_name' => null,
        'pat_email' => null,
        'pat_phone' => null,
        'pat_date_of_birth' => null,
        'pat_gender' => null,
        'pat_address' => null,
        'pat_emergency_contact' => null,
        'pat_emergency_phone' => null,
        'pat_medical_history' => null,
        'pat_allergies' => null,
        'pat_insurance_provider' => null,
        'pat_insurance_number' => null,
        'created_at' => null,
        'updated_at' => null
    ];
}

require_once __DIR__ . '/../../views/patient/edit-profile.view.php';

