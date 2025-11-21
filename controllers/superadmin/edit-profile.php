<?php
// Set headers to prevent caching - must be before any output
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireSuperAdmin();

$db = Database::getInstance();
$user_id = $auth->getUserId();
$error = '';
$success = '';

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
                error_log("Superadmin Edit Profile Update Error: " . $e->getMessage());
            }
        }
    }
}

// Check for success message from redirect
if (isset($_GET['success']) && $_GET['success'] === 'updated') {
    $success = 'Account information updated successfully';
}

// Fetch user data using User class
try {
    if ($user_id === null) {
        throw new Exception('User ID is null. Cannot fetch user data.');
    }
    
    $userModel = new User();
    $user = $userModel->getById($user_id);
    
    if (!$user || empty($user)) {
        throw new Exception('User data not found for user_id: ' . $user_id);
    }
    
    // Ensure all required keys exist with defaults
    $defaults = [
        'user_id' => $user_id,
        'user_email' => null,
        'user_password' => null,
        'user_is_superadmin' => null,
        'pat_id' => null,
        'staff_id' => null,
        'doc_id' => null,
        'profile_picture_url' => null,
        'created_at' => null,
        'updated_at' => null
    ];
    $user = array_merge($defaults, $user);
    
    // Ensure email is always available (fallback to session if not in user data)
    if (empty($user['user_email']) && isset($_SESSION['user_email'])) {
        $user['user_email'] = $_SESSION['user_email'];
    }
    
    // Get profile picture URL using User class
    $profile_picture_url = User::getProfilePicture($user_id);
    $user['profile_picture_url'] = $profile_picture_url;
    
    // Debug: Log what we're passing to the view
    error_log("Superadmin Edit Profile - Data fetched successfully");
    error_log("Superadmin Edit Profile - User array keys: " . implode(', ', array_keys($user)));
    error_log("Superadmin Edit Profile - Email: " . ($user['user_email'] ?? 'NULL'));
} catch (Exception $e) {
    error_log("Superadmin Edit Profile Fetch Error: " . $e->getMessage());
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    // Fallback: create user array with email from session
    $user = [
        'user_id' => $user_id,
        'user_email' => $_SESSION['user_email'] ?? '',
        'user_password' => null,
        'user_is_superadmin' => null,
        'pat_id' => null,
        'staff_id' => null,
        'doc_id' => null,
        'profile_picture_url' => null,
        'created_at' => null,
        'updated_at' => null
    ];
    $profile_picture_url = null;
}

// Ensure user is always set before view
if (!isset($user) || !is_array($user)) {
    error_log("Superadmin Edit Profile - CRITICAL: User not set before view!");
    $user = [
        'user_id' => $user_id ?? null,
        'user_email' => $_SESSION['user_email'] ?? '',
        'user_password' => null,
        'user_is_superadmin' => null,
        'pat_id' => null,
        'staff_id' => null,
        'doc_id' => null,
        'profile_picture_url' => null,
        'created_at' => null,
        'updated_at' => null
    ];
}

require_once __DIR__ . '/../../views/superadmin/edit-profile.view.php';

