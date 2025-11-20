<?php
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/Database.php';

$auth = new Auth();

// If already logged in, redirect to appropriate dashboard
if ($auth->isLoggedIn()) {
    $role = $auth->getRole();
    switch ($role) {
        case 'superadmin':
            header('Location: /superadmin/dashboard');
            break;
        case 'staff':
            header('Location: /staff/dashboard');
            break;
        case 'doctor':
            header('Location: /doctor/appointments/today');
            break;
        case 'patient':
            header('Location: /patient/appointments');
            break;
        default:
            header('Location: /');
    }
    exit;
}

$db = Database::getInstance();
$error = '';
$success = '';
$role = $_GET['role'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = sanitize($_POST['role'] ?? '');
    
    if (empty($role) || !in_array($role, ['patient', 'doctor', 'staff'])) {
        $error = 'Invalid role selected';
    } else {
        // Validate common fields
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $first_name = sanitize($_POST['first_name'] ?? '');
        $middle_initial = sanitize($_POST['middle_initial'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        
        // Basic validation
        if (empty($email) || !isValidEmail($email)) {
            $error = 'Please enter a valid email address';
        } elseif (empty($password) || strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match';
        } elseif (empty($first_name) || empty($last_name)) {
            $error = 'First name and last name are required';
        } else {
            // Role-specific validation and registration
            if ($role === 'patient') {
                $data = [
                    'email' => $email,
                    'password' => $password,
                    'first_name' => $first_name,
                    'middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                    'last_name' => $last_name,
                    'phone' => $phone,
                    'date_of_birth' => !empty($_POST['date_of_birth']) ? sanitize($_POST['date_of_birth']) : null,
                    'gender' => !empty($_POST['gender']) ? sanitize($_POST['gender']) : null,
                    'address' => !empty($_POST['address']) ? sanitize($_POST['address']) : null,
                    'emergency_contact' => !empty($_POST['emergency_contact']) ? sanitize($_POST['emergency_contact']) : null,
                    'emergency_phone' => !empty($_POST['emergency_phone']) ? sanitize($_POST['emergency_phone']) : null
                ];
                
                $result = $auth->registerPatient($data);
                if ($result['success']) {
                    // Auto-login after registration
                    if ($auth->login($email, $password)) {
                        header('Location: /patient/appointments');
                        exit;
                    } else {
                        $success = 'Registration successful! Please login.';
                        $role = ''; // Reset role to show login link
                    }
                } else {
                    $error = $result['error'];
                }
            } elseif ($role === 'doctor') {
                $license_number = sanitize($_POST['license_number'] ?? '');
                
                if (empty($license_number)) {
                    $error = 'License number is required for doctors';
                } else {
                    $data = [
                        'email' => $email,
                        'password' => $password,
                        'first_name' => $first_name,
                        'middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                        'last_name' => $last_name,
                        'phone' => $phone,
                        'license_number' => $license_number,
                        'specialization_id' => !empty($_POST['specialization_id']) ? (int)$_POST['specialization_id'] : null,
                        'experience_years' => !empty($_POST['experience_years']) ? (int)$_POST['experience_years'] : null,
                        'consultation_fee' => !empty($_POST['consultation_fee']) ? (float)$_POST['consultation_fee'] : null,
                        'qualification' => !empty($_POST['qualification']) ? sanitize($_POST['qualification']) : null,
                        'bio' => !empty($_POST['bio']) ? sanitize($_POST['bio']) : null
                    ];
                    
                    $result = $auth->registerDoctor($data);
                    if ($result['success']) {
                        // Auto-login after registration
                        if ($auth->login($email, $password)) {
                            header('Location: /doctor/appointments/today');
                            exit;
                        } else {
                            $success = 'Registration successful! Please login.';
                            $role = ''; // Reset role to show login link
                        }
                    } else {
                        $error = $result['error'];
                    }
                }
            } elseif ($role === 'staff') {
                $data = [
                    'email' => $email,
                    'password' => $password,
                    'first_name' => $first_name,
                    'middle_initial' => !empty($middle_initial) ? strtoupper(substr($middle_initial, 0, 1)) : null,
                    'last_name' => $last_name,
                    'phone' => $phone,
                    'position' => !empty($_POST['position']) ? sanitize($_POST['position']) : null
                ];
                
                $result = $auth->registerStaff($data);
                if ($result['success']) {
                    // Auto-login after registration
                    if ($auth->login($email, $password)) {
                        header('Location: /staff/dashboard');
                        exit;
                    } else {
                        $success = 'Registration successful! Please login.';
                        $role = ''; // Reset role to show login link
                    }
                } else {
                    $error = $result['error'];
                }
            }
        }
    }
}

// Fetch specializations for doctor registration
$specializations = [];
if ($role === 'doctor') {
    try {
        $stmt = $db->query("SELECT spec_id, spec_name FROM specializations ORDER BY spec_name");
        $specializations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $specializations = [];
    }
}

// Include appropriate view
if (empty($role)) {
    include __DIR__ . '/../views/register-role.view.php';
} else {
    include __DIR__ . '/../views/register-form.view.php';
}

