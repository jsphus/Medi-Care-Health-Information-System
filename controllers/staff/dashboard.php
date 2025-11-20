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
    if ($staff_id) {
        $staff = (new Staff())->getById($staff_id);
        if ($staff) {
            $staff_name = htmlspecialchars(($staff['staff_first_name'] ?? '') . ' ' . ($staff['staff_last_name'] ?? ''));
            $staff_name = trim($staff_name) ?: 'Staff';
        }
        
        // Get profile picture URL
        $profile_picture_url = User::getProfilePicture($auth);
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
    
    // Recent services
    $recent_services = $db->fetchAll("
        SELECT service_id, service_name, service_price, service_category
        FROM services
        ORDER BY created_at DESC
        LIMIT 10
    ");
    
    // Chart data for services
    $chart_data = [
        'services' => [5, 8, 12, 10, 15, 18, 16],
        'active' => [4, 7, 11, 9, 14, 17, 15]
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
