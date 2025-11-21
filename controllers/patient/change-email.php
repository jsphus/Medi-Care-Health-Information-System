<?php
// Set headers to prevent caching - must be before any output
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Patient.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$db = Database::getInstance();
$patient_id = $auth->getPatientId();
$user_id = $auth->getUserId();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Fetch current patient data
try {
    $patientModel = new Patient();
    $patient = $patientModel->getById($patient_id);
    
    if (!$patient || empty($patient)) {
        throw new Exception('Patient data not found for patient_id: ' . $patient_id);
    }
    
    // Ensure email is set - fetch directly from database if not in patient data
    if (empty($patient['pat_email'])) {
        $email_data = $db->fetchOne("SELECT pat_email FROM patients WHERE pat_id = :patient_id", ['patient_id' => $patient_id]);
        if ($email_data && !empty($email_data['pat_email'])) {
            $patient['pat_email'] = $email_data['pat_email'];
        } else {
            // Fallback to users table
            $user_data = $db->fetchOne("SELECT user_email FROM users WHERE pat_id = :patient_id", ['patient_id' => $patient_id]);
            if ($user_data && !empty($user_data['user_email'])) {
                $patient['pat_email'] = $user_data['user_email'];
            }
        }
    }
    
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
        'created_at' => null,
        'updated_at' => null
    ];
    $patient = array_merge($defaults, $patient);
    
    $profile_picture_url = User::getProfilePicture($user_id);
} catch (Exception $e) {
    error_log("Patient Change Email Fetch Error: " . $e->getMessage());
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    $patient = [
        'pat_id' => $patient_id,
        'pat_email' => null
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
        } elseif ($new_email === $patient['pat_email']) {
            $error = 'New email must be different from your current email';
        } else {
            // Check if email is already in use
            try {
                $existingPatient = $db->fetchOne("SELECT pat_id FROM patients WHERE pat_email = :email AND pat_id != :patient_id", [
                    'email' => $new_email,
                    'patient_id' => $patient_id
                ]);
                
                $existingUser = $db->fetchOne("SELECT user_id FROM users WHERE user_email = :email AND pat_id != :patient_id", [
                    'email' => $new_email,
                    'patient_id' => $patient_id
                ]);
                
                if ($existingPatient || $existingUser) {
                    $error = 'This email address is already in use';
                } else {
                    // Generate random 6-digit OTP
                    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    
                    // Store OTP and new email in session
                    $_SESSION['email_change_otp'] = $otp;
                    $_SESSION['email_change_new_email'] = $new_email;
                    $_SESSION['email_change_timestamp'] = time();
                    
                    // Redirect to OTP confirmation page
                    header('Location: /patient/change-email?step=confirm');
                    exit;
                }
            } catch (Exception $e) {
                $error = 'Failed to verify email: ' . $e->getMessage();
                error_log("Patient Change Email Verification Error: " . $e->getMessage());
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
                
                // Update patient table
                $patientModel = new Patient();
                $updateData = [
                    'pat_email' => $new_email
                ];
                $result = $patientModel->update($patient_id, $updateData);
                
                if (!$result['success']) {
                    throw new Exception($result['message'] ?? 'Failed to update email');
                }
                
                // Update users table
                $userModel = new User();
                $userModel->update(['user_email' => $new_email, 'pat_id' => $patient_id]);
                
                // Clear session and set completion flag
                unset($_SESSION['email_change_otp']);
                unset($_SESSION['email_change_new_email']);
                unset($_SESSION['email_change_timestamp']);
                $_SESSION['email_change_completed'] = true;
                
                // Redirect to success page
                header('Location: /patient/change-email-success');
                exit;
            } catch (Exception $e) {
                $error = 'Failed to update email: ' . $e->getMessage();
                error_log("Patient Change Email Update Error: " . $e->getMessage());
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
    header('Location: /patient/change-email');
    exit;
}

require_once __DIR__ . '/../../views/patient/change-email.view.php';

