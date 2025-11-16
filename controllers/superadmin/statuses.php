<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requireSuperAdmin();

$db = Database::getInstance();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = initializeProfilePicture($auth, $db);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $status_name = sanitize($_POST['status_name']);
        $status_description = sanitize($_POST['status_description'] ?? '');
        $status_color = sanitize($_POST['status_color'] ?? '#3B82F6');
        
        if (empty($status_name)) {
            $error = 'Status name is required';
        } else {
            try {
                $stmt = $db->prepare("
                    INSERT INTO appointment_statuses (status_name, status_description, status_color, created_at) 
                    VALUES (:status_name, :status_description, :status_color, NOW())
                ");
                $stmt->execute([
                    'status_name' => $status_name,
                    'status_description' => $status_description,
                    'status_color' => $status_color
                ]);
                $success = 'Status created successfully';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $status_name = sanitize($_POST['status_name']);
        $status_description = sanitize($_POST['status_description'] ?? '');
        $status_color = sanitize($_POST['status_color'] ?? '#3B82F6');
        
        if (empty($status_name)) {
            $error = 'Status name is required';
        } else {
            try {
                $stmt = $db->prepare("
                    UPDATE appointment_statuses 
                    SET status_name = :status_name, status_description = :status_description, 
                        status_color = :status_color
                    WHERE status_id = :id
                ");
                $stmt->execute([
                    'status_name' => $status_name,
                    'status_description' => $status_description,
                    'status_color' => $status_color,
                    'id' => $id
                ]);
                $success = 'Status updated successfully';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        try {
            $stmt = $db->prepare("DELETE FROM appointment_statuses WHERE status_id = :id");
            $stmt->execute(['id' => $id]);
            $success = 'Status deleted successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch all statuses with appointment count
try {
    $stmt = $db->query("
        SELECT s.*, COUNT(a.appointment_id) as appointment_count
        FROM appointment_statuses s
        LEFT JOIN appointments a ON s.status_id = a.status_id
        GROUP BY s.status_id
        ORDER BY s.status_id ASC
    ");
    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch statuses: ' . $e->getMessage();
    $statuses = [];
}

// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'total_appointments' => 0
];

try {
    // Total appointment statuses
    $stmt = $db->query("SELECT COUNT(*) as count FROM appointment_statuses");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total appointments
    $stmt = $db->query("SELECT COUNT(*) as count FROM appointments");
    $stats['total_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/superadmin/statuses.view.php';
