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
    <div class="content-container">
<?php else: ?>
    <!-- No navigation for public pages -->
<?php endif; ?>
