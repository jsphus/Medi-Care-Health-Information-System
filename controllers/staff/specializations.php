<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireStaff();

$db = Database::getInstance();
$error = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Fetch all specializations with doctor count
try {
    $specializations = $db->fetchAll("
        SELECT s.*, COUNT(d.doc_id) as doctor_count
        FROM specializations s
        LEFT JOIN doctors d ON s.spec_id = d.doc_specialization_id
        GROUP BY s.spec_id
        ORDER BY s.spec_name ASC
    ");
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
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM specializations");
    $stats['total'] = $result['count'] ?? 0;
    
    // Specializations with doctors
    $result = $db->fetchOne("
        SELECT COUNT(DISTINCT s.spec_id) as count 
        FROM specializations s
        INNER JOIN doctors d ON s.spec_id = d.doc_specialization_id
    ");
    $stats['with_doctors'] = $result['count'] ?? 0;
    
    // Total doctors across all specializations
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM doctors WHERE doc_specialization_id IS NOT NULL");
    $stats['total_doctors'] = $result['count'] ?? 0;
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/staff/specializations.view.php';
