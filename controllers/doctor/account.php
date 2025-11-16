<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $first_name = sanitize($_POST['first_name'] ?? '');
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
                        doc_last_name = :last_name,
                        doc_email = :email,
                        doc_phone = :phone,
                        doc_specialization_id = :specialization_id,
                        updated_at = NOW()
                    WHERE doc_id = :doctor_id
                ");
                $stmt->execute([
                    'first_name' => $first_name,
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
}

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
    
    // Get profile picture URL
    $user_id = $auth->getUserId();
    $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_picture_url = $user['profile_picture_url'] ?? null;
    
    // Fetch specializations for dropdown
    $stmt = $db->query("SELECT * FROM specializations ORDER BY spec_name");
    $specializations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    $doctor = null;
    $profile_picture_url = null;
    $specializations = [];
}

require_once __DIR__ . '/../../views/doctor/account.view.php';

