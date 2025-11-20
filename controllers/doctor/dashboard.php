<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/services/DoctorService.php';

$auth = new Auth();
$auth->requireDoctor();

$doc_id = $_SESSION['doc_id'];
$service = new DoctorService();

$data = $service->getDashboardData($doc_id, $auth);

$doctor = $data['doctor'];
$profile_picture_url = $data['profile_picture_url'];
$stats = $data['stats'];
$recent_appointments = $data['recent_appointments'];
$today_appointments = $data['today_appointments'];
$today_schedule = $data['today_schedule'];
$recent_patients = $data['recent_patients'];
$recent_records = $data['recent_records'];
$notifications = $data['notifications'];
$upcoming_appointments = $data['upcoming_appointments'];
$patient_list = $data['patient_list'];
$appointment_type_chart = $data['appointment_type_chart'];
$weekly_visits = $data['weekly_visits'];
$new_appointments = $data['new_appointments'];
$chart_data = $data['chart_data'];

require_once __DIR__ . '/../../views/doctor/dashboard.view.php';
