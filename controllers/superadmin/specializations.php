<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireSuperAdmin();

$db = Database::getInstance();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $spec_name = sanitize($_POST['spec_name']);
        $spec_description = sanitize($_POST['spec_description'] ?? '');
        
        if (empty($spec_name)) {
            $error = 'Specialization name is required';
        } else {
            try {
                $stmt = $db->prepare("
                    INSERT INTO specializations (spec_name, spec_description, created_at) 
                    VALUES (:spec_name, :spec_description, NOW())
                ");
                $stmt->execute([
                    'spec_name' => $spec_name,
                    'spec_description' => $spec_description
                ]);
                $success = 'Specialization created successfully';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $spec_name = sanitize($_POST['spec_name']);
        $spec_description = sanitize($_POST['spec_description'] ?? '');
        
        if (empty($spec_name)) {
            $error = 'Specialization name is required';
        } else {
            try {
                $stmt = $db->prepare("
                    UPDATE specializations 
                    SET spec_name = :spec_name, spec_description = :spec_description, updated_at = NOW()
                    WHERE spec_id = :id
                ");
                $stmt->execute([
                    'spec_name' => $spec_name,
                    'spec_description' => $spec_description,
                    'id' => $id
                ]);
                $success = 'Specialization updated successfully';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        try {
            $stmt = $db->prepare("DELETE FROM specializations WHERE spec_id = :id");
            $stmt->execute(['id' => $id]);
            $success = 'Specialization deleted successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Handle AJAX request to get doctors for a specialization
if (isset($_GET['action']) && $_GET['action'] === 'get_doctors' && isset($_GET['spec_id'])) {
    header('Content-Type: application/json');
    $spec_id = (int)$_GET['spec_id'];
    
    try {
        $stmt = $db->prepare("
            SELECT d.*
            FROM doctors d
            WHERE d.doc_specialization_id = :spec_id
            ORDER BY d.doc_first_name, d.doc_last_name
        ");
        $stmt->execute(['spec_id' => $spec_id]);
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'doctors' => $doctors]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to fetch doctors: ' . $e->getMessage()]);
        exit;
    }
}

// Pagination - check if we should load all results (for client-side filtering)
$load_all = isset($_GET['all_results']) && $_GET['all_results'] == '1';

// Handle sorting
$sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'spec_name';
$sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';

// Validate sort column to prevent SQL injection
$allowed_columns = ['spec_name', 'created_at', 'updated_at'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'spec_name';
}

$order_by = "s.$sort_column $sort_order";

// Fetch all specializations with doctor count
try {
    $stmt = $db->query("
        SELECT s.*, COUNT(d.doc_id) as doctor_count
        FROM specializations s
        LEFT JOIN doctors d ON s.spec_id = d.doc_specialization_id
        GROUP BY s.spec_id
        ORDER BY $order_by
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

require_once __DIR__ . '/../../views/superadmin/specializations.view.php';
