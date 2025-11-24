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
        $middle_initial = sanitize($_POST['middle_initial'] ?? '');
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        if (!empty($phone)) {
            $phone = formatPhoneNumber($phone);
        }
        $position = sanitize($_POST['position']);
        $salary = !empty($_POST['salary']) ? floatval($_POST['salary']) : null;
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
                    // Begin transaction to ensure both staff and user are created together
                    $db->beginTransaction();
                    
                    try {
                        // Insert staff
                        // Use PHP's date() to ensure correct timezone (Asia/Manila from config.php)
                        $current_timestamp = date('Y-m-d H:i:s');
                        $stmt = $db->prepare("
                            INSERT INTO staff (staff_first_name, staff_middle_initial, staff_last_name, staff_email, staff_phone, staff_position,
                                              staff_salary, created_at) 
                            VALUES (:first_name, :middle_initial, :last_name, :email, :phone, :position, :salary, :created_at)
                        ");
                        $stmt->execute([
                            'first_name' => $first_name,
                            'middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                            'last_name' => $last_name,
                            'email' => $email,
                            'phone' => $phone,
                            'position' => $position,
                            'salary' => $salary,
                            'created_at' => $current_timestamp
                        ]);
                        
                        $staff_id = $db->lastInsertId();
                        
                        // Always create user account
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
                        
                        // Commit transaction
                        $db->commit();
                        $success = 'Staff and user account created successfully';
                    } catch (PDOException $e) {
                        // Rollback on error
                        $db->rollBack();
                        throw $e;
                    }
                    
                    // Redirect to prevent form resubmission and refresh the data
                    // Preserve filters but reset to page 1 and ensure sort by created_at DESC so new staff appears first
                    $redirect_params = $_GET;
                    $redirect_params['success'] = $success;
                    $redirect_params['page'] = 1;
                    $redirect_params['sort'] = 'created_at';
                    $redirect_params['order'] = 'DESC';
                    header('Location: /superadmin/staff?' . http_build_query($redirect_params));
                    exit;
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
        $position = sanitize($_POST['position']);
        $salary = !empty($_POST['salary']) ? floatval($_POST['salary']) : null;
        
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
                    SET staff_first_name = :first_name, staff_middle_initial = :middle_initial, staff_last_name = :last_name, staff_email = :email, 
                        staff_phone = :phone, staff_position = :position,
                        staff_salary = :salary, updated_at = NOW()
                    WHERE staff_id = :id
                ");
                $stmt->execute([
                    'first_name' => $first_name,
                    'middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone' => $phone,
                    'position' => $position,
                    'salary' => $salary,
                    'id' => $id
                ]);
                $success = 'Staff member updated successfully';
                
                // Redirect to prevent form resubmission and refresh the data
                // Preserve filters and current page
                $redirect_params = $_GET;
                $redirect_params['success'] = $success;
                header('Location: /superadmin/staff?' . http_build_query($redirect_params));
                exit;
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
            
            // Redirect to prevent form resubmission and refresh the data
            // Preserve filters but reset to page 1
            $redirect_params = $_GET;
            $redirect_params['success'] = $success;
            $redirect_params['page'] = 1;
            header('Location: /superadmin/staff?' . http_build_query($redirect_params));
            exit;
        } catch (PDOException $e) {
            // Rollback on error
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $error = 'Failed to delete staff member: ' . $e->getMessage();
        }
    }
}

// Handle success message from URL parameter (after redirect)
if (isset($_GET['success']) && !empty($_GET['success'])) {
    $success = sanitize($_GET['success']);
}

// Handle filters from URL
$filter_name = isset($_GET['filter_name']) ? sanitize($_GET['filter_name']) : '';
$filter_email = isset($_GET['filter_email']) ? sanitize($_GET['filter_email']) : '';
$filter_phone = isset($_GET['filter_phone']) ? sanitize($_GET['filter_phone']) : '';
$filter_status = isset($_GET['filter_status']) ? sanitize($_GET['filter_status']) : '';
$filter_position = isset($_GET['filter_position']) ? sanitize($_GET['filter_position']) : '';
$filter_date_from = isset($_GET['filter_date_from']) ? sanitize($_GET['filter_date_from']) : '';
$filter_date_to = isset($_GET['filter_date_to']) ? sanitize($_GET['filter_date_to']) : '';

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = 25;
$offset = (($page - 1) * $items_per_page);

// Fetch staff members with filters
try {
    $where_conditions = [];
    $params = [];
    
    if (!empty($filter_name)) {
        // Case-insensitive search in first name, last name, middle initial, and concatenated full name
        $where_conditions[] = "(LOWER(s.staff_first_name) LIKE :name OR LOWER(s.staff_last_name) LIKE :name OR LOWER(s.staff_middle_initial) LIKE :name OR LOWER(TRIM(CONCAT(COALESCE(s.staff_first_name, ''), ' ', COALESCE(s.staff_middle_initial, ''), ' ', COALESCE(s.staff_last_name, '')))) LIKE :name)";
        $params['name'] = '%' . strtolower($filter_name) . '%';
    }
    
    if (!empty($filter_email)) {
        // Case-insensitive email search with trimmed input
        $email_clean = trim(strtolower($filter_email));
        $where_conditions[] = "LOWER(TRIM(s.staff_email)) LIKE :email";
        $params['email'] = '%' . $email_clean . '%';
    }
    
    if (!empty($filter_phone)) {
        // Remove non-numeric characters for phone search to make it more flexible
        $phone_clean = preg_replace('/[^0-9]/', '', $filter_phone);
        $where_conditions[] = "REPLACE(REPLACE(REPLACE(REPLACE(s.staff_phone, ' ', ''), '-', ''), '(', ''), ')', '') LIKE :phone";
        $params['phone'] = '%' . $phone_clean . '%';
    }
    
    if (!empty($filter_position)) {
        $where_conditions[] = "s.staff_position = :position";
        $params['position'] = $filter_position;
    }
    
    if (!empty($filter_date_from)) {
        $where_conditions[] = "DATE(s.created_at) >= :date_from";
        $params['date_from'] = $filter_date_from;
    }
    
    if (!empty($filter_date_to)) {
        $where_conditions[] = "DATE(s.created_at) <= :date_to";
        $params['date_to'] = $filter_date_to;
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'created_at';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['staff_first_name', 'staff_last_name', 'staff_email', 'staff_phone', 'created_at', 'updated_at'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'created_at';
    }
    
    // Special handling for name sorting (sort by first name, then last name)
    if ($sort_column === 'staff_first_name') {
        $order_by = "s.staff_first_name $sort_order, s.staff_last_name $sort_order";
    } else {
        // For created_at, order by full timestamp (date and time) with staff_id as tiebreaker
        // This ensures most recent staff (by creation timestamp, then by ID) appear first
        if ($sort_column === 'created_at') {
            // Order by full timestamp including time, with staff_id DESC as secondary sort for records with same timestamp
            $order_by = "s.created_at $sort_order NULLS LAST, s.staff_id DESC";
        } else {
            $order_by = "s.$sort_column $sort_order";
        }
    }
    
    // Get total count for pagination
    $count_stmt = $db->prepare("SELECT COUNT(*) FROM staff s $where_clause");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);
    
    // Fetch paginated results with profile pictures
    $stmt = $db->prepare("
        SELECT s.*, u.profile_picture_url
        FROM staff s
        LEFT JOIN users u ON s.staff_id = u.staff_id
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
    
    // Active staff (all staff are considered active)
    $stmt = $db->query("SELECT COUNT(*) as count FROM staff");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['active'] = $result ? (int)$result['count'] : 0;
    
    // Inactive staff (set to 0 since we removed status)
    $stats['inactive'] = 0;
    
    // Pending (staff without user accounts - can be used as "pending" if needed)
    $stmt = $db->query("SELECT COUNT(*) as count FROM staff s LEFT JOIN users u ON s.staff_id = u.staff_id WHERE u.user_id IS NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['pending'] = $result ? (int)$result['count'] : 0;
} catch (PDOException $e) {
    // Keep default values
}

// Fetch recently added staff
$recently_added_staff = [];
try {
    $stmt = $db->query("
        SELECT 
            s.staff_id,
            s.staff_first_name,
            s.staff_middle_initial,
            s.staff_last_name,
            s.staff_email,
            s.staff_phone,
            s.staff_position,
            s.staff_salary,
            s.created_at,
            u.profile_picture_url
        FROM staff s
        LEFT JOIN users u ON s.staff_id = u.staff_id
        ORDER BY s.created_at DESC NULLS LAST, s.staff_id DESC
        LIMIT 10
    ");
    $recently_added_staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Keep empty array if error
    $recently_added_staff = [];
}

require_once __DIR__ . '/../../views/superadmin/staff.view.php';
