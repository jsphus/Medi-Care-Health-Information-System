<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Schedule.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireSuperAdmin();

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
        $schedule_date = sanitize($_POST['schedule_date'] ?? '');
        $start_time = sanitize($_POST['start_time'] ?? '');
        $end_time = sanitize($_POST['end_time'] ?? '');
        
        if (empty($doc_id) || empty($schedule_date) || empty($start_time) || empty($end_time)) {
            $error = 'Doctor, date, start time, and end time are required';
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
    
    if ($action === 'batch_create') {
        $doc_id = (int)$_POST['doc_id'];
        $start_date = sanitize($_POST['start_date'] ?? '');
        $end_date = sanitize($_POST['end_date'] ?? '');
        $days_of_week = isset($_POST['days_of_week']) ? $_POST['days_of_week'] : [];
        $start_time = sanitize($_POST['start_time'] ?? '');
        $end_time = sanitize($_POST['end_time'] ?? '');
        
        if (empty($doc_id) || empty($start_date) || empty($end_date) || empty($start_time) || empty($end_time)) {
            $error = 'Doctor, start date, end date, start time, and end time are required';
        } elseif ($start_time >= $end_time) {
            $error = 'End time must be after start time';
        } elseif (empty($days_of_week)) {
            $error = 'Please select at least one day of the week';
        } elseif ($start_date > $end_date) {
            $error = 'End date must be after or equal to start date';
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
                        
                        // Check for exact duplicate
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
                                'doc_id' => $doc_id,
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
                                    'doc_id' => $doc_id,
                                    'schedule_date' => $schedule_date,
                                    'start_time' => $start_time,
                                    'end_time' => $end_time
                                ]);
                                $created_count++;
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
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $schedule_date = sanitize($_POST['schedule_date'] ?? '');
        $start_time = sanitize($_POST['start_time'] ?? '');
        $end_time = sanitize($_POST['end_time'] ?? '');
        
        if (empty($schedule_date) || empty($start_time) || empty($end_time)) {
            $error = 'Date, start time, and end time are required';
        } else {
            try {
                $schedule = new Schedule();
                $updateData = [
                    'schedule_id' => $id,
                    'schedule_date' => $schedule_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ];
                $result = $schedule->update($updateData);
                if ($result['success']) {
                    $success = 'Schedule updated successfully';
                } else {
                    $error = $result['message'] ?? 'Database error';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        try {
            $schedule = new Schedule();
            $result = $schedule->delete($id);
            if ($result['success']) {
                $success = 'Schedule deleted successfully';
            } else {
                $error = $result['message'] ?? 'Database error';
            }
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

$filter_doctor = isset($_GET['doctor']) ? (int)$_GET['doctor'] : null;
$filter_available = isset($_GET['available']) ? $_GET['available'] : '';

// Pagination - check if we should load all results (for client-side filtering)
$load_all = isset($_GET['all_results']) && $_GET['all_results'] == '1';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = $load_all ? 10000 : 25; // Load all if filtering, otherwise paginate
$offset = $load_all ? 0 : (($page - 1) * $items_per_page);

// Fetch schedules with filters
try {
    $where_conditions = [];
    $params = [];

    if (!empty($search_query)) {
        $where_conditions[] = "(d.doc_first_name LIKE :search OR d.doc_middle_initial LIKE :search OR d.doc_last_name LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }

    if ($filter_doctor) {
        $where_conditions[] = "s.doc_id = :doctor";
        $params['doctor'] = $filter_doctor;
    }


    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Handle sorting - default to showing newest schedules first by creation date
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'created_at';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['schedule_date', 'start_time', 'end_time', 'created_at'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'created_at';
    }
    
    // Special handling for date/time sorting
    if ($sort_column === 'schedule_date') {
        $order_by = "s.schedule_date $sort_order, s.start_time $sort_order";
    } elseif ($sort_column === 'created_at') {
        $order_by = "COALESCE(s.created_at, '1970-01-01'::timestamp) $sort_order, s.schedule_id DESC";
    } else {
        $order_by = "s.$sort_column $sort_order";
    }

    // Get total count for pagination
    $count_stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM schedules s
        LEFT JOIN doctors d ON s.doc_id = d.doc_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        $where_clause
    ");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

    $stmt = $db->prepare("
        SELECT s.*, 
               d.doc_first_name, d.doc_last_name,
               sp.spec_name
        FROM schedules s
        LEFT JOIN doctors d ON s.doc_id = d.doc_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
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

// Fetch filter data from database
$filter_doctors = [];
try {
    // Get unique doctors from schedules
    $filter_doctors = $db->fetchAll("SELECT DISTINCT d.doc_id, d.doc_first_name, d.doc_last_name FROM schedules s JOIN doctors d ON s.doc_id = d.doc_id ORDER BY d.doc_first_name");
} catch (PDOException $e) {
    $filter_doctors = [];
}

// Get today's schedules
try {
    $today = date('Y-m-d');
    $today_schedules = $db->fetchAll("
        SELECT s.*, 
               d.doc_first_name, d.doc_last_name,
               sp.spec_name,
               u.profile_picture_url
        FROM schedules s
        LEFT JOIN doctors d ON s.doc_id = d.doc_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        LEFT JOIN users u ON d.doc_id = u.doc_id
        WHERE s.schedule_date = :today
        ORDER BY s.start_time ASC
    ", ['today' => $today]);
} catch (PDOException $e) {
    $today_schedules = [];
}

// Get tomorrow's schedules
try {
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $tomorrow_schedules = $db->fetchAll("
        SELECT s.*, 
               d.doc_first_name, d.doc_last_name,
               sp.spec_name,
               u.profile_picture_url
        FROM schedules s
        LEFT JOIN doctors d ON s.doc_id = d.doc_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        LEFT JOIN users u ON d.doc_id = u.doc_id
        WHERE s.schedule_date = :tomorrow
        ORDER BY s.start_time ASC
    ", ['tomorrow' => $tomorrow]);
} catch (PDOException $e) {
    $tomorrow_schedules = [];
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

require_once __DIR__ . '/../../views/superadmin/schedules.view.php';
