<?php
require_once __DIR__ . '/../classes/Auth.php';

$auth = new Auth();

// Only super admin can use view-as
// Check if original user is super admin
$isOriginalSuperAdmin = false;
if ($auth->isViewingAs()) {
    $isOriginalSuperAdmin = $_SESSION['original_is_superadmin'] ?? false;
} else {
    $isOriginalSuperAdmin = $auth->isSuperAdmin();
}

if (!$isOriginalSuperAdmin) {
    header('Location: /');
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'doctor':
        if ($auth->startViewAs('doctor')) {
            header('Location: /doctor/dashboard');
            exit;
        } else {
            $_SESSION['error'] = 'Unable to switch to doctor view. Please ensure there is at least one doctor in the system.';
            header('Location: /superadmin/dashboard');
            exit;
        }
        break;
        
    case 'patient':
        if ($auth->startViewAs('patient')) {
            header('Location: /patient/dashboard');
            exit;
        } else {
            $_SESSION['error'] = 'Unable to switch to patient view. Please ensure there is at least one patient in the system.';
            header('Location: /superadmin/dashboard');
            exit;
        }
        break;
        
    case 'staff':
        if ($auth->startViewAs('staff')) {
            header('Location: /staff/dashboard');
            exit;
        } else {
            $_SESSION['error'] = 'Unable to switch to staff view. Please ensure there is at least one staff member in the system.';
            header('Location: /superadmin/dashboard');
            exit;
        }
        break;
        
    case 'exit':
        if ($auth->stopViewAs()) {
            header('Location: /superadmin/dashboard');
            exit;
        } else {
            header('Location: /');
            exit;
        }
        break;
        
    default:
        header('Location: /superadmin/dashboard');
        exit;
}

