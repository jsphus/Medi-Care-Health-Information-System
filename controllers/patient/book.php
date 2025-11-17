<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requirePatient();

$db = Database::getInstance();
$error = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = initializeProfilePicture($auth, $db);

// Handle search and filter
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

$filter_specialization = isset($_GET['specialization']) ? (int)$_GET['specialization'] : null;

// Fetch all active doctors with specializations
try {
    $where_conditions = ["d.doc_status = 'active'"];
    $params = [];

    if (!empty($search_query)) {
        $where_conditions[] = "(d.doc_first_name LIKE :search OR d.doc_last_name LIKE :search OR s.spec_name LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }

    if ($filter_specialization) {
        $where_conditions[] = "d.doc_specialization_id = :specialization";
        $params['specialization'] = $filter_specialization;
    }

    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

    $stmt = $db->prepare("
        SELECT d.*, s.spec_name, u.profile_picture_url
        FROM doctors d
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
        LEFT JOIN users u ON d.doc_id = u.doc_id
        $where_clause
        ORDER BY d.doc_first_name, d.doc_last_name
    ");
    $stmt->execute($params);
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch doctors: ' . $e->getMessage();
    $doctors = [];
}

// Fetch all specializations for filter
$specializations = [];
try {
    $stmt = $db->query("SELECT * FROM specializations ORDER BY spec_name");
    $specializations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $specializations = [];
}

require_once __DIR__ . '/../../views/patient/book.view.php';

