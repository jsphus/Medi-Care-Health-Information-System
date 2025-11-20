<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/AppointmentStatus.php';
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
        $status_color = sanitize($_POST['status_color'] ?? '#3B82F6');
        
        if (empty($status_name)) {
            $error = 'Status name is required';
        } else {
            try {
                $appointmentStatus = new AppointmentStatus();
                $createData = [
                    'status_name' => $status_name,
                    'status_description' => $status_description,
                    'status_color' => $status_color
                ];
                $result = $appointmentStatus->create($createData);
                if ($result['success']) {
                    $success = 'Status created successfully';
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
        $status_color = sanitize($_POST['status_color'] ?? '#3B82F6');
        
        if (empty($status_name)) {
            $error = 'Status name is required';
        } else {
            try {
                $appointmentStatus = new AppointmentStatus();
                $updateData = [
                    'status_id' => $id,
                    'status_name' => $status_name,
                    'status_description' => $status_description,
                    'status_color' => $status_color
                ];
                $result = $appointmentStatus->update($updateData);
                if ($result['success']) {
                    $success = 'Status updated successfully';
                } else {
                    $error = $result['message'] ?? 'Database error';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// Fetch all statuses with appointment count
try {
    $statuses = $db->fetchAll("
        SELECT s.*, COUNT(a.appointment_id) as appointment_count
        FROM appointment_statuses s
        LEFT JOIN appointments a ON s.status_id = a.status_id
        GROUP BY s.status_id
        ORDER BY s.status_id ASC
    ");
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
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM appointment_statuses");
    $stats['total'] = $result['count'] ?? 0;
    
    // Total appointments
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM appointments");
    $stats['total_appointments'] = $result['count'] ?? 0;
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/staff/statuses.view.php';
