<?php
return [
    '' => 'controllers/index.php',
    'login' => 'controllers/login.php',
    'register' => 'controllers/register.php',
    'logout' => 'controllers/logout.php',
    'view-as' => 'controllers/view-as.php',
    
    // Super Admin Routes
    'superadmin/dashboard' => 'controllers/superadmin/dashboard.php',
    'superadmin/users' => 'controllers/superadmin/users.php',
    'superadmin/patients' => 'controllers/superadmin/patients.php',
    'superadmin/doctors' => 'controllers/superadmin/doctors.php',
    'superadmin/staff' => 'controllers/superadmin/staff.php',
    'superadmin/services' => 'controllers/superadmin/services.php',
    'superadmin/appointments' => 'controllers/superadmin/appointments.php',
    'superadmin/specializations' => 'controllers/superadmin/specializations.php',
    'superadmin/statuses' => 'controllers/superadmin/statuses.php',
    'superadmin/payment-methods' => 'controllers/superadmin/payment-methods.php',
    'superadmin/payment-statuses' => 'controllers/superadmin/payment-statuses.php',
    'superadmin/payments' => 'controllers/superadmin/payments.php',
    'superadmin/medical-records' => 'controllers/superadmin/medical-records.php',
    'superadmin/schedules' => 'controllers/superadmin/schedules.php',
    
    // Staff Routes
    'staff/dashboard' => 'controllers/staff/dashboard.php',
    'staff/staff' => 'controllers/staff/staff.php',
    'staff/services' => 'controllers/staff/services.php',
    'staff/service-appointments' => 'controllers/staff/service-appointments.php',
    'staff/specializations' => 'controllers/staff/specializations.php',
    'staff/specialization-doctors' => 'controllers/staff/specialization-doctors.php',
    'staff/statuses' => 'controllers/staff/statuses.php',
    'staff/payment-methods' => 'controllers/staff/payment-methods.php',
    'staff/payment-statuses' => 'controllers/staff/payment-statuses.php',
    'staff/payments' => 'controllers/staff/payments.php',
    'staff/medical-records' => 'controllers/staff/medical-records.php',
    
    // Doctor Routes
    'doctor/dashboard' => 'controllers/doctor/dashboard.php',
    'doctor/appointments' => 'controllers/doctor/appointments.php',
    'doctor/appointments/today' => 'controllers/doctor/appointments-today.php',
    'doctor/appointments/previous' => 'controllers/doctor/appointments-previous.php',
    'doctor/appointments/future' => 'controllers/doctor/appointments-future.php',
    'doctor/appointment-actions' => 'controllers/doctor/appointment-actions.php',
    'doctor/profile' => 'controllers/doctor/profile.php',
    'doctor/schedules' => 'controllers/doctor/schedules.php',
    'doctor/schedules/manage' => 'controllers/doctor/schedules-manage.php',
    'doctor/doctors' => 'controllers/doctor/doctors.php',
    'doctor/medical-records' => 'controllers/doctor/medical-records.php',
    
    // Patient Routes
    'patient/dashboard' => 'controllers/patient/dashboard.php',
    'patient/appointments' => 'controllers/patient/appointments.php',
    'patient/appointments/today' => 'controllers/patient/appointments-today.php',
    'patient/appointments/upcoming' => 'controllers/patient/appointments-upcoming.php',
    'patient/appointments/past' => 'controllers/patient/appointments-past.php',
    'patient/appointments/create' => 'controllers/patient/create-appointment.php',
    'patient/create-appointment' => 'controllers/patient/create-appointment.php',
    'patient/reschedule-appointment' => 'controllers/patient/reschedule-appointment.php',
    'patient/appointment-review' => 'controllers/patient/appointment-review.php',
    'patient/book' => 'controllers/patient/book.php',
    'patient/doctor-detail' => 'controllers/patient/doctor-detail.php',
    'patient/payment' => 'controllers/patient/payment.php',
    'patient/payment-confirmation' => 'controllers/patient/payment-confirmation.php',
    'patient/notifications' => 'controllers/patient/notifications.php',
    'patient/profile' => 'controllers/patient/profile.php',
    'patient/account' => 'controllers/patient/account.php',
    'patient/settings' => 'controllers/patient/settings.php',
    'patient/privacy' => 'controllers/patient/privacy.php',
    
    // Doctor Routes - Additional
    'doctor/account' => 'controllers/doctor/account.php',
    'doctor/edit-profile' => 'controllers/doctor/edit-profile.php',
    'doctor/settings' => 'controllers/doctor/settings.php',
    'doctor/privacy' => 'controllers/doctor/privacy.php',
    
    // Staff Routes - Additional
    'staff/account' => 'controllers/staff/account.php',
    'staff/edit-profile' => 'controllers/staff/edit-profile.php',
    'staff/change-email' => 'controllers/staff/change-email.php',
    'staff/change-email-success' => 'controllers/staff/change-email-success.php',
    'staff/settings' => 'controllers/staff/settings.php',
    'staff/privacy' => 'controllers/staff/privacy.php',
    
    // Super Admin Routes - Additional
    'superadmin/account' => 'controllers/superadmin/account.php',
    'superadmin/edit-profile' => 'controllers/superadmin/edit-profile.php',
    'superadmin/settings' => 'controllers/superadmin/settings.php',
    'superadmin/privacy' => 'controllers/superadmin/privacy.php',
    
    // Patient Routes - Additional
    'patient/edit-profile' => 'controllers/patient/edit-profile.php',
    'patient/change-email' => 'controllers/patient/change-email.php',
    'patient/change-email-success' => 'controllers/patient/change-email-success.php',
    
    // Doctor Routes - Additional (change-email)
    'doctor/change-email' => 'controllers/doctor/change-email.php',
    'doctor/change-email-success' => 'controllers/doctor/change-email-success.php',
];