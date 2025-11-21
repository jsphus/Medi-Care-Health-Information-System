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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
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
                error_log("Staff Edit Profile Update Error: " . $e->getMessage());
            }
        }
    }
}

// Check for success message from redirect
if (isset($_GET['success']) && $_GET['success'] === 'updated') {
    $success = 'Account information updated successfully';
}

// Fetch staff data using Staff class
try {
    if ($staff_id === null) {
        // Try to get staff_id from users table
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
    
    // Ensure all required keys exist with defaults
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
    error_log("Staff Edit Profile Fetch Error: " . $e->getMessage());
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    // Don't set staff to null - try to fetch with defaults
    $staff = [
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
    $profile_picture_url = null;
}

// Ensure staff is always set before view
if (!isset($staff) || !is_array($staff)) {
    $staff = [
        'staff_id' => $staff_id ?? null,
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
}

require_once __DIR__ . '/../../views/staff/edit-profile.view.php';

