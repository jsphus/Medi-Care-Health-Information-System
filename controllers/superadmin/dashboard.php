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

// Get dashboard statistics with percentage changes
try {
    // Get current month start for comparisons
    $this_month_start = date('Y-m-01');
    
    // Total Patients - Current and Last Month (compare total counts)
    $stmt = $db->query("SELECT COUNT(*) as count FROM patients");
    $current_patients = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count patients that existed at the end of last month (created before start of this month)
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM patients 
        WHERE created_at < '$this_month_start'
    ");
    $last_month_patients = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $patients_change = $last_month_patients > 0 ? round((($current_patients - $last_month_patients) / $last_month_patients) * 100, 1) : ($current_patients > 0 ? 100 : 0);
    
    // New Appointments - Within selected date range
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM appointments 
        WHERE appointment_date >= :start_date AND appointment_date <= :end_date
    ");
    $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
    $current_appointments = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM appointments 
        WHERE DATE_TRUNC('month', appointment_date) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month')
    ");
    $last_month_appointments = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $appointments_change = $last_month_appointments > 0 ? round((($current_appointments - $last_month_appointments) / $last_month_appointments) * 100, 1) : 0;
    
    // Completed Appointments (Medical Records) - Within selected date range
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM medical_records 
        WHERE record_date >= :start_date AND record_date <= :end_date
    ");
    $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
    $current_records = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM medical_records 
        WHERE DATE_TRUNC('month', record_date) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month')
    ");
    $last_month_records = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $records_change = $last_month_records > 0 ? round((($current_records - $last_month_records) / $last_month_records) * 100, 1) : 0;
    
    // Total Users - Current and Last Month (compare total counts)
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $current_users = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count users that existed at the end of last month (created before start of this month)
    $this_month_start = date('Y-m-01');
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM users 
        WHERE created_at < '$this_month_start'
    ");
    $last_month_users = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $users_change = $last_month_users > 0 ? round((($current_users - $last_month_users) / $last_month_users) * 100, 1) : ($current_users > 0 ? 100 : 0);
    
    // Patient Statistics Chart - Monthly data for last 12 months
    $patient_chart_data = [];
    for ($i = 11; $i >= 0; $i--) {
        $month_start = date('Y-m-01', strtotime("-$i months"));
        $month_end = date('Y-m-t', strtotime("-$i months"));
        $stmt = $db->query("
            SELECT COUNT(*) as count 
            FROM appointments 
            WHERE appointment_date >= '$month_start' AND appointment_date <= '$month_end'
        ");
        $patient_chart_data[] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
    
    // Users by Role (Donut Chart)
    $users_by_role = [];
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE pat_id IS NOT NULL");
    $users_by_role['Patient'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE doc_id IS NOT NULL");
    $users_by_role['Doctor'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE staff_id IS NOT NULL");
    $users_by_role['Staff'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE user_is_superadmin = true");
    $users_by_role['Admin'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Top Services (Most Booked)
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
    
    // Staff List (Top 3)
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
    $total_staff_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Satisfaction/Completion Rate - Based on completed appointments
    $stmt = $db->query("
        SELECT COUNT(*) as total 
        FROM appointments a
        JOIN appointment_statuses s ON a.status_id = s.status_id
        WHERE LOWER(s.status_name) = 'completed'
    ");
    $completed = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM appointments");
    $total_appts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $completion_rate = $total_appts > 0 ? round(($completed / $total_appts) * 100, 1) : 0;
    
    // Completion rate over last 12 months
    $completion_chart_data = [];
    for ($i = 11; $i >= 0; $i--) {
        $month_start = date('Y-m-01', strtotime("-$i months"));
        $month_end = date('Y-m-t', strtotime("-$i months"));
        
        $stmt = $db->query("
            SELECT COUNT(*) as completed 
            FROM appointments a
            JOIN appointment_statuses s ON a.status_id = s.status_id
            WHERE a.appointment_date >= '$month_start' 
            AND a.appointment_date <= '$month_end'
            AND LOWER(s.status_name) = 'completed'
        ");
        $month_completed = $stmt->fetch(PDO::FETCH_ASSOC)['completed'];
        
        $stmt = $db->query("
            SELECT COUNT(*) as total 
            FROM appointments 
            WHERE appointment_date >= '$month_start' AND appointment_date <= '$month_end'
        ");
        $month_total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $completion_chart_data[] = $month_total > 0 ? round(($month_completed / $month_total) * 100, 1) : 0;
    }
    
    // Calculate last month completion rate for comparison
    $last_month_start = date('Y-m-01', strtotime('-1 month'));
    $last_month_end = date('Y-m-t', strtotime('-1 month'));
    
    $stmt = $db->query("
        SELECT COUNT(*) as completed 
        FROM appointments a
        JOIN appointment_statuses s ON a.status_id = s.status_id
        WHERE a.appointment_date >= '$last_month_start' 
        AND a.appointment_date <= '$last_month_end'
        AND LOWER(s.status_name) = 'completed'
    ");
    $last_month_completed = $stmt->fetch(PDO::FETCH_ASSOC)['completed'];
    
    $stmt = $db->query("
        SELECT COUNT(*) as total 
        FROM appointments 
        WHERE appointment_date >= '$last_month_start' AND appointment_date <= '$last_month_end'
    ");
    $last_month_total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $last_month_completion = $last_month_total > 0 ? round(($last_month_completed / $last_month_total) * 100, 1) : 0;
    $completion_change = $last_month_completion > 0 ? round($completion_rate - $last_month_completion, 1) : 0;
    
    // Payment Statistics - Within selected date range
    // Total payments in date range
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM payments p
        WHERE DATE(p.payment_date) >= :start_date AND DATE(p.payment_date) <= :end_date
    ");
    $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
    $payments_this_month = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total amount in date range
    $stmt = $db->prepare("
        SELECT COALESCE(SUM(p.payment_amount), 0) as total 
        FROM payments p
        WHERE DATE(p.payment_date) >= :start_date AND DATE(p.payment_date) <= :end_date
    ");
    $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
    $total_amount_this_month = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Paid payments in date range
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM payments p
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE DATE(p.payment_date) >= :start_date AND DATE(p.payment_date) <= :end_date
        AND LOWER(ps.status_name) = 'paid'
    ");
    $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
    $paid_this_month = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending payments in date range
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM payments p
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE DATE(p.payment_date) >= :start_date AND DATE(p.payment_date) <= :end_date
        AND LOWER(ps.status_name) = 'pending'
    ");
    $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
    $pending_this_month = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Last month payments for comparison
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM payments p
        JOIN appointments a ON p.appointment_id = a.appointment_id
        WHERE DATE_TRUNC('month', p.payment_date) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month')
    ");
    $last_month_payments = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $payments_change = $last_month_payments > 0 ? round((($payments_this_month - $last_month_payments) / $last_month_payments) * 100, 1) : ($payments_this_month > 0 ? 100 : 0);
    
    // Patients Today (count of unique patients with appointments today)
    $stmt = $db->query("
        SELECT COUNT(DISTINCT a.pat_id) as count 
        FROM appointments a
        WHERE a.appointment_date = CURRENT_DATE
    ");
    $patients_today = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
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
