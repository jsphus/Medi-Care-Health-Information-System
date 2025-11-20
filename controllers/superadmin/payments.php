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
                $stmt = $db->prepare("
                    INSERT INTO payments (appointment_id, payment_amount, payment_method_id, payment_status_id, 
                                         payment_date, payment_notes, created_at) 
                    VALUES (:appointment_id, :payment_amount, :payment_method_id, :payment_status_id, 
                           :payment_date, :payment_notes, NOW())
                ");
                $stmt->execute([
                    'appointment_id' => $appointment_id,
                    'payment_amount' => $amount,
                    'payment_method_id' => $payment_method_id,
                    'payment_status_id' => $payment_status_id,
                    'payment_date' => $payment_date,
                    'payment_notes' => $notes
                ]);
                $success = 'Payment record created successfully';
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
            $stmt = $db->prepare("
                UPDATE payments 
                SET payment_amount = :payment_amount, payment_method_id = :payment_method_id, 
                    payment_status_id = :payment_status_id, payment_date = :payment_date,
                    payment_notes = :payment_notes, updated_at = NOW()
                WHERE payment_id = :id
            ");
            $stmt->execute([
                'payment_amount' => $amount,
                'payment_method_id' => $payment_method_id,
                'payment_status_id' => $payment_status_id,
                'payment_date' => $payment_date,
                'payment_notes' => $notes,
                'id' => $id
            ]);
            $success = 'Payment record updated successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
    
    if ($action === 'update_status') {
        $id = (int)$_POST['id'];
        $payment_status_id = (int)$_POST['payment_status_id'];
        
        try {
            $stmt = $db->prepare("
                UPDATE payments 
                SET payment_status_id = :payment_status_id, updated_at = NOW()
                WHERE payment_id = :id
            ");
            $stmt->execute([
                'payment_status_id' => $payment_status_id,
                'id' => $id
            ]);
            $success = 'Payment status updated successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        try {
            $stmt = $db->prepare("DELETE FROM payments WHERE payment_id = :id");
            $stmt->execute(['id' => $id]);
            $success = 'Payment record deleted successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Pagination - check if we should load all results (for client-side filtering)
$load_all = isset($_GET['all_results']) && $_GET['all_results'] == '1';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = $load_all ? 10000 : 10; // Load all if filtering, otherwise paginate
$offset = $load_all ? 0 : (($page - 1) * $items_per_page);

// Fetch all payments with related data
try {
    // Get total count for pagination
    $count_stmt = $db->query("SELECT COUNT(*) FROM payments p");
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);
    
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
    
    // Fetch paginated results
    $stmt = $db->prepare("
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
        ORDER BY $order_by
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch payments: ' . $e->getMessage();
    $payments = [];
    $total_items = 0;
    $total_pages = 0;
}

// Fetch payment methods and statuses for dropdowns
try {
    $payment_methods = $db->query("SELECT * FROM payment_methods WHERE is_active = true ORDER BY method_name")->fetchAll(PDO::FETCH_ASSOC);
    $payment_statuses = $db->query("SELECT * FROM payment_statuses ORDER BY status_name")->fetchAll(PDO::FETCH_ASSOC);
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
    $stmt = $db->query("SELECT COUNT(*) as count FROM payments WHERE DATE_TRUNC('month', payment_date) = DATE_TRUNC('month', CURRENT_DATE)");
    $stats['total_this_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Paid payments
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM payments p
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE LOWER(ps.status_name) = 'paid'
    ");
    $stats['paid'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending payments
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM payments p
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE LOWER(ps.status_name) = 'pending'
    ");
    $stats['pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total amount
    $stmt = $db->query("SELECT COALESCE(SUM(payment_amount), 0) as total FROM payments");
    $stats['total_amount'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/superadmin/payments.view.php';
