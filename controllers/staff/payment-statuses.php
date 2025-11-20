<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
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
        $status_name = sanitize($_POST['status_name']);
        $status_description = sanitize($_POST['status_description'] ?? '');
        
        if (empty($status_name)) {
            $error = 'Payment status name is required';
        } else {
            try {
                $paymentStatus = new PaymentStatus();
                $createData = [
                    'status_name' => $status_name,
                    'status_description' => $status_description
                ];
                $result = $paymentStatus->create($createData);
                if ($result['success']) {
                    $success = 'Payment status created successfully';
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
        $status_name = sanitize($_POST['status_name']);
        $status_description = sanitize($_POST['status_description'] ?? '');
        
        if (empty($status_name)) {
            $error = 'Payment status name is required';
        } else {
            try {
                $paymentStatus = new PaymentStatus();
                $updateData = [
                    'payment_status_id' => $id,
                    'status_name' => $status_name,
                    'status_description' => $status_description
                ];
                $result = $paymentStatus->update($updateData);
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
}

// Fetch all payment statuses
try {
    $payment_statuses = $db->fetchAll("
        SELECT ps.*, COUNT(p.payment_id) as payment_count
        FROM payment_statuses ps
        LEFT JOIN payments p ON ps.payment_status_id = p.payment_status_id
        GROUP BY ps.payment_status_id
        ORDER BY ps.status_name ASC
    ");
} catch (PDOException $e) {
    $error = 'Failed to fetch payment statuses: ' . $e->getMessage();
    $payment_statuses = [];
}

// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'total_payments' => 0
];

try {
    // Total payment statuses
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM payment_statuses");
    $stats['total'] = $result['count'] ?? 0;
    
    // Total payments
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM payments");
    $stats['total_payments'] = $result['count'] ?? 0;
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/staff/payment-statuses.view.php';
