<?php
// Set headers to prevent caching - must be before any output
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/CloudinaryUpload.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();
$user_id = $auth->getUserId();
$error = '';
$success = '';

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

// Fetch doctor data first (needed for email change)
try {
    $stmt = $db->prepare("
        SELECT d.*, s.spec_name 
        FROM doctors d 
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id 
        WHERE d.doc_id = :doctor_id
    ");
    $stmt->execute(['doctor_id' => $doctor_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$doctor || empty($doctor)) {
        throw new Exception('Doctor data not found');
    }
    
    // Ensure email is set
    if (empty($doctor['doc_email'])) {
        $email_data = $db->fetchOne("SELECT doc_email FROM doctors WHERE doc_id = :doctor_id", ['doctor_id' => $doctor_id]);
        if ($email_data && !empty($email_data['doc_email'])) {
            $doctor['doc_email'] = $email_data['doc_email'];
        } else {
            $user_data = $db->fetchOne("SELECT user_email FROM users WHERE doc_id = :doctor_id", ['doctor_id' => $doctor_id]);
            if ($user_data && !empty($user_data['user_email'])) {
                $doctor['doc_email'] = $user_data['user_email'];
            }
        }
    }
    
    // Get profile picture URL
    $user_id = $auth->getUserId();
    $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_picture_url = $user['profile_picture_url'] ?? null;
    
    // Fetch specializations for dropdown
    require_once __DIR__ . '/../../classes/Specialization.php';
    $specModel = new Specialization();
    $specializations = $specModel->getAllSpecializations();
} catch (PDOException $e) {
    error_log("Doctor Account Fetch Error: " . $e->getMessage());
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    $specializations = [];
    $doctor = null;
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
        } else {
            // Get current email
            $current_email = $doctor['doc_email'] ?? '';
            if (empty($current_email)) {
                $email_data = $db->fetchOne("SELECT doc_email FROM doctors WHERE doc_id = :doctor_id", ['doctor_id' => $doctor_id]);
                $current_email = $email_data['doc_email'] ?? '';
            }
            
            if ($new_email === $current_email) {
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
                    error_log("Doctor Change Email Verification Error: " . $e->getMessage());
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
                
                // Update doctor table - use direct SQL to avoid validation issues
                $db->execute(
                    "UPDATE doctors SET doc_email = :email, updated_at = NOW() WHERE doc_id = :doc_id",
                    ['email' => $new_email, 'doc_id' => $doctor_id]
                );
                
                // Update users table - fetch existing data first to preserve all fields
                $user_data = $db->fetchOne("SELECT user_id, user_email, user_password, user_is_superadmin, pat_id, staff_id, doc_id FROM users WHERE doc_id = :doc_id", ['doc_id' => $doctor_id]);
                if ($user_data && !empty($user_data['user_id'])) {
                    require_once __DIR__ . '/../../classes/User.php';
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
                    throw new Exception('User account not found for this doctor');
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
                error_log("Doctor Change Email Update Error: " . $e->getMessage());
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
        $specialization_id = isset($_POST['specialization_id']) ? (int)$_POST['specialization_id'] : null;
        $license_number = sanitize($_POST['license_number'] ?? '');
        $experience_years = !empty($_POST['experience_years']) ? (int)$_POST['experience_years'] : null;
        $consultation_fee = !empty($_POST['consultation_fee']) ? floatval($_POST['consultation_fee']) : null;
        $qualification = sanitize($_POST['qualification'] ?? '');
        $bio = sanitize($_POST['bio'] ?? '');
        
        if (empty($first_name) || empty($last_name)) {
            $error = 'First name and last name are required';
        } else {
            try {
                require_once __DIR__ . '/../../classes/Doctor.php';
                require_once __DIR__ . '/../../classes/User.php';
                
                // Use Doctor class update method
                $doctorModel = new Doctor();
                $updateData = [
                    'doc_first_name' => $first_name,
                    'doc_middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                    'doc_last_name' => $last_name,
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
                error_log("Doctor Account Update Error: " . $e->getMessage());
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
                $stmt = $db->prepare("SELECT u.user_password FROM users u JOIN doctors d ON u.doc_id = d.doc_id WHERE d.doc_id = :doctor_id");
                $stmt->execute(['doctor_id' => $doctor_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($current_password, $user['user_password'])) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE users SET user_password = :password WHERE doc_id = :doctor_id");
                    $stmt->execute(['password' => $hashed_password, 'doctor_id' => $doctor_id]);
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
                        // Ensure profile_picture_url column exists
                        try {
                            $db->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_picture_url TEXT");
                        } catch (Exception $e) {
                            // Column might already exist, ignore error
                        }
                        
                        // Get old profile picture URL before updating
                        $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
                        $stmt->execute(['user_id' => $user_id]);
                        $oldUser = $stmt->fetch(PDO::FETCH_ASSOC);
                        $oldUrl = $oldUser['profile_picture_url'] ?? null;
                        
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
            $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
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

// Check for success message from redirect (for profile picture/password updates)
if (isset($_GET['success']) && $_GET['success'] === 'updated') {
    $success = 'Account information updated successfully';
}

// Check for email changed success
if (isset($_GET['email_changed']) && $_GET['email_changed'] == '1') {
    $success = 'Email address changed successfully';
    // Refresh doctor data to show new email
    try {
        $stmt = $db->prepare("
            SELECT d.*, s.spec_name 
            FROM doctors d 
            LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id 
            WHERE d.doc_id = :doctor_id
        ");
        $stmt->execute(['doctor_id' => $doctor_id]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Ignore refresh error
    }
}

// Doctor data should already be fetched above, but ensure specializations are set
if (!isset($specializations)) {
    try {
        require_once __DIR__ . '/../../classes/Specialization.php';
        $specModel = new Specialization();
        $specializations = $specModel->getAllSpecializations();
    } catch (Exception $e) {
        $specializations = [];
    }
}

require_once __DIR__ . '/../../views/doctor/account.view.php';

