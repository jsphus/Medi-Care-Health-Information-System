<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medi-Care Health Portal</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/confirm-modal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="margin: 0; padding: 0;">

<div class="app-container">
<?php 
// Only show navigation if user is logged in
if (isset($_SESSION['user_id'])): 
    // Get user name for display
    $userName = 'User';
    $userInitial = 'U';
    
    if (isset($_SESSION['user_email'])) {
        $userName = $_SESSION['user_email'];
        $userInitial = strtoupper(substr($_SESSION['user_email'], 0, 1));
    }
    
    // Try to get full name from session
    if (isset($_SESSION['pat_first_name']) && isset($_SESSION['pat_last_name'])) {
        $userName = ($_SESSION['pat_first_name'] ?? '') . ' ' . ($_SESSION['pat_last_name'] ?? '');
        $userInitial = strtoupper(substr($_SESSION['pat_first_name'] ?? 'P', 0, 1));
    } elseif (isset($_SESSION['doc_first_name']) && isset($_SESSION['doc_last_name'])) {
        $userName = ($_SESSION['doc_first_name'] ?? '') . ' ' . ($_SESSION['doc_last_name'] ?? '');
        $userInitial = strtoupper(substr($_SESSION['doc_first_name'] ?? 'D', 0, 1));
    }
    
    include __DIR__ . '/sidebar.php';
?>

<div class="main-content">
    <?php
    // Show view-as banner if super admin is viewing as another role
    require_once __DIR__ . '/../../classes/Auth.php';
    $auth = new Auth();
    if ($auth->isViewingAs() && ($_SESSION['original_is_superadmin'] ?? false)):
        $viewingAsRole = $_SESSION['view_as_role'] ?? 'unknown';
    ?>
    <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-bottom: 2px solid #fbbf24; padding: 0.75rem 1.5rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-eye" style="color: #92400e; font-size: 1.125rem;"></i>
            <div>
                <div style="font-weight: 600; color: #92400e; font-size: 0.875rem;">Viewing as <?= ucfirst($viewingAsRole) ?></div>
                <div style="font-size: 0.75rem; color: #78350f; margin-top: 0.125rem;">You are viewing the system as a <?= $viewingAsRole ?>. Click "Exit View As" in the sidebar to return to Super Admin view.</div>
            </div>
        </div>
        <a href="/view-as?action=exit" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #ef4444; color: white; border: none; border-radius: 0.375rem; text-decoration: none; font-size: 0.8125rem; font-weight: 500; transition: background 0.2s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
            <i class="fas fa-times"></i>
            <span>Exit View As</span>
        </a>
    </div>
    <?php endif; ?>
    <div class="content-container">
<?php else: ?>
    <!-- No navigation for public pages -->
<?php endif; ?>
