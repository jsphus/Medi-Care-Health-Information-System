<?php
require_once __DIR__ . '/../../classes/Auth.php';
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

// Fetch patient data
$patient = $patientModel->getById($patient_id);
$profile_picture_url = $userModel->getProfilePictureByUserId($user_id);
if (!$patient) {
    $error = 'Failed to fetch account information.';
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
        $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $gender = sanitize($_POST['gender'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error = 'First name, last name, and email are required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } else {
            $updateResult = $patientModel->update($patient_id, [
                'pat_first_name' => $first_name,
                'pat_middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                'pat_last_name' => $last_name,
                'pat_email' => $email,
                'pat_phone' => $phone,
                'pat_date_of_birth' => $date_of_birth,
                'pat_gender' => $gender ?: null,
                'pat_address' => $address ?: null
            ]);

            if ($updateResult['success']) {
                $success = 'Account information updated successfully';
                $patient = $patientModel->getById($patient_id);
            } else {
                $error = implode(', ', $updateResult['errors'] ?? ['Failed to update account.']);
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

require_once __DIR__ . '/../../views/patient/settings.view.php';

