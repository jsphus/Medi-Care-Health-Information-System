<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../classes/Appointment.php';
require_once __DIR__ . '/../../classes/User.php';

$auth = new Auth();
$auth->requirePatient();

$patient_id = $auth->getPatientId();
$error = '';
$appointmentModel = new Appointment();

// Initialize profile picture for consistent display across the system
$profile_picture_url = User::initializeProfilePicture($auth);

// For now, we'll show appointment-based notifications
// In a full implementation, you'd have a notifications table
// Use upcoming appointments as notifications
$notifications = $appointmentModel->getUpcomingForPatient($patient_id, 20);

require_once __DIR__ . '/../../views/patient/notifications.view.php';

