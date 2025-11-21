<?php
// Set headers to prevent caching - must be before any output
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/CloudinaryUpload.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireSuperAdmin();

$db = Database::getInstance();
$user_id = $auth->getUserId();
$error = '';
$success = '';
$user = null; // Initialize user variable

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $email = sanitize($_POST['email'] ?? '');
        
        if (empty($email)) {
            $error = 'Email is required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } else {
            try {
                require_once __DIR__ . '/../../classes/User.php';
                
                // Use User class update method
                $userModel = new User();
                $result = $userModel->update(['user_id' => $user_id, 'user_email' => $email]);
                
                if (!$result['success']) {
                    throw new Exception($result['message'] ?? 'Failed to update user');
                }
                
                $_SESSION['user_email'] = $email;
                
                // Redirect to account page with success message
                header('Location: /superadmin/account?success=updated');
                exit;
            } catch (Exception $e) {
                $error = 'Failed to update account: ' . $e->getMessage();
                error_log("Superadmin Account Update Error: " . $e->getMessage());
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
                $user = $db->fetchOne("SELECT user_password FROM users WHERE user_id = :user_id", ['user_id' => $user_id]);
                
                if ($user && password_verify($current_password, $user['user_password'])) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $db->execute("UPDATE users SET user_password = :password WHERE user_id = :user_id", ['password' => $hashed_password, 'user_id' => $user_id]);
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
                        $oldUser = $db->fetchOne("SELECT profile_picture_url FROM users WHERE user_id = :user_id", ['user_id' => $user_id]);
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
            $userData = $db->fetchOne("SELECT profile_picture_url FROM users WHERE user_id = :user_id", ['user_id' => $user_id]);
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

// Check for success message from redirect (for profile picture/password updates)
if (isset($_GET['success']) && $_GET['success'] === 'updated') {
    $success = 'Account information updated successfully';
}

// Fetch user data - use direct query to ensure fresh data
try {
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT user_id, user_email, profile_picture_url FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || empty($user)) {
        throw new Exception('User data not found');
    }
    
    // Ensure email is always available (fallback to session if not in user data)
    if (empty($user['user_email']) && isset($_SESSION['user_email'])) {
        $user['user_email'] = $_SESSION['user_email'];
    }
    
    $profile_picture_url = $user['profile_picture_url'] ?? User::getProfilePicture($user_id);
    
    // CRITICAL: Make a protected copy to prevent modification
    $user_data = $user;
    $required_keys = ['user_id', 'user_email', 'profile_picture_url'];
    foreach ($required_keys as $key) {
        if (!isset($user_data[$key])) {
            $user_data[$key] = null;
        }
    }
    $user = $user_data;
} catch (Exception $e) {
    error_log("Superadmin Account Fetch Error: " . $e->getMessage());
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    // Fallback: create user array with email from session
    $user = [
        'user_id' => $user_id,
        'user_email' => $_SESSION['user_email'] ?? '',
        'profile_picture_url' => null
    ];
    $profile_picture_url = null;
}

require_once __DIR__ . '/../../views/superadmin/account.view.php';

