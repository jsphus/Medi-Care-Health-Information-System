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

// Get basic doctor list
$doctors = $doctorModel->searchDoctors([
    'search' => $search_query,
    'specialization' => $filter_specialization
]);

// Fetch full details for each doctor (for modal display)
$doctorsWithDetails = [];
foreach ($doctors as $doctor) {
    $fullDetails = $doctorModel->getDetailsById($doctor['doc_id']);
    if ($fullDetails) {
        // Also get specialization description if available
        if ($fullDetails['doc_specialization_id']) {
            $spec = Specialization::findById($fullDetails['doc_specialization_id']);
            if ($spec) {
                $fullDetails['spec_description'] = $spec['spec_description'] ?? null;
            }
        }
        $doctorsWithDetails[] = $fullDetails;
    }
}
$doctors = $doctorsWithDetails;

$specializations = $specializationModel->getAllSpecializations();

require_once __DIR__ . '/../../views/patient/book.view.php';

