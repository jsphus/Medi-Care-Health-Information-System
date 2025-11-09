<?php
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../includes/functions.php';

$auth = new Auth();

// If already logged in, redirect to appropriate dashboard
if ($auth->isLoggedIn()) {
    if ($auth->isSuperAdmin()) {
        header('Location: /superadmin/dashboard');
    } elseif ($auth->isStaff()) {
        header('Location: /staff/dashboard');
    } elseif ($auth->isDoctor()) {
        header('Location: /doctor/dashboard');
    } elseif ($auth->isPatient()) {
        header('Location: /patient/dashboard');
    } else {
        header('Location: /');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } elseif (!isValidEmail($email)) {
        $error = 'Please enter a valid email address.';
    } else {
        if ($auth->login($email, $password)) {
            // Redirect based on role
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
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

include __DIR__ . '/../views/login.view.php';