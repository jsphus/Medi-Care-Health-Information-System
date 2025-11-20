<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Payment.php';
require_once __DIR__ . '/../../classes/PaymentMethod.php';
require_once __DIR__ . '/../../classes/PaymentStatus.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireStaff();

$db = Database::getInstance();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Handle form submissions (Staff can Add and Update, but NOT Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $appointment_id = sanitize($_POST['appointment_id']);
        $amount = floatval($_POST['amount']);
        $payment_method_id = (int)$_POST['payment_method_id'];
        $payment_status_id = (int)$_POST['payment_status_id'];
        $payment_date = $_POST['payment_date'];
        $notes = sanitize($_POST['notes'] ?? '');
        
        if (empty($appointment_id) || empty($amount) || empty($payment_method_id) || empty($payment_status_id)) {
            $error = 'Appointment ID, amount, payment method, and status are required';
        } else {
            try {
                $payment = new Payment();
                $createData = [
                    'appointment_id' => $appointment_id,
                    'payment_amount' => $amount,
                    'payment_method_id' => $payment_method_id,
                    'payment_status_id' => $payment_status_id,
                    'payment_date' => $payment_date,
                    'payment_notes' => $notes
                ];
                $result = $payment->create($createData);
                if ($result['success']) {
                    $success = 'Payment record created successfully';
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
        $amount = floatval($_POST['amount']);
        $payment_method_id = (int)$_POST['payment_method_id'];
        $payment_status_id = (int)$_POST['payment_status_id'];
        $payment_date = $_POST['payment_date'];
        $notes = sanitize($_POST['notes'] ?? '');
        
        try {
            $payment = new Payment();
            $updateData = [
                'payment_id' => $id,
                'payment_amount' => $amount,
                'payment_method_id' => $payment_method_id,
                'payment_status_id' => $payment_status_id,
                'payment_date' => $payment_date,
                'payment_notes' => $notes
            ];
            $result = $payment->update($updateData);
            if ($result['success']) {
                $success = 'Payment record updated successfully';
            } else {
                $error = $result['message'] ?? 'Database error';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
    
    if ($action === 'update_status') {
        $id = (int)$_POST['id'];
        $payment_status_id = (int)$_POST['payment_status_id'];
        
        try {
            $payment = new Payment();
            $updateData = [
                'payment_id' => $id,
                'payment_status_id' => $payment_status_id
            ];
            $result = $payment->update($updateData);
            if ($result['success']) {
                $success = 'Payment status updated successfully';
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

$filter_status = isset($_GET['status']) ? (int)$_GET['status'] : null;
$filter_method = isset($_GET['method']) ? (int)$_GET['method'] : null;

// Fetch payments with filters
try {
    $where_conditions = [];
    $params = [];

    if (!empty($search_query)) {
        $where_conditions[] = "(p.payment_id LIKE :search OR a.appointment_id LIKE :search OR pat.pat_first_name LIKE :search OR pat.pat_last_name LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }

    if ($filter_status) {
        $where_conditions[] = "p.payment_status_id = :status";
        $params['status'] = $filter_status;
    }

    if ($filter_method) {
        $where_conditions[] = "p.payment_method_id = :method";
        $params['method'] = $filter_method;
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'payment_date';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['payment_date', 'payment_amount', 'payment_id', 'created_at'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'payment_date';
    }
    
    // Special handling for date sorting
    if ($sort_column === 'payment_date') {
        $order_by = "p.payment_date $sort_order, p.created_at $sort_order";
    } else {
        $order_by = "p.$sort_column $sort_order";
    }

    $sql = "
        SELECT p.*, 
               a.appointment_id, a.appointment_date,
               pat.pat_first_name, pat.pat_last_name,
               pm.method_name,
               ps.status_name
        FROM payments p
        LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
        LEFT JOIN patients pat ON a.pat_id = pat.pat_id
        LEFT JOIN payment_methods pm ON p.payment_method_id = pm.method_id
        LEFT JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        $where_clause
        ORDER BY $order_by
    ";
    $payments = $db->fetchAll($sql, $params);
} catch (PDOException $e) {
    $error = 'Failed to fetch payments: ' . $e->getMessage();
    $payments = [];
}

// Fetch filter data from database
$filter_methods = [];
try {
    // Get unique payment methods from payments
    $filter_methods = $db->fetchAll("SELECT DISTINCT pm.method_id, pm.method_name FROM payments p JOIN payment_methods pm ON p.payment_method_id = pm.method_id ORDER BY pm.method_name");
} catch (PDOException $e) {
    $filter_methods = [];
}

// Fetch payment methods and statuses for dropdowns
try {
    $payment_methods = (new PaymentMethod())->findAll(['is_active' => true], 'method_name ASC');
    $payment_statuses = (new PaymentStatus())->findAll([], 'status_name ASC');
} catch (PDOException $e) {
    $payment_methods = [];
    $payment_statuses = [];
}

// Calculate statistics for summary cards
$stats = [
    'total_this_month' => 0,
    'paid' => 0,
    'pending' => 0,
    'total_amount' => 0
];

try {
    // Total payments this month
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM payments WHERE DATE_TRUNC('month', payment_date) = DATE_TRUNC('month', CURRENT_DATE)");
    $stats['total_this_month'] = $result['count'] ?? 0;
    
    // Paid payments
    $result = $db->fetchOne("
        SELECT COUNT(*) as count 
        FROM payments p
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE LOWER(ps.status_name) = 'paid'
    ");
    $stats['paid'] = $result['count'] ?? 0;
    
    // Pending payments
    $result = $db->fetchOne("
        SELECT COUNT(*) as count 
        FROM payments p
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE LOWER(ps.status_name) = 'pending'
    ");
    $stats['pending'] = $result['count'] ?? 0;
    
    // Total amount
    $result = $db->fetchOne("SELECT COALESCE(SUM(payment_amount), 0) as total FROM payments");
    $stats['total_amount'] = $result['total'] ?? 0;
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/staff/payments.view.php';
