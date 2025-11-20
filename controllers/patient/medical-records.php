<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/MedicalRecord.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$patient_id = $auth->getPatientId();
$error = '';
$medicalRecordModel = new MedicalRecord();

// Handle search
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

// Get all medical records for this patient
$records = $medicalRecordModel->searchByPatient($patient_id, $search_query);

// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'this_month' => 0,
    'pending_followup' => 0
];

$stats = $medicalRecordModel->getStatsForPatient($patient_id);

require_once __DIR__ . '/../../views/patient/medical-records.view.php';

