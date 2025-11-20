<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Staff.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireStaff();

$db = Database::getInstance();
$error = '';
$success = '';
$search_query = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Handle form submissions (Staff can Add and Update, but NOT Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone'] ?? '');
        if (!empty($phone)) {
            $phone = formatPhoneNumber($phone);
        }
        $position = sanitize($_POST['position'] ?? '');
        $hire_date = !empty($_POST['hire_date']) ? $_POST['hire_date'] : null;
        $salary = !empty($_POST['salary']) ? floatval($_POST['salary']) : null;
        $status = sanitize($_POST['status'] ?? 'active');
        
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error = 'First name, last name, and email are required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } else {
            try {
                $staff = new Staff();
                $createData = [
                    'staff_first_name' => $first_name,
                    'staff_last_name' => $last_name,
                    'staff_email' => $email,
                    'staff_phone' => $phone,
                    'staff_position' => $position,
                    'staff_hire_date' => $hire_date,
                    'staff_salary' => $salary,
                    'staff_status' => $status
                ];
                $result = $staff->create($createData);
                if ($result['success']) {
                    $success = 'Staff member created successfully';
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
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone'] ?? '');
        if (!empty($phone)) {
            $phone = formatPhoneNumber($phone);
        }
        $position = sanitize($_POST['position'] ?? '');
        $hire_date = !empty($_POST['hire_date']) ? $_POST['hire_date'] : null;
        $salary = !empty($_POST['salary']) ? floatval($_POST['salary']) : null;
        $status = sanitize($_POST['status'] ?? 'active');
        
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error = 'First name, last name, and email are required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } else {
            try {
                $staff = new Staff();
                $updateData = [
                    'staff_id' => $id,
                    'staff_first_name' => $first_name,
                    'staff_last_name' => $last_name,
                    'staff_email' => $email,
                    'staff_phone' => $phone,
                    'staff_position' => $position,
                    'staff_hire_date' => $hire_date,
                    'staff_salary' => $salary,
                    'staff_status' => $status
                ];
                $result = $staff->update($updateData);
                if ($result['success']) {
                    $success = 'Staff member updated successfully';
                } else {
                    $error = $result['message'] ?? 'Database error';
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

$filter_status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$filter_position = isset($_GET['position']) ? sanitize($_GET['position']) : '';

// Fetch staff members with filters
try {
    $where_conditions = [];
    $params = [];

    if (!empty($search_query)) {
        $where_conditions[] = "(staff_first_name LIKE :search OR staff_last_name LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }

    if (!empty($filter_status)) {
        $where_conditions[] = "staff_status = :status";
        $params['status'] = $filter_status;
    }

    if (!empty($filter_position)) {
        $where_conditions[] = "staff_position = :position";
        $params['position'] = $filter_position;
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    $sql = "SELECT * FROM staff $where_clause ORDER BY staff_first_name, staff_last_name";
    $staff_members = $db->fetchAll($sql, $params);
} catch (PDOException $e) {
    $error = 'Failed to fetch staff: ' . $e->getMessage();
    $staff_members = [];
}

// Fetch filter data from database
$filter_positions = [];
try {
    $filter_positions = $db->fetchAll("SELECT DISTINCT staff_position FROM staff WHERE staff_position IS NOT NULL AND staff_position != '' ORDER BY staff_position");
    $filter_positions = array_column($filter_positions, 'staff_position');
} catch (PDOException $e) {
    $filter_positions = [];
}

// Calculate statistics for summary cards
$stats = [
    'total_this_month' => 0,
    'active' => 0,
    'inactive' => 0
];

try {
    // Total staff this month
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM staff WHERE DATE_TRUNC('month', created_at) = DATE_TRUNC('month', CURRENT_DATE)");
    $stats['total_this_month'] = $result['count'] ?? 0;
    
    // Active staff
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM staff WHERE staff_status = 'active'");
    $stats['active'] = $result['count'] ?? 0;
    
    // Inactive staff
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM staff WHERE staff_status = 'inactive'");
    $stats['inactive'] = $result['count'] ?? 0;
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/staff/staff.view.php';
