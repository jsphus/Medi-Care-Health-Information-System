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

// Initialize profile picture for consistent display across the system
$profile_picture_url = initializeProfilePicture($auth, $db);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone'] ?? '');
    $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
    $gender = sanitize($_POST['gender'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $emergency_contact = sanitize($_POST['emergency_contact'] ?? '');
    $emergency_phone = sanitize($_POST['emergency_phone'] ?? '');
    
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'First name, last name, and email are required';
    } elseif (!isValidEmail($email)) {
        $error = 'Invalid email format';
    } else {
        try {
            $stmt = $db->prepare("
                UPDATE patients 
                SET pat_first_name = :first_name, pat_last_name = :last_name, pat_email = :email, 
                    pat_phone = :phone, pat_date_of_birth = :date_of_birth, pat_gender = :gender,
                    pat_address = :address, pat_emergency_contact = :emergency_contact,
                    pat_emergency_phone = :emergency_phone, updated_at = NOW()
                WHERE pat_id = :id
            ");
            $stmt->execute([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'date_of_birth' => $date_of_birth,
                'gender' => $gender,
                'address' => $address,
                'emergency_contact' => $emergency_contact,
                'emergency_phone' => $emergency_phone,
                'id' => $patient_id
            ]);
            $success = 'Profile updated successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch patient profile
try {
    $stmt = $db->prepare("SELECT * FROM patients WHERE pat_id = :patient_id");
    $stmt->execute(['patient_id' => $patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$patient) {
        $error = 'Patient profile not found';
        $patient = [];
    }
} catch (PDOException $e) {
    $error = 'Failed to fetch profile: ' . $e->getMessage();
    $patient = [];
}

require_once __DIR__ . '/../../views/patient/profile.view.php';
