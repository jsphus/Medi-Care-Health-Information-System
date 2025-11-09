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
    <title>MediCare System</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/dashboard.css">
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;">

<?php 
if (isset($_SESSION['user_id'])): 
    include __DIR__ . '/sidebar.php';
?>

<div class="main-content">
    <div class="top-bar">
        <div>
            <h1 style="margin: 0; font-size: 24px; color: #2c3e50; display: flex; align-items: center; gap: 10px;">
                <span class="material-icons" style="color: #3b82f6;">dashboard</span>
                <?php
                $pageTitles = [
                    'superadmin' => 'Super Admin Dashboard',
                    'staff' => 'Staff Dashboard',
                    'doctor' => 'Doctor Portal',
                    'patient' => 'Patient Portal'
                ];
                echo $pageTitles[$role] ?? 'Dashboard';
                ?>
            </h1>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <?php
                if (isset($_SESSION['user_email'])) {
                    echo strtoupper(substr($_SESSION['user_email'], 0, 1));
                } else {
                    echo 'U';
                }
                ?>
            </div>
            <div>
                <div style="font-weight: 500; color: #2c3e50;">
                    <?= htmlspecialchars($_SESSION['user_email'] ?? 'User') ?>
                </div>
                <div style="font-size: 12px; color: #7f8c8d;">
                    <?= ucfirst($role) ?>
                </div>
            </div>
        </div>
    </div>
    
    <div style="padding: 30px;">
<?php else: ?>
<?php endif; ?>
