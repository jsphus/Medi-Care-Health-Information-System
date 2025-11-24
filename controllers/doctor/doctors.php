<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireDoctor();

$db = Database::getInstance();
$doctor_id = $auth->getDoctorId();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Get current doctor info for header
try {
    $stmt = $db->prepare("
        SELECT d.*, s.spec_name 
        FROM doctors d 
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id 
        WHERE d.doc_id = :doctor_id
    ");
    $stmt->execute(['doctor_id' => $doctor_id]);
    $current_doctor = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $current_doctor = null;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $first_name = sanitize($_POST['first_name']);
        $middle_initial = sanitize($_POST['middle_initial'] ?? '');
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        if (!empty($phone)) {
            $phone = formatPhoneNumber($phone);
        }
        $specialization_id = !empty($_POST['specialization_id']) ? (int)$_POST['specialization_id'] : null;
        $license_number = sanitize($_POST['license_number']);
        $experience_years = !empty($_POST['experience_years']) ? (int)$_POST['experience_years'] : null;
        $consultation_fee = !empty($_POST['consultation_fee']) ? floatval($_POST['consultation_fee']) : null;
        $qualification = sanitize($_POST['qualification'] ?? '');
        $bio = sanitize($_POST['bio'] ?? '');
        $status = sanitize($_POST['status'] ?? 'active');
        $password = $_POST['password'] ?? '';
        
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error = 'First name, last name, and email are required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } elseif (empty($password)) {
            $error = 'Password is required';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters';
        } else {
            try {
                // Check if email already exists in users table
                $stmt = $db->prepare("SELECT user_id FROM users WHERE user_email = :email");
                $stmt->execute(['email' => $email]);
                if ($stmt->fetch()) {
                    $error = 'A user account with this email already exists';
                }
                
                if (empty($error)) {
                    // Begin transaction to ensure both doctor and user are created together
                    $db->beginTransaction();
                    
                    try {
                        // Insert doctor
                        $stmt = $db->prepare("
                            INSERT INTO doctors (doc_first_name, doc_middle_initial, doc_last_name, doc_email, doc_phone, doc_specialization_id, 
                                                doc_license_number, doc_experience_years, doc_consultation_fee, 
                                                doc_qualification, doc_bio, doc_status, created_at) 
                            VALUES (:first_name, :middle_initial, :last_name, :email, :phone, :specialization_id, :license_number,
                                   :experience_years, :consultation_fee, :qualification, :bio, :status, NOW())
                        ");
                        $stmt->execute([
                            'first_name' => $first_name,
                            'middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                            'last_name' => $last_name,
                            'email' => $email,
                            'phone' => $phone,
                            'specialization_id' => $specialization_id,
                            'license_number' => $license_number,
                            'experience_years' => $experience_years,
                            'consultation_fee' => $consultation_fee,
                            'qualification' => $qualification,
                            'bio' => $bio,
                            'status' => $status
                        ]);
                        
                        $doc_id = $db->lastInsertId();
                        
                        // Always create user account
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("
                            INSERT INTO users (user_email, user_password, doc_id, user_is_superadmin, created_at) 
                            VALUES (:email, :password, :doc_id, false, NOW())
                        ");
                        $stmt->execute([
                            'email' => $email,
                            'password' => $hashedPassword,
                            'doc_id' => $doc_id
                        ]);
                        
                        // Commit transaction
                        $db->commit();
                        $success = 'Doctor and user account created successfully';
                    } catch (PDOException $e) {
                        // Rollback on error
                        $db->rollBack();
                        throw $e;
                    }
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $first_name = sanitize($_POST['first_name']);
        $middle_initial = sanitize($_POST['middle_initial'] ?? '');
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        if (!empty($phone)) {
            $phone = formatPhoneNumber($phone);
        }
        $specialization_id = !empty($_POST['specialization_id']) ? (int)$_POST['specialization_id'] : null;
        $license_number = sanitize($_POST['license_number']);
        $experience_years = !empty($_POST['experience_years']) ? (int)$_POST['experience_years'] : null;
        $consultation_fee = !empty($_POST['consultation_fee']) ? floatval($_POST['consultation_fee']) : null;
        $qualification = sanitize($_POST['qualification'] ?? '');
        $bio = sanitize($_POST['bio'] ?? '');
        $status = sanitize($_POST['status'] ?? 'active');
        
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error = 'First name, last name, and email are required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } else {
            try {
                $stmt = $db->prepare("
                    UPDATE doctors 
                    SET doc_first_name = :first_name, doc_middle_initial = :middle_initial, doc_last_name = :last_name, doc_email = :email, 
                        doc_phone = :phone, doc_specialization_id = :specialization_id, 
                        doc_license_number = :license_number, doc_experience_years = :experience_years,
                        doc_consultation_fee = :consultation_fee, doc_qualification = :qualification,
                        doc_bio = :bio, doc_status = :status, updated_at = NOW()
                    WHERE doc_id = :id
                ");
                $stmt->execute([
                    'id' => $id,
                    'first_name' => $first_name,
                    'middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone' => $phone,
                    'specialization_id' => $specialization_id,
                    'license_number' => $license_number,
                    'experience_years' => $experience_years,
                    'consultation_fee' => $consultation_fee,
                    'qualification' => $qualification,
                    'bio' => $bio,
                    'status' => $status
                ]);
                $success = 'Doctor updated successfully';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// Handle filters from URL parameters
$filter_name = isset($_GET['filter_name']) ? sanitize($_GET['filter_name']) : '';
$filter_email = isset($_GET['filter_email']) ? sanitize($_GET['filter_email']) : '';
$filter_specialization = isset($_GET['filter_specialization']) ? sanitize($_GET['filter_specialization']) : '';
$filter_status = isset($_GET['filter_status']) ? sanitize($_GET['filter_status']) : '';
$filter_date_from = isset($_GET['filter_date_from']) ? sanitize($_GET['filter_date_from']) : '';
$filter_date_to = isset($_GET['filter_date_to']) ? sanitize($_GET['filter_date_to']) : '';

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = 25;
$offset = ($page - 1) * $items_per_page;

// Handle sorting
$sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'created_at';
$sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
$allowed_columns = ['doc_first_name', 'doc_last_name', 'doc_email', 'doc_phone', 'doc_specialization_id', 'doc_license_number', 'doc_consultation_fee', 'doc_status', 'created_at', 'updated_at'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'created_at';
}

// Build safe ORDER BY clause
$order_by = "d.$sort_column $sort_order";
if ($sort_column !== 'doc_last_name' && $sort_column !== 'doc_first_name') {
    $order_by .= ", d.doc_last_name, d.doc_first_name";
}

// Fetch doctors with filters
try {
    $where_conditions = [];
    $params = [];

    if (!empty($filter_name)) {
        $where_conditions[] = "(LOWER(d.doc_first_name) LIKE LOWER(:filter_name) OR LOWER(d.doc_middle_initial) LIKE LOWER(:filter_name) OR LOWER(d.doc_last_name) LIKE LOWER(:filter_name) OR LOWER(CONCAT(d.doc_first_name, ' ', COALESCE(d.doc_middle_initial, ''), ' ', d.doc_last_name)) LIKE LOWER(:filter_name))";
        $params['filter_name'] = '%' . $filter_name . '%';
    }

    if (!empty($filter_email)) {
        $where_conditions[] = "LOWER(d.doc_email) LIKE LOWER(:filter_email)";
        $params['filter_email'] = '%' . $filter_email . '%';
    }

    if (!empty($filter_specialization)) {
        // Check if it's a numeric ID or a name
        if (is_numeric($filter_specialization)) {
            $where_conditions[] = "d.doc_specialization_id = :filter_specialization";
            $params['filter_specialization'] = (int)$filter_specialization;
        } else {
            $where_conditions[] = "LOWER(s.spec_name) LIKE LOWER(:filter_specialization)";
            $params['filter_specialization'] = '%' . $filter_specialization . '%';
        }
    }

    if (!empty($filter_status)) {
        $where_conditions[] = "LOWER(d.doc_status) = LOWER(:filter_status)";
        $params['filter_status'] = $filter_status;
    }

    if (!empty($filter_date_from)) {
        $where_conditions[] = "DATE(d.created_at) >= :filter_date_from";
        $params['filter_date_from'] = $filter_date_from;
    }

    if (!empty($filter_date_to)) {
        $where_conditions[] = "DATE(d.created_at) <= :filter_date_to";
        $params['filter_date_to'] = $filter_date_to;
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Get total count for pagination
    $count_stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM doctors d
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
        $where_clause
    ");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

    // Fetch paginated results
    $stmt = $db->prepare("
        SELECT d.*, s.spec_name, u.profile_picture_url
        FROM doctors d
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
        LEFT JOIN users u ON u.doc_id = d.doc_id
        $where_clause
        ORDER BY $order_by
        LIMIT :limit OFFSET :offset
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch doctors: ' . $e->getMessage();
    $doctors = [];
    $total_items = 0;
    $total_pages = 0;
}

// Fetch specializations for dropdown
try {
    $specializations = $db->query("SELECT * FROM specializations ORDER BY spec_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $specializations = [];
}

// Calculate useful statistics for summary cards
$stats = [
    'total_doctors' => 0,
    'active_doctors' => 0,
    'doctors_with_schedules_today' => 0,
    'doctors_with_user_accounts' => 0,
    'average_consultation_fee' => 0
];

try {
    $today = date('Y-m-d');
    
    // Total doctors
    $stmt = $db->query("SELECT COUNT(*) as count FROM doctors");
    $stats['total_doctors'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Active doctors
    $stmt = $db->query("SELECT COUNT(*) as count FROM doctors WHERE doc_status = 'active'");
    $stats['active_doctors'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Doctors with schedules today
    $stmt = $db->prepare("
        SELECT COUNT(DISTINCT doc_id) as count 
        FROM schedules 
        WHERE schedule_date = :today
    ");
    $stmt->execute(['today' => $today]);
    $stats['doctors_with_schedules_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Doctors with user accounts
    $stmt = $db->query("
        SELECT COUNT(DISTINCT doc_id) as count 
        FROM users 
        WHERE doc_id IS NOT NULL
    ");
    $stats['doctors_with_user_accounts'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Average consultation fee
    $stmt = $db->query("
        SELECT AVG(doc_consultation_fee) as avg_fee 
        FROM doctors 
        WHERE doc_status = 'active' AND doc_consultation_fee IS NOT NULL AND doc_consultation_fee > 0
    ");
    $avg_fee = $stmt->fetch(PDO::FETCH_ASSOC)['avg_fee'];
    $stats['average_consultation_fee'] = $avg_fee ? round($avg_fee, 2) : 0;
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/doctor/doctors.view.php';
