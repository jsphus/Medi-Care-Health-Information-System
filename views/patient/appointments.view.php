<?php 
require_once __DIR__ . '/../partials/header.php';

// Determine current tab
$currentTab = 'all';
if (strpos($_SERVER['REQUEST_URI'], '/today') !== false) {
    $currentTab = 'today';
} elseif (strpos($_SERVER['REQUEST_URI'], '/upcoming') !== false) {
    $currentTab = 'upcoming';
} elseif (strpos($_SERVER['REQUEST_URI'], '/past') !== false) {
    $currentTab = 'past';
}

// Determine which appointments to show
$displayAppointments = [];
if ($currentTab === 'today') {
    $displayAppointments = $today_appointments ?? [];
} elseif ($currentTab === 'upcoming') {
    $displayAppointments = $upcoming_appointments ?? [];
} elseif ($currentTab === 'past') {
    $displayAppointments = $past_appointments ?? [];
} else {
    // All tab - combine upcoming and past
    $displayAppointments = array_merge($upcoming_appointments ?? [], $past_appointments ?? []);
}
?>

<style>
.tab-link {
    transition: all 0.2s;
}

.tab-link.active {
    color: var(--primary-blue) !important;
    border-bottom-color: var(--primary-blue) !important;
    font-weight: 600 !important;
}

.tab-link:hover {
    color: var(--primary-blue) !important;
}

.appointment-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 1rem;
    transition: all 0.2s;
}

.appointment-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    border-color: var(--primary-blue);
}

.appointment-card.urgent {
    border-left: 4px solid #ef4444;
    background: #fef2f2;
}

.appointment-card.today {
    border-left: 4px solid #3b82f6;
    background: #eff6ff;
}

.appointment-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.appointment-doctor-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.appointment-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.25rem;
    flex-shrink: 0;
    overflow: hidden;
}

.appointment-doctor-details h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.appointment-doctor-details p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.appointment-status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

.appointment-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.appointment-detail-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.appointment-detail-item .icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    flex-shrink: 0;
}

.appointment-detail-item .label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin-bottom: 0.25rem;
}

.appointment-detail-item .value {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary);
}

.appointment-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    padding-top: 1rem;
    margin-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.next-appointment-banner {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 12px;
    padding: 1.5rem;
    color: white;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.next-appointment-banner.urgent {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.next-appointment-banner.soon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.next-appointment-banner-content h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    font-weight: 700;
}

.next-appointment-banner-content p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.875rem;
}

.quick-filter-chips {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}

.filter-chip {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    background: #f3f4f6;
    color: var(--text-primary);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid transparent;
    text-decoration: none;
    display: inline-block;
}

.filter-chip:hover {
    background: #e5e7eb;
}

.filter-chip.active {
    background: var(--primary-blue);
    color: white;
    border-color: var(--primary-blue);
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 1px solid #e5e7eb;
}

.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.stat-card-content {
    flex: 1;
}

.stat-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.stat-trend {
    font-size: 0.75rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(59, 130, 246, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-blue);
    font-size: 1.5rem;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .appointment-details-grid {
        grid-template-columns: 1fr;
    }
    
    .appointment-card-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .next-appointment-banner {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>

<div class="page-header">
    <div class="page-header-top">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 class="page-title" style="margin: 0;">My Appointments</h1>
                <p style="color: var(--text-secondary); font-size: 0.9375rem; margin-top: 0.5rem;">
                    Manage all your appointments in one place
                </p>
            </div>
            <a href="/patient/book" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem; white-space: nowrap;">
                <i class="fas fa-plus"></i>
                <span>Book Appointment</span>
            </a>
        </div>
    </div>
    <!-- Navigation Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem; border-bottom: 2px solid var(--border-light); flex-wrap: wrap;">
        <a href="/patient/appointments/today" class="tab-link <?= $currentTab === 'today' ? 'active' : '' ?>" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-calendar-day"></i>
            <span>Today</span>
            <?php if (($stats['today'] ?? 0) > 0): ?>
                <span class="badge" style="background: var(--primary-blue); color: white; padding: 0.125rem 0.5rem; border-radius: 10px; font-size: 0.75rem;"><?= $stats['today'] ?></span>
            <?php endif; ?>
        </a>
        <a href="/patient/appointments" class="tab-link <?= $currentTab === 'all' ? 'active' : '' ?>" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-th-large"></i>
            <span>All</span>
        </a>
        <a href="/patient/appointments/upcoming" class="tab-link <?= $currentTab === 'upcoming' ? 'active' : '' ?>" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-calendar-check"></i>
            <span>Upcoming</span>
            <?php if (($stats['upcoming'] ?? 0) > 0): ?>
                <span class="badge" style="background: var(--status-success); color: white; padding: 0.125rem 0.5rem; border-radius: 10px; font-size: 0.75rem;"><?= $stats['upcoming'] ?></span>
            <?php endif; ?>
        </a>
        <a href="/patient/appointments/past" class="tab-link <?= $currentTab === 'past' ? 'active' : '' ?>" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-history"></i>
            <span>Past</span>
            <?php if (($stats['past'] ?? 0) > 0): ?>
                <span class="badge" style="background: var(--text-secondary); color: white; padding: 0.125rem 0.5rem; border-radius: 10px; font-size: 0.75rem;"><?= $stats['past'] ?></span>
            <?php endif; ?>
        </a>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span><?= $success ?></span>
    </div>
<?php endif; ?>

<!-- Enhanced Statistics Cards -->
<div class="stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-content">
                <div class="stat-label">
                    <i class="fas fa-calendar" style="color: #8b5cf6;"></i>
                    <span>Total Appointments</span>
                </div>
                <div class="stat-value"><?= number_format($stats['total'] ?? 0) ?></div>
                <div class="stat-trend">
                    <i class="fas fa-info-circle"></i>
                    <span>All time</span>
                </div>
            </div>
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                <i class="fas fa-calendar"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-content">
                <div class="stat-label">
                    <i class="fas fa-calendar-day" style="color: var(--primary-blue);"></i>
                    <span>Today</span>
                </div>
                <div class="stat-value"><?= number_format($stats['today'] ?? 0) ?></div>
                <div class="stat-trend">
                    <i class="fas fa-clock"></i>
                    <span>Scheduled for today</span>
                </div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-content">
                <div class="stat-label">
                    <i class="fas fa-calendar-check" style="color: var(--status-success);"></i>
                    <span>Upcoming</span>
                </div>
                <div class="stat-value"><?= number_format($stats['upcoming'] ?? 0) ?></div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>Future scheduled</span>
                </div>
            </div>
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--status-success);">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-content">
                <div class="stat-label">
                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                    <span>Completed</span>
                </div>
                <div class="stat-value"><?= number_format($stats['completed'] ?? 0) ?></div>
                <div class="stat-trend">
                    <i class="fas fa-history"></i>
                    <span>Past appointments</span>
                </div>
            </div>
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    
    <?php if (isset($stats['this_week']) && $stats['this_week'] > 0): ?>
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-content">
                <div class="stat-label">
                    <i class="fas fa-calendar-week" style="color: #8b5cf6;"></i>
                    <span>This Week</span>
                </div>
                <div class="stat-value"><?= number_format($stats['this_week']) ?></div>
                <div class="stat-trend">
                    <i class="fas fa-calendar"></i>
                    <span>This week's appointments</span>
                </div>
            </div>
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                <i class="fas fa-calendar-week"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
</div>

<!-- Next Appointment Banner (for Today/Upcoming tabs) -->
<?php if ($next_appointment && ($currentTab === 'today' || $currentTab === 'upcoming' || $currentTab === 'all')): ?>
    <?php
    $appointmentDate = $next_appointment['appointment_date'];
    $appointmentTime = isset($next_appointment['appointment_time']) ? strtotime($next_appointment['appointment_time']) : 0;
    $appointmentDateTime = strtotime($appointmentDate . ' ' . ($next_appointment['appointment_time'] ?? '00:00:00'));
    $currentTime = time();
    $timeUntil = $appointmentDateTime - $currentTime;
    $hoursUntil = floor($timeUntil / 3600);
    $minutesUntil = floor(($timeUntil % 3600) / 60);
    
    $isUrgent = $timeUntil > 0 && $timeUntil < 3600; // Less than 1 hour
    $isSoon = $timeUntil > 0 && $timeUntil < 86400; // Less than 24 hours
    $isOverdue = $timeUntil < 0;
    
    $bannerClass = $isUrgent ? 'urgent' : ($isSoon ? 'soon' : '');
    $timeText = $isOverdue ? 'Overdue' : ($hoursUntil > 0 ? "In $hoursUntil hours" : ($minutesUntil > 0 ? "In $minutesUntil minutes" : 'Starting now'));
    
    $docName = 'Dr. ' . htmlspecialchars(formatFullName($next_appointment['doc_first_name'] ?? '', $next_appointment['doc_middle_initial'] ?? null, $next_appointment['doc_last_name'] ?? ''));
    $serviceName = htmlspecialchars($next_appointment['service_name'] ?? 'Consultation');
    $appointmentDateFormatted = date('l, M j, Y', strtotime($appointmentDate));
    $appointmentTimeFormatted = isset($next_appointment['appointment_time']) ? date('g:i A', strtotime($next_appointment['appointment_time'])) : 'N/A';
    ?>
    <div class="next-appointment-banner <?= $bannerClass ?>">
        <div class="next-appointment-banner-content">
            <h3>Next Appointment</h3>
            <p><strong><?= $docName ?></strong> - <?= $serviceName ?></p>
            <p><?= $appointmentDateFormatted ?> at <?= $appointmentTimeFormatted ?></p>
            <p style="margin-top: 0.5rem; font-weight: 600;"><?= $timeText ?></p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="/patient/reschedule-appointment?id=<?= htmlspecialchars($next_appointment['appointment_id']) ?>" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-calendar-alt"></i> Reschedule
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Appointments List -->
<?php if (empty($displayAppointments)): ?>
    <div class="empty-state">
        <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
        <div class="empty-state-text">
            <?php if ($currentTab === 'today'): ?>
                No appointments scheduled for today.
            <?php elseif ($currentTab === 'upcoming'): ?>
                No upcoming appointments scheduled.
            <?php elseif ($currentTab === 'past'): ?>
                No past appointments found.
            <?php else: ?>
                No appointments found.
            <?php endif; ?>
        </div>
        <a href="/patient/book" class="empty-state-link">Book your first appointment now!</a>
    </div>
<?php else: ?>
    <?php foreach ($displayAppointments as $apt): ?>
        <?php
        $statusName = strtolower($apt['status_name'] ?? 'scheduled');
        $isCompleted = $statusName === 'completed';
        $isCanceled = $statusName === 'canceled' || $statusName === 'cancelled';
        $statusClass = $isCompleted ? 'badge-success' : ($isCanceled ? 'badge-error' : 'badge-warning');
        
        $docInitial = strtoupper(substr($apt['doc_first_name'] ?? 'D', 0, 1));
        $docName = 'Dr. ' . htmlspecialchars(formatFullName($apt['doc_first_name'] ?? '', $apt['doc_middle_initial'] ?? null, $apt['doc_last_name'] ?? ''));
        $specName = htmlspecialchars($apt['spec_name'] ?? 'General Practice');
        
        // Check if urgent (within 24 hours)
        $appointmentDate = $apt['appointment_date'];
        $appointmentTime = isset($apt['appointment_time']) ? strtotime($apt['appointment_time']) : 0;
        $appointmentDateTime = strtotime($appointmentDate . ' ' . ($apt['appointment_time'] ?? '00:00:00'));
        $currentTime = time();
        $timeUntil = $appointmentDateTime - $currentTime;
        $isUrgent = $timeUntil > 0 && $timeUntil < 86400 && !$isCompleted && !$isCanceled;
        $isToday = $appointmentDate === date('Y-m-d');
        
        $cardClass = $isUrgent ? 'urgent' : ($isToday ? 'today' : '');
        
        // Payment status (for Pay Now button only)
        $paymentStatusName = isset($apt['payment_status_name']) ? strtolower($apt['payment_status_name']) : '';
        $isPaid = $paymentStatusName === 'paid';
        $isPending = $paymentStatusName === 'pending';
        $isUnpaid = empty($paymentStatusName) || (!$isPaid && !$isPending);
        ?>
        <div class="appointment-card <?= $cardClass ?>">
            <div class="appointment-card-header">
                <div class="appointment-doctor-info">
                    <div class="appointment-avatar" style="overflow: hidden;">
                        <?php if (!empty($apt['doctor_profile_picture'])): ?>
                            <img src="<?= htmlspecialchars($apt['doctor_profile_picture']) ?>" alt="Doctor" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?= $docInitial ?>
                        <?php endif; ?>
                    </div>
                    <div class="appointment-doctor-details">
                        <h3><?= $docName ?></h3>
                        <p><?= $specName ?></p>
                    </div>
                </div>
                <span class="appointment-status-badge" style="background: <?= $apt['status_color'] ?? '#3B82F6' ?>; color: white;">
                    <?= htmlspecialchars($apt['status_name'] ?? 'Scheduled') ?>
                </span>
            </div>
            
            <div class="appointment-details-grid">
                <div class="appointment-detail-item">
                    <div class="icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div>
                        <div class="label">Date</div>
                        <div class="value"><?= date('l, M j, Y', strtotime($apt['appointment_date'])) ?></div>
                    </div>
                </div>
                
                <?php if ($apt['appointment_time']): ?>
                <div class="appointment-detail-item">
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="label">Time</div>
                        <div class="value"><?= date('g:i A', strtotime($apt['appointment_time'])) ?></div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (isset($apt['service_name']) && $apt['service_name']): ?>
                <div class="appointment-detail-item">
                    <div class="icon">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div>
                        <div class="label">Service</div>
                        <div class="value"><?= htmlspecialchars($apt['service_name']) ?></div>
                    </div>
                </div>
                <?php endif; ?>
                
            </div>
            
            <?php if (!$isCanceled && !$isCompleted): ?>
            <div class="appointment-actions">
                <a href="/patient/reschedule-appointment?id=<?= htmlspecialchars($apt['appointment_id']) ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-calendar-alt"></i> Reschedule
                </a>
                <button type="button" onclick="cancelAppointment('<?= htmlspecialchars($apt['appointment_id']) ?>')" class="btn btn-danger" style="padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; background: #fee2e2; color: #991b1b; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <?php if ($isUnpaid || $isPending): ?>
                <a href="/patient/payment?appointment_id=<?= htmlspecialchars($apt['appointment_id']) ?>" class="btn" style="padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; background: #f59e0b; color: white; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-credit-card"></i> Pay Now
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

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
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.location.pathname;
            form.style.display = 'none';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'cancel';
            form.appendChild(actionInput);
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'appointment_id';
            idInput.value = appointmentId;
            form.appendChild(idInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
    return false;
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
