<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $pat_id = (int)$_POST['pat_id'];
        $appointment_id = sanitize($_POST['appointment_id'] ?? '');
        $record_date = $_POST['record_date'];
        $diagnosis = sanitize($_POST['diagnosis']);
        $treatment = sanitize($_POST['treatment']);
        $prescription = sanitize($_POST['prescription'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');
        $follow_up_date = !empty($_POST['follow_up_date']) ? $_POST['follow_up_date'] : null;
        
        if (empty($pat_id) || empty($record_date) || empty($diagnosis)) {
            $error = 'Patient, date, and diagnosis are required';
        } else {
            try {
                $stmt = $db->prepare("
                    INSERT INTO medical_records (pat_id, doc_id, appointment_id, record_date, diagnosis, 
                                                 treatment, prescription, notes, follow_up_date, created_at) 
                    VALUES (:pat_id, :doc_id, :appointment_id, :record_date, :diagnosis, 
                           :treatment, :prescription, :notes, :follow_up_date, NOW())
                ");
                $stmt->execute([
                    'pat_id' => $pat_id,
                    'doc_id' => $doctor_id,
                    'appointment_id' => $appointment_id ?: null,
                    'record_date' => $record_date,
                    'diagnosis' => $diagnosis,
                    'treatment' => $treatment,
                    'prescription' => $prescription,
                    'notes' => $notes,
                    'follow_up_date' => $follow_up_date ?: null
                ]);
                $success = 'Medical record created successfully';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $diagnosis = sanitize($_POST['diagnosis']);
        $treatment = sanitize($_POST['treatment']);
        $prescription = sanitize($_POST['prescription'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');
        $follow_up_date = !empty($_POST['follow_up_date']) ? $_POST['follow_up_date'] : null;
        
        try {
            // Verify this record belongs to this doctor
            $stmt = $db->prepare("
                UPDATE medical_records 
                SET diagnosis = :diagnosis, treatment = :treatment, prescription = :prescription,
                    notes = :notes, follow_up_date = :follow_up_date, updated_at = NOW()
                WHERE record_id = :id AND doc_id = :doc_id
            ");
            $stmt->execute([
                'diagnosis' => $diagnosis,
                'treatment' => $treatment,
                'prescription' => $prescription,
                'notes' => $notes,
                'follow_up_date' => $follow_up_date ?: null,
                'id' => $id,
                'doc_id' => $doctor_id
            ]);
            $success = 'Medical record updated successfully';
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

$filter_patient = isset($_GET['patient']) ? (int)$_GET['patient'] : null;

// Fetch medical records with filters
try {
    $where_conditions = ['mr.doc_id = :doctor_id'];
    $params = ['doctor_id' => $doctor_id];

    if (!empty($search_query)) {
        $where_conditions[] = "(p.pat_first_name LIKE :search OR p.pat_last_name LIKE :search OR mr.diagnosis LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }

    if ($filter_patient) {
        $where_conditions[] = "mr.pat_id = :patient";
        $params['patient'] = $filter_patient;
    }

    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

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
               a.appointment_date,
               up.profile_picture_url as patient_profile_picture
        FROM medical_records mr
        LEFT JOIN patients p ON mr.pat_id = p.pat_id
        LEFT JOIN appointments a ON mr.appointment_id = a.appointment_id
        LEFT JOIN users up ON up.pat_id = p.pat_id
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
$filter_patients = [];
try {
    // Get unique patients from this doctor's medical records
    $stmt = $db->prepare("SELECT DISTINCT p.pat_id, p.pat_first_name, p.pat_last_name FROM medical_records mr JOIN patients p ON mr.pat_id = p.pat_id WHERE mr.doc_id = :doctor_id ORDER BY p.pat_first_name");
    $stmt->execute(['doctor_id' => $doctor_id]);
    $filter_patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $filter_patients = [];
}

// Fetch patients for dropdown (all patients, not just those with appointments)
try {
    $stmt = $db->query("
        SELECT pat_id, pat_first_name, pat_last_name
        FROM patients
        ORDER BY pat_first_name, pat_last_name
    ");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $patients = [];
}

// Calculate useful statistics for summary cards
$stats = [
    'records_this_month' => 0,
    'pending_followups' => 0,
    'unique_patients' => 0,
    'records_today' => 0
];

try {
    $today = date('Y-m-d');
    $month_start = date('Y-m-01');
    
    // Records this month
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM medical_records 
        WHERE doc_id = :doctor_id 
        AND record_date >= :month_start
    ");
    $stmt->execute(['doctor_id' => $doctor_id, 'month_start' => $month_start]);
    $stats['records_this_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending follow-ups (upcoming follow-up dates)
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM medical_records 
        WHERE doc_id = :doctor_id 
        AND follow_up_date IS NOT NULL 
        AND follow_up_date >= :today
    ");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $stats['pending_followups'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Unique patients with records
    $stmt = $db->prepare("
        SELECT COUNT(DISTINCT pat_id) as count 
        FROM medical_records 
        WHERE doc_id = :doctor_id
    ");
    $stmt->execute(['doctor_id' => $doctor_id]);
    $stats['unique_patients'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Records created today
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM medical_records 
        WHERE doc_id = :doctor_id 
        AND DATE(created_at) = :today
    ");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $stats['records_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/doctor/medical-records.view.php';
