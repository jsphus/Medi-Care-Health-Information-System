<?php
// Set headers to prevent caching - must be before any output
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Doctor.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requireDoctor();

// Clear the completion flag if it exists
if (isset($_SESSION['email_change_completed'])) {
    unset($_SESSION['email_change_completed']);
}

require_once __DIR__ . '/../../views/doctor/change-email-success.view.php';

