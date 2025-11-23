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
            ORDER BY d.created_at DESC, d.doc_first_name, d.doc_last_name
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

// Handle filters from URL parameters
$filter_name = isset($_GET['filter_name']) ? sanitize($_GET['filter_name']) : '';
$filter_description = isset($_GET['filter_description']) ? sanitize($_GET['filter_description']) : '';
$filter_min_doctors = isset($_GET['filter_min_doctors']) && $_GET['filter_min_doctors'] !== '' ? (int)$_GET['filter_min_doctors'] : null;
$filter_date_from = isset($_GET['filter_date_from']) ? sanitize($_GET['filter_date_from']) : '';
$filter_date_to = isset($_GET['filter_date_to']) ? sanitize($_GET['filter_date_to']) : '';

// Handle sorting
$sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'spec_name';
$sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';

// Validate sort column to prevent SQL injection
$allowed_columns = ['spec_name', 'created_at', 'updated_at'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'spec_name';
}

$order_by = "s.$sort_column $sort_order";

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = 25;
$offset = ($page - 1) * $items_per_page;

// Fetch specializations with filters
try {
    $where_conditions = [];
    $params = [];

    if (!empty($filter_name)) {
        $where_conditions[] = "LOWER(s.spec_name) LIKE LOWER(:filter_name)";
        $params['filter_name'] = '%' . $filter_name . '%';
    }

    if (!empty($filter_description)) {
        $where_conditions[] = "LOWER(s.spec_description) LIKE LOWER(:filter_description)";
        $params['filter_description'] = '%' . $filter_description . '%';
    }

    if (!empty($filter_date_from)) {
        $where_conditions[] = "DATE(s.created_at) >= :filter_date_from";
        $params['filter_date_from'] = $filter_date_from;
    }

    if (!empty($filter_date_to)) {
        $where_conditions[] = "DATE(s.created_at) <= :filter_date_to";
        $params['filter_date_to'] = $filter_date_to;
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Get total count for pagination (need to account for HAVING clause)
    $count_having = '';
    if ($filter_min_doctors !== null) {
        $count_having = 'HAVING COUNT(d.doc_id) >= :count_min_doctors';
    }
    $count_params = $params;
    if ($filter_min_doctors !== null) {
        $count_params['count_min_doctors'] = $filter_min_doctors;
    }
    $count_stmt = $db->prepare("
        SELECT COUNT(*) FROM (
            SELECT s.spec_id
            FROM specializations s
            LEFT JOIN doctors d ON s.spec_id = d.doc_specialization_id
            $where_clause
            GROUP BY s.spec_id
            $count_having
        ) as filtered_specs
    ");
    $count_stmt->execute($count_params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

    // Build HAVING clause for min_doctors filter
    $having_clause = '';
    if ($filter_min_doctors !== null) {
        $having_clause = 'HAVING COUNT(d.doc_id) >= :filter_min_doctors';
        $params['filter_min_doctors'] = $filter_min_doctors;
    }

    // Fetch paginated results with doctor count
    $stmt = $db->prepare("
        SELECT s.*, COUNT(d.doc_id) as doctor_count
        FROM specializations s
        LEFT JOIN doctors d ON s.spec_id = d.doc_specialization_id
        $where_clause
        GROUP BY s.spec_id
        $having_clause
        ORDER BY $order_by
        LIMIT :limit OFFSET :offset
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $specializations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch specializations: ' . $e->getMessage();
    $specializations = [];
    $total_items = 0;
    $total_pages = 0;
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
