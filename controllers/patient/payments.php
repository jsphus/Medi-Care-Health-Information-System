<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requirePatient();

$db = Database::getInstance();
$patient_id = $auth->getPatientId();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = initializeProfilePicture($auth, $db);

// Handle payment creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_payment') {
    $appointment_id = sanitize($_POST['appointment_id'] ?? '');
    $payment_method_id = isset($_POST['payment_method_id']) ? (int)$_POST['payment_method_id'] : 0;
    $payment_reference = sanitize($_POST['payment_reference'] ?? '');
    $payment_notes = sanitize($_POST['payment_notes'] ?? '');
    
    if (empty($appointment_id) || empty($payment_method_id)) {
        $error = 'Appointment and payment method are required';
    } else {
        try {
            // Verify appointment belongs to this patient
            $stmt = $db->prepare("
                SELECT a.*, s.service_price, d.doc_consultation_fee
                FROM appointments a
                LEFT JOIN services s ON a.service_id = s.service_id
                LEFT JOIN doctors d ON a.doc_id = d.doc_id
                WHERE a.appointment_id = :appointment_id AND a.pat_id = :patient_id
            ");
            $stmt->execute(['appointment_id' => $appointment_id, 'patient_id' => $patient_id]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$appointment) {
                $error = 'Appointment not found or does not belong to you';
            } else {
                // Check if payment already exists for this appointment
                $stmt = $db->prepare("SELECT payment_id FROM payments WHERE appointment_id = :appointment_id");
                $stmt->execute(['appointment_id' => $appointment_id]);
                if ($stmt->fetch()) {
                    $error = 'Payment already exists for this appointment';
                } else {
                    // Calculate payment amount (use service price or doctor consultation fee)
                    $payment_amount = 0;
                    if (!empty($appointment['service_price'])) {
                        $payment_amount = floatval($appointment['service_price']);
                    } elseif (!empty($appointment['doc_consultation_fee'])) {
                        $payment_amount = floatval($appointment['doc_consultation_fee']);
                    } else {
                        $error = 'Cannot determine payment amount. Please contact the clinic.';
                    }
                    
                    if (empty($error) && $payment_amount > 0) {
                        // Get pending status ID
                        $stmt = $db->prepare("SELECT payment_status_id FROM payment_statuses WHERE LOWER(status_name) = 'pending' LIMIT 1");
                        $stmt->execute();
                        $pending_status = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($pending_status) {
                            // Create payment record
                            $stmt = $db->prepare("
                                INSERT INTO payments (appointment_id, payment_amount, payment_method_id, payment_status_id, 
                                                     payment_date, payment_reference, payment_notes, created_at) 
                                VALUES (:appointment_id, :payment_amount, :payment_method_id, :payment_status_id, 
                                       NOW(), :payment_reference, :payment_notes, NOW())
                            ");
                            $stmt->execute([
                                'appointment_id' => $appointment_id,
                                'payment_amount' => $payment_amount,
                                'payment_method_id' => $payment_method_id,
                                'payment_status_id' => $pending_status['payment_status_id'],
                                'payment_reference' => $payment_reference ?: null,
                                'payment_notes' => $payment_notes ?: null
                            ]);
                            $success = 'Payment submitted successfully! It will be reviewed and confirmed by the clinic.';
                            // Redirect to prevent form resubmission
                            header('Location: /patient/payments?success=created');
                            exit;
                        } else {
                            $error = 'Payment status not found in system';
                        }
                    }
                }
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Check for success message from redirect
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'created') {
        $success = 'Payment submitted successfully! It will be reviewed and confirmed by the clinic.';
    }
}

// Handle search
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

// Get all payments for this patient
try {
    $where_conditions = ['a.pat_id = :patient_id'];
    $params = ['patient_id' => $patient_id];

    if (!empty($search_query)) {
        $where_conditions[] = "(p.payment_id LIKE :search OR a.appointment_id LIKE :search OR pm.method_name LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }

    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

    $stmt = $db->prepare("
        SELECT p.*, 
               a.appointment_id, a.appointment_date, a.appointment_time,
               pm.method_name,
               ps.status_name, ps.status_color
        FROM payments p
        LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
        LEFT JOIN payment_methods pm ON p.payment_method_id = pm.method_id
        LEFT JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        $where_clause
        ORDER BY p.payment_date DESC
    ");
    $stmt->execute($params);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch payments: ' . $e->getMessage();
    $payments = [];
}

// Calculate totals
$total_paid = 0;
$total_pending = 0;
foreach ($payments as $payment) {
    if (strtolower($payment['status_name']) === 'paid') {
        $total_paid += $payment['payment_amount'];
    } elseif (strtolower($payment['status_name']) === 'pending') {
        $total_pending += $payment['payment_amount'];
    }
}

// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'paid' => 0,
    'pending' => 0,
    'total_amount' => 0
];

try {
    // Total payments for this patient
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM payments p JOIN appointments a ON p.appointment_id = a.appointment_id WHERE a.pat_id = :patient_id");
    $stmt->execute(['patient_id' => $patient_id]);
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Paid payments
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM payments p
        JOIN appointments a ON p.appointment_id = a.appointment_id
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE a.pat_id = :patient_id AND LOWER(ps.status_name) = 'paid'
    ");
    $stmt->execute(['patient_id' => $patient_id]);
    $stats['paid'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending payments
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM payments p
        JOIN appointments a ON p.appointment_id = a.appointment_id
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE a.pat_id = :patient_id AND LOWER(ps.status_name) = 'pending'
    ");
    $stmt->execute(['patient_id' => $patient_id]);
    $stats['pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total amount
    $stmt = $db->prepare("
        SELECT COALESCE(SUM(p.payment_amount), 0) as total 
        FROM payments p
        JOIN appointments a ON p.appointment_id = a.appointment_id
        WHERE a.pat_id = :patient_id
    ");
    $stmt->execute(['patient_id' => $patient_id]);
    $stats['total_amount'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    // Keep default values
}

// Get unpaid appointments (appointments without payments)
$unpaid_appointments = [];
try {
    $stmt = $db->prepare("
        SELECT a.*, 
               s.service_name, s.service_price,
               d.doc_first_name, d.doc_last_name, d.doc_consultation_fee,
               st.status_name
        FROM appointments a
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN doctors d ON a.doc_id = d.doc_id
        LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
        WHERE a.pat_id = :patient_id
          AND a.appointment_id NOT IN (SELECT appointment_id FROM payments WHERE appointment_id IS NOT NULL)
          AND LOWER(st.status_name) != 'cancelled'
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $stmt->execute(['patient_id' => $patient_id]);
    $unpaid_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Keep empty array
}

// Fetch payment methods for dropdown
try {
    $payment_methods = $db->query("SELECT * FROM payment_methods WHERE is_active = true ORDER BY method_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $payment_methods = [];
}

require_once __DIR__ . '/../../views/patient/payments.view.php';

