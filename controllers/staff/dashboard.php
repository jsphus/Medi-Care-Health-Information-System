<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../classes/Staff.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireStaff();

$db = Database::getInstance();

// Get staff name for greeting
$staff_name = 'Staff';
$profile_picture_url = null;
try {
    $staff_id = $auth->getStaffId();
    $user_id = $auth->getUserId();
    if ($staff_id) {
        $staff = (new Staff())->getById($staff_id);
        if ($staff) {
            $staff_name = htmlspecialchars(($staff['staff_first_name'] ?? '') . ' ' . ($staff['staff_last_name'] ?? ''));
            $staff_name = trim($staff_name) ?: 'Staff';
        }
        
        // Get profile picture URL
        if ($user_id) {
            $profile_picture_url = User::getProfilePicture($user_id);
        }
    }
} catch (PDOException $e) {
    // Use default name
}

// Get dashboard statistics
try {
    // Count staff
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM staff");
    $stats['total_staff'] = $result['count'] ?? 0;
    
    // Count services
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM services");
    $stats['total_services'] = $result['count'] ?? 0;
    
    // Count specializations
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM specializations");
    $stats['total_specializations'] = $result['count'] ?? 0;
    
    // Count payment methods
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM payment_methods WHERE is_active = true");
    $stats['total_payment_methods'] = $result['count'] ?? 0;
    
    // Most booked service (overall) for Quick Stats
    $most_booked_service = $db->fetchOne("
        SELECT s.service_id, s.service_name, COUNT(a.appointment_id) as booking_count
        FROM services s
        LEFT JOIN appointments a ON s.service_id = a.service_id
        WHERE a.service_id IS NOT NULL
        GROUP BY s.service_id, s.service_name
        ORDER BY booking_count DESC
        LIMIT 1
    ");
    
    // If no bookings found, get a default service
    if (!$most_booked_service || empty($most_booked_service['service_name'])) {
        $most_booked_service = $db->fetchOne("
            SELECT service_id, service_name, 0 as booking_count
            FROM services
            ORDER BY created_at DESC
            LIMIT 1
        ");
        if (!$most_booked_service) {
            $most_booked_service = ['service_name' => 'N/A', 'booking_count' => 0];
        }
    }
    
    // Get monthly service booking data for current year and last year
    $current_year = date('Y');
    $last_year = $current_year - 1;
    $current_month = date('n'); // 1-12
    
    // Get most booked service per month for current year (last 7 months)
    // Using window function to rank services per month and get top one
    $monthly_services_current = $db->fetchAll("
        WITH monthly_ranked AS (
            SELECT 
                EXTRACT(MONTH FROM a.appointment_date)::INTEGER as month_num,
                s.service_id,
                s.service_name,
                COUNT(a.appointment_id) as booking_count,
                ROW_NUMBER() OVER (PARTITION BY EXTRACT(MONTH FROM a.appointment_date) ORDER BY COUNT(a.appointment_id) DESC) as rn
            FROM appointments a
            JOIN services s ON a.service_id = s.service_id
            WHERE EXTRACT(YEAR FROM a.appointment_date) = :current_year
                AND EXTRACT(MONTH FROM a.appointment_date) >= GREATEST(1, :current_month - 6)
                AND EXTRACT(MONTH FROM a.appointment_date) <= :current_month
            GROUP BY EXTRACT(MONTH FROM a.appointment_date), s.service_id, s.service_name
        )
        SELECT month_num, service_name, booking_count
        FROM monthly_ranked
        WHERE rn = 1
        ORDER BY month_num
    ", ['current_year' => $current_year, 'current_month' => $current_month]);
    
    // Get most booked service per month for last year (same months)
    $monthly_services_last_year = $db->fetchAll("
        WITH monthly_ranked AS (
            SELECT 
                EXTRACT(MONTH FROM a.appointment_date)::INTEGER as month_num,
                s.service_id,
                s.service_name,
                COUNT(a.appointment_id) as booking_count,
                ROW_NUMBER() OVER (PARTITION BY EXTRACT(MONTH FROM a.appointment_date) ORDER BY COUNT(a.appointment_id) DESC) as rn
            FROM appointments a
            JOIN services s ON a.service_id = s.service_id
            WHERE EXTRACT(YEAR FROM a.appointment_date) = :last_year
                AND EXTRACT(MONTH FROM a.appointment_date) >= GREATEST(1, :current_month - 6)
                AND EXTRACT(MONTH FROM a.appointment_date) <= :current_month
            GROUP BY EXTRACT(MONTH FROM a.appointment_date), s.service_id, s.service_name
        )
        SELECT month_num, service_name, booking_count
        FROM monthly_ranked
        WHERE rn = 1
        ORDER BY month_num
    ", ['last_year' => $last_year, 'current_month' => $current_month]);
    
    // Process monthly data - get top service per month
    $month_names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $chart_labels = [];
    $chart_current_year = [];
    $chart_last_year = [];
    
    // Initialize arrays for last 7 months
    $start_month = max(1, $current_month - 6);
    
    // Process current year data - create map of month to booking count
    $monthly_max_current = [];
    foreach ($monthly_services_current as $row) {
        $month = (int)$row['month_num'];
        $monthly_max_current[$month] = (int)$row['booking_count'];
    }
    
    // Process last year data - create map of month to booking count
    $monthly_max_last_year = [];
    foreach ($monthly_services_last_year as $row) {
        $month = (int)$row['month_num'];
        $monthly_max_last_year[$month] = (int)$row['booking_count'];
    }
    
    // Fill chart data arrays in order
    for ($i = $start_month; $i <= $current_month; $i++) {
        $chart_labels[] = $month_names[$i - 1];
        $chart_current_year[] = $monthly_max_current[$i] ?? 0;
        $chart_last_year[] = $monthly_max_last_year[$i] ?? 0;
    }
    
    // Recent services with dates
    $recent_services = $db->fetchAll("
        SELECT service_id, service_name, service_price, service_category, created_at, updated_at
        FROM services
        ORDER BY COALESCE(updated_at, created_at) DESC
        LIMIT 5
    ");
    
    // Chart data for services
    $chart_data = [
        'labels' => $chart_labels,
        'current_year' => $chart_current_year,
        'last_year' => $chart_last_year
    ];
    
    // Payment Statistics
    $result = $db->fetchOne("
        SELECT COUNT(*) as count, COALESCE(SUM(payment_amount), 0) as total
        FROM payments p
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE LOWER(ps.status_name) = 'pending'
    ");
    $stats['pending_payments'] = (int)($result['count'] ?? 0);
    $stats['pending_amount'] = (float)($result['total'] ?? 0);
    
    $result = $db->fetchOne("
        SELECT COUNT(*) as count, COALESCE(SUM(payment_amount), 0) as total
        FROM payments
        WHERE DATE(payment_date) = CURRENT_DATE
    ");
    $stats['today_payments'] = (int)($result['count'] ?? 0);
    $stats['today_amount'] = (float)($result['total'] ?? 0);
    
    $result = $db->fetchOne("
        SELECT COUNT(*) as count, COALESCE(SUM(payment_amount), 0) as total
        FROM payments
        WHERE payment_date >= DATE_TRUNC('week', CURRENT_DATE)
    ");
    $stats['this_week_payments'] = (int)($result['count'] ?? 0);
    $stats['this_week_amount'] = (float)($result['total'] ?? 0);
    
    // Pending Payments List
    $pending_payments = $db->fetchAll("
        SELECT p.*,
               pat.pat_first_name, pat.pat_last_name,
               u.profile_picture_url as patient_profile_picture,
               pm.method_name,
               ps.status_name, ps.status_color
        FROM payments p
        LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
        LEFT JOIN patients pat ON a.pat_id = pat.pat_id
        LEFT JOIN users u ON pat.pat_id = u.pat_id
        LEFT JOIN payment_methods pm ON p.payment_method_id = pm.method_id
        LEFT JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE LOWER(ps.status_name) = 'pending'
        ORDER BY p.payment_date ASC, p.created_at ASC
        LIMIT 10
    ");
    
    // Next Payment to Process
    $next_payment = $db->fetchOne("
        SELECT p.*,
               pat.pat_first_name, pat.pat_last_name,
               u.profile_picture_url as patient_profile_picture,
               pm.method_name,
               ps.status_name, ps.status_color
        FROM payments p
        LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
        LEFT JOIN patients pat ON a.pat_id = pat.pat_id
        LEFT JOIN users u ON pat.pat_id = u.pat_id
        LEFT JOIN payment_methods pm ON p.payment_method_id = pm.method_id
        LEFT JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE LOWER(ps.status_name) = 'pending'
        ORDER BY p.payment_date ASC, p.created_at ASC
        LIMIT 1
    ");
    
    // Recent Activity - Get payments and services separately then merge
    $recent_activity = [];
    try {
        $payment_activities = $db->fetchAll("
            SELECT 
                p.payment_id::text as id,
                p.payment_date as activity_date,
                p.updated_at,
                'Processed payment' as activity_description,
                p.payment_amount,
                'payment' as activity_type,
                pat.pat_first_name,
                pat.pat_last_name,
                u.profile_picture_url as profile_picture
            FROM payments p
            LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
            LEFT JOIN patients pat ON a.pat_id = pat.pat_id
            LEFT JOIN users u ON pat.pat_id = u.pat_id
            WHERE p.updated_at >= CURRENT_DATE - INTERVAL '7 days'
            ORDER BY p.updated_at DESC
            LIMIT 5
        ");
        
        $service_activities = $db->fetchAll("
            SELECT 
                s.service_id::text as id,
                s.created_at::date as activity_date,
                COALESCE(s.updated_at, s.created_at) as updated_at,
                CASE WHEN s.updated_at IS NOT NULL THEN 'Service updated' ELSE 'Service created' END as activity_description,
                0 as payment_amount,
                'service' as activity_type,
                NULL::text as pat_first_name,
                NULL::text as pat_last_name,
                NULL::text as profile_picture,
                s.service_name
            FROM services s
            WHERE s.created_at >= CURRENT_DATE - INTERVAL '7 days' 
               OR s.updated_at >= CURRENT_DATE - INTERVAL '7 days'
            ORDER BY COALESCE(s.updated_at, s.created_at) DESC
            LIMIT 5
        ");
        
        // Format payment activities
        foreach ($payment_activities as $act) {
            $patName = trim(($act['pat_first_name'] ?? '') . ' ' . ($act['pat_last_name'] ?? ''));
            $act['activity_description'] = 'Processed payment of ₱' . number_format($act['payment_amount'] ?? 0, 2) . 
                ($patName ? ' for ' . $patName : '');
            $recent_activity[] = $act;
        }
        
        // Format service activities
        foreach ($service_activities as $act) {
            $act['activity_description'] = ($act['activity_description'] ?? 'Service') . ': ' . ($act['service_name'] ?? '');
            $recent_activity[] = $act;
        }
        
        // Sort by updated_at descending
        usort($recent_activity, function($a, $b) {
            $timeA = strtotime($a['updated_at'] ?? '1970-01-01');
            $timeB = strtotime($b['updated_at'] ?? '1970-01-01');
            return $timeB - $timeA;
        });
        
        // Limit to 8 most recent
        $recent_activity = array_slice($recent_activity, 0, 8);
    } catch (PDOException $e) {
        error_log("Recent activity query error: " . $e->getMessage());
        $recent_activity = [];
    }
    
    // Popular Services (by revenue)
    $popular_services = $db->fetchAll("
        SELECT s.service_id, s.service_name, s.service_price, s.service_category,
               COALESCE(SUM(p.payment_amount), 0) as total_revenue,
               COUNT(DISTINCT p.payment_id) as payment_count
        FROM services s
        LEFT JOIN appointments a ON s.service_id = a.service_id
        LEFT JOIN payments p ON a.appointment_id = p.appointment_id
        GROUP BY s.service_id, s.service_name, s.service_price, s.service_category
        ORDER BY total_revenue DESC, payment_count DESC
        LIMIT 5
    ");
    
} catch (PDOException $e) {
    error_log("Staff Dashboard error: " . $e->getMessage());
    $stats = [
        'total_staff' => 0,
        'total_services' => 0,
        'total_specializations' => 0,
        'total_payment_methods' => 0,
        'pending_payments' => 0,
        'pending_amount' => 0,
        'today_payments' => 0,
        'today_amount' => 0,
        'this_week_payments' => 0,
        'this_week_amount' => 0
    ];
    $recent_services = [];
    $pending_payments = [];
    $next_payment = null;
    $recent_activity = [];
    $popular_services = [];
    $chart_data = [
        'services' => [0, 0, 0, 0, 0, 0, 0],
        'active' => [0, 0, 0, 0, 0, 0, 0]
    ];
}

if (!isset($chart_data)) {
    $chart_data = [
        'labels' => [],
        'current_year' => [],
        'last_year' => []
    ];
}

// Initialize variables if not set
if (!isset($pending_payments)) $pending_payments = [];
if (!isset($next_payment)) $next_payment = null;
if (!isset($recent_activity)) $recent_activity = [];
if (!isset($popular_services)) $popular_services = [];

require_once __DIR__ . '/../../views/staff/dashboard.view.php';
