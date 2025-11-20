<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name']);
    $middle_initial = sanitize($_POST['middle_initial'] ?? '');
    $last_name = sanitize($_POST['last_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $specialization_id = !empty($_POST['specialization_id']) ? (int)$_POST['specialization_id'] : null;
    $license_number = sanitize($_POST['license_number']);
    $experience_years = !empty($_POST['experience_years']) ? (int)$_POST['experience_years'] : null;
    $consultation_fee = !empty($_POST['consultation_fee']) ? floatval($_POST['consultation_fee']) : null;
    $qualification = sanitize($_POST['qualification'] ?? '');
    $bio = sanitize($_POST['bio'] ?? '');
    
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'First name, last name, and email are required';
    } elseif (!isValidEmail($email)) {
        $error = 'Invalid email format';
    } else {
        try {
            $stmt = $db->prepare("
                UPDATE doctors 
                SET doc_first_name = :first_name, doc_middle_initial = :middle_initial, doc_last_name = :last_name, doc_email = :email, 
                    doc_phone = :phone, doc_specialization_id = :specialization_id, doc_license_number = :license_number,
                    doc_experience_years = :experience_years, doc_consultation_fee = :consultation_fee,
                    doc_qualification = :qualification, doc_bio = :bio, updated_at = NOW()
                WHERE doc_id = :id
            ");
            $stmt->execute([
                'first_name' => $first_name,
                'middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'specialization_id' => $specialization_id,
                'license_number' => $license_number,
                'experience_years' => $experience_years,
                'consultation_fee' => $consultation_fee,
                'qualification' => $qualification,
                'bio' => $bio,
                'id' => $doctor_id
            ]);
            $success = 'Profile updated successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch doctor profile
try {
    $stmt = $db->prepare("
        SELECT d.*, s.spec_name 
        FROM doctors d
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
        WHERE d.doc_id = :doctor_id
    ");
    $stmt->execute(['doctor_id' => $doctor_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$doctor) {
        $error = 'Doctor profile not found';
        $doctor = [];
    }
} catch (PDOException $e) {
    $error = 'Failed to fetch profile: ' . $e->getMessage();
    $doctor = [];
}

// Fetch specializations for dropdown
try {
    $stmt = $db->query("SELECT spec_id, spec_name FROM specializations ORDER BY spec_name");
    $specializations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $specializations = [];
}

require_once __DIR__ . '/../../views/doctor/profile.view.php';
