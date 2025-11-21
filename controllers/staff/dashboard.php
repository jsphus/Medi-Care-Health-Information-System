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
        SELECT service_id, service_name, service_price, service_category, created_at
        FROM services
        ORDER BY created_at DESC
        LIMIT 10
    ");
    
    // Chart data for services
    $chart_data = [
        'labels' => $chart_labels,
        'current_year' => $chart_current_year,
        'last_year' => $chart_last_year
    ];
    
} catch (PDOException $e) {
    error_log("Staff Dashboard error: " . $e->getMessage());
    $stats = [
        'total_staff' => 0,
        'total_services' => 0,
        'total_specializations' => 0,
        'total_payment_methods' => 0
    ];
    $recent_services = [];
    $chart_data = [
        'services' => [0, 0, 0, 0, 0, 0, 0],
        'active' => [0, 0, 0, 0, 0, 0, 0]
    ];
}

if (!isset($chart_data)) {
    $chart_data = [
        'services' => [0, 0, 0, 0, 0, 0, 0],
        'active' => [0, 0, 0, 0, 0, 0, 0]
    ];
}

require_once __DIR__ . '/../../views/staff/dashboard.view.php';
