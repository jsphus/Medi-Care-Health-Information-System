<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Appointment.php';

$auth = new Auth();
$auth->requireSuperAdmin();

$db = Database::getInstance();
$appointmentModel = new Appointment();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $appointment_id = sanitize($_POST['appointment_id']);
        $amount = floatval($_POST['amount']);
        $payment_method_id = (int)$_POST['payment_method_id'];
        $payment_status_id = (int)$_POST['payment_status_id'];
        $payment_date = !empty($_POST['payment_date']) ? $_POST['payment_date'] : date('Y-m-d H:i:s');
        $notes = sanitize($_POST['notes'] ?? '');
        
        if (empty($appointment_id) || empty($amount) || empty($payment_method_id) || empty($payment_status_id)) {
            $error = 'Appointment ID, amount, payment method, and status are required';
        } else {
            try {
                // Ensure payment_date is always set - use NOW() if not provided or empty
                if (empty($payment_date)) {
                    $stmt = $db->prepare("
                        INSERT INTO payments (appointment_id, payment_amount, payment_method_id, payment_status_id, 
                                             payment_date, payment_notes, created_at) 
                        VALUES (:appointment_id, :payment_amount, :payment_method_id, :payment_status_id, 
                               NOW(), :payment_notes, NOW())
                    ");
                    $stmt->execute([
                        'appointment_id' => $appointment_id,
                        'payment_amount' => $amount,
                        'payment_method_id' => $payment_method_id,
                        'payment_status_id' => $payment_status_id,
                        'payment_notes' => $notes
                    ]);
                } else {
                    $stmt = $db->prepare("
                        INSERT INTO payments (appointment_id, payment_amount, payment_method_id, payment_status_id, 
                                             payment_date, payment_notes, created_at) 
                        VALUES (:appointment_id, :payment_amount, :payment_method_id, :payment_status_id, 
                               :payment_date, :payment_notes, NOW())
                    ");
                    $stmt->execute([
                        'appointment_id' => $appointment_id,
                        'payment_amount' => $amount,
                        'payment_method_id' => $payment_method_id,
                        'payment_status_id' => $payment_status_id,
                        'payment_date' => $payment_date,
                        'payment_notes' => $notes
                    ]);
                }
                $success = 'Payment record created successfully';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $amount = floatval($_POST['amount']);
        $payment_method_id = (int)$_POST['payment_method_id'];
        $payment_status_id = (int)$_POST['payment_status_id'];
        $payment_date = $_POST['payment_date'];
        $notes = sanitize($_POST['notes'] ?? '');
        
        try {
            $stmt = $db->prepare("
                UPDATE payments 
                SET payment_amount = :payment_amount, payment_method_id = :payment_method_id, 
                    payment_status_id = :payment_status_id, payment_date = :payment_date,
                    payment_notes = :payment_notes, updated_at = NOW()
                WHERE payment_id = :id
            ");
            $stmt->execute([
                'payment_amount' => $amount,
                'payment_method_id' => $payment_method_id,
                'payment_status_id' => $payment_status_id,
                'payment_date' => $payment_date,
                'payment_notes' => $notes,
                'id' => $id
            ]);
            $success = 'Payment record updated successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
    
    if ($action === 'update_status') {
        $id = (int)$_POST['id'];
        $payment_status_id = (int)$_POST['payment_status_id'];
        
        try {
            $stmt = $db->prepare("
                UPDATE payments 
                SET payment_status_id = :payment_status_id, updated_at = NOW()
                WHERE payment_id = :id
            ");
            $stmt->execute([
                'payment_status_id' => $payment_status_id,
                'id' => $id
            ]);
            $success = 'Payment status updated successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        try {
            $stmt = $db->prepare("DELETE FROM payments WHERE payment_id = :id");
            $stmt->execute(['id' => $id]);
            $success = 'Payment record deleted successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Handle AJAX request for appointment details
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_appointment_details') {
    header('Content-Type: application/json');
    $appointment_id = sanitize($_GET['appointment_id'] ?? '');
    
    if (empty($appointment_id)) {
        echo json_encode(['success' => false, 'error' => 'Appointment ID is required']);
        exit;
    }
    
    try {
        $appointment = $appointmentModel->getById($appointment_id);
        if ($appointment) {
            // Also fetch profile pictures
            $patient_user = $db->fetchOne("SELECT profile_picture_url FROM users WHERE pat_id = :pat_id", ['pat_id' => $appointment['pat_id']]);
            $doctor_user = $db->fetchOne("SELECT profile_picture_url FROM users WHERE doc_id = :doc_id", ['doc_id' => $appointment['doc_id']]);
            
            $appointment['patient_profile_picture'] = $patient_user['profile_picture_url'] ?? null;
            $appointment['doctor_profile_picture'] = $doctor_user['profile_picture_url'] ?? null;
            
            echo json_encode(['success' => true, 'data' => $appointment]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Appointment not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// Pagination - check if we should load all results (for client-side filtering)
$load_all = isset($_GET['all_results']) && $_GET['all_results'] == '1';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = $load_all ? 10000 : 25; // Load all if filtering, otherwise paginate
$offset = $load_all ? 0 : (($page - 1) * $items_per_page);

// Fetch all payments with related data
try {
    // Get total count for pagination
    $count_stmt = $db->query("SELECT COUNT(*) FROM payments p");
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);
    
    // Handle sorting - default to showing newest payments first by creation date
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'created_at';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['payment_date', 'payment_amount', 'payment_id', 'created_at'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'created_at';
    }
    
    // Special handling for date sorting - ensure NULL dates are handled and newest appear first
    if ($sort_column === 'payment_date') {
        // Use COALESCE to fall back to created_at if payment_date is NULL
        $order_by = "COALESCE(p.payment_date, p.created_at) $sort_order, p.created_at DESC";
    } elseif ($sort_column === 'created_at') {
        $order_by = "COALESCE(p.created_at, '1970-01-01'::timestamp) $sort_order, p.payment_id DESC";
    } else {
        $order_by = "p.$sort_column $sort_order";
        // For non-date sorts, still add a secondary sort by created_at DESC to show newest first
        $order_by .= ", p.created_at DESC";
    }
    
    // Fetch paginated results - ensure all payments are shown even if JOINs fail
    // Include appointment details with doctor and patient profile pictures
    $stmt = $db->prepare("
        SELECT p.*, 
               a.appointment_id, a.pat_id, a.doc_id, a.service_id, a.status_id,
               a.appointment_date, a.appointment_time, a.appointment_notes,
               pat.pat_first_name, pat.pat_last_name,
               d.doc_first_name, d.doc_last_name,
               srv.service_name, srv.service_price,
               sp.spec_name,
               st.status_name as appointment_status_name, st.status_color as appointment_status_color,
               up.profile_picture_url as patient_profile_picture,
               ud.profile_picture_url as doctor_profile_picture,
               pm.method_name,
               ps.status_name
        FROM payments p
        LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
        LEFT JOIN patients pat ON a.pat_id = pat.pat_id
        LEFT JOIN users up ON up.pat_id = pat.pat_id
        LEFT JOIN doctors d ON a.doc_id = d.doc_id
        LEFT JOIN users ud ON ud.doc_id = d.doc_id
        LEFT JOIN services srv ON a.service_id = srv.service_id
        LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
        LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
        LEFT JOIN payment_methods pm ON p.payment_method_id = pm.method_id
        LEFT JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        ORDER BY $order_by
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch payments: ' . $e->getMessage();
    $payments = [];
    $total_items = 0;
    $total_pages = 0;
}

// Fetch payment methods and statuses for dropdowns
try {
    $payment_methods = $db->query("SELECT * FROM payment_methods WHERE is_active = true ORDER BY method_name")->fetchAll(PDO::FETCH_ASSOC);
    $payment_statuses = $db->query("SELECT * FROM payment_statuses ORDER BY status_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $payment_methods = [];
    $payment_statuses = [];
}

// Calculate statistics for summary cards
$stats = [
    'total_this_month' => 0,
    'paid' => 0,
    'pending' => 0,
    'total_amount' => 0
];

try {
    // Total payments this month - handle NULL payment_date by using COALESCE
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM payments 
        WHERE payment_date IS NOT NULL 
        AND DATE_TRUNC('month', payment_date) = DATE_TRUNC('month', CURRENT_DATE)
    ");
    $stats['total_this_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Paid payments
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM payments p
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE LOWER(ps.status_name) = 'paid'
    ");
    $stats['paid'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending payments
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM payments p
        JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE LOWER(ps.status_name) = 'pending'
    ");
    $stats['pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total amount
    $stmt = $db->query("SELECT COALESCE(SUM(payment_amount), 0) as total FROM payments");
    $stats['total_amount'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    // Keep default values
    error_log("Payment statistics error: " . $e->getMessage());
}

// Fetch recent payments for the card (last 10, ordered by most recent - use payment_id DESC for most recently created)
$recent_payments = [];
try {
    $stmt = $db->prepare("
        SELECT p.*, 
               a.appointment_id, a.pat_id, a.doc_id,
               pat.pat_first_name, pat.pat_last_name,
               up.profile_picture_url as patient_profile_picture,
               pm.method_name,
               ps.status_name
        FROM payments p
        LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
        LEFT JOIN patients pat ON a.pat_id = pat.pat_id
        LEFT JOIN users up ON up.pat_id = pat.pat_id
        LEFT JOIN payment_methods pm ON p.payment_method_id = pm.method_id
        LEFT JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        ORDER BY p.payment_id DESC
        LIMIT 10
    ");
    $stmt->execute();
    $recent_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Recent payments error: " . $e->getMessage());
}

// Fetch pending payments for the card (all pending payments, ordered by most recent)
$pending_payments = [];
try {
    $stmt = $db->prepare("
        SELECT p.*, 
               a.appointment_id, a.pat_id, a.doc_id,
               pat.pat_first_name, pat.pat_last_name,
               up.profile_picture_url as patient_profile_picture,
               pm.method_name,
               ps.status_name
        FROM payments p
        LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
        LEFT JOIN patients pat ON a.pat_id = pat.pat_id
        LEFT JOIN users up ON up.pat_id = pat.pat_id
        LEFT JOIN payment_methods pm ON p.payment_method_id = pm.method_id
        LEFT JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
        WHERE LOWER(ps.status_name) = 'pending'
        ORDER BY p.payment_id DESC
    ");
    $stmt->execute();
    $pending_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Pending payments error: " . $e->getMessage());
}

require_once __DIR__ . '/../../views/superadmin/payments.view.php';
