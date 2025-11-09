<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div style="margin-bottom: 30px; background: white; border: 1px solid #e5e7eb; padding: 24px; border-radius: 8px;">
    <h1 style="margin: 0 0 8px 0; color: #1f2937; font-size: 28px; font-weight: 700; display: flex; align-items: center; gap: 12px;">
        <span class="material-icons" style="color: #3b82f6; font-size: 32px;">person</span>
        Welcome, Dr. <?= htmlspecialchars($doctor['doc_last_name'] ?? 'Doctor') ?>
    </h1>
    <p style="margin: 0; color: #6b7280; font-size: 14px; display: flex; align-items: center; gap: 8px;">
        <span class="material-icons md-18">medical_services</span>
        <?= htmlspecialchars($doctor['spec_name'] ?? 'General Practice') ?>
        <span style="margin: 0 8px;">•</span>
        <span class="material-icons md-18">calendar_today</span>
        <?= date('l, F j, Y') ?>
    </p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div style="background: white; border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 13px; font-weight: 500;">Total Appointments</p>
                <h2 style="margin: 0; font-size: 28px; color: #1f2937; font-weight: 700;"><?= $stats['total_appointments'] ?></h2>
            </div>
            <div style="background: #eff6ff; padding: 10px; border-radius: 6px;">
                <span class="material-icons" style="color: #3b82f6; font-size: 24px;">event_note</span>
            </div>
        </div>
    </div>

    <div style="background: white; border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 13px; font-weight: 500;">Today's Appointments</p>
                <h2 style="margin: 0; font-size: 28px; color: #1f2937; font-weight: 700;"><?= $stats['today_appointments'] ?></h2>
            </div>
            <div style="background: #fef3c7; padding: 10px; border-radius: 6px;">
                <span class="material-icons" style="color: #f59e0b; font-size: 24px;">today</span>
            </div>
        </div>
    </div>

    <div style="background: white; border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 13px; font-weight: 500;">Upcoming</p>
                <h2 style="margin: 0; font-size: 28px; color: #1f2937; font-weight: 700;"><?= $stats['upcoming_appointments'] ?></h2>
            </div>
            <div style="background: #dbeafe; padding: 10px; border-radius: 6px;">
                <span class="material-icons" style="color: #2563eb; font-size: 24px;">event_available</span>
            </div>
        </div>
    </div>

    <div style="background: white; border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 13px; font-weight: 500;">Completed</p>
                <h2 style="margin: 0; font-size: 28px; color: #1f2937; font-weight: 700;"><?= $stats['completed_appointments'] ?></h2>
            </div>
            <div style="background: #d1fae5; padding: 10px; border-radius: 6px;">
                <span class="material-icons" style="color: #10b981; font-size: 24px;">check_circle</span>
            </div>
        </div>
    </div>

    <div style="background: white; border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 13px; font-weight: 500;">Total Patients</p>
                <h2 style="margin: 0; font-size: 28px; color: #1f2937; font-weight: 700;"><?= $stats['total_patients'] ?></h2>
            </div>
            <div style="background: #f3e8ff; padding: 10px; border-radius: 6px;">
                <span class="material-icons" style="color: #a855f7; font-size: 24px;">local_hospital</span>
            </div>
        </div>
    </div>

    <div style="background: white; border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 13px; font-weight: 500;">My Schedules</p>
                <h2 style="margin: 0; font-size: 28px; color: #1f2937; font-weight: 700;"><?= $stats['my_schedules'] ?></h2>
            </div>
            <div style="background: #fce7f3; padding: 10px; border-radius: 6px;">
                <span class="material-icons" style="color: #ec4899; font-size: 24px;">schedule</span>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
    <div class="table-container" style="margin: 0;">
        <div class="table-header">
            <h2 style="margin: 0; display: flex; align-items: center; gap: 8px; font-size: 18px;">
                <span class="material-icons md-20" style="color: #3b82f6;">today</span>
                Today's Appointments
            </h2>
            <a href="/doctor/appointments/today" class="btn btn-primary" style="font-size: 13px; padding: 8px 16px;">View All</a>
        </div>
        <div class="table-responsive">
            <?php if (empty($today_appointments)): ?>
                <div style="padding: 30px; text-align: center; color: #6b7280;">
                    <span class="material-icons" style="font-size: 40px; color: #d1d5db;">event_busy</span>
                    <p style="margin: 8px 0 0 0; font-size: 14px;">No appointments today</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($today_appointments as $apt): ?>
                            <tr>
                                <td><?= date('g:i A', strtotime($apt['appointment_time'])) ?></td>
                                <td><?= htmlspecialchars($apt['pat_first_name'] . ' ' . $apt['pat_last_name']) ?></td>
                                <td>
                                    <span style="padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 500;
                                        background: <?= $apt['status_color'] ?? '#e0e0e0' ?>20; 
                                        color: <?= $apt['status_color'] ?? '#666' ?>;">
                                        <?= htmlspecialchars($apt['status_name']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-container" style="margin: 0;">
        <div class="table-header">
            <h2 style="margin: 0; display: flex; align-items: center; gap: 8px; font-size: 18px;">
                <span class="material-icons md-20" style="color: #3b82f6;">schedule</span>
                My Schedule Today
            </h2>
            <a href="/doctor/schedules" class="btn btn-primary" style="font-size: 13px; padding: 8px 16px;">Manage</a>
        </div>
        <div class="table-responsive">
            <?php if (empty($today_schedule)): ?>
                <div style="padding: 30px; text-align: center; color: #6b7280;">
                    <span class="material-icons" style="font-size: 40px; color: #d1d5db;">schedule</span>
                    <p style="margin: 8px 0 0 0; font-size: 14px;">No schedule for today</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($today_schedule as $schedule): ?>
                            <tr>
                                <td><?= date('g:i A', strtotime($schedule['start_time'])) ?></td>
                                <td><?= date('g:i A', strtotime($schedule['end_time'])) ?></td>
                                <td>
                                    <span style="padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 500;
                                        background: <?= $schedule['is_available'] ? '#d1fae5' : '#fee2e2' ?>; 
                                        color: <?= $schedule['is_available'] ? '#059669' : '#dc2626' ?>;">
                                        <?= $schedule['is_available'] ? 'Yes' : 'No' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h2 style="margin: 0; display: flex; align-items: center; gap: 8px;">
            <span class="material-icons" style="color: #3b82f6;">event_available</span>
            Upcoming Appointments
        </h2>
        <a href="/doctor/appointments/future" class="btn btn-primary">View All</a>
    </div>
    <div class="table-responsive">
        <?php if (empty($upcoming_appointments)): ?>
            <div style="padding: 40px; text-align: center; color: #6b7280;">
                <span class="material-icons" style="font-size: 48px; color: #d1d5db; margin-bottom: 12px;">event_busy</span>
                <p style="margin: 0;">No upcoming appointments</p>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcoming_appointments as $apt): ?>
                        <tr>
                            <td><?= date('M j, Y', strtotime($apt['appointment_date'])) ?></td>
                            <td><?= date('g:i A', strtotime($apt['appointment_time'])) ?></td>
                            <td><?= htmlspecialchars($apt['pat_first_name'] . ' ' . $apt['pat_last_name']) ?></td>
                            <td><?= htmlspecialchars($apt['status_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-top: 30px;">
    <a href="/doctor/appointments/today" style="background: white; border: 2px solid #3b82f6; color: #3b82f6; padding: 20px; border-radius: 8px; text-decoration: none; text-align: center; transition: all 0.2s;" onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='white'">
        <span class="material-icons" style="font-size: 32px; margin-bottom: 8px; display: block;">today</span>
        <div style="font-size: 15px; font-weight: 600;">Today's Appointments</div>
    </a>
    
    <a href="/doctor/schedules" style="background: white; border: 2px solid #10b981; color: #10b981; padding: 20px; border-radius: 8px; text-decoration: none; text-align: center; transition: all 0.2s;" onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background='white'">
        <span class="material-icons" style="font-size: 32px; margin-bottom: 8px; display: block;">schedule</span>
        <div style="font-size: 15px; font-weight: 600;">My Schedules</div>
    </a>
    
    <a href="/doctor/schedules/manage" style="background: white; border: 2px solid #f59e0b; color: #f59e0b; padding: 20px; border-radius: 8px; text-decoration: none; text-align: center; transition: all 0.2s;" onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='white'">
        <span class="material-icons" style="font-size: 32px; margin-bottom: 8px; display: block;">event</span>
        <div style="font-size: 15px; font-weight: 600;">All Schedules</div>
    </a>
    
    <a href="/doctor/doctors" style="background: white; border: 2px solid #a855f7; color: #a855f7; padding: 20px; border-radius: 8px; text-decoration: none; text-align: center; transition: all 0.2s;" onmouseover="this.style.background='#f3e8ff'" onmouseout="this.style.background='white'">
        <span class="material-icons" style="font-size: 32px; margin-bottom: 8px; display: block;">medical_services</span>
        <div style="font-size: 15px; font-weight: 600;">Manage Doctors</div>
    </a>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>