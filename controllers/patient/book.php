<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Doctor.php';
require_once __DIR__ . '/../../classes/Specialization.php';
require_once __DIR__ . '/../../classes/Schedule.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../config/Database.php';

$auth = new Auth();
$auth->requirePatient();

$error = '';
$doctorModel = new Doctor();
$specializationModel = new Specialization();
$scheduleModel = new Schedule();
$db = Database::getInstance();

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Handle search and filter
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

$filter_specialization = isset($_GET['specialization']) ? (int)$_GET['specialization'] : null;

// Get doctors with all details in a single query (optimized)
$all_doctors = $doctorModel->searchDoctorsWithDetails([
    'search' => $search_query,
    'specialization' => $filter_specialization
]);

// Separate doctors into available (with schedules) and unavailable (without schedules)
$available_doctors = [];
$unavailable_doctors = [];
$doctor_schedules = [];
$processed_doctors = []; // Track processed doctors to avoid duplicates

// Get today's date for checking future schedules
$today = date('Y-m-d');

// OPTIMIZATION: Pre-fetch cancelled/completed status IDs to avoid subquery
$excluded_status_ids = $db->fetchAll(
    "SELECT status_id FROM appointment_statuses WHERE LOWER(status_name) IN ('cancelled', 'completed')"
);
$excluded_status_ids_array = array_column($excluded_status_ids, 'status_id');
$excluded_status_ids_placeholder = !empty($excluded_status_ids_array) 
    ? implode(',', array_map('intval', $excluded_status_ids_array)) 
    : '0'; // Use 0 if no excluded statuses (will never match)

// OPTIMIZATION: Get all doctor IDs first
$doctor_ids = array_column($all_doctors, 'doc_id');
$doctor_ids = array_unique($doctor_ids);

if (!empty($doctor_ids)) {
    // OPTIMIZATION: Fetch all schedules for all doctors in a single query
    $placeholders = implode(',', array_map('intval', $doctor_ids));
    $all_schedules = $db->fetchAll(
        "SELECT * FROM schedules 
         WHERE doc_id IN ($placeholders)
         AND schedule_date >= :today 
         ORDER BY doc_id, schedule_date ASC, start_time ASC",
        ['today' => $today]
    );
    
    // OPTIMIZATION: Fetch all appointment counts in a single query using JOIN
    // Group by schedule to get counts per schedule slot
    $appointment_counts = [];
    if (!empty($all_schedules)) {
        $schedule_ids = array_column($all_schedules, 'schedule_id');
        if (!empty($schedule_ids)) {
            // Get appointment counts grouped by schedule characteristics
            // Use COALESCE to handle NULL counts from LEFT JOIN
            $counts_query = "
                SELECT 
                    s.doc_id,
                    s.schedule_date,
                    s.start_time,
                    s.end_time,
                    COALESCE(COUNT(a.appointment_id), 0) as appointment_count
                FROM schedules s
                LEFT JOIN appointments a ON (
                    a.doc_id = s.doc_id
                    AND a.appointment_date = s.schedule_date
                    AND a.appointment_time >= s.start_time
                    AND a.appointment_time < s.end_time
                    AND a.status_id NOT IN ($excluded_status_ids_placeholder)
                )
                WHERE s.schedule_id IN (" . implode(',', array_map('intval', $schedule_ids)) . ")
                GROUP BY s.doc_id, s.schedule_date, s.start_time, s.end_time
            ";
            
            $count_results = $db->fetchAll($counts_query);
            
            // Build a lookup array for fast access
            foreach ($count_results as $count_result) {
                $key = $count_result['doc_id'] . '|' . $count_result['schedule_date'] . '|' . $count_result['start_time'] . '|' . $count_result['end_time'];
                $appointment_counts[$key] = (int)$count_result['appointment_count'];
            }
        }
    }
    
    // Group schedules by doctor
    $schedules_by_doctor = [];
    foreach ($all_schedules as $schedule) {
        $doc_id = $schedule['doc_id'];
        if (!isset($schedules_by_doctor[$doc_id])) {
            $schedules_by_doctor[$doc_id] = [];
        }
        $schedules_by_doctor[$doc_id][] = $schedule;
    }
    
    // Process each doctor
    foreach ($all_doctors as $doctor) {
        $doc_id = $doctor['doc_id'];
        
        // Skip if we've already processed this doctor
        if (isset($processed_doctors[$doc_id])) {
            continue;
        }
        
        // Mark as processed
        $processed_doctors[$doc_id] = true;
        
        // Get schedules for this doctor (from pre-fetched data)
        $schedules = $schedules_by_doctor[$doc_id] ?? [];
        
        // Check if doctor has any schedules
        $has_available_slots = !empty($schedules);
        
        // Store schedules for this doctor
        $doctor_schedules[$doc_id] = $schedules;
        
        // Categorize doctor - ensure doctor is only in one array
        if ($has_available_slots && !empty($schedules)) {
            $available_doctors[] = $doctor;
        } else {
            $unavailable_doctors[] = $doctor;
        }
    }
} else {
    // No doctors found, set empty arrays
    $available_doctors = [];
    $unavailable_doctors = [];
    $doctor_schedules = [];
}

$specializations = $specializationModel->getAllSpecializations();

require_once __DIR__ . '/../../views/patient/book.view.php';

