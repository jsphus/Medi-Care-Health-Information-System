<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$error = '';
$success = '';

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = 'Privacy settings saved successfully';
}

require_once __DIR__ . '/../../views/patient/privacy.view.php';

