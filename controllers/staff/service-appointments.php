<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';

$auth = new Auth();
$auth->requireStaff();

$db = Database::getInstance();
$error = '';

// Get service ID from query string
$service_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($service_id === 0) {
    header('Location: /staff/services');
    exit;
}

// Fetch service details
try {
    $stmt = $db->prepare("SELECT * FROM services WHERE service_id = :service_id");
    $stmt->execute(['service_id' => $service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service) {
        $error = 'Service not found';
        $appointments = [];
    }
} catch (PDOException $e) {
    $error = 'Failed to fetch service: ' . $e->getMessage();
    $service = null;
    $appointments = [];
}

// Fetch appointments for this service
if ($service) {
    try {
        // Handle sorting
        $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'appointment_date';
        $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
        
        // Validate sort column to prevent SQL injection
        $allowed_columns = ['appointment_date', 'appointment_time', 'appointment_id'];
        if (!in_array($sort_column, $allowed_columns)) {
            $sort_column = 'appointment_date';
        }
        
        // Special handling for date/time sorting
        if ($sort_column === 'appointment_date') {
            $order_by = "a.appointment_date $sort_order, a.appointment_time $sort_order";
        } else {
            $order_by = "a.$sort_column $sort_order";
        }
        
        $stmt = $db->prepare("
            SELECT a.*, 
                   p.pat_first_name, p.pat_last_name, p.pat_phone,
                   d.doc_first_name, d.doc_last_name,
                   st.status_name, st.status_color
            FROM appointments a
            LEFT JOIN patients p ON a.pat_id = p.pat_id
            LEFT JOIN doctors d ON a.doc_id = d.doc_id
            LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
            WHERE a.service_id = :service_id
            ORDER BY $order_by
        ");
        $stmt->execute(['service_id' => $service_id]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Failed to fetch appointments: ' . $e->getMessage();
        $appointments = [];
    }
}

require_once __DIR__ . '/../../views/staff/service-appointments.view.php';
