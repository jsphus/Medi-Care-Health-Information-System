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
        $middle_initial = sanitize($_POST['middle_initial'] ?? '');
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
                    'staff_middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
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
        $middle_initial = sanitize($_POST['middle_initial'] ?? '');
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
                    'staff_middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                    'staff_last_name' => $last_name,
                    'staff_email' => $email,
                    'staff_phone' => $phone,
                    'staff_position' => $position,
                    'staff_hire_date' => $hire_date,
                    'staff_salary' => $salary,
                    'staff_status' => $status
                ];
                $result = $staff->update($id, $updateData);
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

// Handle filters from URL
$filter_name = isset($_GET['filter_name']) ? sanitize($_GET['filter_name']) : '';
$filter_email = isset($_GET['filter_email']) ? sanitize($_GET['filter_email']) : '';
$filter_phone = isset($_GET['filter_phone']) ? sanitize($_GET['filter_phone']) : '';
$filter_position = isset($_GET['filter_position']) ? sanitize($_GET['filter_position']) : '';
$filter_date_from = isset($_GET['filter_date_from']) ? sanitize($_GET['filter_date_from']) : '';
$filter_date_to = isset($_GET['filter_date_to']) ? sanitize($_GET['filter_date_to']) : '';

// Fetch staff members with filters
try {
    $where_conditions = [];
    $params = [];

    if (!empty($filter_name)) {
        // Case-insensitive search in first name, last name, middle initial, and concatenated full name
        $where_conditions[] = "(LOWER(s.staff_first_name) LIKE :name OR LOWER(s.staff_last_name) LIKE :name OR LOWER(s.staff_middle_initial) LIKE :name OR LOWER(TRIM(CONCAT(COALESCE(s.staff_first_name, ''), ' ', COALESCE(s.staff_middle_initial, ''), ' ', COALESCE(s.staff_last_name, '')))) LIKE :name)";
        $params['name'] = '%' . strtolower($filter_name) . '%';
    }

    if (!empty($filter_email)) {
        // Case-insensitive email search with trimmed input
        $email_clean = trim(strtolower($filter_email));
        $where_conditions[] = "LOWER(TRIM(s.staff_email)) LIKE :email";
        $params['email'] = '%' . $email_clean . '%';
    }

    if (!empty($filter_phone)) {
        // Remove non-numeric characters for phone search to make it more flexible
        $phone_clean = preg_replace('/[^0-9]/', '', $filter_phone);
        $where_conditions[] = "REPLACE(REPLACE(REPLACE(REPLACE(s.staff_phone, ' ', ''), '-', ''), '(', ''), ')', '') LIKE :phone";
        $params['phone'] = '%' . $phone_clean . '%';
    }

    if (!empty($filter_position)) {
        $where_conditions[] = "s.staff_position = :position";
        $params['position'] = $filter_position;
    }

    if (!empty($filter_date_from)) {
        $where_conditions[] = "DATE(s.created_at) >= :date_from";
        $params['date_from'] = $filter_date_from;
    }

    if (!empty($filter_date_to)) {
        $where_conditions[] = "DATE(s.created_at) <= :date_to";
        $params['date_to'] = $filter_date_to;
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'created_at';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['staff_first_name', 'staff_last_name', 'staff_email', 'staff_phone', 'staff_hire_date', 'staff_salary', 'created_at', 'updated_at'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'created_at';
    }
    
    // Special handling for name sorting (sort by first name, then last name)
    if ($sort_column === 'staff_first_name') {
        $order_by = "s.staff_first_name $sort_order, s.staff_last_name $sort_order";
    } else {
        $order_by = "s.$sort_column $sort_order";
    }

    // Pagination
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $items_per_page = 10;
    $offset = (($page - 1) * $items_per_page);
    
    // Get total count for pagination
    $count_sql = "SELECT COUNT(*) as count FROM staff s $where_clause";
    $count_result = $db->fetchOne($count_sql, $params);
    $total_items = (int)($count_result['count'] ?? 0);
    $total_pages = $items_per_page > 0 ? ceil($total_items / $items_per_page) : 1;

    // Fetch staff with profile pictures
    $sql = "SELECT s.*, u.profile_picture_url 
            FROM staff s 
            LEFT JOIN users u ON s.staff_id = u.staff_id 
            $where_clause 
            ORDER BY $order_by 
            LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $staff_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
