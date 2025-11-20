<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/services/DoctorService.php';
require_once __DIR__ . '/../../includes/functions.php';

$auth = new Auth();
$auth->requireDoctor();

$doctor_id = $auth->getDoctorId();
$service = new DoctorService();

$sort_column = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'appointment_date';
$sort_order = isset($_GET['order']) ? sanitize($_GET['order']) : 'DESC';

$result = $service->getAppointmentsOverview($doctor_id, [
    'sort' => $sort_column,
    'order' => $sort_order
]);

$previous_appointments = $result['previous'];
$today_appointments = $result['today'];
$upcoming_appointments = $result['upcoming'];
$previous_count = $result['counts']['previous'];
$today_count = $result['counts']['today'];
$upcoming_count = $result['counts']['upcoming'];

require_once __DIR__ . '/../../views/doctor/appointments.view.php';

