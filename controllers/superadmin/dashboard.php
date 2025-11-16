<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';

$auth = new Auth();
$auth->requireSuperAdmin();

$db = Database::getInstance();

// Get user name for greeting
$user_name = 'Admin';
$profile_picture_url = null;
try {
    $user_id = $auth->getUserId();
    if ($user_id) {
        $stmt = $db->prepare("SELECT user_email, profile_picture_url FROM users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $user_name = explode('@', $user['user_email'])[0];
            $user_name = ucfirst($user_name);
            $profile_picture_url = $user['profile_picture_url'] ?? null;
        }
    }
} catch (PDOException $e) {
    // Use default name
}

// Use current month for all statistics
$start_date = date('Y-m-01');
$end_date = date('Y-m-t');
$this_month_start = date('Y-m-01');
$last_month_start = date('Y-m-01', strtotime('-1 month'));
$last_month_end = date('Y-m-t', strtotime('-1 month'));

// Get dashboard statistics with optimized queries
try {
    // OPTIMIZATION 1: Get patients and users counts in one query
    $stmt = $db->query("
        SELECT 
            (SELECT COUNT(*) FROM patients) as current_patients,
            (SELECT COUNT(*) FROM patients WHERE created_at < '$this_month_start') as last_month_patients,
            (SELECT COUNT(*) FROM users) as current_users,
            (SELECT COUNT(*) FROM users WHERE created_at < '$this_month_start') as last_month_users
    ");
    $counts = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_patients = (int)$counts['current_patients'];
    $last_month_patients = (int)$counts['last_month_patients'];
    $current_users = (int)$counts['current_users'];
    $last_month_users = (int)$counts['last_month_users'];
    
    $patients_change = $last_month_patients > 0 ? round((($current_patients - $last_month_patients) / $last_month_patients) * 100, 1) : ($current_patients > 0 ? 100 : 0);
    $users_change = $last_month_users > 0 ? round((($current_users - $last_month_users) / $last_month_users) * 100, 1) : ($current_users > 0 ? 100 : 0);
    
    // OPTIMIZATION 2: Get appointments and records counts in one query
    $stmt = $db->prepare("
        SELECT 
            (SELECT COUNT(*) FROM appointments WHERE appointment_date >= :start_date AND appointment_date <= :end_date) as current_appointments,
            (SELECT COUNT(*) FROM appointments WHERE DATE_TRUNC('month', appointment_date) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month')) as last_month_appointments,
            (SELECT COUNT(*) FROM medical_records WHERE record_date >= :start_date AND record_date <= :end_date) as current_records,
            (SELECT COUNT(*) FROM medical_records WHERE DATE_TRUNC('month', record_date) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month')) as last_month_records
    ");
    $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
    $appt_counts = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_appointments = (int)$appt_counts['current_appointments'];
    $last_month_appointments = (int)$appt_counts['last_month_appointments'];
    $current_records = (int)$appt_counts['current_records'];
    $last_month_records = (int)$appt_counts['last_month_records'];
    
    $appointments_change = $last_month_appointments > 0 ? round((($current_appointments - $last_month_appointments) / $last_month_appointments) * 100, 1) : 0;
    $records_change = $last_month_records > 0 ? round((($current_records - $last_month_records) / $last_month_records) * 100, 1) : 0;
    
    // OPTIMIZATION 3: Get all 12 months of patient chart data in one query
    $stmt = $db->query("
        SELECT 
            DATE_TRUNC('month', appointment_date) as month,
            COUNT(*) as count
        FROM appointments
        WHERE appointment_date >= DATE_TRUNC('month', CURRENT_DATE - INTERVAL '11 months')
        GROUP BY DATE_TRUNC('month', appointment_date)
        ORDER BY month ASC
    ");
    $monthly_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Build array for last 12 months, filling missing months with 0
    $patient_chart_data = [];
    for ($i = 11; $i >= 0; $i--) {
        $target_month = date('Y-m-01', strtotime("-$i months"));
        $found = false;
        foreach ($monthly_data as $row) {
            if (date('Y-m-01', strtotime($row['month'])) === $target_month) {
                $patient_chart_data[] = (int)$row['count'];
                $found = true;
                break;
            }
        }
        if (!$found) {
            $patient_chart_data[] = 0;
        }
    }
    
    // OPTIMIZATION 4: Get users by role in one query with conditional aggregation
    $stmt = $db->query("
        SELECT 
            SUM(CASE WHEN pat_id IS NOT NULL THEN 1 ELSE 0 END) as patient_count,
            SUM(CASE WHEN doc_id IS NOT NULL THEN 1 ELSE 0 END) as doctor_count,
            SUM(CASE WHEN staff_id IS NOT NULL THEN 1 ELSE 0 END) as staff_count,
            SUM(CASE WHEN user_is_superadmin = true THEN 1 ELSE 0 END) as admin_count
        FROM users
    ");
    $role_counts = $stmt->fetch(PDO::FETCH_ASSOC);
    $users_by_role = [
        'Patient' => (int)$role_counts['patient_count'],
        'Doctor' => (int)$role_counts['doctor_count'],
        'Staff' => (int)$role_counts['staff_count'],
        'Admin' => (int)$role_counts['admin_count']
    ];
    
    // Top Services (Most Booked) - already optimized
    $stmt = $db->query("
        SELECT s.service_name, COUNT(a.appointment_id) as appointment_count,
               COUNT(a.appointment_id) * 100.0 / (SELECT COUNT(*) FROM appointments WHERE service_id IS NOT NULL) as percentage
        FROM services s
        LEFT JOIN appointments a ON s.service_id = a.service_id
        GROUP BY s.service_id, s.service_name
        ORDER BY appointment_count DESC
        LIMIT 5
    ");
    $top_services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_service_appointments = array_sum(array_column($top_services, 'appointment_count'));
    
    // OPTIMIZATION 5: Get staff list and count in one query
    $stmt = $db->query("
        SELECT d.doc_id, d.doc_first_name, d.doc_last_name, sp.spec_name
        FROM doctors d
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        WHERE d.doc_status = 'active'
        ORDER BY d.doc_first_name, d.doc_last_name
        LIMIT 3
    ");
    $top_staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM doctors WHERE doc_status = 'active'");
    $total_staff_count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // OPTIMIZATION 6: Get completion rate data in one query
    $stmt = $db->query("
        SELECT 
            SUM(CASE WHEN LOWER(s.status_name) = 'completed' THEN 1 ELSE 0 END) as completed,
            COUNT(*) as total
        FROM appointments a
        JOIN appointment_statuses s ON a.status_id = s.status_id
    ");
    $completion_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $completed = (int)$completion_data['completed'];
    $total_appts = (int)$completion_data['total'];
    $completion_rate = $total_appts > 0 ? round(($completed / $total_appts) * 100, 1) : 0;
    
    // OPTIMIZATION 7: Get all 12 months of completion chart data in one query
    $stmt = $db->query("
        SELECT 
            DATE_TRUNC('month', a.appointment_date) as month,
            SUM(CASE WHEN LOWER(s.status_name) = 'completed' THEN 1 ELSE 0 END) as completed,
            COUNT(*) as total
        FROM appointments a
        JOIN appointment_statuses s ON a.status_id = s.status_id
        WHERE a.appointment_date >= DATE_TRUNC('month', CURRENT_DATE - INTERVAL '11 months')
        GROUP BY DATE_TRUNC('month', a.appointment_date)
        ORDER BY month ASC
    ");
    $completion_monthly_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Build completion chart data array and extract last month completion
    $completion_chart_data = [];
    $last_month_completion = 0;
    $last_month_target = date('Y-m-01', strtotime('-1 month'));
    
    for ($i = 11; $i >= 0; $i--) {
        $target_month = date('Y-m-01', strtotime("-$i months"));
        $found = false;
        foreach ($completion_monthly_data as $row) {
            if (date('Y-m-01', strtotime($row['month'])) === $target_month) {
                $month_completed = (int)$row['completed'];
                $month_total = (int)$row['total'];
                $rate = $month_total > 0 ? round(($month_completed / $month_total) * 100, 1) : 0;
                $completion_chart_data[] = $rate;
                
                // Store last month completion rate for comparison (when $i === 1, that's 1 month ago)
                if ($target_month === $last_month_target) {
                    $last_month_completion = $rate;
                }
                $found = true;
                break;
            }
        }
        if (!$found) {
            $completion_chart_data[] = 0;
        }
    }
    
    // Get last month completion rate if not found in the query results
    if ($last_month_completion === 0) {
        $stmt = $db->query("
            SELECT 
                SUM(CASE WHEN LOWER(s.status_name) = 'completed' THEN 1 ELSE 0 END) as completed,
                COUNT(*) as total
            FROM appointments a
            JOIN appointment_statuses s ON a.status_id = s.status_id
            WHERE a.appointment_date >= '$last_month_start' 
            AND a.appointment_date <= '$last_month_end'
        ");
        $last_month_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $last_month_completed = (int)$last_month_data['completed'];
        $last_month_total = (int)$last_month_data['total'];
        $last_month_completion = $last_month_total > 0 ? round(($last_month_completed / $last_month_total) * 100, 1) : 0;
    }
    
    $completion_change = $last_month_completion > 0 ? round($completion_rate - $last_month_completion, 1) : 0;
    
    // OPTIMIZATION 8: Get all payment statistics in one query
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as payments_count,
            COALESCE(SUM(payment_amount), 0) as total_amount,
            SUM(CASE WHEN LOWER(ps.status_name) = 'paid' THEN 1 ELSE 0 END) as paid_count,
            SUM(CASE WHEN LOWER(ps.status_name) = 'pending' THEN 1 ELSE 0 END) as pending_count
        FROM payments p
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE DATE(p.payment_date) >= :start_date AND DATE(p.payment_date) <= :end_date
    ");
    $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
    $payment_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $payments_this_month = (int)$payment_data['payments_count'];
    $total_amount_this_month = (float)$payment_data['total_amount'];
    $paid_this_month = (int)$payment_data['paid_count'];
    $pending_this_month = (int)$payment_data['pending_count'];
    
    // Get last month payments for comparison
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM payments p
        WHERE DATE_TRUNC('month', p.payment_date) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month')
    ");
    $last_month_payments = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $payments_change = $last_month_payments > 0 ? round((($payments_this_month - $last_month_payments) / $last_month_payments) * 100, 1) : ($payments_this_month > 0 ? 100 : 0);
    
    // Patients Today (count of unique patients with appointments today)
    $stmt = $db->query("
        SELECT COUNT(DISTINCT a.pat_id) as count 
        FROM appointments a
        WHERE a.appointment_date = CURRENT_DATE
    ");
    $patients_today = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Today's Appointments
    $stmt = $db->query("
        SELECT a.*, 
               p.pat_first_name, p.pat_last_name, p.pat_date_of_birth,
               d.doc_first_name, d.doc_last_name,
               s.status_name, s.status_color,
               sv.service_name
        FROM appointments a
        LEFT JOIN patients p ON a.pat_id = p.pat_id
        LEFT JOIN doctors d ON a.doc_id = d.doc_id
        LEFT JOIN appointment_statuses s ON a.status_id = s.status_id
        LEFT JOIN services sv ON a.service_id = sv.service_id
        WHERE a.appointment_date = CURRENT_DATE
        ORDER BY a.appointment_time ASC
        LIMIT 10
    ");
    $today_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $current_patients = 0;
    $patients_change = 0;
    $current_appointments = 0;
    $appointments_change = 0;
    $current_records = 0;
    $records_change = 0;
    $current_users = 0;
    $users_change = 0;
    $patient_chart_data = array_fill(0, 12, 0);
    $users_by_role = ['Patient' => 0, 'Doctor' => 0, 'Staff' => 0, 'Admin' => 0];
    $top_services = [];
    $total_service_appointments = 0;
    $top_staff = [];
    $total_staff_count = 0;
    $completion_rate = 0;
    $completion_change = 0;
    $completion_chart_data = array_fill(0, 12, 0);
    $payments_this_month = 0;
    $total_amount_this_month = 0;
    $paid_this_month = 0;
    $pending_this_month = 0;
    $payments_change = 0;
    $patients_today = 0;
    $today_appointments = [];
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');
}

// Include the view
require_once __DIR__ . '/../../views/superadmin/dashboard.view.php';
