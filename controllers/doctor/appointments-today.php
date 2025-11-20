<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/services/DoctorService.php';

$auth = new Auth();
$auth->requireDoctor();

$doctor_id = $auth->getDoctorId();
$service = new DoctorService();

$sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'appointment_time';
$sort_order = isset($_GET['order']) ? sanitize($_GET['order']) : 'DESC';

$result = $service->getAppointmentsForDoctor($doctor_id, 'today', [
    'sort' => $sort_column,
    'order' => $sort_order
]);

$appointments = $result['appointments'];
$today_count = $result['stats']['today'];
$past_count = $result['stats']['past'];
$future_count = $result['stats']['future'];

require_once __DIR__ . '/../../views/doctor/appointments-today.view.php';
