<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doc_id = $_SESSION['doc_id'];

// Get doctor info
try {
    $stmt = $db->prepare("
        SELECT d.*, s.spec_name 
        FROM doctors d
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
        WHERE d.doc_id = :doc_id
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get profile picture URL
    $user_id = $auth->getUserId();
    $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_picture_url = $user['profile_picture_url'] ?? null;
} catch (PDOException $e) {
    $doctor = null;
    $profile_picture_url = null;
}

// Get statistics
$stats = [
    'total_appointments' => 0,
    'today_appointments' => 0,
    'upcoming_appointments' => 0,
    'completed_appointments' => 0,
    'total_patients' => 0,
    'my_schedules' => 0,
    'all_schedules' => 0,
    'pending_lab_results' => 0,
    'active_doctors' => 0,
    'today_revenue' => 0,
    'admitted_patients' => 0
];

try {
    // Total appointments for this doctor
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments WHERE doc_id = :doc_id");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['total_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Today's appointments
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM appointments 
        WHERE doc_id = :doc_id AND appointment_date = CURRENT_DATE
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['today_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Upcoming appointments
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM appointments 
        WHERE doc_id = :doc_id AND appointment_date > CURRENT_DATE
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['upcoming_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Completed appointments
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM appointments a
        JOIN appointment_statuses s ON a.status_id = s.status_id
        WHERE a.doc_id = :doc_id AND s.status_name = 'Completed'
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['completed_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total unique patients
    $stmt = $db->prepare("
        SELECT COUNT(DISTINCT pat_id) as count 
        FROM appointments 
        WHERE doc_id = :doc_id
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['total_patients'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // My schedules count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM schedules WHERE doc_id = :doc_id");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['my_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // All schedules count
    $stmt = $db->query("SELECT COUNT(*) as count FROM schedules");
    $stats['all_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending lab results (medical records that need follow-up or are pending)
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM medical_records 
        WHERE doc_id = :doc_id 
        AND (follow_up_date IS NOT NULL AND follow_up_date >= CURRENT_DATE)
        AND diagnosis IS NOT NULL
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['pending_lab_results'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Active doctors count (total active doctors in system)
    $stmt = $db->query("SELECT COUNT(*) as count FROM doctors WHERE doc_status = 'active'");
    $stats['active_doctors'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
} catch (PDOException $e) {
    // Keep default values
}

// Get today's revenue (from payments for today's appointments)
try {
    $stmt = $db->prepare("
        SELECT COALESCE(SUM(p.payment_amount), 0) as total_revenue
        FROM payments p
        JOIN appointments a ON p.appointment_id = a.appointment_id
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE a.doc_id = :doc_id 
        AND a.appointment_date = CURRENT_DATE
        AND LOWER(ps.status_name) = 'paid'
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['today_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;
} catch (PDOException $e) {
    $stats['today_revenue'] = 0;
}

// Get admitted patients (patients with active medical records requiring follow-up or in treatment)
try {
    $stmt = $db->prepare("
        SELECT COUNT(DISTINCT mr.pat_id) as count
        FROM medical_records mr
        WHERE mr.doc_id = :doc_id
        AND (mr.follow_up_date IS NOT NULL AND mr.follow_up_date >= CURRENT_DATE)
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $stats['admitted_patients'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
} catch (PDOException $e) {
    $stats['admitted_patients'] = 0;
}

// Get recent appointments (today and upcoming) with patient and doctor details
try {
    $stmt = $db->prepare("
        SELECT a.*, 
               p.pat_first_name, p.pat_last_name, p.pat_date_of_birth, p.pat_email, p.pat_phone,
               d.doc_first_name, d.doc_last_name,
               s.status_name, s.status_color,
               sv.service_name
        FROM appointments a
        JOIN patients p ON a.pat_id = p.pat_id
        JOIN doctors d ON a.doc_id = d.doc_id
        JOIN appointment_statuses s ON a.status_id = s.status_id
        LEFT JOIN services sv ON a.service_id = sv.service_id
        WHERE a.doc_id = :doc_id 
        AND (a.appointment_date = CURRENT_DATE OR a.appointment_date > CURRENT_DATE)
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
        LIMIT 6
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $recent_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $recent_appointments = [];
}

// Get today's appointments with patient details
try {
    $stmt = $db->prepare("
        SELECT a.*, 
               p.pat_first_name, p.pat_last_name, p.pat_date_of_birth, p.pat_email, p.pat_phone,
               s.status_name, s.status_color
        FROM appointments a
        JOIN patients p ON a.pat_id = p.pat_id
        JOIN appointment_statuses s ON a.status_id = s.status_id
        WHERE a.doc_id = :doc_id AND a.appointment_date = CURRENT_DATE
        ORDER BY a.appointment_time ASC
        LIMIT 10
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $today_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $today_appointments = [];
}

// Get today's schedule
try {
    $stmt = $db->prepare("
        SELECT * FROM schedules 
        WHERE doc_id = :doc_id AND schedule_date = CURRENT_DATE
        ORDER BY start_time ASC
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $today_schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $today_schedule = [];
}

// Get upcoming appointments
try {
    $stmt = $db->prepare("
        SELECT a.*, 
               p.pat_first_name, p.pat_last_name, p.pat_date_of_birth,
               s.status_name, s.status_color
        FROM appointments a
        JOIN patients p ON a.pat_id = p.pat_id
        JOIN appointment_statuses s ON a.status_id = s.status_id
        WHERE a.doc_id = :doc_id AND a.appointment_date > CURRENT_DATE
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
        LIMIT 10
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $upcoming_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $upcoming_appointments = [];
}

// Get patient list table data (recent appointments with date, patient name, age, reason, type, report)
try {
    $stmt = $db->prepare("
        SELECT a.*, 
               p.pat_first_name, p.pat_last_name, p.pat_date_of_birth,
               s.status_name, s.status_color,
               sv.service_name,
               CASE 
                   WHEN EXISTS (
                       SELECT 1 FROM appointments a2 
                       WHERE a2.pat_id = a.pat_id 
                       AND a2.doc_id = a.doc_id 
                       AND a2.appointment_date < a.appointment_date
                   ) THEN 'Follow up'
                   ELSE 'First visit'
               END as appointment_type,
               CASE 
                   WHEN EXISTS (
                       SELECT 1 FROM medical_records mr 
                       WHERE mr.appointment_id = a.appointment_id
                   ) THEN 'Yes'
                   ELSE 'No'
               END as has_report
        FROM appointments a
        JOIN patients p ON a.pat_id = p.pat_id
        JOIN appointment_statuses s ON a.status_id = s.status_id
        LEFT JOIN services sv ON a.service_id = sv.service_id
        WHERE a.doc_id = :doc_id
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
        LIMIT 10
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $patient_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $patient_list = [];
}


// Get appointment type distribution for donut chart
try {
    $stmt = $db->prepare("
        WITH appointment_types AS (
            SELECT 
                a.appointment_id,
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM appointments a2 
                        WHERE a2.pat_id = a.pat_id 
                        AND a2.doc_id = a.doc_id 
                        AND a2.appointment_date < a.appointment_date
                    ) THEN 'Follow up'
                    ELSE 'First visit'
                END as appointment_type
            FROM appointments a
            WHERE a.doc_id = :doc_id
            AND a.appointment_date >= CURRENT_DATE - INTERVAL '30 days'
        )
        SELECT appointment_type, COUNT(*) as count
        FROM appointment_types
        GROUP BY appointment_type
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $appointment_type_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $appointment_type_chart = [
        'First visit' => 0,
        'Follow up' => 0,
        'Emergency' => 0
    ];
    foreach ($appointment_type_data as $row) {
        $type = $row['appointment_type'];
        if (isset($appointment_type_chart[$type])) {
            $appointment_type_chart[$type] = (int)$row['count'];
        }
    }
} catch (PDOException $e) {
    $appointment_type_chart = [
        'First visit' => 0,
        'Follow up' => 0,
        'Emergency' => 0
    ];
}

// Get monthly/weekly patients visit data for line graph (last 4 weeks)
try {
    $weekly_visits = [];
    for ($i = 3; $i >= 0; $i--) {
        $week_start = date('Y-m-d', strtotime("-$i weeks monday"));
        $week_end = date('Y-m-d', strtotime("$week_start +6 days"));
        
        $stmt = $db->prepare("
            SELECT COUNT(*) as count
            FROM appointments
            WHERE doc_id = :doc_id
            AND appointment_date >= :week_start
            AND appointment_date <= :week_end
        ");
        $stmt->execute([
            'doc_id' => $doc_id,
            'week_start' => $week_start,
            'week_end' => $week_end
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $weekly_visits[] = (int)($result['count'] ?? 0);
    }
} catch (PDOException $e) {
    $weekly_visits = [0, 0, 0, 0];
}

// Get new appointments (upcoming appointments for the calendar widget)
try {
    $stmt = $db->prepare("
        SELECT a.*, 
               p.pat_first_name, p.pat_last_name, p.pat_date_of_birth,
               s.status_name, s.status_color,
               sv.service_name,
               CASE 
                   WHEN EXISTS (
                       SELECT 1 FROM appointments a2 
                       WHERE a2.pat_id = a.pat_id 
                       AND a2.doc_id = a.doc_id 
                       AND a2.appointment_date < a.appointment_date
                   ) THEN 'Follow up'
                   ELSE 'First Visit'
               END as appointment_type
        FROM appointments a
        JOIN patients p ON a.pat_id = p.pat_id
        JOIN appointment_statuses s ON a.status_id = s.status_id
        LEFT JOIN services sv ON a.service_id = sv.service_id
        WHERE a.doc_id = :doc_id 
        AND a.appointment_date >= CURRENT_DATE
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
        LIMIT 5
    ");
    $stmt->execute(['doc_id' => $doc_id]);
    $new_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $new_appointments = [];
}

// Chart data for appointments
$chart_data = [
    'appointments' => [10, 15, 20, 18, 25, 30, 28],
    'completed' => [8, 12, 18, 15, 22, 25, 24]
];

// Include the view
require_once __DIR__ . '/../../views/doctor/dashboard.view.php';
