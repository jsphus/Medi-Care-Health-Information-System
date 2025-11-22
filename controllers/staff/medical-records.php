<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireStaff();

$db = Database::getInstance();
$error = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

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
        $where_conditions[] = "(p.pat_first_name LIKE :search OR p.pat_last_name LIKE :search OR mr.med_rec_diagnosis LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }

    if ($filter_doctor) {
        $where_conditions[] = "a.doc_id = :doctor";
        $params['doctor'] = $filter_doctor;
    }

    if ($filter_patient) {
        $where_conditions[] = "a.pat_id = :patient";
        $params['patient'] = $filter_patient;
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'med_rec_visit_date';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['med_rec_visit_date', 'med_rec_id', 'med_rec_created_at'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'med_rec_visit_date';
    }
    
    $order_by = "mr.$sort_column $sort_order";

    $sql = "
        SELECT mr.*, 
               a.pat_id, a.doc_id, a.appointment_date, a.appointment_time, a.appointment_id,
               a.appointment_notes, a.appointment_duration, a.created_at as appointment_created_at,
               p.pat_first_name, p.pat_last_name, p.pat_middle_initial,
               d.doc_first_name, d.doc_last_name, d.doc_middle_initial,
               s.status_name, s.status_color,
               sv.service_name, sv.service_price,
               up.profile_picture_url as patient_profile_picture,
               ud.profile_picture_url as doctor_profile_picture
        FROM medical_records mr
        JOIN appointments a ON mr.appt_id = a.appointment_id
        JOIN patients p ON a.pat_id = p.pat_id
        JOIN doctors d ON a.doc_id = d.doc_id
        LEFT JOIN appointment_statuses s ON a.status_id = s.status_id
        LEFT JOIN services sv ON a.service_id = sv.service_id
        LEFT JOIN users up ON up.pat_id = p.pat_id
        LEFT JOIN users ud ON ud.doc_id = d.doc_id
        $where_clause
        ORDER BY $order_by
    ";
    $records = $db->fetchAll($sql, $params);
} catch (PDOException $e) {
    $error = 'Failed to fetch medical records: ' . $e->getMessage();
    $records = [];
}

// Fetch filter data from database
$filter_doctors = [];
$filter_patients = [];
try {
    // Get unique doctors from medical records (via appointments)
    $filter_doctors = $db->fetchAll("SELECT DISTINCT d.doc_id, d.doc_first_name, d.doc_last_name FROM medical_records mr JOIN appointments a ON mr.appt_id = a.appointment_id JOIN doctors d ON a.doc_id = d.doc_id ORDER BY d.doc_first_name");

    // Get unique patients from medical records (via appointments)
    $filter_patients = $db->fetchAll("SELECT DISTINCT p.pat_id, p.pat_first_name, p.pat_last_name FROM medical_records mr JOIN appointments a ON mr.appt_id = a.appointment_id JOIN patients p ON a.pat_id = p.pat_id ORDER BY p.pat_first_name");
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
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM medical_records");
    $stats['total'] = $result['count'] ?? 0;
    
    // Records this month
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM medical_records WHERE DATE_TRUNC('month', med_rec_visit_date) = DATE_TRUNC('month', CURRENT_DATE)");
    $stats['this_month'] = $result['count'] ?? 0;
    
    // Pending follow-up removed (field no longer exists)
    $stats['pending_followup'] = 0;
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/staff/medical-records.view.php';
