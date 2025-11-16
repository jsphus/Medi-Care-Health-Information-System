<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';

$auth = new Auth();
$auth->requireStaff();

$db = Database::getInstance();

// Get staff name for greeting
$staff_name = 'Staff';
$profile_picture_url = null;
try {
    $staff_id = $auth->getStaffId();
    if ($staff_id) {
        $stmt = $db->prepare("SELECT staff_first_name, staff_last_name FROM staff WHERE staff_id = :staff_id");
        $stmt->execute(['staff_id' => $staff_id]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($staff) {
            $staff_name = htmlspecialchars(($staff['staff_first_name'] ?? '') . ' ' . ($staff['staff_last_name'] ?? ''));
            $staff_name = trim($staff_name) ?: 'Staff';
        }
        
        // Get profile picture URL
        $user_id = $auth->getUserId();
        $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $profile_picture_url = $user['profile_picture_url'] ?? null;
    }
} catch (PDOException $e) {
    // Use default name
}

// Get dashboard statistics
try {
    // Count staff
    $stmt = $db->query("SELECT COUNT(*) as count FROM staff");
    $stats['total_staff'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count services
    $stmt = $db->query("SELECT COUNT(*) as count FROM services");
    $stats['total_services'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count specializations
    $stmt = $db->query("SELECT COUNT(*) as count FROM specializations");
    $stats['total_specializations'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count payment methods
    $stmt = $db->query("SELECT COUNT(*) as count FROM payment_methods WHERE is_active = true");
    $stats['total_payment_methods'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Recent services
    $stmt = $db->query("
        SELECT service_id, service_name, service_price, service_category
        FROM services
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $recent_services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
