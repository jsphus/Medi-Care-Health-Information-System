<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/CloudinaryUpload.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireSuperAdmin();

$db = Database::getInstance();
$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        if (!empty($phone)) {
            $phone = formatPhoneNumber($phone);
        }
        $position = sanitize($_POST['position']);
        $hire_date = !empty($_POST['hire_date']) ? $_POST['hire_date'] : null;
        $salary = !empty($_POST['salary']) ? floatval($_POST['salary']) : null;
        $status = sanitize($_POST['status'] ?? 'active');
        $password = $_POST['password'] ?? '';
        $create_user = isset($_POST['create_user']) && $_POST['create_user'] === '1';
        
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error = 'First name, last name, and email are required';
        } elseif ($create_user && empty($password)) {
            $error = 'Password is required when creating user account';
        } elseif ($create_user && strlen($password) < 6) {
            $error = 'Password must be at least 6 characters';
        } else {
            try {
                // Check if email already exists in users table
                if ($create_user) {
                    $stmt = $db->prepare("SELECT user_id FROM users WHERE user_email = :email");
                    $stmt->execute(['email' => $email]);
                    if ($stmt->fetch()) {
                        $error = 'A user account with this email already exists';
                    }
                }
                
                if (empty($error)) {
                    // Insert staff
                    $stmt = $db->prepare("
                        INSERT INTO staff (staff_first_name, staff_last_name, staff_email, staff_phone, staff_position,
                                          staff_hire_date, staff_salary, staff_status, created_at) 
                        VALUES (:first_name, :last_name, :email, :phone, :position, :hire_date, :salary, :status, NOW())
                    ");
                    $stmt->execute([
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'email' => $email,
                        'phone' => $phone,
                        'position' => $position,
                        'hire_date' => $hire_date,
                        'salary' => $salary,
                        'status' => $status
                    ]);
                    
                    $staff_id = $db->lastInsertId();
                    
                    // Create user account if requested
                    if ($create_user) {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("
                            INSERT INTO users (user_email, user_password, staff_id, user_is_superadmin, created_at) 
                            VALUES (:email, :password, :staff_id, false, NOW())
                        ");
                        $stmt->execute([
                            'email' => $email,
                            'password' => $hashedPassword,
                            'staff_id' => $staff_id
                        ]);
                        $success = 'Staff and user account created successfully';
                    } else {
                        $success = 'Staff member created successfully (no user account created)';
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
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        if (!empty($phone)) {
            $phone = formatPhoneNumber($phone);
        }
        $position = sanitize($_POST['position']);
        $hire_date = !empty($_POST['hire_date']) ? $_POST['hire_date'] : null;
        $salary = !empty($_POST['salary']) ? floatval($_POST['salary']) : null;
        $status = sanitize($_POST['status'] ?? 'active');
        
        // Get user_id for profile picture update
        $stmt = $db->prepare("SELECT user_id FROM users WHERE staff_id = :staff_id");
        $stmt->execute(['staff_id' => $id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $userData['user_id'] ?? null;
        
        // Handle profile picture upload/removal
        $profilePictureUrl = null;
        $removeProfilePicture = isset($_POST['remove_profile_picture']) && $_POST['remove_profile_picture'] === '1';
        
        if ($user_id) {
            if ($removeProfilePicture) {
                // Get current profile picture URL
                $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
                $oldUrl = $currentUser['profile_picture_url'] ?? null;
                
                // Delete from Cloudinary if exists
                if ($oldUrl) {
                    try {
                        $cloudinary = new CloudinaryUpload();
                        $oldPublicId = $cloudinary->extractPublicId($oldUrl);
                        if ($oldPublicId) {
                            $cloudinary->deleteImage($oldPublicId);
                        }
                    } catch (Exception $e) {
                        error_log('Failed to delete old profile picture: ' . $e->getMessage());
                    }
                }
                $profilePictureUrl = null;
            } elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                try {
                    $cloudinary = new CloudinaryUpload();
                    $result = $cloudinary->uploadImage($_FILES['profile_picture'], 'profile_pictures', $user_id);
                    
                    if (is_array($result) && isset($result['url'])) {
                        // Get old profile picture URL before updating
                        $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
                        $stmt->execute(['user_id' => $user_id]);
                        $oldUser = $stmt->fetch(PDO::FETCH_ASSOC);
                        $oldUrl = $oldUser['profile_picture_url'] ?? null;
                        
                        // Delete old image from Cloudinary if exists
                        if ($oldUrl) {
                            $oldPublicId = $cloudinary->extractPublicId($oldUrl);
                            if ($oldPublicId) {
                                $cloudinary->deleteImage($oldPublicId);
                            }
                        }
                        
                        $profilePictureUrl = $result['url'];
                    } else {
                        $error = is_string($result) ? $result : 'Failed to upload profile picture';
                    }
                } catch (Exception $e) {
                    $error = 'Failed to upload profile picture: ' . $e->getMessage();
                }
            } else {
                // Keep existing profile picture
                $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
                $profilePictureUrl = $currentUser['profile_picture_url'] ?? null;
            }
            
            // Update profile picture in users table if we have a user_id
            if ($user_id && empty($error)) {
                $stmt = $db->prepare("UPDATE users SET profile_picture_url = :profile_picture_url WHERE user_id = :user_id");
                $stmt->execute(['profile_picture_url' => $profilePictureUrl, 'user_id' => $user_id]);
            }
        }
        
        if (empty($error)) {
            try {
                $stmt = $db->prepare("
                    UPDATE staff 
                    SET staff_first_name = :first_name, staff_last_name = :last_name, staff_email = :email, 
                        staff_phone = :phone, staff_position = :position, staff_hire_date = :hire_date,
                        staff_salary = :salary, staff_status = :status, updated_at = NOW()
                    WHERE staff_id = :id
                ");
                $stmt->execute([
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone' => $phone,
                    'position' => $position,
                    'hire_date' => $hire_date,
                    'salary' => $salary,
                    'status' => $status,
                    'id' => $id
                ]);
                $success = 'Staff member updated successfully';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        try {
            // Begin transaction to ensure all deletions happen together
            $db->beginTransaction();
            
            // Step 1: Delete user account linked to this staff member
            $stmt = $db->prepare("DELETE FROM users WHERE staff_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Step 2: Finally, delete the staff member
            $stmt = $db->prepare("DELETE FROM staff WHERE staff_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Commit the transaction
            $db->commit();
            $success = 'Staff member and associated user account deleted successfully';
        } catch (PDOException $e) {
            // Rollback on error
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $error = 'Failed to delete staff member: ' . $e->getMessage();
        }
    }
}

// Handle search and filters
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

$filter_status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$filter_position = isset($_GET['position']) ? sanitize($_GET['position']) : '';

// Pagination - check if we should load all results (for client-side filtering)
$load_all = isset($_GET['all_results']) && $_GET['all_results'] == '1';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = $load_all ? 10000 : 10; // Load all if filtering, otherwise paginate
$offset = $load_all ? 0 : (($page - 1) * $items_per_page);

// Fetch staff members with filters
try {
    $where_conditions = [];
    $params = [];
    
    if (!empty($search_query)) {
        $where_conditions[] = "(staff_first_name LIKE :search OR staff_last_name LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }
    
    if (!empty($filter_status)) {
        $where_conditions[] = "staff_status = :status";
        $params['status'] = $filter_status;
    }
    
    if (!empty($filter_position)) {
        $where_conditions[] = "staff_position = :position";
        $params['position'] = $filter_position;
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'created_at';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['staff_first_name', 'staff_last_name', 'staff_email', 'staff_phone', 'staff_hire_date', 'created_at'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'created_at';
    }
    
    // Special handling for name sorting (sort by first name, then last name)
    if ($sort_column === 'staff_first_name') {
        $order_by = "staff_first_name $sort_order, staff_last_name $sort_order";
    } else {
        $order_by = "$sort_column $sort_order";
    }
    
    // Get total count for pagination
    $count_stmt = $db->prepare("SELECT COUNT(*) FROM staff $where_clause");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);
    
    // Fetch paginated results
    $stmt = $db->prepare("SELECT * FROM staff $where_clause ORDER BY $order_by LIMIT :limit OFFSET :offset");
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch staff: ' . $e->getMessage();
    $staff = [];
    $total_items = 0;
    $total_pages = 0;
}

// Fetch filter data from database
$filter_positions = [];
try {
    $stmt = $db->query("SELECT DISTINCT staff_position FROM staff WHERE staff_position IS NOT NULL AND staff_position != '' ORDER BY staff_position");
    $filter_positions = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $filter_positions = [];
}

// Calculate statistics for summary cards
$stats = [
    'total_this_month' => 0,
    'pending' => 0,
    'active' => 0,
    'inactive' => 0
];

try {
    // Total staff this month
    $stmt = $db->query("SELECT COUNT(*) as count FROM staff WHERE DATE_TRUNC('month', created_at) = DATE_TRUNC('month', CURRENT_DATE)");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_this_month'] = $result ? (int)$result['count'] : 0;
    
    // Active staff
    $stmt = $db->query("SELECT COUNT(*) as count FROM staff WHERE staff_status = 'active'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['active'] = $result ? (int)$result['count'] : 0;
    
    // Inactive staff
    $stmt = $db->query("SELECT COUNT(*) as count FROM staff WHERE staff_status = 'inactive'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['inactive'] = $result ? (int)$result['count'] : 0;
    
    // Pending (staff without user accounts - can be used as "pending" if needed)
    $stmt = $db->query("SELECT COUNT(*) as count FROM staff s LEFT JOIN users u ON s.staff_id = u.staff_id WHERE u.user_id IS NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['pending'] = $result ? (int)$result['count'] : 0;
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/superadmin/staff.view.php';
