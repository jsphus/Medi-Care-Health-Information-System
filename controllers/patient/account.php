<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requirePatient();

$db = Database::getInstance();
$patient_id = $auth->getPatientId();
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
        $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $gender = sanitize($_POST['gender'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error = 'First name, last name, and email are required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } else {
            try {
                $stmt = $db->prepare("
                    UPDATE patients 
                    SET pat_first_name = :first_name, 
                        pat_last_name = :last_name,
                        pat_email = :email,
                        pat_phone = :phone,
                        pat_date_of_birth = :date_of_birth,
                        pat_gender = :gender,
                        pat_address = :address,
                        updated_at = NOW()
                    WHERE pat_id = :patient_id
                ");
                $stmt->execute([
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone' => $phone,
                    'date_of_birth' => $date_of_birth,
                    'gender' => $gender ?: null,
                    'address' => $address ?: null,
                    'patient_id' => $patient_id
                ]);
                $success = 'Account information updated successfully';
                // Refresh patient data
                $stmt = $db->prepare("SELECT * FROM patients WHERE pat_id = :patient_id");
                $stmt->execute(['patient_id' => $patient_id]);
                $patient = $stmt->fetch(PDO::FETCH_ASSOC);
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
                // Get user info
                $stmt = $db->prepare("SELECT u.user_password FROM users u JOIN patients p ON u.pat_id = p.pat_id WHERE p.pat_id = :patient_id");
                $stmt->execute(['patient_id' => $patient_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($current_password, $user['user_password'])) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE users SET user_password = :password WHERE pat_id = :patient_id");
                    $stmt->execute(['password' => $hashed_password, 'patient_id' => $patient_id]);
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

// Fetch patient data
try {
    $stmt = $db->prepare("SELECT * FROM patients WHERE pat_id = :patient_id");
    $stmt->execute(['patient_id' => $patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get profile picture URL
    $user_id = $auth->getUserId();
    $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_picture_url = $user['profile_picture_url'] ?? null;
} catch (PDOException $e) {
    $error = 'Failed to fetch account information: ' . $e->getMessage();
    $patient = null;
    $profile_picture_url = null;
}

require_once __DIR__ . '/../../views/patient/account.view.php';

