<?php require_once __DIR__ . '/../partials/header.php'; ?>

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
</style>

<div class="page-header">
    <div class="page-header-top">
        <h1 class="page-title">Appointments Overview</h1>
        <p style="color: var(--text-secondary); font-size: 0.9375rem; margin-top: 0.5rem;">
            Manage all your appointments in one place
        </p>
    </div>
    <!-- Navigation Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem; border-bottom: 2px solid var(--border-light);">
        <a href="/doctor/appointments/today" class="tab-link <?= strpos($_SERVER['REQUEST_URI'], '/today') !== false ? 'active' : '' ?>" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-calendar-day"></i>
            <span>Today</span>
            <?php if ($today_count > 0): ?>
                <span class="badge" style="background: var(--primary-blue); color: white; padding: 0.125rem 0.5rem; border-radius: 10px; font-size: 0.75rem;"><?= $today_count ?></span>
            <?php endif; ?>
        </a>
        <a href="/doctor/appointments" class="tab-link <?= strpos($_SERVER['REQUEST_URI'], '/today') === false && strpos($_SERVER['REQUEST_URI'], '/future') === false && strpos($_SERVER['REQUEST_URI'], '/previous') === false ? 'active' : '' ?>" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-th-large"></i>
            <span>All</span>
        </a>
        <a href="/doctor/appointments/future" class="tab-link <?= strpos($_SERVER['REQUEST_URI'], '/future') !== false ? 'active' : '' ?>" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-calendar-check"></i>
            <span>Upcoming</span>
            <?php if ($upcoming_count > 0): ?>
                <span class="badge" style="background: var(--status-success); color: white; padding: 0.125rem 0.5rem; border-radius: 10px; font-size: 0.75rem;"><?= $upcoming_count ?></span>
            <?php endif; ?>
        </a>
        <a href="/doctor/appointments/previous" class="tab-link <?= strpos($_SERVER['REQUEST_URI'], '/previous') !== false ? 'active' : '' ?>" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-history"></i>
            <span>Past</span>
            <?php if ($previous_count > 0): ?>
                <span class="badge" style="background: var(--text-secondary); color: white; padding: 0.125rem 0.5rem; border-radius: 10px; font-size: 0.75rem;"><?= $previous_count ?></span>
            <?php endif; ?>
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-content">
                <div class="stat-label">
                    <i class="fas fa-calendar-day"></i>
                    <span>Today's Appointments</span>
                </div>
                <div class="stat-value"><?= $today_count ?></div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
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
                    <i class="fas fa-calendar-check"></i>
                    <span>Upcoming Appointments</span>
                </div>
                <div class="stat-value"><?= $upcoming_count ?></div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>Future scheduled</span>
                </div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-content">
                <div class="stat-label">
                    <i class="fas fa-history"></i>
                    <span>Previous Appointments</span>
                </div>
                <div class="stat-value"><?= $previous_count ?></div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>Completed</span>
                </div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-history"></i>
            </div>
        </div>
    </div>
</div>

<!-- Today's Appointments Section -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 class="card-title" style="margin: 0;">
                <i class="fas fa-calendar-day" style="margin-right: 0.5rem; color: var(--primary-blue);"></i>
                Today's Appointments - <?= date('F d, Y') ?>
            </h2>
            <span class="badge" style="background: var(--primary-blue); color: white; padding: 0.25rem 0.75rem; border-radius: var(--radius-md); font-size: 0.75rem;">
                <?= $today_count ?>
            </span>
        </div>
        <button type="button" id="toggleFilterBtnToday" class="btn btn-sm" onclick="toggleTableFilters('today')" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
            <i class="fas fa-filter"></i>
        </button>
    </div>

    <!-- Filter Bar for Today -->
    <div id="tableFilterBarToday" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Appointments
            </h3>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-sm" onclick="applyTableFilters('today')" style="padding: 0.5rem 1rem; background: var(--primary-blue); border: 1px solid var(--primary-blue); border-radius: var(--radius-md); color: white; cursor: pointer; font-size: 0.875rem;">
                    <i class="fas fa-check"></i>
                    <span>Apply Filters</span>
                </button>
                <button type="button" class="btn btn-sm" onclick="resetTableFilters('today')" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
                    <i class="fas fa-redo"></i>
                    <span>Reset Filters</span>
                </button>
            </div>
        </div>
        <div class="filter-controls-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-user" style="margin-right: 0.25rem;"></i>Patient Name
                </label>
                <input type="text" id="filterPatientToday" class="filter-input" placeholder="Search patient..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-flask" style="margin-right: 0.25rem;"></i>Service
                </label>
                <input type="text" id="filterServiceToday" class="filter-input" placeholder="Search service..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>Time
                </label>
                <input type="time" id="filterTimeToday" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
        </div>
    </div>

    <?php if (empty($today_appointments)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
            <div class="empty-state-text">No appointments scheduled for today.</div>
        </div>
    <?php else: ?>
        <table class="patient-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Time</th>
                    <th>Service</th>
                    <th>Contact</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th style="width: 50px;">
                        <i class="fas fa-sticky-note notes-header-icon" title="Notes - Hover over rows to view"></i>
                    </th>
                </tr>
            </thead>
            <tbody id="tableBodyToday">
                <?php foreach ($today_appointments as $apt): ?>
                    <?php
                    $patInitial = strtoupper(substr($apt['pat_first_name'] ?? 'P', 0, 1));
                    $patName = htmlspecialchars(($apt['pat_first_name'] ?? '') . ' ' . ($apt['pat_last_name'] ?? ''));
                    $statusName = strtolower($apt['status_name'] ?? 'scheduled');
                    $isCompleted = $statusName === 'completed';
                    $isCanceled = $statusName === 'canceled' || $statusName === 'cancelled';
                    $statusClass = $isCompleted ? 'badge-success' : ($isCanceled ? 'badge-error' : 'badge-warning');
                    $appointmentTime = isset($apt['appointment_time']) ? date('g:i A', strtotime($apt['appointment_time'])) : 'N/A';
                    $notes = !empty($apt['appointment_notes']) ? htmlspecialchars($apt['appointment_notes']) : 'No notes available';
                    $paymentStatus = $apt['payment_status_name'] ?? 'N/A';
                    $paymentStatusColor = $apt['payment_status_color'] ?? '#6b7280';
                    $bookingReference = htmlspecialchars($apt['appointment_id'] ?? 'N/A');
                    ?>
                    <tr class="patient-row table-row" 
                        data-section="today"
                        data-patient="<?= htmlspecialchars(strtolower(($apt['pat_first_name'] ?? '') . ' ' . ($apt['pat_last_name'] ?? ''))) ?>"
                        data-service="<?= htmlspecialchars(strtolower($apt['service_name'] ?? '')) ?>"
                        data-time="<?= isset($apt['appointment_time']) ? date('H:i', strtotime($apt['appointment_time'])) : '' ?>"
                        data-status="<?= htmlspecialchars(strtolower($statusName)) ?>">
                        <td><strong style="color: var(--text-primary);"><?= $bookingReference ?></strong></td>
                        <td>
                            <div class="patient-info">
                                <div class="patient-avatar" style="overflow: hidden;">
                                    <?php if (!empty($apt['patient_profile_picture'])): ?>
                                        <img src="<?= htmlspecialchars($apt['patient_profile_picture']) ?>" alt="Patient" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <?= $patInitial ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="patient-name"><?= $patName ?></div>
                                </div>
                            </div>
                        </td>
                        <td><strong><?= $appointmentTime ?></strong></td>
                        <td><?= htmlspecialchars($apt['service_name'] ?? 'N/A') ?></td>
                        <td>
                            <?php if (!empty($apt['pat_phone'])): ?>
                                <a href="tel:<?= htmlspecialchars($apt['pat_phone']) ?>" style="color: var(--primary-blue); text-decoration: none;">
                                    <i class="fas fa-phone"></i> <?= htmlspecialchars($apt['pat_phone']) ?>
                                </a>
                            <?php else: ?>
                                <span style="color: var(--text-secondary);">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($apt['appointment_duration'] ?? 30) ?> min</td>
                        <td>
                            <span class="badge <?= $statusClass ?>" style="background: <?= $apt['status_color'] ?? '#3B82F6' ?>; color: white;">
                                <?= htmlspecialchars($apt['status_name'] ?? 'N/A') ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($paymentStatus !== 'N/A'): ?>
                                <span class="badge" style="background: <?= $paymentStatusColor ?>; color: white; font-size: 0.75rem;">
                                    <?= htmlspecialchars($paymentStatus) ?>
                                </span>
                            <?php else: ?>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">No payment</span>
                            <?php endif; ?>
                        </td>
                        <td class="notes-cell" data-notes="<?= htmlspecialchars($notes) ?>">
                            <?php if (!empty($apt['appointment_notes'])): ?>
                                <i class="fas fa-sticky-note" style="color: var(--primary-blue); cursor: help;"></i>
                            <?php else: ?>
                                <span style="color: var(--text-secondary);">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Upcoming Appointments Section -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 class="card-title" style="margin: 0;">
                <i class="fas fa-calendar-check" style="margin-right: 0.5rem; color: var(--status-success);"></i>
                Upcoming Appointments
            </h2>
            <span class="badge" style="background: var(--status-success); color: white; padding: 0.25rem 0.75rem; border-radius: var(--radius-md); font-size: 0.75rem;">
                <?= $upcoming_count ?>
            </span>
        </div>
        <button type="button" id="toggleFilterBtnUpcoming" class="btn btn-sm" onclick="toggleTableFilters('upcoming')" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
            <i class="fas fa-filter"></i>
        </button>
    </div>

    <!-- Filter Bar for Upcoming -->
    <div id="tableFilterBarUpcoming" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Appointments
            </h3>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-sm" onclick="applyTableFilters('upcoming')" style="padding: 0.5rem 1rem; background: var(--primary-blue); border: 1px solid var(--primary-blue); border-radius: var(--radius-md); color: white; cursor: pointer; font-size: 0.875rem;">
                    <i class="fas fa-check"></i>
                    <span>Apply Filters</span>
                </button>
                <button type="button" class="btn btn-sm" onclick="resetTableFilters('upcoming')" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
                    <i class="fas fa-redo"></i>
                    <span>Reset Filters</span>
                </button>
            </div>
        </div>
        <div class="filter-controls-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-user" style="margin-right: 0.25rem;"></i>Patient Name
                </label>
                <input type="text" id="filterPatientUpcoming" class="filter-input" placeholder="Search patient..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-flask" style="margin-right: 0.25rem;"></i>Service
                </label>
                <input type="text" id="filterServiceUpcoming" class="filter-input" placeholder="Search service..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>Date
                </label>
                <input type="date" id="filterDateUpcoming" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>Time
                </label>
                <input type="time" id="filterTimeUpcoming" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
        </div>
    </div>

    <?php if (empty($upcoming_appointments)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
            <div class="empty-state-text">No upcoming appointments scheduled.</div>
        </div>
    <?php else: ?>
        <table class="patient-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Service</th>
                    <th>Contact</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th style="width: 50px;">
                        <i class="fas fa-sticky-note notes-header-icon" title="Notes - Hover over rows to view"></i>
                    </th>
                </tr>
            </thead>
            <tbody id="tableBodyUpcoming">
                <?php foreach ($upcoming_appointments as $apt): ?>
                    <?php
                    $patInitial = strtoupper(substr($apt['pat_first_name'] ?? 'P', 0, 1));
                    $patName = htmlspecialchars(($apt['pat_first_name'] ?? '') . ' ' . ($apt['pat_last_name'] ?? ''));
                    $statusName = strtolower($apt['status_name'] ?? 'scheduled');
                    $isCompleted = $statusName === 'completed';
                    $isCanceled = $statusName === 'canceled' || $statusName === 'cancelled';
                    $statusClass = $isCompleted ? 'badge-success' : ($isCanceled ? 'badge-error' : 'badge-warning');
                    $appointmentDate = isset($apt['appointment_date']) ? date('M d, Y', strtotime($apt['appointment_date'])) : 'N/A';
                    $appointmentTime = isset($apt['appointment_time']) ? date('g:i A', strtotime($apt['appointment_time'])) : 'N/A';
                    $notes = !empty($apt['appointment_notes']) ? htmlspecialchars($apt['appointment_notes']) : 'No notes available';
                    $paymentStatus = $apt['payment_status_name'] ?? 'N/A';
                    $paymentStatusColor = $apt['payment_status_color'] ?? '#6b7280';
                    $bookingReference = htmlspecialchars($apt['appointment_id'] ?? 'N/A');
                    ?>
                    <tr class="patient-row table-row" 
                        data-section="upcoming"
                        data-patient="<?= htmlspecialchars(strtolower(($apt['pat_first_name'] ?? '') . ' ' . ($apt['pat_last_name'] ?? ''))) ?>"
                        data-service="<?= htmlspecialchars(strtolower($apt['service_name'] ?? '')) ?>"
                        data-date="<?= isset($apt['appointment_date']) ? date('Y-m-d', strtotime($apt['appointment_date'])) : '' ?>"
                        data-time="<?= isset($apt['appointment_time']) ? date('H:i', strtotime($apt['appointment_time'])) : '' ?>"
                        data-status="<?= htmlspecialchars(strtolower($statusName)) ?>">
                        <td><strong style="color: var(--text-primary);"><?= $bookingReference ?></strong></td>
                        <td>
                            <div class="patient-info">
                                <div class="patient-avatar" style="overflow: hidden;">
                                    <?php if (!empty($apt['patient_profile_picture'])): ?>
                                        <img src="<?= htmlspecialchars($apt['patient_profile_picture']) ?>" alt="Patient" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <?= $patInitial ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="patient-name"><?= $patName ?></div>
                                </div>
                            </div>
                        </td>
                        <td><strong><?= $appointmentDate ?></strong></td>
                        <td><?= $appointmentTime ?></td>
                        <td><?= htmlspecialchars($apt['service_name'] ?? 'N/A') ?></td>
                        <td>
                            <?php if (!empty($apt['pat_phone'])): ?>
                                <a href="tel:<?= htmlspecialchars($apt['pat_phone']) ?>" style="color: var(--primary-blue); text-decoration: none;">
                                    <i class="fas fa-phone"></i> <?= htmlspecialchars($apt['pat_phone']) ?>
                                </a>
                            <?php else: ?>
                                <span style="color: var(--text-secondary);">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($apt['appointment_duration'] ?? 30) ?> min</td>
                        <td>
                            <span class="badge <?= $statusClass ?>" style="background: <?= $apt['status_color'] ?? '#3B82F6' ?>; color: white;">
                                <?= htmlspecialchars($apt['status_name'] ?? 'N/A') ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($paymentStatus !== 'N/A'): ?>
                                <span class="badge" style="background: <?= $paymentStatusColor ?>; color: white; font-size: 0.75rem;">
                                    <?= htmlspecialchars($paymentStatus) ?>
                                </span>
                            <?php else: ?>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">No payment</span>
                            <?php endif; ?>
                        </td>
                        <td class="notes-cell" data-notes="<?= htmlspecialchars($notes) ?>">
                            <?php if (!empty($apt['appointment_notes'])): ?>
                                <i class="fas fa-sticky-note" style="color: var(--primary-blue); cursor: help;"></i>
                            <?php else: ?>
                                <span style="color: var(--text-secondary);">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Previous Appointments Section -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 class="card-title" style="margin: 0;">
                <i class="fas fa-history" style="margin-right: 0.5rem; color: var(--text-secondary);"></i>
                Previous Appointments
            </h2>
            <span class="badge" style="background: var(--text-secondary); color: white; padding: 0.25rem 0.75rem; border-radius: var(--radius-md); font-size: 0.75rem;">
                <?= $previous_count ?>
            </span>
        </div>
        <button type="button" id="toggleFilterBtnPrevious" class="btn btn-sm" onclick="toggleTableFilters('previous')" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
            <i class="fas fa-filter"></i>
        </button>
    </div>

    <!-- Filter Bar for Previous -->
    <div id="tableFilterBarPrevious" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Appointments
            </h3>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-sm" onclick="applyTableFilters('previous')" style="padding: 0.5rem 1rem; background: var(--primary-blue); border: 1px solid var(--primary-blue); border-radius: var(--radius-md); color: white; cursor: pointer; font-size: 0.875rem;">
                    <i class="fas fa-check"></i>
                    <span>Apply Filters</span>
                </button>
                <button type="button" class="btn btn-sm" onclick="resetTableFilters('previous')" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
                    <i class="fas fa-redo"></i>
                    <span>Reset Filters</span>
                </button>
            </div>
        </div>
        <div class="filter-controls-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-user" style="margin-right: 0.25rem;"></i>Patient Name
                </label>
                <input type="text" id="filterPatientPrevious" class="filter-input" placeholder="Search patient..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-flask" style="margin-right: 0.25rem;"></i>Service
                </label>
                <input type="text" id="filterServicePrevious" class="filter-input" placeholder="Search service..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>Date
                </label>
                <input type="date" id="filterDatePrevious" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>Time
                </label>
                <input type="time" id="filterTimePrevious" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
        </div>
    </div>

    <?php if (empty($previous_appointments)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
            <div class="empty-state-text">No previous appointments found.</div>
        </div>
    <?php else: ?>
        <table class="patient-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Service</th>
                    <th>Contact</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th style="width: 50px;">
                        <i class="fas fa-sticky-note notes-header-icon" title="Notes - Hover over rows to view"></i>
                    </th>
                </tr>
            </thead>
            <tbody id="tableBodyPrevious">
                <?php foreach ($previous_appointments as $apt): ?>
                    <?php
                    $patInitial = strtoupper(substr($apt['pat_first_name'] ?? 'P', 0, 1));
                    $patName = htmlspecialchars(($apt['pat_first_name'] ?? '') . ' ' . ($apt['pat_last_name'] ?? ''));
                    $statusName = strtolower($apt['status_name'] ?? 'completed');
                    $isCompleted = $statusName === 'completed';
                    $isCanceled = $statusName === 'canceled' || $statusName === 'cancelled';
                    $statusClass = $isCompleted ? 'badge-success' : ($isCanceled ? 'badge-error' : 'badge-warning');
                    $appointmentDate = isset($apt['appointment_date']) ? date('M d, Y', strtotime($apt['appointment_date'])) : 'N/A';
                    $appointmentTime = isset($apt['appointment_time']) ? date('g:i A', strtotime($apt['appointment_time'])) : 'N/A';
                    $notes = !empty($apt['appointment_notes']) ? htmlspecialchars($apt['appointment_notes']) : 'No notes available';
                    $paymentStatus = $apt['payment_status_name'] ?? 'N/A';
                    $paymentStatusColor = $apt['payment_status_color'] ?? '#6b7280';
                    $bookingReference = htmlspecialchars($apt['appointment_id'] ?? 'N/A');
                    ?>
                    <tr class="patient-row table-row" 
                        data-section="previous"
                        data-patient="<?= htmlspecialchars(strtolower(($apt['pat_first_name'] ?? '') . ' ' . ($apt['pat_last_name'] ?? ''))) ?>"
                        data-service="<?= htmlspecialchars(strtolower($apt['service_name'] ?? '')) ?>"
                        data-date="<?= isset($apt['appointment_date']) ? date('Y-m-d', strtotime($apt['appointment_date'])) : '' ?>"
                        data-time="<?= isset($apt['appointment_time']) ? date('H:i', strtotime($apt['appointment_time'])) : '' ?>"
                        data-status="<?= htmlspecialchars(strtolower($statusName)) ?>">
                        <td><strong style="color: var(--text-primary);"><?= $bookingReference ?></strong></td>
                        <td>
                            <div class="patient-info">
                                <div class="patient-avatar" style="overflow: hidden;">
                                    <?php if (!empty($apt['patient_profile_picture'])): ?>
                                        <img src="<?= htmlspecialchars($apt['patient_profile_picture']) ?>" alt="Patient" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <?= $patInitial ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="patient-name"><?= $patName ?></div>
                                </div>
                            </div>
                        </td>
                        <td><strong><?= $appointmentDate ?></strong></td>
                        <td><?= $appointmentTime ?></td>
                        <td><?= htmlspecialchars($apt['service_name'] ?? 'N/A') ?></td>
                        <td>
                            <?php if (!empty($apt['pat_phone'])): ?>
                                <a href="tel:<?= htmlspecialchars($apt['pat_phone']) ?>" style="color: var(--primary-blue); text-decoration: none;">
                                    <i class="fas fa-phone"></i> <?= htmlspecialchars($apt['pat_phone']) ?>
                                </a>
                            <?php else: ?>
                                <span style="color: var(--text-secondary);">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($apt['appointment_duration'] ?? 30) ?> min</td>
                        <td>
                            <span class="badge <?= $statusClass ?>" style="background: <?= $apt['status_color'] ?? '#3B82F6' ?>; color: white;">
                                <?= htmlspecialchars($apt['status_name'] ?? 'N/A') ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($paymentStatus !== 'N/A'): ?>
                                <span class="badge" style="background: <?= $paymentStatusColor ?>; color: white; font-size: 0.75rem;">
                                    <?= htmlspecialchars($paymentStatus) ?>
                                </span>
                            <?php else: ?>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">No payment</span>
                            <?php endif; ?>
                        </td>
                        <td class="notes-cell" data-notes="<?= htmlspecialchars($notes) ?>">
                            <?php if (!empty($apt['appointment_notes'])): ?>
                                <i class="fas fa-sticky-note" style="color: var(--primary-blue); cursor: help;"></i>
                            <?php else: ?>
                                <span style="color: var(--text-secondary);">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
// Table Filtering Functions
function applyTableFilters(section) {
    filterTable(section);
}

function filterTable(section) {
    const patientFilter = document.getElementById(`filterPatient${section.charAt(0).toUpperCase() + section.slice(1)}`)?.value.toLowerCase().trim() || '';
    const serviceFilter = document.getElementById(`filterService${section.charAt(0).toUpperCase() + section.slice(1)}`)?.value.toLowerCase().trim() || '';
    const dateFilter = document.getElementById(`filterDate${section.charAt(0).toUpperCase() + section.slice(1)}`)?.value || '';
    const timeFilter = document.getElementById(`filterTime${section.charAt(0).toUpperCase() + section.slice(1)}`)?.value || '';
    
    const rows = document.querySelectorAll(`.table-row[data-section="${section}"]`);
    let visibleCount = 0;
    
    rows.forEach(row => {
        const patient = row.getAttribute('data-patient') || '';
        const service = row.getAttribute('data-service') || '';
        const date = row.getAttribute('data-date') || '';
        const time = row.getAttribute('data-time') || '';
        
        const matchesPatient = !patientFilter || patient.includes(patientFilter);
        const matchesService = !serviceFilter || service.includes(serviceFilter);
        const matchesDate = !dateFilter || date === dateFilter;
        const matchesTime = !timeFilter || time.startsWith(timeFilter);
        
        if (matchesPatient && matchesService && matchesDate && matchesTime) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    const hasActiveFilters = patientFilter || serviceFilter || dateFilter || timeFilter;
    const tableBody = document.getElementById(`tableBody${section.charAt(0).toUpperCase() + section.slice(1)}`);
    const noResultsMsg = document.getElementById(`noResultsMessage${section.charAt(0).toUpperCase() + section.slice(1)}`);
    
    if (visibleCount === 0 && rows.length > 0 && hasActiveFilters) {
        if (!noResultsMsg) {
            const msg = document.createElement('tr');
            msg.id = `noResultsMessage${section.charAt(0).toUpperCase() + section.slice(1)}`;
            const colCount = document.querySelector(`#tableBody${section.charAt(0).toUpperCase() + section.slice(1)}`)?.closest('table')?.querySelector('thead tr')?.querySelectorAll('th').length || 9;
            msg.innerHTML = `<td colspan="${colCount}" style="padding: 3rem; text-align: center; color: var(--text-secondary);"><i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i><p style="margin: 0;">No appointments match the current filters.</p></td>`;
            if (tableBody) tableBody.appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

function resetTableFilters(section) {
    const capitalized = section.charAt(0).toUpperCase() + section.slice(1);
    const inputs = [`filterPatient${capitalized}`, `filterService${capitalized}`, `filterDate${capitalized}`, `filterTime${capitalized}`];
    inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    filterTable(section);
}

function toggleTableFilters(section) {
    const capitalized = section.charAt(0).toUpperCase() + section.slice(1);
    const filterBar = document.getElementById(`tableFilterBar${capitalized}`);
    const toggleBtn = document.getElementById(`toggleFilterBtn${capitalized}`);
    
    if (filterBar && toggleBtn) {
        if (filterBar.style.display === 'none') {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
        } else {
            filterBar.style.display = 'none';
            toggleBtn.classList.remove('active');
        }
    }
}

// Initialize filtering for all sections
document.addEventListener('DOMContentLoaded', function() {
    const sections = ['today', 'upcoming', 'previous'];
    
    sections.forEach(section => {
        const capitalized = section.charAt(0).toUpperCase() + section.slice(1);
        const filterInputs = [`filterPatient${capitalized}`, `filterService${capitalized}`, `filterDate${capitalized}`, `filterTime${capitalized}`];
        
        // Filters only apply when "Apply Filters" button is clicked
    });
    
    // Notes tooltip functionality
    const notesCells = document.querySelectorAll('.notes-cell');
    notesCells.forEach(cell => {
        const notes = cell.getAttribute('data-notes');
        if (notes && notes !== 'No notes available') {
            cell.addEventListener('mouseenter', function(e) {
                const tooltip = document.createElement('div');
                tooltip.className = 'notes-tooltip';
                tooltip.textContent = notes;
                tooltip.style.cssText = `
                    position: absolute;
                    background: var(--text-primary);
                    color: white;
                    padding: 0.5rem 0.75rem;
                    border-radius: var(--radius-md);
                    font-size: 0.875rem;
                    z-index: 1000;
                    max-width: 300px;
                    box-shadow: var(--shadow-lg);
                    pointer-events: none;
                `;
                document.body.appendChild(tooltip);
                
                const rect = cell.getBoundingClientRect();
                tooltip.style.left = rect.right + 10 + 'px';
                tooltip.style.top = rect.top + 'px';
                
                cell._tooltip = tooltip;
            });
            
            cell.addEventListener('mouseleave', function() {
                if (cell._tooltip) {
                    cell._tooltip.remove();
                    cell._tooltip = null;
                }
            });
        }
    });
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

