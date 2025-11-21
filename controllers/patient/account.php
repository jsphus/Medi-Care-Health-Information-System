<?php
// Set headers to prevent caching - must be before any output
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/CloudinaryUpload.php';
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

// Fetch patient data first (needed for email change)
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT pat_id, pat_first_name, pat_middle_initial, pat_last_name, pat_email, pat_phone, pat_date_of_birth, pat_gender, pat_address, pat_emergency_contact, pat_emergency_phone, created_at, updated_at FROM patients WHERE pat_id = :patient_id");
    $stmt->execute(['patient_id' => $patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$patient || empty($patient)) {
        throw new Exception('Patient data not found');
    }
    
    // Ensure email is set
    if (empty($patient['pat_email'])) {
        $email_data = $db->fetchOne("SELECT pat_email FROM patients WHERE pat_id = :patient_id", ['patient_id' => $patient_id]);
        if ($email_data && !empty($email_data['pat_email'])) {
            $patient['pat_email'] = $email_data['pat_email'];
        } else {
            $user_data = $db->fetchOne("SELECT user_email FROM users WHERE pat_id = :patient_id", ['patient_id' => $patient_id]);
            if ($user_data && !empty($user_data['user_email'])) {
                $patient['pat_email'] = $user_data['user_email'];
            }
        }
    }
    
    $required_keys = ['pat_id', 'pat_first_name', 'pat_middle_initial', 'pat_last_name', 'pat_email', 'pat_phone', 'pat_date_of_birth', 'pat_gender', 'pat_address', 'pat_emergency_contact', 'pat_emergency_phone', 'created_at', 'updated_at'];
    foreach ($required_keys as $key) {
        if (!isset($patient[$key])) {
            $patient[$key] = null;
        }
    }
    
    $profile_picture_url = $userModel->getProfilePictureByUserId($user_id);
} catch (Exception $e) {
    error_log("Patient Account Fetch Error: " . $e->getMessage());
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
        } else {
            // Get current email
            $current_email = $patient['pat_email'] ?? '';
            if (empty($current_email)) {
                $email_data = $db->fetchOne("SELECT pat_email FROM patients WHERE pat_id = :patient_id", ['patient_id' => $patient_id]);
                $current_email = $email_data['pat_email'] ?? '';
            }
            
            if ($new_email === $current_email) {
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
                    error_log("Patient Change Email Verification Error: " . $e->getMessage());
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
                
                // Update patient table - use direct SQL to avoid validation issues
                $db->execute(
                    "UPDATE patients SET pat_email = :email, updated_at = NOW() WHERE pat_id = :pat_id",
                    ['email' => $new_email, 'pat_id' => $patient_id]
                );
                
                // Update users table - fetch existing data first to preserve all fields
                $user_data = $db->fetchOne("SELECT user_id, user_email, user_password, user_is_superadmin, pat_id, staff_id, doc_id FROM users WHERE pat_id = :pat_id", ['pat_id' => $patient_id]);
                if ($user_data && !empty($user_data['user_id'])) {
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
                    throw new Exception('User account not found for this patient');
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
                error_log("Patient Change Email Update Error: " . $e->getMessage());
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
        $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $gender = sanitize($_POST['gender'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $emergency_contact = sanitize($_POST['emergency_contact'] ?? '');
        $emergency_phone = sanitize($_POST['emergency_phone'] ?? '');
        if (!empty($emergency_phone)) {
            $emergency_phone = formatPhoneNumber($emergency_phone);
        }
        
        if (empty($first_name) || empty($last_name)) {
            $error = 'First name and last name are required';
        } else {
            $updateResult = $patientModel->update($patient_id, [
                'pat_first_name' => $first_name,
                'pat_middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                'pat_last_name' => $last_name,
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
    } elseif ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if ($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } else {
            $passwordResult = $userModel->updatePasswordForPatient($patient_id, $current_password, $new_password);
            if ($passwordResult['success']) {
                $success = 'Password changed successfully';
            } else {
                $error = $passwordResult['error'] ?? 'Failed to change password';
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
                        $oldUrl = $userModel->getProfilePictureByUserId($user_id);
                        
                        // Update user profile picture URL
                        $userModel->setProfilePicture($user_id, $result['url']);
                        
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
                $currentUrl = $userModel->getProfilePictureByUserId($user_id);
            
            if ($currentUrl) {
                $cloudinary = new CloudinaryUpload();
                $publicId = $cloudinary->extractPublicId($currentUrl);
                
                // Delete from Cloudinary
                if ($publicId) {
                    $cloudinary->deleteImage($publicId);
                }
                
                // Remove from database
                $userModel->setProfilePicture($user_id, null);
                
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
    // Refresh patient data to show new email
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT pat_id, pat_first_name, pat_middle_initial, pat_last_name, pat_email, pat_phone, pat_date_of_birth, pat_gender, pat_address, pat_emergency_contact, pat_emergency_phone, created_at, updated_at FROM patients WHERE pat_id = :patient_id");
        $stmt->execute(['patient_id' => $patient_id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($patient) {
            $required_keys = ['pat_id', 'pat_first_name', 'pat_middle_initial', 'pat_last_name', 'pat_email', 'pat_phone', 'pat_date_of_birth', 'pat_gender', 'pat_address', 'pat_emergency_contact', 'pat_emergency_phone', 'created_at', 'updated_at'];
            foreach ($required_keys as $key) {
                if (!isset($patient[$key])) {
                    $patient[$key] = null;
                }
            }
        }
    } catch (Exception $e) {
        // Ignore refresh error
    }
}

// Patient data should already be fetched above, but ensure it's set
if (!isset($patient) || empty($patient)) {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT pat_id, pat_first_name, pat_middle_initial, pat_last_name, pat_email, pat_phone, pat_date_of_birth, pat_gender, pat_address, pat_emergency_contact, pat_emergency_phone, created_at, updated_at FROM patients WHERE pat_id = :patient_id");
        $stmt->execute(['patient_id' => $patient_id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$patient || empty($patient)) {
            throw new Exception('Patient data not found');
        }
        
        $required_keys = ['pat_id', 'pat_first_name', 'pat_middle_initial', 'pat_last_name', 'pat_email', 'pat_phone', 'pat_date_of_birth', 'pat_gender', 'pat_address', 'pat_emergency_contact', 'pat_emergency_phone', 'created_at', 'updated_at'];
        foreach ($required_keys as $key) {
            if (!isset($patient[$key])) {
                $patient[$key] = null;
            }
        }
        
        if (!isset($profile_picture_url)) {
            $profile_picture_url = $userModel->getProfilePictureByUserId($user_id);
        }
    } catch (Exception $e) {
        error_log("Patient Account Fetch Error: " . $e->getMessage());
        $error = 'Failed to fetch account information: ' . $e->getMessage();
        $patient = null;
        $profile_picture_url = null;
    }
}

require_once __DIR__ . '/../../views/patient/account.view.php';

