<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div style="background: white; border: 1px solid #e5e7eb; padding: 24px; border-radius: 8px; margin-bottom: 30px;">
    <h2 style="margin: 0 0 8px 0; color: #1f2937; font-size: 24px; font-weight: 700; display: flex; align-items: center; gap: 12px;">
        <span class="material-icons" style="color: #3b82f6; font-size: 28px;">person</span>
        Welcome, <?= htmlspecialchars($patient['pat_first_name'] ?? 'Patient') ?> <?= htmlspecialchars($patient['pat_last_name'] ?? '') ?>
    </h2>
    <p style="margin: 0; color: #6b7280; font-size: 14px;">Manage your appointments and health information</p>
</div>
    
<?php if ($error): ?>
    <div class="alert alert-error">
        <span class="material-icons md-20">error</span>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<div style="margin-bottom: 30px;">
    <a href="/patient/appointments/create" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-size: 15px; padding: 12px 24px;">
        <span class="material-icons md-20">add_circle</span>
        Book New Appointment
    </a>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div style="background: white; border: 1px solid #e5e7eb; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 14px; font-weight: 500;">Total Appointments</p>
                <h2 style="margin: 0; font-size: 32px; color: #1f2937; font-weight: 700;"><?= count($appointments) ?></h2>
            </div>
            <div style="background: #eff6ff; padding: 12px; border-radius: 8px;">
                <span class="material-icons" style="color: #3b82f6; font-size: 28px;">calendar_today</span>
            </div>
        </div>
    </div>
    
    <div style="background: white; border: 1px solid #e5e7eb; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 14px; font-weight: 500;">Upcoming</p>
                <h2 style="margin: 0; font-size: 32px; color: #1f2937; font-weight: 700;"><?= count($upcoming_appointments) ?></h2>
            </div>
            <div style="background: #fef3c7; padding: 12px; border-radius: 8px;">
                <span class="material-icons" style="color: #f59e0b; font-size: 28px;">event_available</span>
            </div>
        </div>
    </div>
    
    <div style="background: white; border: 1px solid #e5e7eb; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 14px; font-weight: 500;">Past</p>
                <h2 style="margin: 0; font-size: 32px; color: #1f2937; font-weight: 700;"><?= count($past_appointments) ?></h2>
            </div>
            <div style="background: #d1fae5; padding: 12px; border-radius: 8px;">
                <span class="material-icons" style="color: #10b981; font-size: 28px;">check_circle</span>
            </div>
        </div>
    </div>
</div>

<div class="table-container" style="margin-bottom: 30px;">
    <div class="table-header">
        <h2 style="margin: 0; display: flex; align-items: center; gap: 8px;">
            <span class="material-icons" style="color: #3b82f6;">event_available</span>
            Upcoming Appointments
        </h2>
    </div>
    <?php if (empty($upcoming_appointments)): ?>
        <div style="padding: 60px 20px; text-align: center; color: #6b7280;">
            <span class="material-icons" style="font-size: 64px; color: #d1d5db; margin-bottom: 16px; display: block;">event_busy</span>
            <p style="margin: 0 0 12px 0; font-size: 16px; font-weight: 500;">No upcoming appointments</p>
            <a href="/patient/appointments/create" class="btn btn-primary" style="margin-top: 8px;">Book one now</a>
        </div>
    <?php else: ?>
        <div style="padding: 20px; display: grid; gap: 16px;">
            <?php foreach ($upcoming_appointments as $apt): ?>
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; background: #f9fafb;">
                    <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 20px; align-items: start;">
                        <div style="text-align: center; min-width: 80px;">
                            <div style="background: #3b82f6; color: white; padding: 12px; border-radius: 8px;">
                                <div style="font-size: 24px; font-weight: bold;"><?= date('d', strtotime($apt['appointment_date'])) ?></div>
                                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;"><?= date('M Y', strtotime($apt['appointment_date'])) ?></div>
                            </div>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 12px 0; font-size: 18px; color: #1f2937; display: flex; align-items: center; gap: 8px;">
                                <span class="material-icons md-20" style="color: #3b82f6;">medical_services</span>
                                Dr. <?= htmlspecialchars($apt['doc_first_name'] . ' ' . $apt['doc_last_name']) ?>
                            </h3>
                            <div style="display: grid; gap: 6px; font-size: 14px; color: #6b7280;">
                                <p style="margin: 0; display: flex; align-items: center; gap: 8px;">
                                    <span class="material-icons md-18" style="color: #9ca3af;">school</span>
                                    <strong>Specialization:</strong> <?= htmlspecialchars($apt['spec_name'] ?? 'N/A') ?>
                                </p>
                                <p style="margin: 0; display: flex; align-items: center; gap: 8px;">
                                    <span class="material-icons md-18" style="color: #9ca3af;">medical_services</span>
                                    <strong>Service:</strong> <?= htmlspecialchars($apt['service_name'] ?? 'N/A') ?>
                                </p>
                                <p style="margin: 0; display: flex; align-items: center; gap: 8px;">
                                    <span class="material-icons md-18" style="color: #9ca3af;">schedule</span>
                                    <strong>Time:</strong> <?= htmlspecialchars($apt['appointment_time'] ?? 'N/A') ?> (<?= htmlspecialchars($apt['appointment_duration'] ?? 30) ?> min)
                                </p>
                                <p style="margin: 0; display: flex; align-items: center; gap: 8px;">
                                    <span class="material-icons md-18" style="color: #9ca3af;">confirmation_number</span>
                                    <strong>ID:</strong> <?= htmlspecialchars($apt['appointment_id']) ?>
                                </p>
                                <?php if ($apt['appointment_notes']): ?>
                                    <p style="margin: 0; display: flex; align-items: center; gap: 8px;">
                                        <span class="material-icons md-18" style="color: #9ca3af;">notes</span>
                                        <strong>Notes:</strong> <?= htmlspecialchars($apt['appointment_notes']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <span style="background: <?= $apt['status_color'] ?? '#3B82F6' ?>; color: white; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; display: inline-block;">
                                <?= htmlspecialchars($apt['status_name'] ?? 'N/A') ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="table-container">
    <div class="table-header">
        <h2 style="margin: 0; display: flex; align-items: center; gap: 8px;">
            <span class="material-icons" style="color: #3b82f6;">history</span>
            Past Appointments
        </h2>
    </div>
    <div class="table-responsive">
        <?php if (empty($past_appointments)): ?>
            <div style="padding: 40px; text-align: center; color: #6b7280;">
                <span class="material-icons" style="font-size: 48px; color: #d1d5db; margin-bottom: 12px; display: block;">history</span>
                <p style="margin: 0;">No past appointments.</p>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Doctor</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Appointment ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($past_appointments as $apt): ?>
                        <tr>
                            <td><?= htmlspecialchars($apt['appointment_date']) ?></td>
                            <td><?= htmlspecialchars($apt['appointment_time'] ?? 'N/A') ?></td>
                            <td>Dr. <?= htmlspecialchars($apt['doc_first_name'] . ' ' . $apt['doc_last_name']) ?></td>
                            <td><?= htmlspecialchars($apt['service_name'] ?? 'N/A') ?></td>
                            <td>
                                <span style="background: <?= $apt['status_color'] ?? '#3B82F6' ?>; color: white; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                    <?= htmlspecialchars($apt['status_name'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($apt['appointment_id']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>