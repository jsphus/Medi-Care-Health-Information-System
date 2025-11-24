<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $schedule_date = $_POST['schedule_date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        
        if (empty($schedule_date) || empty($start_time) || empty($end_time)) {
            $error = 'Date, start time, and end time are required';
        } elseif ($start_time >= $end_time) {
            $error = 'End time must be after start time';
        } else {
            // Validate that schedule date/time is not in the past
            $scheduleDateTime = strtotime($schedule_date . ' ' . $start_time);
            $currentDateTime = time();
            
            if ($scheduleDateTime < ($currentDateTime - 300)) {
                $error = 'Cannot create schedules in the past. Please select a future date and time.';
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
                        'doc_id' => $doctor_id,
                        'schedule_date' => $schedule_date,
                        'start_time' => $start_time
                    ]);
                    
                    if ($stmt->fetch()) {
                        $error = 'A schedule with the same date and start time already exists. Please choose a different time.';
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
                            'doc_id' => $doctor_id,
                            'schedule_date' => $schedule_date,
                            'start_time' => $start_time,
                            'end_time' => $end_time
                        ]);
                        
                        if ($stmt->fetch()) {
                            $error = 'This schedule overlaps with an existing schedule for this date. Please choose a different time.';
                        } else {
                            $stmt = $db->prepare("
                                INSERT INTO schedules (doc_id, schedule_date, start_time, end_time, created_at) 
                                VALUES (:doc_id, :schedule_date, :start_time, :end_time, NOW())
                            ");
                            $stmt->execute([
                                'doc_id' => $doctor_id,
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
                        $error = 'A schedule with the same date and start time already exists. Please choose a different time.';
                    } else {
                        $error = 'Database error: ' . $e->getMessage();
                    }
                }
            }
        }
    }
    
    if ($action === 'batch_create') {
        $start_date = sanitize($_POST['start_date'] ?? '');
        $end_date = sanitize($_POST['end_date'] ?? '');
        $days_of_week = isset($_POST['days_of_week']) ? $_POST['days_of_week'] : [];
        $start_time = sanitize($_POST['start_time'] ?? '');
        $end_time = sanitize($_POST['end_time'] ?? '');
        
        if (empty($start_date) || empty($end_date) || empty($start_time) || empty($end_time)) {
            $error = 'Start date, end date, start time, and end time are required';
        } elseif ($start_time >= $end_time) {
            $error = 'End time must be after start time';
        } elseif (empty($days_of_week)) {
            $error = 'Please select at least one day of the week';
        } elseif ($start_date > $end_date) {
            $error = 'End date must be after or equal to start date';
        } else {
            // Validate that start date/time is not in the past
            $startDateTime = strtotime($start_date . ' ' . $start_time);
            $currentDateTime = time();
            
            if ($startDateTime < ($currentDateTime - 300)) {
                $error = 'Cannot create schedules in the past. Please select a future start date and time.';
            } else {
                try {
                    $created_count = 0;
                    $skipped_count = 0;
                    $errors = [];
                    
                    // Convert start_date and end_date to DateTime objects
                    $start = new DateTime($start_date);
                    $end = new DateTime($end_date);
                    $end->modify('+1 day'); // Include end date
                    
                    // Map day names to numbers (0 = Sunday, 1 = Monday, etc.)
                    $day_map = [
                        'sunday' => 0,
                        'monday' => 1,
                        'tuesday' => 2,
                        'wednesday' => 3,
                        'thursday' => 4,
                        'friday' => 5,
                        'saturday' => 6
                    ];
                    
                    $selected_days = [];
                    foreach ($days_of_week as $day) {
                        if (isset($day_map[strtolower($day)])) {
                            $selected_days[] = $day_map[strtolower($day)];
                        }
                    }
                    
                    // Iterate through each day in the range
                    $current = clone $start;
                    while ($current < $end) {
                        $day_of_week = (int)$current->format('w'); // 0 = Sunday, 6 = Saturday
                        
                        // Check if this day is in the selected days
                        if (in_array($day_of_week, $selected_days)) {
                            $schedule_date = $current->format('Y-m-d');
                            
                            // Validate that schedule date/time is not in the past
                            $scheduleDateTime = strtotime($schedule_date . ' ' . $start_time);
                            $currentDateTime = time();
                            
                            if ($scheduleDateTime < ($currentDateTime - 300)) {
                                $skipped_count++;
                            } else {
                                // Check for exact duplicate
                                $stmt = $db->prepare("
                                    SELECT schedule_id FROM schedules 
                                    WHERE doc_id = :doc_id 
                                    AND schedule_date = :schedule_date 
                                    AND start_time = :start_time
                                ");
                                $stmt->execute([
                                    'doc_id' => $doctor_id,
                                    'schedule_date' => $schedule_date,
                                    'start_time' => $start_time
                                ]);
                                
                                if ($stmt->fetch()) {
                                    $skipped_count++;
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
                                        'doc_id' => $doctor_id,
                                        'schedule_date' => $schedule_date,
                                        'start_time' => $start_time,
                                        'end_time' => $end_time
                                    ]);
                                    
                                    if ($stmt->fetch()) {
                                        $skipped_count++;
                                    } else {
                                        // Create the schedule
                                        $stmt = $db->prepare("
                                            INSERT INTO schedules (doc_id, schedule_date, start_time, end_time, created_at) 
                                            VALUES (:doc_id, :schedule_date, :start_time, :end_time, NOW())
                                        ");
                                        $stmt->execute([
                                            'doc_id' => $doctor_id,
                                            'schedule_date' => $schedule_date,
                                            'start_time' => $start_time,
                                            'end_time' => $end_time
                                        ]);
                                        $created_count++;
                                    }
                                }
                            }
                        }
                        
                        $current->modify('+1 day');
                    }
                    
                    if ($created_count > 0) {
                        $success = "Successfully created $created_count schedule(s)";
                        if ($skipped_count > 0) {
                            $success .= ". $skipped_count schedule(s) were skipped due to conflicts.";
                        }
                    } else {
                        $error = "No schedules were created. All dates either already have schedules or overlap with existing ones.";
                    }
                } catch (PDOException $e) {
                    $error = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $schedule_date = $_POST['schedule_date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        
        if (empty($schedule_date) || empty($start_time) || empty($end_time)) {
            $error = 'Date, start time, and end time are required';
        } elseif ($start_time >= $end_time) {
            $error = 'End time must be after start time';
        } else {
            // Validate that schedule date/time is not in the past
            $scheduleDateTime = strtotime($schedule_date . ' ' . $start_time);
            $currentDateTime = time();
            
            if ($scheduleDateTime < ($currentDateTime - 300)) {
                $error = 'Cannot update schedules to a past date and time. Please select a future date and time.';
            } else {
                try {
                    // Check for exact duplicate (excluding current schedule)
                    $stmt = $db->prepare("
                        SELECT schedule_id FROM schedules 
                        WHERE doc_id = :doc_id 
                        AND schedule_date = :schedule_date 
                        AND start_time = :start_time
                        AND schedule_id != :id
                    ");
                    $stmt->execute([
                        'doc_id' => $doctor_id,
                        'schedule_date' => $schedule_date,
                        'start_time' => $start_time,
                        'id' => $id
                    ]);
                    
                    if ($stmt->fetch()) {
                        $error = 'A schedule with the same date and start time already exists. Please choose a different time.';
                    } else {
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
                            'doc_id' => $doctor_id,
                            'schedule_date' => $schedule_date,
                            'start_time' => $start_time,
                            'end_time' => $end_time,
                            'id' => $id
                        ]);
                        
                        if ($stmt->fetch()) {
                            $error = 'This schedule overlaps with an existing schedule for this date. Please choose a different time.';
                        } else {
                            $stmt = $db->prepare("
                                UPDATE schedules 
                                SET schedule_date = :schedule_date, start_time = :start_time, end_time = :end_time, updated_at = NOW()
                                WHERE schedule_id = :id AND doc_id = :doc_id
                            ");
                            $stmt->execute([
                                'schedule_date' => $schedule_date,
                                'start_time' => $start_time,
                                'end_time' => $end_time,
                                'id' => $id,
                                'doc_id' => $doctor_id
                            ]);
                            $success = 'Schedule updated successfully';
                        }
                    }
                } catch (PDOException $e) {
                    // Check if it's a unique constraint violation
                    if (strpos($e->getMessage(), '23505') !== false || strpos($e->getMessage(), 'duplicate key') !== false) {
                        $error = 'A schedule with the same date and start time already exists. Please choose a different time.';
                    } else {
                        $error = 'Database error: ' . $e->getMessage();
                    }
                }
            }
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

// Handle filters from URL parameters
$filter_date_from = isset($_GET['filter_date_from']) ? sanitize($_GET['filter_date_from']) : '';
$filter_date_to = isset($_GET['filter_date_to']) ? sanitize($_GET['filter_date_to']) : '';
$filter_start_time = isset($_GET['filter_start_time']) ? sanitize($_GET['filter_start_time']) : '';
$filter_end_time = isset($_GET['filter_end_time']) ? sanitize($_GET['filter_end_time']) : '';

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = 25;
$offset = ($page - 1) * $items_per_page;

// Handle sorting
$sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'schedule_date';
$sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
$allowed_columns = ['schedule_date', 'start_time', 'end_time', 'created_at', 'updated_at'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'schedule_date';
}

// Special handling for date/time sorting
if ($sort_column === 'schedule_date') {
    $order_by = "schedule_date $sort_order, start_time $sort_order";
} else {
    $order_by = "$sort_column $sort_order";
}

// Fetch schedules with filters
try {
    $where_conditions = ['doc_id = :doctor_id'];
    $params = ['doctor_id' => $doctor_id];

    if (!empty($filter_date_from)) {
        $where_conditions[] = "DATE(schedule_date) >= :filter_date_from";
        $params['filter_date_from'] = $filter_date_from;
    }

    if (!empty($filter_date_to)) {
        $where_conditions[] = "DATE(schedule_date) <= :filter_date_to";
        $params['filter_date_to'] = $filter_date_to;
    }

    if (!empty($filter_start_time)) {
        $where_conditions[] = "start_time >= :filter_start_time";
        $params['filter_start_time'] = $filter_start_time;
    }

    if (!empty($filter_end_time)) {
        $where_conditions[] = "end_time <= :filter_end_time";
        $params['filter_end_time'] = $filter_end_time;
    }

    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

    // Get total count for pagination
    $count_stmt = $db->prepare("SELECT COUNT(*) FROM schedules $where_clause");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

    // Fetch paginated results
    $stmt = $db->prepare("
        SELECT * FROM schedules 
        $where_clause
        ORDER BY $order_by
        LIMIT :limit OFFSET :offset
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch schedules: ' . $e->getMessage();
    $schedules = [];
    $total_items = 0;
    $total_pages = 0;
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
    'schedules_today' => [],
    'this_week_schedules' => 0,
    'next_month_schedules' => 0,
    'past_schedules' => 0
];

try {
    $today = date('Y-m-d');
    $week_end = date('Y-m-d', strtotime('+7 days'));
    $next_month_end = date('Y-m-d', strtotime('+1 month'));
    
    // Schedules today with start and end times
    $stmt = $db->prepare("
        SELECT start_time, end_time 
        FROM schedules 
        WHERE doc_id = :doctor_id 
        AND schedule_date = :today
        ORDER BY start_time ASC
    ");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $stats['schedules_today'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // This week's schedules count (next 7 days, excluding today)
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM schedules 
        WHERE doc_id = :doctor_id 
        AND schedule_date > :today 
        AND schedule_date <= :week_end
    ");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today, 'week_end' => $week_end]);
    $stats['this_week_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Next month's schedules count (excluding this week)
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM schedules 
        WHERE doc_id = :doctor_id 
        AND schedule_date > :week_end 
        AND schedule_date <= :next_month_end
    ");
    $stmt->execute(['doctor_id' => $doctor_id, 'week_end' => $week_end, 'next_month_end' => $next_month_end]);
    $stats['next_month_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Past schedules count
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM schedules 
        WHERE doc_id = :doctor_id 
        AND schedule_date < :today
    ");
    $stmt->execute(['doctor_id' => $doctor_id, 'today' => $today]);
    $stats['past_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/doctor/schedules.view.php';
