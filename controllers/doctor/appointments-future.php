<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();
$error = '';
$success = '';

// Handle filters from URL parameters
$filter_patient = isset($_GET['filter_patient']) ? sanitize($_GET['filter_patient']) : '';
$filter_service = isset($_GET['filter_service']) ? sanitize($_GET['filter_service']) : '';
$filter_status = isset($_GET['filter_status']) ? sanitize($_GET['filter_status']) : '';
$filter_date_from = isset($_GET['filter_date_from']) ? sanitize($_GET['filter_date_from']) : '';
$filter_date_to = isset($_GET['filter_date_to']) ? sanitize($_GET['filter_date_to']) : '';

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = 25;
$offset = ($page - 1) * $items_per_page;

// Handle sorting - default to nearest first (date ASC, time ASC)
$sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'appointment_date';
$sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : (isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC');
$allowed_columns = ['appointment_date', 'appointment_time', 'appointment_id'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'appointment_date';
    $sort_order = 'ASC';
}
$sort_order = $sort_order === 'ASC' ? 'ASC' : 'DESC';

$today = date('Y-m-d');

// Fetch appointments with filters
try {
    $where_conditions = ['a.doc_id = :doctor_id', 'a.appointment_date > :today'];
    $params = ['doctor_id' => $doctor_id, 'today' => $today];

    if (!empty($filter_patient)) {
        $where_conditions[] = "(LOWER(p.pat_first_name) LIKE LOWER(:filter_patient) OR LOWER(p.pat_middle_initial) LIKE LOWER(:filter_patient) OR LOWER(p.pat_last_name) LIKE LOWER(:filter_patient) OR LOWER(CONCAT(p.pat_first_name, ' ', COALESCE(p.pat_middle_initial, ''), ' ', p.pat_last_name)) LIKE LOWER(:filter_patient))";
        $params['filter_patient'] = '%' . $filter_patient . '%';
    }

    if (!empty($filter_service)) {
        $where_conditions[] = "LOWER(s.service_name) LIKE LOWER(:filter_service)";
        $params['filter_service'] = '%' . $filter_service . '%';
    }

    if (!empty($filter_status)) {
        $where_conditions[] = "LOWER(st.status_name) = LOWER(:filter_status)";
        $params['filter_status'] = $filter_status;
    }

    if (!empty($filter_date_from)) {
        $where_conditions[] = "DATE(a.appointment_date) >= :filter_date_from";
        $params['filter_date_from'] = $filter_date_from;
    }

    if (!empty($filter_date_to)) {
        $where_conditions[] = "DATE(a.appointment_date) <= :filter_date_to";
        $params['filter_date_to'] = $filter_date_to;
    }

    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

    // Build ORDER BY clause - always include time when sorting by date
    if ($sort_column === 'appointment_date') {
        $order_by = "a.appointment_date $sort_order, a.appointment_time ASC";
    } else {
        $order_by = "a.$sort_column $sort_order";
    }

    // Get total count for pagination
    $count_stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM appointments a
        LEFT JOIN patients p ON a.pat_id = p.pat_id
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
        $where_clause
    ");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

    // Fetch paginated results
    $stmt = $db->prepare("
        SELECT a.*, 
               p.pat_first_name, p.pat_last_name, p.pat_middle_initial, p.pat_phone,
               s.service_name,
               st.status_name, st.status_color,
               up.profile_picture_url as patient_profile_picture
        FROM appointments a
        LEFT JOIN patients p ON a.pat_id = p.pat_id
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
        LEFT JOIN users up ON up.pat_id = p.pat_id
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
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch appointments: ' . $e->getMessage();
    $appointments = [];
    $total_items = 0;
    $total_pages = 0;
}

// Get stats
try {
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments WHERE doc_id = :doctor_id AND appointment_date = :today");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $today_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments WHERE doc_id = :doctor_id AND appointment_date < :today");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $past_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments WHERE doc_id = :doctor_id AND appointment_date > :today");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $future_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    $today_count = 0;
    $past_count = 0;
    $future_count = 0;
}

require_once __DIR__ . '/../../views/doctor/appointments-future.view.php';
