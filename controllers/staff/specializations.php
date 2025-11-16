<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requireStaff();

$db = Database::getInstance();
$error = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = initializeProfilePicture($auth, $db);

// Fetch all specializations with doctor count
try {
    $stmt = $db->query("
        SELECT s.*, COUNT(d.doc_id) as doctor_count
        FROM specializations s
        LEFT JOIN doctors d ON s.spec_id = d.doc_specialization_id
        GROUP BY s.spec_id
        ORDER BY s.spec_name ASC
    ");
    $specializations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch specializations: ' . $e->getMessage();
    $specializations = [];
}

// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'with_doctors' => 0,
    'total_doctors' => 0
];

try {
    // Total specializations
    $stmt = $db->query("SELECT COUNT(*) as count FROM specializations");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Specializations with doctors
    $stmt = $db->query("
        SELECT COUNT(DISTINCT s.spec_id) as count 
        FROM specializations s
        INNER JOIN doctors d ON s.spec_id = d.doc_specialization_id
    ");
    $stats['with_doctors'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total doctors across all specializations
    $stmt = $db->query("SELECT COUNT(*) as count FROM doctors WHERE doc_specialization_id IS NOT NULL");
    $stats['total_doctors'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/staff/specializations.view.php';
