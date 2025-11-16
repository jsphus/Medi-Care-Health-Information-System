<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requirePatient();

$db = Database::getInstance();
$error = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = initializeProfilePicture($auth, $db);

// Get doctor ID from URL
$doctor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($doctor_id === 0) {
    header('Location: /patient/book');
    exit;
}

// Fetch doctor details
try {
    $stmt = $db->prepare("
        SELECT d.*, s.spec_name, s.spec_description
        FROM doctors d
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
        WHERE d.doc_id = :doctor_id AND d.doc_status = 'active'
    ");
    $stmt->execute(['doctor_id' => $doctor_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$doctor) {
        $error = 'Doctor not found or not available';
        $doctor = null;
    }
} catch (PDOException $e) {
    $error = 'Failed to fetch doctor details: ' . $e->getMessage();
    $doctor = null;
}

require_once __DIR__ . '/../../views/patient/doctor-detail.view.php';

