<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requireStaff();

$db = Database::getInstance();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = initializeProfilePicture($auth, $db);

// Handle form submissions (Staff can Add and Update, but NOT Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $method_name = sanitize($_POST['method_name']);
        $method_description = sanitize($_POST['method_description'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($method_name)) {
            $error = 'Payment method name is required';
        } else {
            try {
                $stmt = $db->prepare("
                    INSERT INTO payment_methods (method_name, method_description, is_active, created_at) 
                    VALUES (:method_name, :method_description, :is_active, NOW())
                ");
                $stmt->execute([
                    'method_name' => $method_name,
                    'method_description' => $method_description,
                    'is_active' => $is_active
                ]);
                $success = 'Payment method created successfully';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $method_name = sanitize($_POST['method_name']);
        $method_description = sanitize($_POST['method_description'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($method_name)) {
            $error = 'Payment method name is required';
        } else {
            try {
                $stmt = $db->prepare("
                    UPDATE payment_methods 
                    SET method_name = :method_name, method_description = :method_description, 
                        is_active = :is_active, updated_at = NOW()
                    WHERE method_id = :id
                ");
                $stmt->execute([
                    'method_name' => $method_name,
                    'method_description' => $method_description,
                    'is_active' => $is_active,
                    'id' => $id
                ]);
                $success = 'Payment method updated successfully';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// Handle search and filters
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

$filter_status = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Fetch payment methods with filters
try {
    $where_conditions = [];
    $params = [];

    if (!empty($search_query)) {
        $where_conditions[] = "pm.method_name LIKE :search";
        $params['search'] = '%' . $search_query . '%';
    }

    if ($filter_status === 'active') {
        $where_conditions[] = "pm.is_active = 1";
    } elseif ($filter_status === 'inactive') {
        $where_conditions[] = "pm.is_active = 0";
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    $stmt = $db->prepare("
        SELECT pm.*, COUNT(p.payment_id) as payment_count
        FROM payment_methods pm
        LEFT JOIN payments p ON pm.method_id = p.payment_method_id
        $where_clause
        GROUP BY pm.method_id
        ORDER BY pm.method_name ASC
    ");
    $stmt->execute($params);
    $payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch payment methods: ' . $e->getMessage();
    $payment_methods = [];
}

// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'active' => 0,
    'inactive' => 0,
    'total_payments' => 0
];

try {
    // Total payment methods
    $stmt = $db->query("SELECT COUNT(*) as count FROM payment_methods");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Active payment methods
    $stmt = $db->query("SELECT COUNT(*) as count FROM payment_methods WHERE is_active = true");
    $stats['active'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Inactive payment methods
    $stmt = $db->query("SELECT COUNT(*) as count FROM payment_methods WHERE is_active = false");
    $stats['inactive'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total payments using these methods
    $stmt = $db->query("SELECT COUNT(*) as count FROM payments");
    $stats['total_payments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/staff/payment-methods.view.php';
