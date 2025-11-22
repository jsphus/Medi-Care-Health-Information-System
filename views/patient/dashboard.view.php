<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
.dashboard-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.dashboard-header-left {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.dashboard-welcome {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.dashboard-date {
    font-size: 0.875rem;
    color: #6b7280;
}

.dashboard-header-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.book-appointment-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
}

.book-appointment-btn:hover {
    background: #2563eb;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.kpi-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #f3f4f6;
}

.kpi-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.75rem;
    font-weight: 500;
}

.kpi-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.kpi-trend {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
}

.kpi-trend.positive {
    color: #10b981;
}

.kpi-trend.negative {
    color: #ef4444;
}

.kpi-trend-text {
    color: #6b7280;
}

.charts-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.chart-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #f3f4f6;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.chart-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.chart-wrapper {
    height: 250px;
    position: relative;
}

.bottom-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr;
    gap: 1.5rem;
}

.today-appointments-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #f3f4f6;
    margin-bottom: 2rem;
}

.appointments-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.appointment-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    transition: all 0.2s;
}

.appointment-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.appointment-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.appointment-info {
    flex: 1;
}

.appointment-doctor-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.appointment-details {
    font-size: 0.75rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.appointment-time {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.appointment-status {
    padding: 0.25rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.records-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.record-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    transition: all 0.2s;
}

.record-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.record-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #10b981;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.record-info {
    flex: 1;
}

.record-doctor-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.record-date-info {
    font-size: 0.75rem;
    color: #6b7280;
}

.payments-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.payment-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    transition: all 0.2s;
}

.payment-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.payment-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f59e0b;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.payment-info {
    flex: 1;
}

.payment-amount {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.payment-date-info {
    font-size: 0.75rem;
    color: #6b7280;
}

.view-all-btn {
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background: #f3f4f6;
    border: none;
    border-radius: 0.5rem;
    color: #374151;
    font-size: 0.875rem;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    display: block;
}

.view-all-btn:hover {
    background: #e5e7eb;
}

@media (max-width: 1024px) {
    .kpi-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .charts-grid {
        grid-template-columns: 1fr;
    }
    
    .bottom-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }
    
    .kpi-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>

<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="dashboard-header-left" style="display: flex; align-items: flex-start; gap: 1rem;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.5rem; overflow: hidden; flex-shrink: 0;">
                <?php if (!empty($profile_picture_url)): ?>
                    <img src="<?= htmlspecialchars($profile_picture_url) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <?= strtoupper(substr($patient['pat_first_name'] ?? 'P', 0, 1)) ?>
                <?php endif; ?>
            </div>
            <div>
                <h1 class="dashboard-welcome">Welcome back, <?= htmlspecialchars($patient['pat_first_name'] ?? 'Patient') ?>! 👋</h1>
                <div class="dashboard-date"><?= date('l, F d, Y') ?></div>
            </div>
        </div>
        <div class="dashboard-header-right">
            <a href="/patient/book" class="book-appointment-btn">
                <i class="fas fa-plus"></i>
                <span>Book Appointment</span>
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Total Appointments</div>
            <div class="kpi-value"><?= number_format($stats['total_appointments']) ?></div>
            <div class="kpi-trend-text" style="margin-top: 0.5rem; color: #6b7280;">
                All time appointments
            </div>
        </div>
        
        <div class="kpi-card">
            <div class="kpi-label">Upcoming Appointments</div>
            <div class="kpi-value"><?= number_format($stats['upcoming_appointments']) ?></div>
            <div class="kpi-trend positive">
                <i class="fas fa-calendar-check"></i>
                <span class="kpi-trend-text">Scheduled</span>
            </div>
        </div>
        
    </div>

    <!-- Upcoming Appointments -->
    <div class="today-appointments-card">
        <div class="chart-header">
            <h2 class="chart-title">Upcoming Appointments</h2>
            <a href="/patient/appointments" style="font-size: 0.875rem; color: #3b82f6; text-decoration: none;">View All</a>
        </div>
        <?php if (empty($upcoming_appointments)): ?>
            <div style="text-align: center; padding: 2rem; color: #6b7280;">
                <i class="fas fa-calendar-times" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                <div>No upcoming appointments</div>
                <a href="/patient/book" class="book-appointment-btn" style="margin-top: 1rem; display: inline-flex;">Book Your First Appointment</a>
            </div>
        <?php else: ?>
            <div class="appointments-list">
                <?php foreach ($upcoming_appointments as $apt): ?>
                    <?php
                    $docInitial = strtoupper(substr($apt['doc_first_name'] ?? 'D', 0, 1));
                    $docName = htmlspecialchars(formatFullName($apt['doc_first_name'] ?? '', $apt['doc_middle_initial'] ?? null, $apt['doc_last_name'] ?? ''));
                    $specName = htmlspecialchars($apt['spec_name'] ?? 'General Practice');
                    $statusName = strtolower($apt['status_name'] ?? 'scheduled');
                    $statusColor = $apt['status_color'] ?? '#3b82f6';
                    $appointmentTime = isset($apt['appointment_time']) ? date('g:i A', strtotime($apt['appointment_time'])) : 'N/A';
                    $appointmentDate = isset($apt['appointment_date']) ? date('M j, Y', strtotime($apt['appointment_date'])) : 'N/A';
                    $serviceName = htmlspecialchars($apt['service_name'] ?? 'General Consultation');
                    ?>
                    <div class="appointment-item">
                        <div class="appointment-avatar" style="overflow: hidden;">
                            <?php if (!empty($apt['doctor_profile_picture'])): ?>
                                <img src="<?= htmlspecialchars($apt['doctor_profile_picture']) ?>" alt="Doctor" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <?= $docInitial ?>
                            <?php endif; ?>
                        </div>
                        <div class="appointment-info">
                            <div class="appointment-doctor-name">Dr. <?= $docName ?></div>
                            <div class="appointment-details">
                                <span><i class="fas fa-stethoscope"></i> <?= $serviceName ?></span>
                                <span><i class="fas fa-calendar"></i> <?= $appointmentDate ?></span>
                            </div>
                        </div>
                        <div class="appointment-time"><?= $appointmentTime ?></div>
                        <span class="appointment-status" style="background: <?= $statusColor ?>20; color: <?= $statusColor ?>;">
                            <?= htmlspecialchars($apt['status_name'] ?? 'Scheduled') ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bottom Row -->
    <div class="bottom-grid">
        <!-- Quick Actions -->
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h2 class="chart-title">Quick Actions</h2>
                    <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">
                        Common tasks
                    </div>
                </div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1rem;">
                <a href="/patient/book" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-radius: 0.5rem; transition: background 0.2s; cursor: pointer; text-decoration: none; color: #1f2937; border: 1px solid #e5e7eb;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem;">
                        <i class="fas fa-book"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 0.875rem; font-weight: 600; color: #1f2937; margin-bottom: 0.125rem;">Book Appointment</div>
                        <div style="font-size: 0.75rem; color: #6b7280;">Schedule a new visit</div>
                    </div>
                    <i class="fas fa-chevron-right" style="color: #9ca3af;"></i>
                </a>
                
                <a href="/patient/appointments" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-radius: 0.5rem; transition: background 0.2s; cursor: pointer; text-decoration: none; color: #1f2937; border: 1px solid #e5e7eb;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem;">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 0.875rem; font-weight: 600; color: #1f2937; margin-bottom: 0.125rem;">My Appointments</div>
                        <div style="font-size: 0.75rem; color: #6b7280;">View all appointments</div>
                    </div>
                    <i class="fas fa-chevron-right" style="color: #9ca3af;"></i>
                </a>
                
            </div>
        </div>
    </div>
</div>

<script>
function cancelAppointment(appointmentId) {
    showConfirm(
        'Are you sure you want to cancel this appointment? This action cannot be undone.',
        'Cancel Appointment',
        'Yes, Cancel',
        'No, Keep It',
        'danger'
    ).then(confirmed => {
        if (confirmed) {
            // Create a form dynamically
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/patient/appointments';
            form.style.display = 'none';
            
            // Add action field
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'cancel';
            form.appendChild(actionInput);
            
            // Add appointment_id field
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'appointment_id';
            idInput.value = appointmentId;
            form.appendChild(idInput);
            
            // Append to body and submit
            document.body.appendChild(form);
            form.submit();
        }
    });
    return false;
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
