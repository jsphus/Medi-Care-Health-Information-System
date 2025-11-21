<?php
// Set headers to prevent caching - must be before any output
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/CloudinaryUpload.php';
require_once __DIR__ . '/../../classes/Staff.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireStaff();

$db = Database::getInstance();
$user_id = $auth->getUserId();
$staff_id = $auth->getStaffId();

// Always verify and get staff_id from the users table to ensure we have the correct one
if ($user_id !== null) {
    try {
        $user_data = $db->fetchOne("SELECT staff_id FROM users WHERE user_id = :user_id", ['user_id' => $user_id]);
        if ($user_data && !empty($user_data['staff_id'])) {
            $db_staff_id = $user_data['staff_id'];
            if ($staff_id !== $db_staff_id) {
                $staff_id = $db_staff_id;
                // Update session for future requests
                $_SESSION['staff_id'] = $staff_id;
            }
        }
    } catch (Exception $e) {
        error_log("Staff Account - Failed to fetch staff_id from users table: " . $e->getMessage());
    }
}

$error = '';
$success = '';

// If we still don't have a staff_id, show an error
if ($staff_id === null) {
    $error = 'Staff ID not found. Please contact an administrator.';
    $staff = null;
    $profile_picture_url = null;
    require_once __DIR__ . '/../../views/staff/account.view.php';
    exit;
}

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Handle get OTP request (for AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_otp']) && $_GET['get_otp'] == '1') {
    header('Content-Type: application/json');
    $otp = $_SESSION['email_change_otp'] ?? null;
    $new_email = $_SESSION['email_change_new_email'] ?? null;
    echo json_encode([
        'otp' => $otp,
        'new_email' => $new_email
    ]);
    exit;
}

// Fetch staff data first (needed for email change)
try {
    if ($staff_id === null) {
        $user_data = $db->fetchOne("SELECT user_id, staff_id FROM users WHERE user_id = :user_id", ['user_id' => $user_id]);
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
    
    // Ensure email is set
    if (empty($staff['staff_email'])) {
        $email_data = $db->fetchOne("SELECT staff_email FROM staff WHERE staff_id = :staff_id", ['staff_id' => $staff_id]);
        if ($email_data && !empty($email_data['staff_email'])) {
            $staff['staff_email'] = $email_data['staff_email'];
        } else {
            $user_data = $db->fetchOne("SELECT user_email FROM users WHERE staff_id = :staff_id", ['staff_id' => $staff_id]);
            if ($user_data && !empty($user_data['user_email'])) {
                $staff['staff_email'] = $user_data['user_email'];
            }
        }
    }
} catch (Exception $e) {
    error_log("Staff Account Fetch Error: " . $e->getMessage());
    $staff = [
        'staff_id' => $staff_id,
        'staff_email' => null
    ];
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
        } else {
            // Get current email
            $current_email = $staff['staff_email'] ?? '';
            if (empty($current_email)) {
                $email_data = $db->fetchOne("SELECT staff_email FROM staff WHERE staff_id = :staff_id", ['staff_id' => $staff_id]);
                $current_email = $email_data['staff_email'] ?? '';
            }
            
            if ($new_email === $current_email) {
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
                        
                        // Return JSON for AJAX
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'otp' => $otp,
                            'new_email' => $new_email
                        ]);
                        exit;
                    }
                } catch (Exception $e) {
                    $error = 'Failed to verify email: ' . $e->getMessage();
                    error_log("Staff Change Email Verification Error: " . $e->getMessage());
                }
            }
        }
        
        // If there's an error, return JSON
        if (!empty($error)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
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
                
                // Update staff table - use direct SQL to avoid validation issues
                $db->execute(
                    "UPDATE staff SET staff_email = :email, updated_at = NOW() WHERE staff_id = :staff_id",
                    ['email' => $new_email, 'staff_id' => $staff_id]
                );
                
                // Update users table - fetch existing data first to preserve all fields
                $user_data = $db->fetchOne("SELECT user_id, user_email, user_password, user_is_superadmin, pat_id, staff_id, doc_id FROM users WHERE staff_id = :staff_id", ['staff_id' => $staff_id]);
                if ($user_data && !empty($user_data['user_id'])) {
                    $userModel = new User();
                    // Merge existing data with new email - exclude user_is_superadmin (only editable in database)
                    $updateData = [
                        'user_id' => $user_data['user_id'],
                        'user_email' => $new_email,
                        'user_password' => $user_data['user_password'],
                        // user_is_superadmin excluded - only editable in database
                        'pat_id' => $user_data['pat_id'] ?? null,
                        'staff_id' => $user_data['staff_id'] ?? null,
                        'doc_id' => $user_data['doc_id'] ?? null
                    ];
                    $updateResult = $userModel->update($updateData);
                    if (!$updateResult['success']) {
                        $errorMessage = 'Failed to update user email';
                        if (isset($updateResult['errors']) && !empty($updateResult['errors'])) {
                            $errorMessage = implode(', ', $updateResult['errors']);
                        } elseif (isset($updateResult['message'])) {
                            $errorMessage = $updateResult['message'];
                        }
                        throw new Exception($errorMessage);
                    }
                } else {
                    throw new Exception('User account not found for this staff member');
                }
                
                // Clear session
                unset($_SESSION['email_change_otp']);
                unset($_SESSION['email_change_new_email']);
                unset($_SESSION['email_change_timestamp']);
                
                // Return JSON for AJAX
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Email updated successfully']);
                exit;
            } catch (Exception $e) {
                $error = 'Failed to update email: ' . $e->getMessage();
                error_log("Staff Change Email Update Error: " . $e->getMessage());
                error_log("Staff Change Email Update Error - Staff ID: " . $staff_id . ", New Email: " . $new_email);
                error_log("Staff Change Email Update Error - Stack Trace: " . $e->getTraceAsString());
            }
        }
        
        // If there's an error, return JSON
        if (!empty($error)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        }
    } elseif ($action === 'update_profile') {
        $first_name = sanitize($_POST['first_name'] ?? '');
        $middle_initial = sanitize($_POST['middle_initial'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        if (!empty($phone)) {
            $phone = formatPhoneNumber($phone);
        }
        $position = sanitize($_POST['position'] ?? '');
        
        if (empty($first_name) || empty($last_name)) {
            $error = 'First name and last name are required';
        } else {
            try {
                require_once __DIR__ . '/../../classes/Staff.php';
                
                // Prepare update data using Staff class
                $middle_initial_processed = !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null;
                
                $updateData = [
                    'staff_first_name' => $first_name,
                    'staff_middle_initial' => $middle_initial_processed,
                    'staff_last_name' => $last_name,
                    'staff_phone' => !empty($phone) ? $phone : null,
                    'staff_position' => !empty($position) ? $position : null
                ];
                
                // Use Staff class update method
                $staffModel = new Staff();
                $result = $staffModel->update($staff_id, $updateData);
                
                if (!$result['success']) {
                    throw new Exception($result['message'] ?? 'Failed to update staff');
                }
                
                // Redirect to account page with success message
                header('Location: /staff/account?success=updated');
                exit;
            } catch (Exception $e) {
                $error = 'Failed to update account: ' . $e->getMessage();
                error_log("Staff Account Update Error: " . $e->getMessage());
            }
        }
    } elseif ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if ($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } else {
            try {
                $user = $db->fetchOne("SELECT u.user_password FROM users u JOIN staff s ON u.staff_id = s.staff_id WHERE s.staff_id = :staff_id", ['staff_id' => $staff_id]);
                
                if ($user && password_verify($current_password, $user['user_password'])) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $db->execute("UPDATE users SET user_password = :password WHERE staff_id = :staff_id", ['password' => $hashed_password, 'staff_id' => $staff_id]);
                    $success = 'Password changed successfully';
                } else {
                    $error = 'Current password is incorrect';
                }
            } catch (PDOException $e) {
                $error = 'Failed to change password: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'update_profile_picture') {
        if (isset($_FILES['profile_picture'])) {
            $uploadError = $_FILES['profile_picture']['error'] ?? UPLOAD_ERR_NO_FILE;
            
            if ($uploadError === UPLOAD_ERR_OK) {
                try {
                    $cloudinary = new CloudinaryUpload();
                    $result = $cloudinary->uploadImage($_FILES['profile_picture'], 'profile_pictures', $user_id);
                    
                    // Check if result is an array (success) or string (error message)
                    if (is_array($result) && isset($result['url'])) {
                        // Get old profile picture URL before updating
                        $oldUser = $db->fetchOne("SELECT profile_picture_url FROM users WHERE user_id = :user_id", ['user_id' => $user_id]);
                        $oldUrl = $oldUser['profile_picture_url'] ?? null;
                        
                        // Ensure profile_picture_url column exists
                        try {
                            $db->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_picture_url TEXT");
                        } catch (Exception $e) {
                            // Column might already exist, ignore error
                        }
                        
                        // Update user profile picture URL
                        $stmt = $db->prepare("UPDATE users SET profile_picture_url = :url WHERE user_id = :user_id");
                        $stmt->execute(['url' => $result['url'], 'user_id' => $user_id]);
                        
                        // Delete old image from Cloudinary if exists
                        if ($oldUrl) {
                            $oldPublicId = $cloudinary->extractPublicId($oldUrl);
                            if ($oldPublicId) {
                                $cloudinary->deleteImage($oldPublicId);
                            }
                        }
                        
                        $success = 'Profile picture updated successfully';
                        $profile_picture_url = $result['url'];
                    } else {
                        // Result is an error message string
                        $error = is_string($result) ? $result : 'Failed to upload image. Please ensure the file is a valid image (jpg, png, gif, webp) and under 5MB.';
                    }
                } catch (Exception $e) {
                    $error = 'Failed to upload profile picture: ' . $e->getMessage();
                }
            } else {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
                    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
                ];
                $error = $errorMessages[$uploadError] ?? 'File upload error occurred (Error code: ' . $uploadError . ')';
            }
        } else {
            $error = 'No file was selected';
        }
    }
    
    if ($action === 'delete_profile_picture') {
        try {
            // Get current profile picture URL
            $user = $db->fetchOne("SELECT profile_picture_url FROM users WHERE user_id = :user_id", ['user_id' => $user_id]);
            $currentUrl = $user['profile_picture_url'] ?? null;
            
            if ($currentUrl) {
                $cloudinary = new CloudinaryUpload();
                $publicId = $cloudinary->extractPublicId($currentUrl);
                
                // Delete from Cloudinary
                if ($publicId) {
                    $cloudinary->deleteImage($publicId);
                }
                
                // Remove from database
                $stmt = $db->prepare("UPDATE users SET profile_picture_url = NULL WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                
                $success = 'Profile picture removed successfully';
                $profile_picture_url = null;
            }
        } catch (Exception $e) {
            $error = 'Failed to remove profile picture: ' . $e->getMessage();
        }
    }
}

// Check for success message from redirect
if (isset($_GET['success']) && $_GET['success'] === 'updated') {
    $success = 'Account information updated successfully';
}

// Check for email changed success
if (isset($_GET['email_changed']) && $_GET['email_changed'] == '1') {
    $success = 'Email address changed successfully';
}

// Staff data should already be fetched above, but ensure profile picture is set
if (!isset($profile_picture_url)) {
    $profile_picture_url = User::getProfilePicture($user_id);
}

// Create a protected copy of $staff for the view
$display_staff = $staff;

require_once __DIR__ . '/../../views/staff/account.view.php';

