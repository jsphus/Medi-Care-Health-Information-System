<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';
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
        $status_name = sanitize($_POST['status_name']);
        $status_description = sanitize($_POST['status_description'] ?? '');
        
        if (empty($status_name)) {
            $error = 'Payment status name is required';
        } else {
            try {
                $stmt = $db->prepare("
                    INSERT INTO payment_statuses (status_name, status_description, created_at) 
                    VALUES (:status_name, :status_description, NOW())
                ");
                $stmt->execute([
                    'status_name' => $status_name,
                    'status_description' => $status_description
                ]);
                $success = 'Payment status created successfully';
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
                $stmt = $db->prepare("
                    UPDATE payment_statuses 
                    SET status_name = :status_name, status_description = :status_description, updated_at = NOW()
                    WHERE payment_status_id = :id
                ");
                $stmt->execute([
                    'status_name' => $status_name,
                    'status_description' => $status_description,
                    'id' => $id
                ]);
                $success = 'Payment status updated successfully';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// Fetch all payment statuses
try {
    $stmt = $db->query("
        SELECT ps.*, COUNT(p.payment_id) as payment_count
        FROM payment_statuses ps
        LEFT JOIN payments p ON ps.payment_status_id = p.payment_status_id
        GROUP BY ps.payment_status_id
        ORDER BY ps.status_name ASC
    ");
    $payment_statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    $stmt = $db->query("SELECT COUNT(*) as count FROM payment_statuses");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total payments
    $stmt = $db->query("SELECT COUNT(*) as count FROM payments");
    $stats['total_payments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/staff/payment-statuses.view.php';
