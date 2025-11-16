<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();

// Get all appointments for this doctor
try {
    $today = date('Y-m-d');
    
    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'appointment_date';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['appointment_date', 'appointment_time', 'appointment_id'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'appointment_date';
    }
    
    // Special handling for date/time sorting
    if ($sort_column === 'appointment_date') {
        $order_by = "a.appointment_date $sort_order, a.appointment_time $sort_order";
    } else {
        $order_by = "a.$sort_column $sort_order";
    }
    
    // Fetch all appointments with latest payment information
    $stmt = $db->prepare("
        SELECT a.*, 
               p.pat_first_name, p.pat_last_name, p.pat_phone,
               s.service_name,
               st.status_name, st.status_color,
               up.profile_picture_url as patient_profile_picture,
               pay.payment_status_id,
               ps.status_name as payment_status_name,
               ps.status_color as payment_status_color,
               pay.payment_reference,
               pay.payment_amount
        FROM appointments a
        LEFT JOIN patients p ON a.pat_id = p.pat_id
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
        LEFT JOIN users up ON up.pat_id = p.pat_id
        LEFT JOIN (
            SELECT p1.appointment_id, p1.payment_status_id, p1.payment_reference, p1.payment_amount
            FROM payments p1
            INNER JOIN (
                SELECT appointment_id, MAX(payment_date) as max_date
                FROM payments
                GROUP BY appointment_id
            ) p2 ON p1.appointment_id = p2.appointment_id AND p1.payment_date = p2.max_date
        ) pay ON pay.appointment_id = a.appointment_id
        LEFT JOIN payment_statuses ps ON pay.payment_status_id = ps.payment_status_id
        WHERE a.doc_id = :doctor_id
        ORDER BY $order_by
    ");
    $stmt->execute([
        'doctor_id' => $doctor_id
    ]);
    $all_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Categorize appointments dynamically
    $previous_appointments = [];
    $today_appointments = [];
    $upcoming_appointments = [];
    
    foreach ($all_appointments as $apt) {
        $appointment_date = $apt['appointment_date'];
        
        if ($appointment_date < $today) {
            $previous_appointments[] = $apt;
        } elseif ($appointment_date == $today) {
            $today_appointments[] = $apt;
        } else {
            $upcoming_appointments[] = $apt;
        }
    }
    
    // Sort today's appointments by time
    usort($today_appointments, function($a, $b) {
        return strtotime($a['appointment_time']) - strtotime($b['appointment_time']);
    });
    
    // Get statistics
    $previous_count = count($previous_appointments);
    $today_count = count($today_appointments);
    $upcoming_count = count($upcoming_appointments);
    
} catch (PDOException $e) {
    error_log("Doctor appointments error: " . $e->getMessage());
    $previous_appointments = [];
    $today_appointments = [];
    $upcoming_appointments = [];
    $previous_count = 0;
    $today_count = 0;
    $upcoming_count = 0;
    $error = 'Failed to fetch appointments: ' . $e->getMessage();
}

require_once __DIR__ . '/../../views/doctor/appointments.view.php';

