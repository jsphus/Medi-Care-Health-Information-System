<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireSuperAdmin();

$db = Database::getInstance();

// Get user name for greeting
$user_name = 'Admin';
$profile_picture_url = null;
try {
    $user_id = $auth->getUserId();
    if ($user_id) {
        $user = $db->fetchOne("SELECT user_email, profile_picture_url FROM users WHERE user_id = :user_id", ['user_id' => $user_id]);
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
    $counts = $db->fetchOne("
        SELECT 
            (SELECT COUNT(*) FROM patients) as current_patients,
            (SELECT COUNT(*) FROM patients WHERE created_at < '$this_month_start') as last_month_patients,
            (SELECT COUNT(*) FROM users) as current_users,
            (SELECT COUNT(*) FROM users WHERE created_at < '$this_month_start') as last_month_users
    ");
    $current_patients = (int)$counts['current_patients'];
    $last_month_patients = (int)$counts['last_month_patients'];
    $current_users = (int)$counts['current_users'];
    $last_month_users = (int)$counts['last_month_users'];
    
    $patients_change = $last_month_patients > 0 ? round((($current_patients - $last_month_patients) / $last_month_patients) * 100, 1) : ($current_patients > 0 ? 100 : 0);
    $users_change = $last_month_users > 0 ? round((($current_users - $last_month_users) / $last_month_users) * 100, 1) : ($current_users > 0 ? 100 : 0);
    
    // OPTIMIZATION 2: Get appointments and records counts in one query
    $appt_counts = $db->fetchOne("
        SELECT 
            (SELECT COUNT(*) FROM appointments WHERE appointment_date >= :start_date AND appointment_date <= :end_date) as current_appointments,
            (SELECT COUNT(*) FROM appointments WHERE DATE_TRUNC('month', appointment_date) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month')) as last_month_appointments,
            (SELECT COUNT(*) FROM medical_records WHERE med_rec_visit_date >= :start_date AND med_rec_visit_date <= :end_date) as current_records,
            (SELECT COUNT(*) FROM medical_records WHERE DATE_TRUNC('month', med_rec_visit_date) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month')) as last_month_records
    ", ['start_date' => $start_date, 'end_date' => $end_date]);
    $current_appointments = (int)$appt_counts['current_appointments'];
    $last_month_appointments = (int)$appt_counts['last_month_appointments'];
    $current_records = (int)$appt_counts['current_records'];
    $last_month_records = (int)$appt_counts['last_month_records'];
    
    $appointments_change = $last_month_appointments > 0 ? round((($current_appointments - $last_month_appointments) / $last_month_appointments) * 100, 1) : ($current_appointments > 0 ? 100 : 0);
    $records_change = $last_month_records > 0 ? round((($current_records - $last_month_records) / $last_month_records) * 100, 1) : ($current_records > 0 ? 100 : 0);
    
    // OPTIMIZATION 3: Get all 12 months of appointments statistics (total and completed) in one query
    $monthly_data = $db->fetchAll("
        SELECT 
            DATE_TRUNC('month', a.appointment_date) as month,
            COUNT(*) as total_count,
            SUM(CASE WHEN LOWER(s.status_name) = 'completed' THEN 1 ELSE 0 END) as completed_count
        FROM appointments a
        LEFT JOIN appointment_statuses s ON a.status_id = s.status_id
        WHERE a.appointment_date >= DATE_TRUNC('month', CURRENT_DATE - INTERVAL '11 months')
        GROUP BY DATE_TRUNC('month', a.appointment_date)
        ORDER BY month ASC
    ");
    
    // Build arrays for last 12 months, filling missing months with 0
    $appointments_chart_data = [];
    $completed_appointments_chart_data = [];
    for ($i = 11; $i >= 0; $i--) {
        $target_month = date('Y-m-01', strtotime("-$i months"));
        $found = false;
        foreach ($monthly_data as $row) {
            if (date('Y-m-01', strtotime($row['month'])) === $target_month) {
                $appointments_chart_data[] = (int)$row['total_count'];
                $completed_appointments_chart_data[] = (int)$row['completed_count'];
                $found = true;
                break;
            }
        }
        if (!$found) {
            $appointments_chart_data[] = 0;
            $completed_appointments_chart_data[] = 0;
        }
    }
    
    // OPTIMIZATION 4: Get users by role in one query with conditional aggregation
    $role_counts = $db->fetchOne("
        SELECT 
            SUM(CASE WHEN pat_id IS NOT NULL THEN 1 ELSE 0 END) as patient_count,
            SUM(CASE WHEN doc_id IS NOT NULL THEN 1 ELSE 0 END) as doctor_count,
            SUM(CASE WHEN staff_id IS NOT NULL THEN 1 ELSE 0 END) as staff_count
        FROM users
    ");
    $users_by_role = [
        'Patient' => (int)$role_counts['patient_count'],
        'Doctor' => (int)$role_counts['doctor_count'],
        'Staff' => (int)$role_counts['staff_count']
    ];
    
    // Top Services (Most Booked) - already optimized
    $top_services = $db->fetchAll("
        SELECT s.service_name, COUNT(a.appointment_id) as appointment_count,
               COUNT(a.appointment_id) * 100.0 / (SELECT COUNT(*) FROM appointments WHERE service_id IS NOT NULL) as percentage
        FROM services s
        LEFT JOIN appointments a ON s.service_id = a.service_id
        GROUP BY s.service_id, s.service_name
        ORDER BY appointment_count DESC
        LIMIT 5
    ");
    $total_service_appointments = array_sum(array_column($top_services, 'appointment_count'));
    
    // OPTIMIZATION 5: Get staff list and count in one query
    $top_staff = $db->fetchAll("
        SELECT d.doc_id, d.doc_first_name, d.doc_last_name, sp.spec_name
        FROM doctors d
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        WHERE d.doc_status = 'active'
        ORDER BY d.doc_first_name, d.doc_last_name
        LIMIT 3
    ");
    
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM doctors WHERE doc_status = 'active'");
    $total_staff_count = (int)($result['count'] ?? 0);
    
    // OPTIMIZATION 8: Get all payment statistics in one query
    $payment_data = $db->fetchOne("
        SELECT 
            COUNT(*) as payments_count,
            COALESCE(SUM(payment_amount), 0) as total_amount,
            SUM(CASE WHEN LOWER(ps.status_name) = 'paid' THEN 1 ELSE 0 END) as paid_count,
            SUM(CASE WHEN LOWER(ps.status_name) = 'pending' THEN 1 ELSE 0 END) as pending_count
        FROM payments p
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE DATE(p.payment_date) >= :start_date AND DATE(p.payment_date) <= :end_date
    ", ['start_date' => $start_date, 'end_date' => $end_date]);
    $payments_this_month = (int)$payment_data['payments_count'];
    $total_amount_this_month = (float)$payment_data['total_amount'];
    $paid_this_month = (int)$payment_data['paid_count'];
    $pending_this_month = (int)$payment_data['pending_count'];
    
    // Get last month payments for comparison
    $result = $db->fetchOne("
        SELECT COUNT(*) as count 
        FROM payments p
        WHERE DATE_TRUNC('month', p.payment_date) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month')
    ");
    $last_month_payments = (int)($result['count'] ?? 0);
    $payments_change = $last_month_payments > 0 ? round((($payments_this_month - $last_month_payments) / $last_month_payments) * 100, 1) : ($payments_this_month > 0 ? 100 : 0);
    
    // Patients Today (count of unique patients with appointments today)
    $result = $db->fetchOne("
        SELECT COUNT(DISTINCT a.pat_id) as count 
        FROM appointments a
        WHERE a.appointment_date = CURRENT_DATE
    ");
    $patients_today = (int)($result['count'] ?? 0);
    
    // Today's Appointments
    $today_appointments = $db->fetchAll("
        SELECT a.*, 
               p.pat_first_name, p.pat_middle_initial, p.pat_last_name, p.pat_date_of_birth,
               d.doc_first_name, d.doc_middle_initial, d.doc_last_name,
               s.status_name, s.status_color,
               sv.service_name,
               up.profile_picture_url as patient_profile_picture,
               ud.profile_picture_url as doctor_profile_picture
        FROM appointments a
        LEFT JOIN patients p ON a.pat_id = p.pat_id
        LEFT JOIN doctors d ON a.doc_id = d.doc_id
        LEFT JOIN appointment_statuses s ON a.status_id = s.status_id
        LEFT JOIN services sv ON a.service_id = sv.service_id
        LEFT JOIN users up ON up.pat_id = p.pat_id
        LEFT JOIN users ud ON ud.doc_id = d.doc_id
        WHERE a.appointment_date = CURRENT_DATE
        ORDER BY a.appointment_time ASC
        LIMIT 10
    ");
    
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
    $appointments_chart_data = array_fill(0, 12, 0);
    $completed_appointments_chart_data = array_fill(0, 12, 0);
    $users_by_role = ['Patient' => 0, 'Doctor' => 0, 'Staff' => 0];
    $top_services = [];
    $total_service_appointments = 0;
    $top_staff = [];
    $total_staff_count = 0;
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
