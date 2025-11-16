<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requireStaff();

$db = Database::getInstance();
$error = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = initializeProfilePicture($auth, $db);

// Get specialization ID from URL or query string
$spec_id = 0;
if (isset($_GET['id'])) {
    $spec_id = (int)$_GET['id'];
} else {
    // Try to get from URL path
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $parts = explode('/', $uri);
    if (count($parts) >= 4 && $parts[count($parts) - 2] === 'doctors') {
        $spec_id = (int)$parts[count($parts) - 1];
    }
}

if ($spec_id === 0) {
    header('Location: /staff/specializations');
    exit;
}

// Fetch specialization details
try {
    $stmt = $db->prepare("SELECT * FROM specializations WHERE spec_id = :spec_id");
    $stmt->execute(['spec_id' => $spec_id]);
    $specialization = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$specialization) {
        $error = 'Specialization not found';
        $doctors = [];
    }
} catch (PDOException $e) {
    $error = 'Failed to fetch specialization: ' . $e->getMessage();
    $specialization = null;
    $doctors = [];
}

// Fetch doctors with this specialization
if ($specialization) {
    try {
        $stmt = $db->prepare("
            SELECT d.*, 
                   COUNT(a.appointment_id) as total_appointments
            FROM doctors d
            LEFT JOIN appointments a ON d.doc_id = a.doc_id
            WHERE d.doc_specialization_id = :spec_id
            GROUP BY d.doc_id
            ORDER BY d.doc_first_name, d.doc_last_name
        ");
        $stmt->execute(['spec_id' => $spec_id]);
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Failed to fetch doctors: ' . $e->getMessage();
        $doctors = [];
    }
}

require_once __DIR__ . '/../../views/staff/specialization-doctors.view.php';
