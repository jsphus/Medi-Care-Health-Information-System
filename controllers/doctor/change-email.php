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

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();
$user_id = $auth->getUserId();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Fetch current doctor data
try {
    $doctorModel = new Doctor();
    $doctor = $doctorModel->getById($doctor_id);
    
    if (!$doctor || empty($doctor)) {
        throw new Exception('Doctor data not found for doctor_id: ' . $doctor_id);
    }
    
    // Ensure email is set - fetch directly from database if not in doctor data
    if (empty($doctor['doc_email'])) {
        $email_data = $db->fetchOne("SELECT doc_email FROM doctors WHERE doc_id = :doctor_id", ['doctor_id' => $doctor_id]);
        if ($email_data && !empty($email_data['doc_email'])) {
            $doctor['doc_email'] = $email_data['doc_email'];
        } else {
            // Fallback to users table
            $user_data = $db->fetchOne("SELECT user_email FROM users WHERE doc_id = :doctor_id", ['doctor_id' => $doctor_id]);
            if ($user_data && !empty($user_data['user_email'])) {
                $doctor['doc_email'] = $user_data['user_email'];
            }
        }
    }
    
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
        'created_at' => null,
        'updated_at' => null
    ];
    $doctor = array_merge($defaults, $doctor);
    
    $profile_picture_url = User::getProfilePicture($user_id);
} catch (Exception $e) {
    error_log("Doctor Change Email Fetch Error: " . $e->getMessage());
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    $doctor = [
        'doc_id' => $doctor_id,
        'doc_email' => null
    ];
    $profile_picture_url = null;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'request_email_change') {
        // Step 1: User requests email change - generate OTP
        $new_email = sanitize($_POST['new_email'] ?? '');
        
        if (empty($new_email)) {
            $error = 'Email address is required';
        } elseif (!isValidEmail($new_email)) {
            $error = 'Invalid email format';
        } elseif ($new_email === $doctor['doc_email']) {
            $error = 'New email must be different from your current email';
        } else {
            // Check if email is already in use
            try {
                $existingDoctor = $db->fetchOne("SELECT doc_id FROM doctors WHERE doc_email = :email AND doc_id != :doctor_id", [
                    'email' => $new_email,
                    'doctor_id' => $doctor_id
                ]);
                
                $existingUser = $db->fetchOne("SELECT user_id FROM users WHERE user_email = :email AND doc_id != :doctor_id", [
                    'email' => $new_email,
                    'doctor_id' => $doctor_id
                ]);
                
                if ($existingDoctor || $existingUser) {
                    $error = 'This email address is already in use';
                } else {
                    // Generate random 6-digit OTP
                    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    
                    // Store OTP and new email in session
                    $_SESSION['email_change_otp'] = $otp;
                    $_SESSION['email_change_new_email'] = $new_email;
                    $_SESSION['email_change_timestamp'] = time();
                    
                    // Redirect to OTP confirmation page
                    header('Location: /doctor/change-email?step=confirm');
                    exit;
                }
            } catch (Exception $e) {
                $error = 'Failed to verify email: ' . $e->getMessage();
                error_log("Doctor Change Email Verification Error: " . $e->getMessage());
            }
        }
    } elseif ($action === 'verify_otp') {
        // Step 2: Verify OTP and update email
        $entered_otp = sanitize($_POST['otp'] ?? '');
        
        if (empty($entered_otp)) {
            $error = 'OTP is required';
        } elseif (!isset($_SESSION['email_change_otp']) || !isset($_SESSION['email_change_new_email'])) {
            $error = 'OTP session expired. Please start over.';
            unset($_SESSION['email_change_otp']);
            unset($_SESSION['email_change_new_email']);
            unset($_SESSION['email_change_timestamp']);
        } elseif (time() - $_SESSION['email_change_timestamp'] > 600) { // 10 minutes expiry
            $error = 'OTP has expired. Please request a new one.';
            unset($_SESSION['email_change_otp']);
            unset($_SESSION['email_change_new_email']);
            unset($_SESSION['email_change_timestamp']);
        } elseif ($entered_otp !== $_SESSION['email_change_otp']) {
            $error = 'Invalid OTP. Please try again.';
        } else {
            // OTP is valid - update email
            try {
                $new_email = $_SESSION['email_change_new_email'];
                
                // Update doctor table
                $doctorModel = new Doctor();
                $updateData = [
                    'doc_email' => $new_email
                ];
                $result = $doctorModel->update($doctor_id, $updateData);
                
                if (!$result['success']) {
                    throw new Exception($result['message'] ?? 'Failed to update email');
                }
                
                // Update users table
                $userModel = new User();
                $userModel->update(['user_email' => $new_email, 'doc_id' => $doctor_id]);
                
                // Clear session and set completion flag
                unset($_SESSION['email_change_otp']);
                unset($_SESSION['email_change_new_email']);
                unset($_SESSION['email_change_timestamp']);
                $_SESSION['email_change_completed'] = true;
                
                // Redirect to success page
                header('Location: /doctor/change-email-success');
                exit;
            } catch (Exception $e) {
                $error = 'Failed to update email: ' . $e->getMessage();
                error_log("Doctor Change Email Update Error: " . $e->getMessage());
            }
        }
    }
}

// Determine which step to show
$step = $_GET['step'] ?? 'request';
$otp = $_SESSION['email_change_otp'] ?? null;
$new_email = $_SESSION['email_change_new_email'] ?? null;

// If on confirm step but no OTP in session, redirect back to request
if ($step === 'confirm' && (!$otp || !$new_email)) {
    header('Location: /doctor/change-email');
    exit;
}

require_once __DIR__ . '/../../views/doctor/change-email.view.php';

