<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requirePatient();

$db = Database::getInstance();
$patient_id = $auth->getPatientId();
$error = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = initializeProfilePicture($auth, $db);

// For now, we'll show appointment-based notifications
// In a full implementation, you'd have a notifications table
try {
    // Get upcoming appointments as notifications
    $today = date('Y-m-d');
    $stmt = $db->prepare("
        SELECT a.*, 
               d.doc_first_name, d.doc_last_name,
               st.status_name,
               sp.spec_name
        FROM appointments a
        LEFT JOIN doctors d ON a.doc_id = d.doc_id
        LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        WHERE a.pat_id = :patient_id AND a.appointment_date >= :today
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
    ");
    $stmt->execute(['patient_id' => $patient_id, 'today' => $today]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch notifications: ' . $e->getMessage();
    $notifications = [];
}

require_once __DIR__ . '/../../views/patient/notifications.view.php';

