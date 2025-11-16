<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/database.php';
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
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $gender = sanitize($_POST['gender']);
        $address = sanitize($_POST['address']);
        $emergency_contact = sanitize($_POST['emergency_contact'] ?? '');
        $emergency_phone = sanitize($_POST['emergency_phone'] ?? '');
        $medical_history = sanitize($_POST['medical_history'] ?? '');
        $allergies = sanitize($_POST['allergies'] ?? '');
        $insurance_provider = sanitize($_POST['insurance_provider'] ?? '');
        $insurance_number = sanitize($_POST['insurance_number'] ?? '');
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
                    // Insert patient
                    $stmt = $db->prepare("
                        INSERT INTO patients (pat_first_name, pat_last_name, pat_email, pat_phone, pat_date_of_birth, 
                                             pat_gender, pat_address, pat_emergency_contact, pat_emergency_phone,
                                             pat_medical_history, pat_allergies, pat_insurance_provider, 
                                             pat_insurance_number, created_at) 
                        VALUES (:first_name, :last_name, :email, :phone, :date_of_birth, :gender, :address,
                               :emergency_contact, :emergency_phone, :medical_history, :allergies,
                               :insurance_provider, :insurance_number, NOW())
                    ");
                    $stmt->execute([
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'email' => $email,
                        'phone' => $phone,
                        'date_of_birth' => $date_of_birth,
                        'gender' => $gender,
                        'address' => $address,
                        'emergency_contact' => $emergency_contact,
                        'emergency_phone' => $emergency_phone,
                        'medical_history' => $medical_history,
                        'allergies' => $allergies,
                        'insurance_provider' => $insurance_provider,
                        'insurance_number' => $insurance_number
                    ]);
                    
                    $pat_id = $db->lastInsertId();
                    
                    // Create user account if requested
                    if ($create_user) {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("
                            INSERT INTO users (user_email, user_password, pat_id, user_is_superadmin, created_at) 
                            VALUES (:email, :password, :pat_id, false, NOW())
                        ");
                        $stmt->execute([
                            'email' => $email,
                            'password' => $hashedPassword,
                            'pat_id' => $pat_id
                        ]);
                        $success = 'Patient and user account created successfully';
                    } else {
                        $success = 'Patient created successfully (no user account created)';
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
        $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $gender = sanitize($_POST['gender']);
        $address = sanitize($_POST['address']);
        $emergency_contact = sanitize($_POST['emergency_contact'] ?? '');
        $emergency_phone = sanitize($_POST['emergency_phone'] ?? '');
        if (!empty($emergency_phone)) {
            $emergency_phone = formatPhoneNumber($emergency_phone);
        }
        $medical_history = sanitize($_POST['medical_history'] ?? '');
        $allergies = sanitize($_POST['allergies'] ?? '');
        $insurance_provider = sanitize($_POST['insurance_provider'] ?? '');
        $insurance_number = sanitize($_POST['insurance_number'] ?? '');
        
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error = 'First name, last name, and email are required';
        } else {
            // Get user_id for profile picture update
            $stmt = $db->prepare("SELECT user_id FROM users WHERE pat_id = :pat_id");
            $stmt->execute(['pat_id' => $id]);
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
                        UPDATE patients 
                        SET pat_first_name = :first_name, pat_last_name = :last_name, pat_email = :email, 
                            pat_phone = :phone, pat_date_of_birth = :date_of_birth, pat_gender = :gender, 
                            pat_address = :address, pat_emergency_contact = :emergency_contact,
                            pat_emergency_phone = :emergency_phone, pat_medical_history = :medical_history,
                            pat_allergies = :allergies, pat_insurance_provider = :insurance_provider,
                            pat_insurance_number = :insurance_number, updated_at = NOW()
                        WHERE pat_id = :id
                    ");
                    $stmt->execute([
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'email' => $email,
                        'phone' => $phone,
                        'date_of_birth' => $date_of_birth,
                        'gender' => $gender,
                        'address' => $address,
                        'emergency_contact' => $emergency_contact,
                        'emergency_phone' => $emergency_phone,
                        'medical_history' => $medical_history,
                        'allergies' => $allergies,
                        'insurance_provider' => $insurance_provider,
                        'insurance_number' => $insurance_number,
                        'id' => $id
                    ]);
                    
                    // Update profile picture in users table if we have a user_id
                    if ($user_id) {
                        $stmt = $db->prepare("UPDATE users SET profile_picture_url = :profile_picture_url WHERE user_id = :user_id");
                        $stmt->execute(['profile_picture_url' => $profilePictureUrl, 'user_id' => $user_id]);
                    }
                    
                    $success = 'Patient updated successfully';
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
            
            // Step 1: Get all appointment IDs for this patient
            $stmt = $db->prepare("SELECT appointment_id FROM appointments WHERE pat_id = :id");
            $stmt->execute(['id' => $id]);
            $appointments = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Step 2: Delete medical records for this patient (before deleting appointments they reference)
            $stmt = $db->prepare("DELETE FROM medical_records WHERE pat_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Step 3: Delete payments for these appointments
            if (!empty($appointments)) {
                $placeholders = str_repeat('?,', count($appointments) - 1) . '?';
                $stmt = $db->prepare("DELETE FROM payments WHERE appointment_id IN ($placeholders)");
                $stmt->execute($appointments);
            }
            
            // Step 4: Delete appointments for this patient
            $stmt = $db->prepare("DELETE FROM appointments WHERE pat_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Step 5: Delete user account linked to this patient
            $stmt = $db->prepare("DELETE FROM users WHERE pat_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Step 6: Finally, delete the patient
            $stmt = $db->prepare("DELETE FROM patients WHERE pat_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Commit the transaction
            $db->commit();
            $success = 'Patient and all associated records deleted successfully';
        } catch (PDOException $e) {
            // Rollback on error
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $error = 'Failed to delete patient: ' . $e->getMessage();
        }
    }
}

// Handle search and filters
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

$filter_gender = isset($_GET['gender']) ? sanitize($_GET['gender']) : '';
$filter_insurance = isset($_GET['insurance']) ? sanitize($_GET['insurance']) : '';

// Pagination - check if we should load all results (for client-side filtering)
$load_all = isset($_GET['all_results']) && $_GET['all_results'] == '1';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = $load_all ? 10000 : 10; // Load all if filtering, otherwise paginate
$offset = $load_all ? 0 : (($page - 1) * $items_per_page);

// Fetch all patients with filters
try {
    $where_conditions = [];
    $params = [];
    
    if (!empty($search_query)) {
        $where_conditions[] = "(pat_first_name LIKE :search OR pat_last_name LIKE :search)";
        $params['search'] = '%' . $search_query . '%';
    }
    
    if (!empty($filter_gender)) {
        $where_conditions[] = "LOWER(TRIM(pat_gender)) = LOWER(TRIM(:gender))";
        $params['gender'] = $filter_gender;
    }
    
    if (!empty($filter_insurance)) {
        $where_conditions[] = "pat_insurance_provider = :insurance";
        $params['insurance'] = $filter_insurance;
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Get total count for pagination
    $count_stmt = $db->prepare("SELECT COUNT(*) FROM patients $where_clause");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);
    
    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'created_at';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['pat_first_name', 'pat_last_name', 'pat_email', 'pat_phone', 'pat_gender', 'pat_date_of_birth', 'created_at'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'created_at';
    }
    
    // Special handling for name sorting (sort by first name, then last name)
    if ($sort_column === 'pat_first_name') {
        $order_by = "pat_first_name $sort_order, pat_last_name $sort_order";
    } else {
        $order_by = "$sort_column $sort_order";
    }
    
    // Fetch paginated results with profile pictures
    $stmt = $db->prepare("
        SELECT p.*, u.profile_picture_url
        FROM patients p
        LEFT JOIN users u ON p.pat_id = u.pat_id
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
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch patients: ' . $e->getMessage();
    $patients = [];
    $total_items = 0;
    $total_pages = 0;
}

// Fetch filter data from database
$filter_genders = [];
$filter_insurance_providers = [];
try {
    // Get unique genders (normalize to lowercase to avoid duplicates)
    $stmt = $db->query("SELECT DISTINCT LOWER(TRIM(pat_gender)) as gender FROM patients WHERE pat_gender IS NOT NULL AND pat_gender != '' ORDER BY gender");
    $filter_genders = $stmt->fetchAll(PDO::FETCH_COLUMN);
    // Remove any empty values and ensure we have valid genders
    $filter_genders = array_filter($filter_genders, function($g) { return !empty($g); });
    $filter_genders = array_unique($filter_genders); // Remove duplicates
    $filter_genders = array_values($filter_genders); // Re-index array
    
    // Get unique insurance providers
    $stmt = $db->query("SELECT DISTINCT pat_insurance_provider FROM patients WHERE pat_insurance_provider IS NOT NULL AND pat_insurance_provider != '' ORDER BY pat_insurance_provider");
    $filter_insurance_providers = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    // If error, use empty arrays
    $filter_genders = [];
    $filter_insurance_providers = [];
}

// Fetch doctors with profile pictures for doctors cards
$doctors = [];
try {
    $stmt = $db->query("
        SELECT d.*, s.spec_name, u.profile_picture_url
        FROM doctors d
        LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
        LEFT JOIN users u ON d.doc_id = u.doc_id
        WHERE d.doc_status = 'active'
        ORDER BY d.doc_first_name, d.doc_last_name
        LIMIT 12
    ");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Keep empty array if error
    $doctors = [];
}

// Include the view
// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'total_this_month' => 0,
    'active' => 0,
    'inactive' => 0
];

try {
    // Total patients
    $stmt = $db->query("SELECT COUNT(*) as count FROM patients");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total patients this month
    $stmt = $db->query("SELECT COUNT(*) as count FROM patients WHERE DATE_TRUNC('month', created_at) = DATE_TRUNC('month', CURRENT_DATE)");
    $stats['total_this_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Active patients (patients with user accounts)
    $stmt = $db->query("SELECT COUNT(*) as count FROM patients p INNER JOIN users u ON p.pat_id = u.pat_id");
    $stats['active'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Inactive patients (patients without user accounts)
    $stmt = $db->query("SELECT COUNT(*) as count FROM patients p LEFT JOIN users u ON p.pat_id = u.pat_id WHERE u.user_id IS NULL");
    $stats['inactive'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/superadmin/patients.view.php';
