<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/CloudinaryUpload.php';
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

// Fetch staff data
try {
    $staff = (new Staff())->getById($staff_id);
} catch (PDOException $e) {
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    $staff = null;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_settings') {
        $notifications = isset($_POST['notifications']) ? 1 : 0;
        $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
        $language = sanitize($_POST['language'] ?? 'en');
        
        $success = 'Settings saved successfully';
    }
    
    if ($action === 'update_profile') {
        $first_name = sanitize($_POST['first_name'] ?? '');
        $middle_initial = sanitize($_POST['middle_initial'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        if (!empty($phone)) {
            $phone = formatPhoneNumber($phone);
        }
        $position = sanitize($_POST['position'] ?? '');
        
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error = 'First name, last name, and email are required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } else {
            try {
                $staffObj = new Staff();
                $updateData = [
                    'staff_id' => $staff_id,
                    'staff_first_name' => $first_name,
                    'staff_middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                    'staff_last_name' => $last_name,
                    'staff_email' => $email,
                    'staff_phone' => $phone,
                    'staff_position' => $position
                ];
                $result = $staffObj->update($updateData);
                if ($result['success']) {
                    $success = 'Account information updated successfully';
                    // Refresh staff data
                    $staff = $staffObj->getById($staff_id);
                } else {
                    $error = $result['message'] ?? 'Failed to update account';
                }
            } catch (PDOException $e) {
                $error = 'Failed to update account: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'change_password') {
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
                        
                        // Update user profile picture URL
                        $db->execute("UPDATE users SET profile_picture_url = :url WHERE user_id = :user_id", ['url' => $result['url'], 'user_id' => $user_id]);
                        
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
                $db->execute("UPDATE users SET profile_picture_url = NULL WHERE user_id = :user_id", ['user_id' => $user_id]);
                
                $success = 'Profile picture removed successfully';
                $profile_picture_url = null;
            }
        } catch (Exception $e) {
            $error = 'Failed to remove profile picture: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../../views/staff/settings.view.php';

