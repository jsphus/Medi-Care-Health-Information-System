<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = initializeProfilePicture($auth, $db);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $schedule_date = $_POST['schedule_date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $max_appointments = !empty($_POST['max_appointments']) ? (int)$_POST['max_appointments'] : 10;
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        
        if (empty($schedule_date) || empty($start_time) || empty($end_time)) {
            $error = 'Date, start time, and end time are required';
        } else {
            try {
                $stmt = $db->prepare("
                    INSERT INTO schedules (doc_id, schedule_date, start_time, end_time, max_appointments, is_available, created_at) 
                    VALUES (:doc_id, :schedule_date, :start_time, :end_time, :max_appointments, :is_available, NOW())
                ");
                $stmt->execute([
                    'doc_id' => $doctor_id,
                    'schedule_date' => $schedule_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'max_appointments' => $max_appointments,
                    'is_available' => $is_available
                ]);
                $success = 'Schedule created successfully';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $schedule_date = $_POST['schedule_date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $max_appointments = !empty($_POST['max_appointments']) ? (int)$_POST['max_appointments'] : 10;
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        
        try {
            $stmt = $db->prepare("
                UPDATE schedules 
                SET schedule_date = :schedule_date, start_time = :start_time, end_time = :end_time,
                    max_appointments = :max_appointments, is_available = :is_available, updated_at = NOW()
                WHERE schedule_id = :id AND doc_id = :doc_id
            ");
            $stmt->execute([
                'schedule_date' => $schedule_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'max_appointments' => $max_appointments,
                'is_available' => $is_available,
                'id' => $id,
                'doc_id' => $doctor_id
            ]);
            $success = 'Schedule updated successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        try {
            $stmt = $db->prepare("DELETE FROM schedules WHERE schedule_id = :id AND doc_id = :doc_id");
            $stmt->execute(['id' => $id, 'doc_id' => $doctor_id]);
            $success = 'Schedule deleted successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch all schedules for this doctor
try {
    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'schedule_date';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['schedule_date', 'start_time', 'end_time'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'schedule_date';
    }
    
    // Special handling for date/time sorting
    if ($sort_column === 'schedule_date') {
        $order_by = "schedule_date $sort_order, start_time $sort_order";
    } else {
        $order_by = "$sort_column $sort_order";
    }
    
    $stmt = $db->prepare("
        SELECT * FROM schedules 
        WHERE doc_id = :doctor_id 
        ORDER BY $order_by
    ");
    $stmt->execute(['doctor_id' => $doctor_id]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch schedules: ' . $e->getMessage();
    $schedules = [];
}

// Get today's schedules
try {
    $today = date('Y-m-d');
    $stmt = $db->prepare("
        SELECT * FROM schedules 
        WHERE doc_id = :doctor_id AND schedule_date = :today
        ORDER BY start_time ASC
    ");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $today_schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $today_schedules = [];
}

// Calculate useful statistics for summary cards
$stats = [
    'today_appointments' => 0,
    'available_slots_today' => 0,
    'next_schedule' => null,
    'this_week_schedules' => 0
];

try {
    $today = date('Y-m-d');
    
    // Today's appointments count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments WHERE doc_id = :doctor_id AND appointment_date = :today");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $stats['today_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Available slots today (total max appointments - booked appointments)
    $total_max_slots = 0;
    foreach ($today_schedules as $sched) {
        if ($sched['is_available']) {
            $total_max_slots += (int)$sched['max_appointments'];
        }
    }
    $stats['available_slots_today'] = max(0, $total_max_slots - $stats['today_appointments']);
    
    // Next upcoming schedule
    $stmt = $db->prepare("
        SELECT schedule_date, start_time 
        FROM schedules 
        WHERE doc_id = :doctor_id 
        AND (schedule_date > :today OR (schedule_date = :today AND start_time > TIME(NOW())))
        AND is_available = 1
        ORDER BY schedule_date ASC, start_time ASC 
        LIMIT 1
    ");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $next_schedule = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['next_schedule'] = $next_schedule;
    
    // This week's schedules count (next 7 days)
    $week_end = date('Y-m-d', strtotime('+7 days'));
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM schedules 
        WHERE doc_id = :doctor_id 
        AND schedule_date >= :today 
        AND schedule_date <= :week_end
    ");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today, 'week_end' => $week_end]);
    $stats['this_week_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/doctor/schedules.view.php';
