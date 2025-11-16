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
        $service_name = sanitize($_POST['service_name']);
        $service_description = sanitize($_POST['service_description'] ?? '');
        $service_price = !empty($_POST['service_price']) ? floatval($_POST['service_price']) : 0;
        $service_duration = !empty($_POST['service_duration']) ? (int)$_POST['service_duration'] : 30;
        $service_category = sanitize($_POST['service_category'] ?? '');
        
        if (empty($service_name)) {
            $error = 'Service name is required';
        } else {
            try {
                $stmt = $db->prepare("
                    INSERT INTO services (service_name, service_description, service_price, 
                                         service_duration_minutes, service_category, created_at) 
                    VALUES (:service_name, :service_description, :service_price, 
                           :service_duration, :service_category, NOW())
                ");
                $stmt->execute([
                    'service_name' => $service_name,
                    'service_description' => $service_description,
                    'service_price' => $service_price,
                    'service_duration' => $service_duration,
                    'service_category' => $service_category
                ]);
                $success = 'Service created successfully';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $service_name = sanitize($_POST['service_name']);
        $service_description = sanitize($_POST['service_description'] ?? '');
        $service_price = !empty($_POST['service_price']) ? floatval($_POST['service_price']) : 0;
        $service_duration = !empty($_POST['service_duration']) ? (int)$_POST['service_duration'] : 30;
        $service_category = sanitize($_POST['service_category'] ?? '');
        
        if (empty($service_name)) {
            $error = 'Service name is required';
        } else {
            try {
                $stmt = $db->prepare("
                    UPDATE services 
                    SET service_name = :service_name, service_description = :service_description, 
                        service_price = :service_price, service_duration_minutes = :service_duration,
                        service_category = :service_category, updated_at = NOW()
                    WHERE service_id = :id
                ");
                $stmt->execute([
                    'service_name' => $service_name,
                    'service_description' => $service_description,
                    'service_price' => $service_price,
                    'service_duration' => $service_duration,
                    'service_category' => $service_category,
                    'id' => $id
                ]);
                $success = 'Service updated successfully';
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

$filter_category = isset($_GET['category']) ? sanitize($_GET['category']) : '';

// Fetch services with filters
try {
    $where_conditions = [];
    $params = [];

    if (!empty($search_query)) {
        $where_conditions[] = "s.service_name LIKE :search";
        $params['search'] = '%' . $search_query . '%';
    }

    if (!empty($filter_category)) {
        $where_conditions[] = "s.service_category = :category";
        $params['category'] = $filter_category;
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    $stmt = $db->prepare("
        SELECT s.*, COUNT(a.appointment_id) as appointment_count
        FROM services s
        LEFT JOIN appointments a ON s.service_id = a.service_id
        $where_clause
        GROUP BY s.service_id
        ORDER BY s.service_name ASC
    ");
    $stmt->execute($params);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch services: ' . $e->getMessage();
    $services = [];
}

// Fetch filter data from database
$filter_categories = [];
try {
    $stmt = $db->query("SELECT DISTINCT service_category FROM services WHERE service_category IS NOT NULL AND service_category != '' ORDER BY service_category");
    $filter_categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $filter_categories = [];
}

// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'total_appointments' => 0,
    'total_revenue' => 0
];

try {
    // Total services
    $stmt = $db->query("SELECT COUNT(*) as count FROM services");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total appointments using services
    $stmt = $db->query("SELECT COUNT(*) as count FROM appointments WHERE service_id IS NOT NULL");
    $stats['total_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total revenue from services
    $stmt = $db->query("
        SELECT COALESCE(SUM(s.service_price), 0) as total 
        FROM appointments a
        JOIN services s ON a.service_id = s.service_id
    ");
    $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/staff/services.view.php';
