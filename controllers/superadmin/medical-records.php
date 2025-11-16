<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requireSuperAdmin();

$db = Database::getInstance();
$error = '';
$success = '';

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];
    try {
        $stmt = $db->prepare("DELETE FROM medical_records WHERE record_id = :id");
        $stmt->execute(['id' => $id]);
        $success = 'Medical record deleted successfully';
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Handle search and filters
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

$filter_doctor = isset($_GET['doctor']) ? (int)$_GET['doctor'] : null;
$filter_patient = isset($_GET['patient']) ? (int)$_GET['patient'] : null;

// Fetch medical records with filters
try {
    $where_conditions = [];
    $params = [];

    if (!empty($search_query)) {
        $where_conditions[] = "(p.pat_first_name LIKE :search OR p.pat_last_name LIKE :search OR mr.diagnosis LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }

    if ($filter_doctor) {
        $where_conditions[] = "mr.doc_id = :doctor";
        $params['doctor'] = $filter_doctor;
    }

    if ($filter_patient) {
        $where_conditions[] = "mr.pat_id = :patient";
        $params['patient'] = $filter_patient;
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'record_date';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['record_date', 'record_id', 'follow_up_date'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'record_date';
    }
    
    $order_by = "mr.$sort_column $sort_order";

    $stmt = $db->prepare("
        SELECT mr.*, 
               p.pat_first_name, p.pat_last_name,
               d.doc_first_name, d.doc_last_name,
               a.appointment_date,
               up.profile_picture_url as patient_profile_picture,
               ud.profile_picture_url as doctor_profile_picture
        FROM medical_records mr
        LEFT JOIN patients p ON mr.pat_id = p.pat_id
        LEFT JOIN doctors d ON mr.doc_id = d.doc_id
        LEFT JOIN appointments a ON mr.appointment_id = a.appointment_id
        LEFT JOIN users up ON up.pat_id = p.pat_id
        LEFT JOIN users ud ON ud.doc_id = d.doc_id
        $where_clause
        ORDER BY $order_by
    ");
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch medical records: ' . $e->getMessage();
    $records = [];
}

// Fetch filter data from database
$filter_doctors = [];
$filter_patients = [];
try {
    // Get unique doctors from medical records
    $stmt = $db->query("SELECT DISTINCT d.doc_id, d.doc_first_name, d.doc_last_name FROM medical_records mr JOIN doctors d ON mr.doc_id = d.doc_id ORDER BY d.doc_first_name");
    $filter_doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get unique patients from medical records
    $stmt = $db->query("SELECT DISTINCT p.pat_id, p.pat_first_name, p.pat_last_name FROM medical_records mr JOIN patients p ON mr.pat_id = p.pat_id ORDER BY p.pat_first_name");
    $filter_patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $filter_doctors = [];
    $filter_patients = [];
}

// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'this_month' => 0,
    'pending_followup' => 0
];

try {
    // Total medical records
    $stmt = $db->query("SELECT COUNT(*) as count FROM medical_records");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Records this month
    $stmt = $db->query("SELECT COUNT(*) as count FROM medical_records WHERE DATE_TRUNC('month', record_date) = DATE_TRUNC('month', CURRENT_DATE)");
    $stats['this_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending follow-up (records with follow-up date in the future)
    $stmt = $db->query("SELECT COUNT(*) as count FROM medical_records WHERE follow_up_date IS NOT NULL AND follow_up_date >= CURRENT_DATE");
    $stats['pending_followup'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/superadmin/medical-records.view.php';
