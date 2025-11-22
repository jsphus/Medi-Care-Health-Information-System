<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $doc_id = (int)$_POST['doc_id'];
        $schedule_date = $_POST['schedule_date'];
        $start_time = sanitize($_POST['start_time']);
        $end_time = sanitize($_POST['end_time']);
        
        if (empty($doc_id) || empty($schedule_date) || empty($start_time) || empty($end_time)) {
            $error = 'All fields are required';
        } elseif ($start_time >= $end_time) {
            $error = 'End time must be after start time';
        } else {
            try {
                // Check for exact duplicate (same doc_id, schedule_date, and start_time)
                $stmt = $db->prepare("
                    SELECT schedule_id FROM schedules 
                    WHERE doc_id = :doc_id 
                    AND schedule_date = :schedule_date 
                    AND start_time = :start_time
                ");
                $stmt->execute([
                    'doc_id' => $doc_id,
                    'schedule_date' => $schedule_date,
                    'start_time' => $start_time
                ]);
                
                if ($stmt->fetch()) {
                    $error = 'A schedule with the same date and start time already exists for this doctor. Please choose a different time.';
                } else {
                    // Check for overlapping schedules
                    $stmt = $db->prepare("
                        SELECT schedule_id FROM schedules 
                        WHERE doc_id = :doc_id 
                        AND schedule_date = :schedule_date 
                        AND (
                            (start_time <= :start_time AND end_time > :start_time) OR
                            (start_time < :end_time AND end_time >= :end_time) OR
                            (start_time >= :start_time AND end_time <= :end_time)
                        )
                    ");
                    $stmt->execute([
                        'doc_id' => $doc_id,
                        'schedule_date' => $schedule_date,
                        'start_time' => $start_time,
                        'end_time' => $end_time
                    ]);
                    
                    if ($stmt->fetch()) {
                        $error = 'This schedule overlaps with an existing schedule for this doctor on this date. Please choose a different time.';
                    } else {
                        $stmt = $db->prepare("
                            INSERT INTO schedules (doc_id, schedule_date, start_time, end_time, created_at) 
                            VALUES (:doc_id, :schedule_date, :start_time, :end_time, NOW())
                        ");
                        $stmt->execute([
                            'doc_id' => $doc_id,
                            'schedule_date' => $schedule_date,
                            'start_time' => $start_time,
                            'end_time' => $end_time,
                        ]);
                        $success = 'Schedule created successfully';
                    }
                }
            } catch (PDOException $e) {
                // Check if it's a unique constraint violation
                if (strpos($e->getMessage(), '23505') !== false || strpos($e->getMessage(), 'duplicate key') !== false) {
                    $error = 'A schedule with the same date and start time already exists for this doctor. Please choose a different time.';
                } else {
                    $error = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $doc_id = (int)$_POST['doc_id'];
        $schedule_date = $_POST['schedule_date'];
        $start_time = sanitize($_POST['start_time']);
        $end_time = sanitize($_POST['end_time']);
        
        if (empty($doc_id) || empty($schedule_date) || empty($start_time) || empty($end_time)) {
            $error = 'All fields are required';
        } elseif ($start_time >= $end_time) {
            $error = 'End time must be after start time';
        } else {
            try {
                // Check for overlapping schedules (excluding current schedule)
                $stmt = $db->prepare("
                    SELECT schedule_id FROM schedules 
                    WHERE doc_id = :doc_id 
                    AND schedule_date = :schedule_date 
                    AND schedule_id != :id
                    AND (
                        (start_time <= :start_time AND end_time > :start_time) OR
                        (start_time < :end_time AND end_time >= :end_time) OR
                        (start_time >= :start_time AND end_time <= :end_time)
                    )
                ");
                $stmt->execute([
                    'doc_id' => $doc_id,
                    'schedule_date' => $schedule_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'id' => $id
                ]);
                
                if ($stmt->fetch()) {
                    $error = 'This schedule overlaps with an existing schedule for this doctor on this date';
                } else {
                    $stmt = $db->prepare("
                        UPDATE schedules 
                        SET doc_id = :doc_id, schedule_date = :schedule_date, start_time = :start_time, 
                            end_time = :end_time, updated_at = NOW()
                        WHERE schedule_id = :id
                    ");
                    $stmt->execute([
                        'id' => $id,
                        'doc_id' => $doc_id,
                        'schedule_date' => $schedule_date,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                    ]);
                    $success = 'Schedule updated successfully';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        try {
            $stmt = $db->prepare("DELETE FROM schedules WHERE schedule_id = :id");
            $stmt->execute(['id' => $id]);
            $success = 'Schedule deleted successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch all schedules with doctor info
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
        $order_by = "s.schedule_date $sort_order, s.start_time $sort_order";
    } else {
        $order_by = "s.$sort_column $sort_order";
    }
    
    $stmt = $db->query("
        SELECT s.*, 
               CONCAT(d.doc_first_name, ' ', COALESCE(d.doc_middle_initial || '. ', ''), d.doc_last_name) as doctor_name,
               sp.spec_name
        FROM schedules s
        JOIN doctors d ON s.doc_id = d.doc_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        ORDER BY $order_by
    ");
    $all_schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch schedules: ' . $e->getMessage();
    $all_schedules = [];
}

// Fetch today's schedules
try {
    $stmt = $db->prepare("
        SELECT s.*, 
               CONCAT(d.doc_first_name, ' ', COALESCE(d.doc_middle_initial || '. ', ''), d.doc_last_name) as doctor_name,
               sp.spec_name
        FROM schedules s
        JOIN doctors d ON s.doc_id = d.doc_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        WHERE s.schedule_date = CURRENT_DATE
        ORDER BY s.start_time
    ");
    $stmt->execute();
    $today_schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $today_schedules = [];
}

// Fetch all doctors for dropdown
try {
    $doctors = $db->query("
        SELECT doc_id, CONCAT(doc_first_name, ' ', COALESCE(doc_middle_initial || '. ', ''), doc_last_name) as doctor_name 
        FROM doctors 
        WHERE doc_status = 'active'
        ORDER BY doc_last_name, doc_first_name
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $doctors = [];
}

// Calculate useful statistics for summary cards
$stats = [
    'total_schedules' => 0,
    'schedules_today' => 0,
    'upcoming_schedules' => 0,
    'doctors_with_schedules' => 0
];

try {
    $today = date('Y-m-d');
    
    // Total schedules
    $stmt = $db->query("SELECT COUNT(*) as count FROM schedules");
    $stats['total_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Schedules today
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM schedules 
        WHERE schedule_date = :today
    ");
    $stmt->execute(['today' => $today]);
    $stats['schedules_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Upcoming schedules (future dates)
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM schedules 
        WHERE schedule_date > :today
    ");
    $stmt->execute(['today' => $today]);
    $stats['upcoming_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Doctors with schedules
    $stmt = $db->query("
        SELECT COUNT(DISTINCT doc_id) as count 
        FROM schedules
    ");
    $stats['doctors_with_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

// Include the view
require_once __DIR__ . '/../../views/doctor/schedules-manage.view.php';
