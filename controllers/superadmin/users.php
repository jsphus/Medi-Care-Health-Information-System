<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/CloudinaryUpload.php';
require_once __DIR__ . '/../../classes/Patient.php';
require_once __DIR__ . '/../../classes/Staff.php';
require_once __DIR__ . '/../../classes/Doctor.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireSuperAdmin();

$db = Database::getInstance();
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $is_superadmin = isset($_POST['is_superadmin']) && $_POST['is_superadmin'] === '1';
        
        if (empty($email) || empty($password)) {
            $error = 'Email and password are required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } else {
            try {
                // Check if email already exists
                $stmt = $db->prepare("SELECT user_id FROM users WHERE user_email = :email");
                $stmt->execute(['email' => $email]);
                
                if ($stmt->fetch()) {
                    $error = 'Email already exists';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("
                        INSERT INTO users (user_email, user_password, user_is_superadmin, created_at) 
                        VALUES (:email, :password, :is_superadmin, NOW())
                    ");
                    $stmt->execute([
                        'email' => $email,
                        'password' => $hashedPassword,
                        'is_superadmin' => $is_superadmin ? 1 : 0
                    ]);
                    $success = 'User created successfully';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $email = sanitize($_POST['email']);
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'none';
        $role_id = isset($_POST['role_id']) && $_POST['role_id'] !== '' ? (int)$_POST['role_id'] : null;
        
        if (empty($email)) {
            $error = 'Email is required';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } else {
            try {
                // Get current user data to preserve existing role associations
                $stmt = $db->prepare("SELECT user_email, user_is_superadmin, pat_id, staff_id, doc_id FROM users WHERE user_id = :id");
                $stmt->execute(['id' => $id]);
                $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Check if email is being changed and if it already exists in users table
                if ($email !== $currentUser['user_email']) {
                    $existingUserEmail = $db->fetchOne(
                        "SELECT user_id FROM users WHERE user_email = :email AND user_id != :id",
                        ['email' => $email, 'id' => $id]
                    );
                    if ($existingUserEmail) {
                        $error = 'Email already exists for another user.';
                    }
                }
                
                if (!$currentUser) {
                    $error = 'User not found';
                } else {
                    // Determine role flags - preserve existing IDs if role is kept the same and no new role_id provided
                    $is_superadmin = ($role === 'superadmin') ? true : false;
                    
                    // If role is being changed, use the new role_id, otherwise preserve existing
                    if ($role === 'patient') {
                        $pat_id = $role_id ? $role_id : ($currentUser['pat_id'] ?? null);
                        $staff_id = null;
                        $doc_id = null;
                    } elseif ($role === 'staff') {
                        $staff_id = $role_id ? $role_id : ($currentUser['staff_id'] ?? null);
                        $pat_id = null;
                        $doc_id = null;
                    } elseif ($role === 'doctor') {
                        $doc_id = $role_id ? $role_id : ($currentUser['doc_id'] ?? null);
                        $pat_id = null;
                        $staff_id = null;
                    } elseif ($role === 'superadmin') {
                        $pat_id = null;
                        $staff_id = null;
                        $doc_id = null;
                    } else { // 'none'
                        $pat_id = null;
                        $staff_id = null;
                        $doc_id = null;
                    }
                    
                    // Validate role_id exists if linking to a profile (only if a new role_id is provided)
                    if ($role !== 'superadmin' && $role !== 'none') {
                        // Only validate if a new role_id is being set, or if we're preserving an existing one
                        $profile_id_to_check = null;
                        if ($role === 'staff' && $staff_id) {
                            $profile_id_to_check = $staff_id;
                        } elseif ($role === 'doctor' && $doc_id) {
                            $profile_id_to_check = $doc_id;
                        } elseif ($role === 'patient' && $pat_id) {
                            $profile_id_to_check = $pat_id;
                        }
                        
                        // If we have a profile ID to check, verify it exists
                        if ($profile_id_to_check) {
                            if ($role === 'staff') {
                                $stmt = $db->prepare("SELECT staff_id FROM staff WHERE staff_id = :id");
                                $stmt->execute(['id' => $profile_id_to_check]);
                                if (!$stmt->fetch()) {
                                    $error = 'Staff ID does not exist';
                                }
                            } elseif ($role === 'doctor') {
                                $stmt = $db->prepare("SELECT doc_id FROM doctors WHERE doc_id = :id");
                                $stmt->execute(['id' => $profile_id_to_check]);
                                if (!$stmt->fetch()) {
                                    $error = 'Doctor ID does not exist';
                                }
                            } elseif ($role === 'patient') {
                                $stmt = $db->prepare("SELECT pat_id FROM patients WHERE pat_id = :id");
                                $stmt->execute(['id' => $profile_id_to_check]);
                                if (!$stmt->fetch()) {
                                    $error = 'Patient ID does not exist';
                                }
                            }
                        } elseif (!$role_id && !$currentUser['pat_id'] && !$currentUser['staff_id'] && !$currentUser['doc_id']) {
                            // Only require role_id if user doesn't already have a profile linked
                            $error = 'Profile ID is required when assigning Staff, Doctor, or Patient role';
                        }
                    }
                    
                    if (empty($error)) {
                        // Handle profile picture upload/removal
                        $profilePictureUrl = null;
                        $removeProfilePicture = isset($_POST['remove_profile_picture']) && $_POST['remove_profile_picture'] === '1';
                        
                        if ($removeProfilePicture) {
                            // Get current profile picture URL
                            $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :id");
                            $stmt->execute(['id' => $id]);
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
                                    // Log error but don't fail the update
                                    error_log('Failed to delete old profile picture: ' . $e->getMessage());
                                }
                            }
                            $profilePictureUrl = null;
                        } elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                            try {
                                $cloudinary = new CloudinaryUpload();
                                $result = $cloudinary->uploadImage($_FILES['profile_picture'], 'profile_pictures', $id);
                                
                                if (is_array($result) && isset($result['url'])) {
                                    // Get old profile picture URL before updating
                                    $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :id");
                                    $stmt->execute(['id' => $id]);
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
                            $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :id");
                            $stmt->execute(['id' => $id]);
                            $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
                            $profilePictureUrl = $currentUser['profile_picture_url'] ?? null;
                        }
                        
                        if (empty($error)) {
                            try {
                                // Update user account
                                if (!empty($password)) {
                                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                                $stmt = $db->prepare("
                                    UPDATE users 
                                    SET user_email = :email, 
                                        user_password = :password, 
                                        user_is_superadmin = :is_superadmin,
                                        pat_id = :pat_id,
                                        staff_id = :staff_id,
                                        doc_id = :doc_id,
                                        profile_picture_url = :profile_picture_url,
                                        updated_at = NOW()
                                    WHERE user_id = :id
                                ");
                                $stmt->execute([
                                    'email' => $email,
                                    'password' => $hashedPassword,
                                    'is_superadmin' => $is_superadmin ? 1 : 0,
                                    'pat_id' => $pat_id,
                                    'staff_id' => $staff_id,
                                    'doc_id' => $doc_id,
                                    'profile_picture_url' => $profilePictureUrl,
                                    'id' => $id
                                ]);
                                } else {
                                $stmt = $db->prepare("
                                    UPDATE users 
                                    SET user_email = :email, 
                                        user_is_superadmin = :is_superadmin,
                                        pat_id = :pat_id,
                                        staff_id = :staff_id,
                                        doc_id = :doc_id,
                                        profile_picture_url = :profile_picture_url,
                                        updated_at = NOW()
                                    WHERE user_id = :id
                                ");
                                $stmt->execute([
                                    'email' => $email,
                                    'is_superadmin' => $is_superadmin ? 1 : 0,
                                    'pat_id' => $pat_id,
                                    'staff_id' => $staff_id,
                                    'doc_id' => $doc_id,
                                    'profile_picture_url' => $profilePictureUrl,
                                    'id' => $id
                                ]);
                                }
                            
                            // Update profile based on role
                            if ($role === 'patient' && $pat_id) {
                                // Fetch existing patient data using Patient class
                                $existingPatient = Patient::findById($pat_id);
                                
                                if (!$existingPatient) {
                                    $error = 'Patient profile not found';
                                } else {
                                    // Check if email is being changed and if it already exists in patients table
                                    // Only check if the email is different from the current patient email
                                    if ($email !== $existingPatient['pat_email']) {
                                        $existingEmail = $db->fetchOne(
                                            "SELECT pat_id FROM patients WHERE pat_email = :email AND pat_id != :id",
                                            ['email' => $email, 'id' => $pat_id]
                                        );
                                        if ($existingEmail) {
                                            $error = 'Email already exists for another patient.';
                                        }
                                    }
                                    // If email hasn't changed, no need to check
                                    
                                    if (empty($error)) {
                                        // Always use POST values if they exist (even if empty), otherwise preserve existing values
                                        // This ensures that when user fills in fields, they are saved
                                        $updateData = [
                                            'pat_id' => $pat_id,
                                            'pat_email' => $email,
                                            'pat_first_name' => isset($_POST['pat_first_name']) ? sanitize($_POST['pat_first_name']) : $existingPatient['pat_first_name'],
                                            'pat_middle_initial' => isset($_POST['pat_middle_initial']) ? (!empty($_POST['pat_middle_initial']) ? strtoupper(substr(sanitize($_POST['pat_middle_initial']), 0, 1)) : null) : ($existingPatient['pat_middle_initial'] ?? null),
                                            'pat_last_name' => isset($_POST['pat_last_name']) ? sanitize($_POST['pat_last_name']) : $existingPatient['pat_last_name'],
                                            'pat_phone' => isset($_POST['pat_phone']) ? sanitize($_POST['pat_phone']) : $existingPatient['pat_phone'],
                                            'pat_date_of_birth' => isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : $existingPatient['pat_date_of_birth'],
                                            'pat_gender' => isset($_POST['gender']) ? sanitize($_POST['gender']) : $existingPatient['pat_gender'],
                                            'pat_address' => isset($_POST['address']) ? sanitize($_POST['address']) : $existingPatient['pat_address'],
                                            'pat_emergency_contact' => isset($_POST['emergency_contact']) ? sanitize($_POST['emergency_contact']) : $existingPatient['pat_emergency_contact'],
                                            'pat_emergency_phone' => isset($_POST['emergency_phone']) ? sanitize($_POST['emergency_phone']) : $existingPatient['pat_emergency_phone'],
                                            'pat_medical_history' => isset($_POST['medical_history']) ? sanitize($_POST['medical_history']) : $existingPatient['pat_medical_history'],
                                            'pat_allergies' => isset($_POST['allergies']) ? sanitize($_POST['allergies']) : $existingPatient['pat_allergies'],
                                            'pat_insurance_provider' => isset($_POST['insurance_provider']) ? sanitize($_POST['insurance_provider']) : $existingPatient['pat_insurance_provider'],
                                            'pat_insurance_number' => isset($_POST['insurance_number']) ? sanitize($_POST['insurance_number']) : $existingPatient['pat_insurance_number']
                                        ];
                                        
                                        // Format phone numbers if they have values
                                        if (!empty($updateData['pat_phone'])) {
                                            $updateData['pat_phone'] = formatPhoneNumber($updateData['pat_phone']);
                                        }
                                        if (!empty($updateData['pat_emergency_phone'])) {
                                            $updateData['pat_emergency_phone'] = formatPhoneNumber($updateData['pat_emergency_phone']);
                                        }
                                        
                                        // Update using Patient class
                                        $patient = new Patient();
                                        $result = $patient->update($pat_id, $updateData);
                                        if (!$result['success']) {
                                            $error = implode(', ', $result['errors'] ?? ['Failed to update patient']);
                                        }
                                    }
                                }
                            } elseif ($role === 'staff' && $staff_id) {
                                // Fetch existing staff data using Staff class
                                $existingStaff = Staff::findById($staff_id);
                                
                                if (!$existingStaff) {
                                    $error = 'Staff profile not found';
                                } else {
                                    // Check if email is being changed and if it already exists in staff table
                                    // Only check if the email is different from the current staff email
                                    if ($email !== $existingStaff['staff_email']) {
                                        $existingEmail = $db->fetchOne(
                                            "SELECT staff_id FROM staff WHERE staff_email = :email AND staff_id != :id",
                                            ['email' => $email, 'id' => $staff_id]
                                        );
                                        if ($existingEmail) {
                                            $error = 'Email already exists for another staff member.';
                                        }
                                    }
                                    // If email hasn't changed, no need to check
                                    
                                    if (empty($error)) {
                                        // Always use POST values if they exist (even if empty), otherwise preserve existing values
                                        $updateData = [
                                            'staff_id' => $staff_id,
                                            'staff_email' => $email,
                                            'staff_first_name' => isset($_POST['staff_first_name']) ? sanitize($_POST['staff_first_name']) : $existingStaff['staff_first_name'],
                                            'staff_middle_initial' => isset($_POST['staff_middle_initial']) ? (!empty($_POST['staff_middle_initial']) ? strtoupper(substr(sanitize($_POST['staff_middle_initial']), 0, 1)) : null) : ($existingStaff['staff_middle_initial'] ?? null),
                                            'staff_last_name' => isset($_POST['staff_last_name']) ? sanitize($_POST['staff_last_name']) : $existingStaff['staff_last_name'],
                                            'staff_phone' => isset($_POST['staff_phone']) ? sanitize($_POST['staff_phone']) : $existingStaff['staff_phone'],
                                            'staff_position' => isset($_POST['position']) ? sanitize($_POST['position']) : $existingStaff['staff_position'],
                                            'staff_hire_date' => isset($_POST['hire_date']) ? $_POST['hire_date'] : $existingStaff['staff_hire_date'],
                                            'staff_salary' => isset($_POST['salary']) && $_POST['salary'] !== '' ? floatval($_POST['salary']) : $existingStaff['staff_salary'],
                                            'staff_status' => isset($_POST['status']) ? sanitize($_POST['status']) : $existingStaff['staff_status']
                                        ];
                                        
                                        // Format phone number if it has a value
                                        if (!empty($updateData['staff_phone'])) {
                                            $updateData['staff_phone'] = formatPhoneNumber($updateData['staff_phone']);
                                        }
                                        
                                        // Update using Staff class
                                        $staff = new Staff();
                                        $result = $staff->update($staff_id, $updateData);
                                        if (!$result['success']) {
                                            $error = implode(', ', $result['errors'] ?? ['Failed to update staff']);
                                        }
                                    }
                                }
                            } elseif ($role === 'doctor' && $doc_id) {
                                // Fetch existing doctor data using Doctor class
                                $existingDoctor = Doctor::findById($doc_id);
                                
                                if (!$existingDoctor) {
                                    $error = 'Doctor profile not found';
                                } else {
                                    // Check if email is being changed and if it already exists in doctors table
                                    // Only check if the email is different from the current doctor email
                                    if ($email !== $existingDoctor['doc_email']) {
                                        $existingEmail = $db->fetchOne(
                                            "SELECT doc_id FROM doctors WHERE doc_email = :email AND doc_id != :id",
                                            ['email' => $email, 'id' => $doc_id]
                                        );
                                        if ($existingEmail) {
                                            $error = 'Email already exists for another doctor.';
                                        }
                                    }
                                    // If email hasn't changed, no need to check
                                    
                                    if (empty($error)) {
                                        // Always use POST values if they exist (even if empty), otherwise preserve existing values
                                        $updateData = [
                                            'doc_id' => $doc_id,
                                            'doc_email' => $email,
                                            'doc_first_name' => isset($_POST['doc_first_name']) ? sanitize($_POST['doc_first_name']) : $existingDoctor['doc_first_name'],
                                            'doc_middle_initial' => isset($_POST['doc_middle_initial']) ? (!empty($_POST['doc_middle_initial']) ? strtoupper(substr(sanitize($_POST['doc_middle_initial']), 0, 1)) : null) : ($existingDoctor['doc_middle_initial'] ?? null),
                                            'doc_last_name' => isset($_POST['doc_last_name']) ? sanitize($_POST['doc_last_name']) : $existingDoctor['doc_last_name'],
                                            'doc_phone' => isset($_POST['doc_phone']) ? sanitize($_POST['doc_phone']) : $existingDoctor['doc_phone'],
                                            'doc_specialization_id' => isset($_POST['specialization_id']) && $_POST['specialization_id'] !== '' ? (int)$_POST['specialization_id'] : $existingDoctor['doc_specialization_id'],
                                            'doc_license_number' => isset($_POST['license_number']) ? sanitize($_POST['license_number']) : $existingDoctor['doc_license_number'],
                                            'doc_experience_years' => isset($_POST['experience_years']) && $_POST['experience_years'] !== '' ? (int)$_POST['experience_years'] : $existingDoctor['doc_experience_years'],
                                            'doc_consultation_fee' => isset($_POST['consultation_fee']) && $_POST['consultation_fee'] !== '' ? floatval($_POST['consultation_fee']) : $existingDoctor['doc_consultation_fee'],
                                            'doc_qualification' => isset($_POST['qualification']) ? sanitize($_POST['qualification']) : $existingDoctor['doc_qualification'],
                                            'doc_bio' => isset($_POST['bio']) ? sanitize($_POST['bio']) : $existingDoctor['doc_bio'],
                                            'doc_status' => isset($_POST['status']) ? sanitize($_POST['status']) : $existingDoctor['doc_status']
                                        ];
                                        
                                        // Format phone number if it has a value
                                        if (!empty($updateData['doc_phone'])) {
                                            $updateData['doc_phone'] = formatPhoneNumber($updateData['doc_phone']);
                                        }
                                        
                                        // Update using Doctor class
                                        $doctor = new Doctor();
                                        $result = $doctor->update($doc_id, $updateData);
                                        if (!$result['success']) {
                                            $error = implode(', ', $result['errors'] ?? ['Failed to update doctor']);
                                        }
                                    }
                                }
                            }
                        
                            $success = 'User updated successfully';
                            } catch (PDOException $e) {
                                // Check if it's a unique constraint violation
                                if (strpos($e->getCode(), '23505') !== false || strpos($e->getMessage(), 'duplicate key') !== false || strpos($e->getMessage(), 'Unique violation') !== false) {
                                    if (strpos($e->getMessage(), 'pat_email') !== false) {
                                        $error = 'Email already exists for another patient.';
                                    } elseif (strpos($e->getMessage(), 'staff_email') !== false) {
                                        $error = 'Email already exists for another staff member.';
                                    } elseif (strpos($e->getMessage(), 'doc_email') !== false) {
                                        $error = 'Email already exists for another doctor.';
                                    } elseif (strpos($e->getMessage(), 'user_email') !== false) {
                                        $error = 'Email already exists for another user.';
                                    } else {
                                        $error = 'This email is already in use. Please use a different email address.';
                                    }
                                } else {
                                    $error = 'Database error: ' . $e->getMessage();
                                }
                            }
                        }
                    }
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        
        try {
            $stmt = $db->prepare("DELETE FROM users WHERE user_id = :id");
            $stmt->execute(['id' => $id]);
            $success = 'User deleted successfully';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Handle search and filters
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

$filter_role = isset($_GET['role']) ? sanitize($_GET['role']) : '';

// Pagination - check if we should load all results (for client-side filtering)
$load_all = isset($_GET['all_results']) && $_GET['all_results'] == '1';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = $load_all ? 10000 : 10; // Load all if filtering, otherwise paginate
$offset = $load_all ? 0 : (($page - 1) * $items_per_page);

// Fetch users with filters
try {
    $where_conditions = [];
    $params = [];

    if (!empty($search_query)) {
        $where_conditions[] = "u.user_email LIKE :search";
        $params['search'] = '%' . $search_query . '%';
    }

    if (!empty($filter_role)) {
        if ($filter_role === 'superadmin') {
            $where_conditions[] = "u.user_is_superadmin = true";
        } elseif ($filter_role === 'staff') {
            $where_conditions[] = "u.staff_id IS NOT NULL";
        } elseif ($filter_role === 'doctor') {
            $where_conditions[] = "u.doc_id IS NOT NULL";
        } elseif ($filter_role === 'patient') {
            $where_conditions[] = "u.pat_id IS NOT NULL";
        }
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Handle sorting
    $sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'created_at';
    $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column to prevent SQL injection
    $allowed_columns = ['user_email', 'created_at', 'updated_at', 'full_name', 'role'];
    if (!in_array($sort_column, $allowed_columns)) {
        $sort_column = 'created_at';
    }
    
    // Build ORDER BY clause based on sort column
    if ($sort_column === 'full_name') {
        // Sort by full name (patient first, then staff, then doctor, then superadmin)
        $order_by = "COALESCE(p.pat_first_name || ' ' || p.pat_last_name, 
                     s.staff_first_name || ' ' || s.staff_last_name,
                     d.doc_first_name || ' ' || d.doc_last_name,
                     'Super Admin') $sort_order";
    } elseif ($sort_column === 'role') {
        // Sort by role (Super Admin, Staff, Doctor, Patient)
        $order_by = "CASE 
            WHEN u.user_is_superadmin = true THEN 'Super Admin'
            WHEN u.staff_id IS NOT NULL THEN 'Staff'
            WHEN u.doc_id IS NOT NULL THEN 'Doctor'
            WHEN u.pat_id IS NOT NULL THEN 'Patient'
            ELSE 'None'
        END $sort_order";
    } else {
        $order_by = "u.$sort_column $sort_order";
    }

    // Get total count for pagination
    $count_stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM users u
        LEFT JOIN patients p ON u.pat_id = p.pat_id
        LEFT JOIN staff s ON u.staff_id = s.staff_id
        LEFT JOIN doctors d ON u.doc_id = d.doc_id
        $where_clause
    ");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

    // Fetch paginated results with joined data - include all profile fields
    $stmt = $db->prepare("
        SELECT 
            u.user_id, 
            u.user_email, 
            u.user_is_superadmin, 
            u.pat_id, 
            u.staff_id, 
            u.doc_id, 
            u.created_at,
            u.updated_at,
            u.profile_picture_url,
            COALESCE(p.pat_first_name || ' ' || p.pat_last_name, 
                     s.staff_first_name || ' ' || s.staff_last_name,
                     d.doc_first_name || ' ' || d.doc_last_name,
                     'Super Admin') as full_name,
            COALESCE(p.pat_phone, s.staff_phone, d.doc_phone, NULL) as phone_number,
            COALESCE(p.pat_first_name || ' ' || p.pat_last_name, 
                     s.staff_first_name || ' ' || s.staff_last_name,
                     d.doc_first_name || ' ' || d.doc_last_name,
                     'Super Admin') as status_name,
            CASE 
                WHEN u.user_is_superadmin = true THEN 'Super Admin'
                WHEN u.staff_id IS NOT NULL THEN COALESCE(s.staff_status, 'active')
                WHEN u.doc_id IS NOT NULL THEN COALESCE(d.doc_status, 'active')
                WHEN u.pat_id IS NOT NULL THEN 'active'
                ELSE 'inactive'
            END as status,
            -- Patient fields
            p.pat_first_name, p.pat_last_name, p.pat_email, p.pat_phone, p.pat_date_of_birth,
            p.pat_gender, p.pat_address, p.pat_emergency_contact, p.pat_emergency_phone,
            p.pat_medical_history, p.pat_allergies, p.pat_insurance_provider, p.pat_insurance_number,
            -- Staff fields
            s.staff_first_name, s.staff_last_name, s.staff_email, s.staff_phone, s.staff_position,
            s.staff_hire_date, s.staff_salary, s.staff_status,
            -- Doctor fields
            d.doc_first_name, d.doc_last_name, d.doc_email, d.doc_phone, d.doc_specialization_id,
            d.doc_license_number, d.doc_experience_years, d.doc_consultation_fee, d.doc_qualification,
            d.doc_bio, d.doc_status
        FROM users u
        LEFT JOIN patients p ON u.pat_id = p.pat_id
        LEFT JOIN staff s ON u.staff_id = s.staff_id
        LEFT JOIN doctors d ON u.doc_id = d.doc_id
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
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to fetch users: ' . $e->getMessage();
    $users = [];
    $total_items = 0;
    $total_pages = 0;
}

// Fetch specializations for doctor role dropdown
try {
    $stmt = $db->query("SELECT spec_id, spec_name FROM specializations ORDER BY spec_name");
    $specializations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $specializations = [];
}

// Include the view
// Calculate statistics for summary cards
$stats = [
    'total' => 0,
    'superadmin' => 0,
    'staff' => 0,
    'doctor' => 0,
    'patient' => 0
];

try {
    // Total users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Super Admin users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE user_is_superadmin = true");
    $stats['superadmin'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Staff users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE staff_id IS NOT NULL");
    $stats['staff'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Doctor users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE doc_id IS NOT NULL");
    $stats['doctor'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Patient users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE pat_id IS NOT NULL");
    $stats['patient'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Keep default values
}

require_once __DIR__ . '/../../views/superadmin/users.view.php';
