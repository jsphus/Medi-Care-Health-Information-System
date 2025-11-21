<?php
// Set headers to prevent caching - must be before any output
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Doctor.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Specialization.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();
$user_id = $auth->getUserId();
$error = '';
$success = '';

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
        $specialization_id = isset($_POST['specialization_id']) ? (int)$_POST['specialization_id'] : null;
        $license_number = sanitize($_POST['license_number'] ?? '');
        $experience_years = !empty($_POST['experience_years']) ? (int)$_POST['experience_years'] : null;
        $consultation_fee = !empty($_POST['consultation_fee']) ? floatval($_POST['consultation_fee']) : null;
        $qualification = sanitize($_POST['qualification'] ?? '');
        $bio = sanitize($_POST['bio'] ?? '');
        
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error = 'First name, last name, and email are required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } else {
            try {
                // Use Doctor class update method
                $doctorModel = new Doctor();
                $updateData = [
                    'doc_first_name' => $first_name,
                    'doc_middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                    'doc_last_name' => $last_name,
                    'doc_email' => $email,
                    'doc_phone' => $phone,
                    'doc_specialization_id' => $specialization_id,
                    'doc_license_number' => $license_number,
                    'doc_experience_years' => $experience_years,
                    'doc_consultation_fee' => $consultation_fee,
                    'doc_qualification' => $qualification,
                    'doc_bio' => $bio
                ];
                
                $result = $doctorModel->update($doctor_id, $updateData);
                
                if (!$result['success']) {
                    throw new Exception($result['message'] ?? 'Failed to update doctor');
                }
                
                // Redirect to account page with success message
                header('Location: /doctor/account?success=updated');
                exit;
            } catch (Exception $e) {
                $error = 'Failed to update account: ' . $e->getMessage();
                error_log("Doctor Edit Profile Update Error: " . $e->getMessage());
            }
        }
    }
}

// Check for success message from redirect
if (isset($_GET['success']) && $_GET['success'] === 'updated') {
    $success = 'Account information updated successfully';
}

// Fetch doctor data using Doctor class
try {
    if ($doctor_id === null) {
        throw new Exception('Doctor ID is null. Cannot fetch doctor data.');
    }
    
    $doctorModel = new Doctor();
    $doctor = $doctorModel->getById($doctor_id);
    
    if (!$doctor || empty($doctor)) {
        throw new Exception('Doctor data not found for doctor_id: ' . $doctor_id);
    }
    
    // Ensure all required keys exist with defaults
    $defaults = [
        'doc_id' => $doctor_id,
        'doc_first_name' => null,
        'doc_middle_initial' => null,
        'doc_last_name' => null,
        'doc_email' => null,
        'doc_phone' => null,
        'doc_specialization_id' => null,
        'doc_license_number' => null,
        'doc_experience_years' => null,
        'doc_consultation_fee' => null,
        'doc_qualification' => null,
        'doc_bio' => null,
        'doc_status' => null,
        'created_at' => null,
        'updated_at' => null
    ];
    $doctor = array_merge($defaults, $doctor);
    
    // Get specialization name if specialization_id exists using Specialization class
    if (!empty($doctor['doc_specialization_id'])) {
        $specModel = new Specialization();
        $spec = $specModel->getById($doctor['doc_specialization_id']);
        $doctor['spec_name'] = $spec['spec_name'] ?? null;
    }
    
    // Get profile picture URL using User class
    $profile_picture_url = User::getProfilePicture($user_id);
    
    // Fetch specializations for dropdown using Specialization class
    $specModel = new Specialization();
    $specializations = $specModel->getAllSpecializations();
    
    // Debug: Log what we're passing to the view
    error_log("Doctor Edit Profile - Data fetched successfully");
    error_log("Doctor Edit Profile - Doctor array keys: " . implode(', ', array_keys($doctor)));
    error_log("Doctor Edit Profile - Email: " . ($doctor['doc_email'] ?? 'NULL'));
} catch (Exception $e) {
    error_log("Doctor Edit Profile Fetch Error: " . $e->getMessage());
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    // Don't set doctor to null - try to fetch with defaults
    $doctor = [
        'doc_id' => $doctor_id,
        'doc_first_name' => null,
        'doc_middle_initial' => null,
        'doc_last_name' => null,
        'doc_email' => null,
        'doc_phone' => null,
        'doc_specialization_id' => null,
        'doc_license_number' => null,
        'doc_experience_years' => null,
        'doc_consultation_fee' => null,
        'doc_qualification' => null,
        'doc_bio' => null,
        'doc_status' => null,
        'created_at' => null,
        'updated_at' => null
    ];
    $profile_picture_url = null;
    $specializations = [];
}

// Ensure doctor is always set before view
if (!isset($doctor) || !is_array($doctor)) {
    error_log("Doctor Edit Profile - CRITICAL: Doctor not set before view!");
    $doctor = [
        'doc_id' => $doctor_id ?? null,
        'doc_first_name' => null,
        'doc_middle_initial' => null,
        'doc_last_name' => null,
        'doc_email' => null,
        'doc_phone' => null,
        'doc_specialization_id' => null,
        'doc_license_number' => null,
        'doc_experience_years' => null,
        'doc_consultation_fee' => null,
        'doc_qualification' => null,
        'doc_bio' => null,
        'doc_status' => null,
        'created_at' => null,
        'updated_at' => null
    ];
}

require_once __DIR__ . '/../../views/doctor/edit-profile.view.php';

