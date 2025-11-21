<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Service.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireStaff();

$db = Database::getInstance();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Handle API requests for appointments
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_appointments') {
    header('Content-Type: application/json');
    
    $service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;
    
    if ($service_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid service ID']);
        exit;
    }
    
    try {
        $sql = "
            SELECT a.*, 
                   p.pat_first_name, p.pat_last_name, p.pat_phone,
                   d.doc_first_name, d.doc_last_name,
                   st.status_name, st.status_color
            FROM appointments a
            LEFT JOIN patients p ON a.pat_id = p.pat_id
            LEFT JOIN doctors d ON a.doc_id = d.doc_id
            LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
            WHERE a.service_id = :service_id
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ";
        $appointments = $db->fetchAll($sql, ['service_id' => $service_id]);
        
        echo json_encode(['success' => true, 'appointments' => $appointments]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch appointments: ' . $e->getMessage()]);
    }
    exit;
}

// Handle form submissions (Staff can Add, Update, and Delete)
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
                $service = new Service();
                $createData = [
                    'service_name' => $service_name,
                    'service_description' => $service_description,
                    'service_price' => $service_price,
                    'service_duration_minutes' => $service_duration,
                    'service_category' => $service_category
                ];
                $result = $service->create($createData);
                if ($result['success']) {
                    $success = 'Service created successfully';
                } else {
                    $error = $result['message'] ?? 'Database error';
                }
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
                $service = new Service();
                $updateData = [
                    'service_name' => $service_name,
                    'service_description' => $service_description,
                    'service_price' => $service_price,
                    'service_duration_minutes' => $service_duration,
                    'service_category' => $service_category
                ];
                $result = $service->update($id, $updateData);
                if ($result['success']) {
                    $success = 'Service updated successfully';
                } else {
                    $error = $result['message'] ?? 'Database error';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        
        if (empty($id)) {
            $error = 'Invalid service ID';
        } else {
            try {
                // Check if service has associated appointments
                $appointmentCount = $db->fetchOne(
                    "SELECT COUNT(*) as count FROM appointments WHERE service_id = :id",
                    ['id' => $id]
                );
                
                if ($appointmentCount && $appointmentCount['count'] > 0) {
                    $error = 'Cannot delete service: There are ' . $appointmentCount['count'] . ' appointment(s) associated with this service.';
                } else {
                    $service = new Service();
                    $result = $service->delete($id);
                    if ($result['success']) {
                        $success = 'Service deleted successfully';
                    } else {
                        $error = !empty($result['errors']) ? implode(', ', $result['errors']) : 'Failed to delete service';
                    }
                }
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

    $sql = "
        SELECT s.*, COUNT(a.appointment_id) as appointment_count
        FROM services s
        LEFT JOIN appointments a ON s.service_id = a.service_id
        $where_clause
        GROUP BY s.service_id
        ORDER BY s.service_name ASC
    ";
    $services = $db->fetchAll($sql, $params);
} catch (PDOException $e) {
    $error = 'Failed to fetch services: ' . $e->getMessage();
    $services = [];
}

// Fetch filter data from database
$filter_categories = [];
try {
    $filter_categories = $db->fetchAll("SELECT DISTINCT service_category FROM services WHERE service_category IS NOT NULL AND service_category != '' ORDER BY service_category");
    $filter_categories = array_column($filter_categories, 'service_category');
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
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM services");
    $stats['total'] = $result['count'] ?? 0;
    
    // Total appointments using services
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM appointments WHERE service_id IS NOT NULL");
    $stats['total_appointments'] = $result['count'] ?? 0;
    
    // Total revenue from services
    $result = $db->fetchOne("
        SELECT COALESCE(SUM(s.service_price), 0) as total 
        FROM appointments a
        JOIN services s ON a.service_id = s.service_id
    ");
    $stats['total_revenue'] = $result['total'] ?? 0;
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/staff/services.view.php';
