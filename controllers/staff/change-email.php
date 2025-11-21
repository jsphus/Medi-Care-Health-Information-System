<?php
// Set headers to prevent caching - must be before any output
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Staff.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireStaff();

$db = Database::getInstance();
$staff_id = $auth->getStaffId();
$user_id = $auth->getUserId();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Fetch current staff data
try {
    if ($staff_id === null) {
        $userModel = new User();
        $user_data = $userModel->getById($user_id);
        if ($user_data && !empty($user_data['staff_id'])) {
            $staff_id = $user_data['staff_id'];
            $_SESSION['staff_id'] = $staff_id;
        } else {
            throw new Exception('Staff ID is null. Cannot fetch staff data.');
        }
    }
    
    $staffModel = new Staff();
    $staff = $staffModel->getById($staff_id);
    
    if (!$staff || empty($staff)) {
        throw new Exception('Staff data not found for staff_id: ' . $staff_id);
    }
    
    // Ensure email is set - fetch directly from database if not in staff data
    if (empty($staff['staff_email'])) {
        $email_data = $db->fetchOne("SELECT staff_email FROM staff WHERE staff_id = :staff_id", ['staff_id' => $staff_id]);
        if ($email_data && !empty($email_data['staff_email'])) {
            $staff['staff_email'] = $email_data['staff_email'];
        } else {
            // Fallback to users table
            $user_data = $db->fetchOne("SELECT user_email FROM users WHERE staff_id = :staff_id", ['staff_id' => $staff_id]);
            if ($user_data && !empty($user_data['user_email'])) {
                $staff['staff_email'] = $user_data['user_email'];
            }
        }
    }
    
    $defaults = [
        'staff_id' => $staff_id,
        'staff_first_name' => null,
        'staff_middle_initial' => null,
        'staff_last_name' => null,
        'staff_email' => null,
        'staff_phone' => null,
        'staff_position' => null,
        'staff_hire_date' => null,
        'staff_salary' => null,
        'staff_status' => null,
        'created_at' => null,
        'updated_at' => null
    ];
    $staff = array_merge($defaults, $staff);
    
    $profile_picture_url = User::getProfilePicture($user_id);
} catch (Exception $e) {
    error_log("Staff Change Email Fetch Error: " . $e->getMessage());
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    $staff = [
        'staff_id' => $staff_id,
        'staff_email' => null
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
        } elseif ($new_email === $staff['staff_email']) {
            $error = 'New email must be different from your current email';
        } else {
            // Check if email is already in use
            try {
                $existingStaff = $db->fetchOne("SELECT staff_id FROM staff WHERE staff_email = :email AND staff_id != :staff_id", [
                    'email' => $new_email,
                    'staff_id' => $staff_id
                ]);
                
                $existingUser = $db->fetchOne("SELECT user_id FROM users WHERE user_email = :email AND staff_id != :staff_id", [
                    'email' => $new_email,
                    'staff_id' => $staff_id
                ]);
                
                if ($existingStaff || $existingUser) {
                    $error = 'This email address is already in use';
                } else {
                    // Generate random 6-digit OTP
                    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    
                    // Store OTP and new email in session
                    $_SESSION['email_change_otp'] = $otp;
                    $_SESSION['email_change_new_email'] = $new_email;
                    $_SESSION['email_change_timestamp'] = time();
                    
                    // Redirect to OTP confirmation page
                    header('Location: /staff/change-email?step=confirm');
                    exit;
                }
            } catch (Exception $e) {
                $error = 'Failed to verify email: ' . $e->getMessage();
                error_log("Staff Change Email Verification Error: " . $e->getMessage());
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
                
                // Update staff table
                $staffModel = new Staff();
                $updateData = [
                    'staff_email' => $new_email
                ];
                $result = $staffModel->update($staff_id, $updateData);
                
                if (!$result['success']) {
                    throw new Exception($result['message'] ?? 'Failed to update email');
                }
                
                // Update users table
                $userModel = new User();
                $userModel->update(['user_email' => $new_email, 'staff_id' => $staff_id]);
                
                // Clear session and set completion flag
                unset($_SESSION['email_change_otp']);
                unset($_SESSION['email_change_new_email']);
                unset($_SESSION['email_change_timestamp']);
                $_SESSION['email_change_completed'] = true;
                
                // Redirect to success page
                header('Location: /staff/change-email-success');
                exit;
            } catch (Exception $e) {
                $error = 'Failed to update email: ' . $e->getMessage();
                error_log("Staff Change Email Update Error: " . $e->getMessage());
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
    header('Location: /staff/change-email');
    exit;
}

require_once __DIR__ . '/../../views/staff/change-email.view.php';

