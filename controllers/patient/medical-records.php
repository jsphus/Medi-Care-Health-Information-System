<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requirePatient();

$db = Database::getInstance();
$patient_id = $auth->getPatientId();
$error = '';

// Handle search
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

// Get all medical records for this patient
try {
    $where_conditions = ['mr.pat_id = :patient_id'];
    $params = ['patient_id' => $patient_id];

    if (!empty($search_query)) {
        $where_conditions[] = "(d.doc_first_name LIKE :search OR d.doc_last_name LIKE :search OR mr.diagnosis LIKE :search OR mr.treatment LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }

    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

    $stmt = $db->prepare("
        SELECT mr.*, 
               d.doc_first_name, d.doc_last_name, d.doc_specialization_id,
               a.appointment_date, a.appointment_id, a.appointment_time,
               sp.spec_name,
               ud.profile_picture_url as doctor_profile_picture
        FROM medical_records mr
        LEFT JOIN doctors d ON mr.doc_id = d.doc_id
        LEFT JOIN appointments a ON mr.appointment_id = a.appointment_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        LEFT JOIN users ud ON ud.doc_id = d.doc_id
        $where_clause
        ORDER BY mr.record_date DESC
    ");
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch medical records: ' . $e->getMessage();
    $records = [];
}

// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'this_month' => 0,
    'pending_followup' => 0
];

try {
    // Total medical records for this patient
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM medical_records WHERE pat_id = :patient_id");
    $stmt->execute(['patient_id' => $patient_id]);
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Records this month
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM medical_records WHERE pat_id = :patient_id AND DATE_TRUNC('month', record_date) = DATE_TRUNC('month', CURRENT_DATE)");
    $stmt->execute(['patient_id' => $patient_id]);
    $stats['this_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending follow-up
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM medical_records WHERE pat_id = :patient_id AND follow_up_date IS NOT NULL AND follow_up_date >= CURRENT_DATE");
    $stmt->execute(['patient_id' => $patient_id]);
    $stats['pending_followup'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/patient/medical-records.view.php';

