<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Doctor.php';
require_once __DIR__ . '/../../classes/Specialization.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$error = '';
$doctorModel = new Doctor();
$specializationModel = new Specialization();

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// Handle search and filter
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = sanitize($_GET['search']);
}

$filter_specialization = isset($_GET['specialization']) ? (int)$_GET['specialization'] : null;

$doctors = $doctorModel->searchDoctors([
    'search' => $search_query,
    'specialization' => $filter_specialization
]);

$specializations = $specializationModel->getAllSpecializations();

require_once __DIR__ . '/../../views/patient/book.view.php';

