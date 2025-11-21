<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/CloudinaryUpload.php';

$auth = new Auth();
$auth->requireSuperAdmin();

$db = Database::getInstance();
$error = '';
$success = '';

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
        $create_user = isset($_POST['create_user']) && $_POST['create_user'] === '1';
        
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error = 'First name, last name, and email are required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
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
                    
                    // Create user account if requested
                    if ($create_user) {
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
                        $success = 'Doctor and user account created successfully';
                    } else {
                        $success = 'Doctor created successfully (no user account created)';
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
        } else {
            // Get user_id for profile picture update
            $stmt = $db->prepare("SELECT user_id FROM users WHERE doc_id = :doc_id");
            $stmt->execute(['doc_id' => $id]);
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
            }
            
            if (empty($error)) {
                try {
                    $stmt = $db->prepare("
                        UPDATE doctors 
                        SET doc_first_name = :first_name, doc_middle_initial = :middle_initial, doc_last_name = :last_name, doc_email = :email, 
                            doc_phone = :phone, doc_specialization_id = :specialization_id, doc_license_number = :license_number,
                            doc_experience_years = :experience_years, doc_consultation_fee = :consultation_fee,
                            doc_qualification = :qualification, doc_bio = :bio, doc_status = :status, updated_at = NOW()
                        WHERE doc_id = :id
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
                        'status' => $status,
                        'id' => $id
                    ]);
                    
                    // Update profile picture in users table if we have a user_id
                    if ($user_id) {
                        $stmt = $db->prepare("UPDATE users SET profile_picture_url = :profile_picture_url WHERE user_id = :user_id");
                        $stmt->execute(['profile_picture_url' => $profilePictureUrl, 'user_id' => $user_id]);
                    }
                    
                    $success = 'Doctor updated successfully';
                } catch (PDOException $e) {
                    $error = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        
        try {
            // Begin transaction to ensure all deletions happen together
            $db->beginTransaction();
            
            // Step 1: Get all appointment IDs for this doctor
            $stmt = $db->prepare("SELECT appointment_id FROM appointments WHERE doc_id = :id");
            $stmt->execute(['id' => $id]);
            $appointments = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Step 2: Delete medical records for this doctor
            $stmt = $db->prepare("DELETE FROM medical_records WHERE doc_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Step 3: Delete payments for these appointments
            if (!empty($appointments)) {
                $placeholders = str_repeat('?,', count($appointments) - 1) . '?';
                $stmt = $db->prepare("DELETE FROM payments WHERE appointment_id IN ($placeholders)");
                $stmt->execute($appointments);
            }
            
            // Step 4: Delete appointments for this doctor
            $stmt = $db->prepare("DELETE FROM appointments WHERE doc_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Step 5: Delete schedules for this doctor
            $stmt = $db->prepare("DELETE FROM schedules WHERE doc_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Step 6: Delete user account linked to this doctor
            $stmt = $db->prepare("DELETE FROM users WHERE doc_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Step 7: Finally, delete the doctor
            $stmt = $db->prepare("DELETE FROM doctors WHERE doc_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Commit the transaction
            $db->commit();
            $success = 'Doctor and all associated records deleted successfully';
        } catch (PDOException $e) {
            // Rollback on error
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $error = 'Failed to delete doctor: ' . $e->getMessage();
        }
    }
}

// Handle filters
$spec_filter = isset($_GET['spec_id']) ? (int)$_GET['spec_id'] : null;
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$spec_name_filter = '';

// Pagination - check if we should load all results (for client-side filtering)
$load_all = isset($_GET['all_results']) && $_GET['all_results'] == '1';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = $load_all ? 10000 : 10; // Load all if filtering, otherwise paginate
$offset = $load_all ? 0 : (($page - 1) * $items_per_page);

// Fetch all doctors with filters
try {
    $where_conditions = [];
    $params = [];
    
    if ($spec_filter) {
        $where_conditions[] = "d.doc_specialization_id = :spec_id";
        $params['spec_id'] = $spec_filter;
        
        // Get specialization name for display
        $stmt = $db->prepare("SELECT spec_name FROM specializations WHERE spec_id = :spec_id");
        $stmt->execute(['spec_id' => $spec_filter]);
        $spec_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $spec_name_filter = $spec_data ? $spec_data['spec_name'] : '';
    }
    
    if (!empty($status_filter)) {
        $where_conditions[] = "d.doc_status = :status";
        $params['status'] = $status_filter;
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Get total count for pagination
    $count_stmt = $db->prepare("SELECT COUNT(*) FROM doctors d $where_clause");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);
    
    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'created_at';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['doc_first_name', 'doc_last_name', 'doc_email', 'doc_phone', 'doc_specialization_id', 'doc_license_number', 'doc_consultation_fee', 'doc_status', 'created_at', 'updated_at'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'created_at';
    }
    
    // Special handling for name sorting
    if ($sort_column === 'doc_first_name') {
        $order_by = "doc_first_name $sort_order, doc_last_name $sort_order";
    } else {
        $order_by = "d.$sort_column $sort_order";
    }
    
    // Fetch paginated results with profile pictures
    $stmt = $db->prepare("
        SELECT d.*, s.spec_name, u.profile_picture_url
        FROM doctors d
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
        LEFT JOIN users u ON d.doc_id = u.doc_id
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
    $stmt = $db->query("SELECT spec_id, spec_name FROM specializations ORDER BY spec_name");
    $specializations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $specializations = [];
}

// Fetch most active doctors (doctors with most appointments)
$most_active_doctors = [];
try {
    $stmt = $db->query("
        SELECT 
            d.doc_id,
            d.doc_first_name,
            d.doc_middle_initial,
            d.doc_last_name,
            d.doc_email,
            s.spec_name,
            u.profile_picture_url,
            COUNT(a.appointment_id) as appointment_count
        FROM doctors d
        INNER JOIN appointments a ON d.doc_id = a.doc_id
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
        LEFT JOIN users u ON d.doc_id = u.doc_id
        GROUP BY d.doc_id, d.doc_first_name, d.doc_middle_initial, d.doc_last_name, d.doc_email, s.spec_name, u.profile_picture_url
        ORDER BY appointment_count DESC
        LIMIT 10
    ");
    $most_active_doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Keep empty array if error
    $most_active_doctors = [];
}

// Include the view
// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'active' => 0,
    'inactive' => 0
];

try {
    // Total doctors
    $stmt = $db->query("SELECT COUNT(*) as count FROM doctors");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Active doctors
    $stmt = $db->query("SELECT COUNT(*) as count FROM doctors WHERE doc_status = 'active'");
    $stats['active'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Inactive doctors
    $stmt = $db->query("SELECT COUNT(*) as count FROM doctors WHERE doc_status = 'inactive'");
    $stats['inactive'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/superadmin/doctors.view.php';
