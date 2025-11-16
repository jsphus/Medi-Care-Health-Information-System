<?php
class Auth {
    private $db;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require_once __DIR__ . '/../config/Database.php';
        $this->db = Database::getInstance();
    }
    
    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE user_email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['user_password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['is_superadmin'] = $user['user_is_superadmin'];
                $_SESSION['pat_id'] = $user['pat_id'];
                $_SESSION['staff_id'] = $user['staff_id'];
                $_SESSION['doc_id'] = $user['doc_id'];
                $_SESSION['logged_in'] = true;
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
    
    public function logout() {
        session_destroy();
        session_start();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function isSuperAdmin() {
        return $this->isLoggedIn() && isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] === true;
    }
    
    public function isStaff() {
        // If viewing as staff, return true
        if (isset($_SESSION['view_as_role']) && $_SESSION['view_as_role'] === 'staff') {
            return true;
        }
        return $this->isLoggedIn() && isset($_SESSION['staff_id']) && $_SESSION['staff_id'] !== null;
    }
    
    public function isDoctor() {
        // If viewing as doctor, return true
        if (isset($_SESSION['view_as_role']) && $_SESSION['view_as_role'] === 'doctor') {
            return true;
        }
        return $this->isLoggedIn() && isset($_SESSION['doc_id']) && $_SESSION['doc_id'] !== null;
    }
    
    public function isPatient() {
        // If viewing as patient, return true
        if (isset($_SESSION['view_as_role']) && $_SESSION['view_as_role'] === 'patient') {
            return true;
        }
        return $this->isLoggedIn() && isset($_SESSION['pat_id']) && $_SESSION['pat_id'] !== null;
    }
    
    public function getRole() {
        // Check if viewing as another role
        if (isset($_SESSION['view_as_role'])) {
            return $_SESSION['view_as_role'];
        }
        
        if ($this->isSuperAdmin()) return 'superadmin';
        if ($this->isStaff()) return 'staff';
        if ($this->isDoctor()) return 'doctor';
        if ($this->isPatient()) return 'patient';
        return null;
    }
    
    public function isViewingAs() {
        return isset($_SESSION['view_as_role']) && isset($_SESSION['original_user_id']);
    }
    
    public function getOriginalUserId() {
        return $_SESSION['original_user_id'] ?? null;
    }
    
    public function startViewAs($role) {
        // Only super admin can use view-as
        // Check original superadmin status if already viewing as
        $isOriginalSuperAdmin = false;
        if ($this->isViewingAs()) {
            $isOriginalSuperAdmin = $_SESSION['original_is_superadmin'] ?? false;
        } else {
            $isOriginalSuperAdmin = $this->isSuperAdmin();
        }
        
        if (!$isOriginalSuperAdmin) {
            return false;
        }
        
        // If already viewing as, restore first, then start new view-as
        if ($this->isViewingAs()) {
            $this->stopViewAs();
        }
        
        // Store original session data
        $_SESSION['original_user_id'] = $_SESSION['user_id'];
        $_SESSION['original_is_superadmin'] = $_SESSION['is_superadmin'];
        $_SESSION['original_pat_id'] = $_SESSION['pat_id'] ?? null;
        $_SESSION['original_staff_id'] = $_SESSION['staff_id'] ?? null;
        $_SESSION['original_doc_id'] = $_SESSION['doc_id'] ?? null;
        
        // Set view-as role
        $_SESSION['view_as_role'] = $role;
        
        // Get a sample user of the target role from database
        try {
            switch ($role) {
                case 'doctor':
                    $stmt = $this->db->query("SELECT u.user_id, u.doc_id FROM users u WHERE u.doc_id IS NOT NULL LIMIT 1");
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($user) {
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['doc_id'] = $user['doc_id'];
                        $_SESSION['pat_id'] = null;
                        $_SESSION['staff_id'] = null;
                        $_SESSION['is_superadmin'] = false;
                    } else {
                        return false;
                    }
                    break;
                    
                case 'patient':
                    $stmt = $this->db->query("SELECT u.user_id, u.pat_id FROM users u WHERE u.pat_id IS NOT NULL LIMIT 1");
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($user) {
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['pat_id'] = $user['pat_id'];
                        $_SESSION['doc_id'] = null;
                        $_SESSION['staff_id'] = null;
                        $_SESSION['is_superadmin'] = false;
                    } else {
                        return false;
                    }
                    break;
                    
                case 'staff':
                    $stmt = $this->db->query("SELECT u.user_id, u.staff_id FROM users u WHERE u.staff_id IS NOT NULL LIMIT 1");
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($user) {
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['staff_id'] = $user['staff_id'];
                        $_SESSION['pat_id'] = null;
                        $_SESSION['doc_id'] = null;
                        $_SESSION['is_superadmin'] = false;
                    } else {
                        return false;
                    }
                    break;
                    
                default:
                    return false;
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("View as error: " . $e->getMessage());
            return false;
        }
    }
    
    public function stopViewAs() {
        if (!$this->isViewingAs()) {
            return false;
        }
        
        // Restore original session data
        if (isset($_SESSION['original_user_id'])) {
            $_SESSION['user_id'] = $_SESSION['original_user_id'];
            $_SESSION['is_superadmin'] = $_SESSION['original_is_superadmin'] ?? false;
            $_SESSION['pat_id'] = $_SESSION['original_pat_id'] ?? null;
            $_SESSION['staff_id'] = $_SESSION['original_staff_id'] ?? null;
            $_SESSION['doc_id'] = $_SESSION['original_doc_id'] ?? null;
        }
        
        // Clear view-as data
        unset($_SESSION['view_as_role']);
        unset($_SESSION['original_user_id']);
        unset($_SESSION['original_is_superadmin']);
        unset($_SESSION['original_pat_id']);
        unset($_SESSION['original_staff_id']);
        unset($_SESSION['original_doc_id']);
        
        return true;
    }
    
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function getPatientId() {
        return $_SESSION['pat_id'] ?? null;
    }
    
    public function getDoctorId() {
        return $_SESSION['doc_id'] ?? null;
    }
    
    public function getStaffId() {
        return $_SESSION['staff_id'] ?? null;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }
    
    public function requireSuperAdmin() {
        $this->requireLogin();
        if (!$this->isSuperAdmin()) {
            header('Location: /');
            exit;
        }
    }
    
    public function requireStaff() {
        $this->requireLogin();
        // Allow if viewing as staff or is super admin (even when viewing as)
        if (!$this->isStaff() && !$this->isSuperAdmin()) {
            header('Location: /');
            exit;
        }
    }
    
    public function requireDoctor() {
        $this->requireLogin();
        // Allow if viewing as doctor or is super admin (even when viewing as)
        if (!$this->isDoctor() && !$this->isSuperAdmin()) {
            header('Location: /');
            exit;
        }
    }
    
    public function requirePatient() {
        $this->requireLogin();
        // Allow if viewing as patient or is super admin (even when viewing as)
        if (!$this->isPatient() && !$this->isSuperAdmin()) {
            header('Location: /');
            exit;
        }
    }
    
    public function requireRole($roles = []) {
        $this->requireLogin();
        $currentRole = $this->getRole();
        
        if (!in_array($currentRole, $roles)) {
            header('Location: /');
            exit;
        }
    }
    
    public function canAccess($allowedRoles = []) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $currentRole = $this->getRole();
        return in_array($currentRole, $allowedRoles);
    }
    
    public function registerPatient($data) {
        try {
            $this->db->beginTransaction();
            
            // Check if email already exists in patients table
            $stmt = $this->db->prepare("SELECT pat_id FROM patients WHERE pat_email = :email LIMIT 1");
            $stmt->execute(['email' => $data['email']]);
            if ($stmt->fetch()) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Email already registered'];
            }
            
            // Check if email already exists in users table
            $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_email = :email LIMIT 1");
            $stmt->execute(['email' => $data['email']]);
            if ($stmt->fetch()) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Email already registered'];
            }
            
            // Insert into patients table
            $stmt = $this->db->prepare("
                INSERT INTO patients (
                    pat_first_name, pat_last_name, pat_email, pat_phone, 
                    pat_date_of_birth, pat_gender, pat_address, 
                    pat_emergency_contact, pat_emergency_phone
                ) VALUES (
                    :first_name, :last_name, :email, :phone, 
                    :date_of_birth, :gender, :address, 
                    :emergency_contact, :emergency_phone
                ) RETURNING pat_id
            ");
            $stmt->execute([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'emergency_contact' => $data['emergency_contact'] ?? null,
                'emergency_phone' => $data['emergency_phone'] ?? null
            ]);
            $patient = $stmt->fetch(PDO::FETCH_ASSOC);
            $pat_id = $patient['pat_id'];
            
            // Insert into users table
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                INSERT INTO users (user_email, user_password, pat_id) 
                VALUES (:email, :password, :pat_id)
            ");
            $stmt->execute([
                'email' => $data['email'],
                'password' => $hashedPassword,
                'pat_id' => $pat_id
            ]);
            
            $this->db->commit();
            return ['success' => true, 'pat_id' => $pat_id];
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Patient registration error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Registration failed. Please try again.'];
        }
    }
    
    public function registerDoctor($data) {
        try {
            $this->db->beginTransaction();
            
            // Check if email already exists in doctors table
            $stmt = $this->db->prepare("SELECT doc_id FROM doctors WHERE doc_email = :email LIMIT 1");
            $stmt->execute(['email' => $data['email']]);
            if ($stmt->fetch()) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Email already registered'];
            }
            
            // Check if email already exists in users table
            $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_email = :email LIMIT 1");
            $stmt->execute(['email' => $data['email']]);
            if ($stmt->fetch()) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Email already registered'];
            }
            
            // Check if license number already exists
            if (!empty($data['license_number'])) {
                $stmt = $this->db->prepare("SELECT doc_id FROM doctors WHERE doc_license_number = :license LIMIT 1");
                $stmt->execute(['license' => $data['license_number']]);
                if ($stmt->fetch()) {
                    $this->db->rollBack();
                    return ['success' => false, 'error' => 'License number already registered'];
                }
            }
            
            // Insert into doctors table
            $stmt = $this->db->prepare("
                INSERT INTO doctors (
                    doc_first_name, doc_last_name, doc_email, doc_phone, 
                    doc_license_number, doc_specialization_id, doc_experience_years,
                    doc_consultation_fee, doc_qualification, doc_bio
                ) VALUES (
                    :first_name, :last_name, :email, :phone, 
                    :license_number, :specialization_id, :experience_years,
                    :consultation_fee, :qualification, :bio
                ) RETURNING doc_id
            ");
            $stmt->execute([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'license_number' => $data['license_number'] ?? null,
                'specialization_id' => !empty($data['specialization_id']) ? (int)$data['specialization_id'] : null,
                'experience_years' => !empty($data['experience_years']) ? (int)$data['experience_years'] : null,
                'consultation_fee' => !empty($data['consultation_fee']) ? (float)$data['consultation_fee'] : null,
                'qualification' => $data['qualification'] ?? null,
                'bio' => $data['bio'] ?? null
            ]);
            $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
            $doc_id = $doctor['doc_id'];
            
            // Insert into users table
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                INSERT INTO users (user_email, user_password, doc_id) 
                VALUES (:email, :password, :doc_id)
            ");
            $stmt->execute([
                'email' => $data['email'],
                'password' => $hashedPassword,
                'doc_id' => $doc_id
            ]);
            
            $this->db->commit();
            return ['success' => true, 'doc_id' => $doc_id];
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Doctor registration error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Registration failed. Please try again.'];
        }
    }
    
    public function registerStaff($data) {
        try {
            $this->db->beginTransaction();
            
            // Check if email already exists in staff table
            $stmt = $this->db->prepare("SELECT staff_id FROM staff WHERE staff_email = :email LIMIT 1");
            $stmt->execute(['email' => $data['email']]);
            if ($stmt->fetch()) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Email already registered'];
            }
            
            // Check if email already exists in users table
            $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_email = :email LIMIT 1");
            $stmt->execute(['email' => $data['email']]);
            if ($stmt->fetch()) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Email already registered'];
            }
            
            // Insert into staff table
            $stmt = $this->db->prepare("
                INSERT INTO staff (
                    staff_first_name, staff_last_name, staff_email, staff_phone, 
                    staff_position
                ) VALUES (
                    :first_name, :last_name, :email, :phone, 
                    :position
                ) RETURNING staff_id
            ");
            $stmt->execute([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'position' => $data['position'] ?? null
            ]);
            $staff = $stmt->fetch(PDO::FETCH_ASSOC);
            $staff_id = $staff['staff_id'];
            
            // Insert into users table
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                INSERT INTO users (user_email, user_password, staff_id) 
                VALUES (:email, :password, :staff_id)
            ");
            $stmt->execute([
                'email' => $data['email'],
                'password' => $hashedPassword,
                'staff_id' => $staff_id
            ]);
            
            $this->db->commit();
            return ['success' => true, 'staff_id' => $staff_id];
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Staff registration error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Registration failed. Please try again.'];
        }
    }
}
