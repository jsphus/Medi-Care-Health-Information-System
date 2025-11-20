<?php
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

// Fetch doctor data
try {
    $stmt = $db->prepare("
        SELECT d.*, s.spec_name 
        FROM doctors d 
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id 
        WHERE d.doc_id = :doctor_id
    ");
    $stmt->execute(['doctor_id' => $doctor_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fetch specializations for dropdown
    $stmt = $db->query("SELECT * FROM specializations ORDER BY spec_name");
    $specializations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch user data for profile picture
    $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_picture_url = $user['profile_picture_url'] ?? null;
} catch (PDOException $e) {
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    $doctor = null;
    $specializations = [];
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
        $first_name = sanitize($_POST['first_name'] ?? '');
        $middle_initial = sanitize($_POST['middle_initial'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        if (!empty($phone)) {
            $phone = formatPhoneNumber($phone);
        }
        $specialization_id = isset($_POST['specialization_id']) ? (int)$_POST['specialization_id'] : null;
        
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error = 'First name, last name, and email are required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } else {
            try {
            $stmt = $db->prepare("
                UPDATE doctors 
                SET doc_first_name = :first_name, 
                    doc_middle_initial = :middle_initial,
                    doc_last_name = :last_name,
                    doc_email = :email,
                    doc_phone = :phone,
                    doc_specialization_id = :specialization_id,
                    updated_at = NOW()
                WHERE doc_id = :doctor_id
            ");
            $stmt->execute([
                'first_name' => $first_name,
                'middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'specialization_id' => $specialization_id,
                'doctor_id' => $doctor_id
            ]);
                $success = 'Account information updated successfully';
                // Refresh doctor data
                $stmt = $db->prepare("
                    SELECT d.*, s.spec_name 
                    FROM doctors d 
                    LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id 
                    WHERE d.doc_id = :doctor_id
                ");
                $stmt->execute(['doctor_id' => $doctor_id]);
                $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Refresh specializations
                $stmt = $db->query("SELECT * FROM specializations ORDER BY spec_name");
                $specializations = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

require_once __DIR__ . '/../../views/doctor/settings.view.php';

