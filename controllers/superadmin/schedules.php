<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Schedule.php';
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
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $schedule_date = sanitize($_POST['schedule_date'] ?? '');
        $start_time = sanitize($_POST['start_time'] ?? '');
        $end_time = sanitize($_POST['end_time'] ?? '');
        $max_appointments = !empty($_POST['max_appointments']) ? (int)$_POST['max_appointments'] : 10;
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        
        if (empty($schedule_date) || empty($start_time) || empty($end_time)) {
            $error = 'Date, start time, and end time are required';
        } else {
            try {
                $schedule = new Schedule();
                $updateData = [
                    'schedule_id' => $id,
                    'schedule_date' => $schedule_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'max_appointments' => $max_appointments,
                    'is_available' => $is_available
                ];
                $result = $schedule->update($updateData);
                if ($result['success']) {
                    $success = 'Schedule updated successfully';
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
        try {
            $schedule = new Schedule();
            $result = $schedule->delete($id);
            if ($result['success']) {
                $success = 'Schedule deleted successfully';
            } else {
                $error = $result['message'] ?? 'Database error';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Handle search and filters
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

$filter_doctor = isset($_GET['doctor']) ? (int)$_GET['doctor'] : null;
$filter_available = isset($_GET['available']) ? $_GET['available'] : '';

// Pagination - check if we should load all results (for client-side filtering)
$load_all = isset($_GET['all_results']) && $_GET['all_results'] == '1';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = $load_all ? 10000 : 10; // Load all if filtering, otherwise paginate
$offset = $load_all ? 0 : (($page - 1) * $items_per_page);

// Fetch schedules with filters
try {
    $where_conditions = [];
    $params = [];

    if (!empty($search_query)) {
        $where_conditions[] = "(d.doc_first_name LIKE :search OR d.doc_last_name LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }

    if ($filter_doctor) {
        $where_conditions[] = "s.doc_id = :doctor";
        $params['doctor'] = $filter_doctor;
    }

    if ($filter_available !== '') {
        if ($filter_available === 'yes') {
            $where_conditions[] = "s.is_available = 1";
        } elseif ($filter_available === 'no') {
            $where_conditions[] = "s.is_available = 0";
        }
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'schedule_date';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['schedule_date', 'start_time', 'end_time'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'schedule_date';
    }
    
    // Special handling for date/time sorting
    if ($sort_column === 'schedule_date') {
        $order_by = "s.schedule_date $sort_order, s.start_time $sort_order";
    } else {
        $order_by = "s.$sort_column $sort_order";
    }

    // Get total count for pagination
    $count_stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM schedules s
        LEFT JOIN doctors d ON s.doc_id = d.doc_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        $where_clause
    ");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

    $stmt = $db->prepare("
        SELECT s.*, 
               d.doc_first_name, d.doc_last_name,
               sp.spec_name
        FROM schedules s
        LEFT JOIN doctors d ON s.doc_id = d.doc_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        $where_clause
        ORDER BY $order_by
        LIMIT :limit OFFSET :offset
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch schedules: ' . $e->getMessage();
    $schedules = [];
    $total_items = 0;
    $total_pages = 0;
}

// Fetch filter data from database
$filter_doctors = [];
try {
    // Get unique doctors from schedules
    $filter_doctors = $db->fetchAll("SELECT DISTINCT d.doc_id, d.doc_first_name, d.doc_last_name FROM schedules s JOIN doctors d ON s.doc_id = d.doc_id ORDER BY d.doc_first_name");
} catch (PDOException $e) {
    $filter_doctors = [];
}

// Get today's schedules
try {
    $today = date('Y-m-d');
    $today_schedules = $db->fetchAll("
        SELECT s.*, 
               d.doc_first_name, d.doc_last_name,
               sp.spec_name
        FROM schedules s
        LEFT JOIN doctors d ON s.doc_id = d.doc_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        WHERE s.schedule_date = :today
        ORDER BY s.start_time ASC
    ", ['today' => $today]);
} catch (PDOException $e) {
    $today_schedules = [];
}

require_once __DIR__ . '/../../views/superadmin/schedules.view.php';
