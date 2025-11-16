<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/CloudinaryUpload.php';

$auth = new Auth();
$auth->requireSuperAdmin();

$db = Database::getInstance();
$user_id = $auth->getUserId();
$error = '';
$success = '';

// Fetch user data
try {
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_picture_url = $user['profile_picture_url'] ?? null;
} catch (PDOException $e) {
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    $user = null;
    $profile_picture_url = null;
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
        $email = sanitize($_POST['email'] ?? '');
        
        if (empty($email)) {
            $error = 'Email is required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } else {
            try {
                $stmt = $db->prepare("UPDATE users SET user_email = :email, updated_at = NOW() WHERE user_id = :user_id");
                $stmt->execute(['email' => $email, 'user_id' => $user_id]);
                $_SESSION['user_email'] = $email;
                $success = 'Account information updated successfully';
                // Refresh user data
                $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
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
                $stmt = $db->prepare("SELECT user_password FROM users WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($userData && password_verify($current_password, $userData['user_password'])) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE users SET user_password = :password WHERE user_id = :user_id");
                    $stmt->execute(['password' => $hashed_password, 'user_id' => $user_id]);
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
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            $currentUrl = $userData['profile_picture_url'] ?? null;
            
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

require_once __DIR__ . '/../../views/superadmin/settings.view.php';

