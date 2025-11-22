<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.tab-link.active {
    color: var(--primary-blue) !important;
    border-bottom-color: var(--primary-blue) !important;
}

.tab-link:hover {
    color: var(--primary-blue) !important;
}
</style>

<div class="page-header">
    <div class="page-header-top">
        <h1 class="page-title">Today's Schedule</h1>
        <p style="color: var(--text-secondary); font-size: 0.9375rem; margin-top: 0.5rem;">
            <?= date('l, F d, Y') ?>
        </p>
    </div>
    <!-- Navigation Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem; border-bottom: 2px solid var(--border-light);">
        <a href="/doctor/appointments/today" class="tab-link active" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--primary-blue); font-weight: 600; border-bottom: 2px solid var(--primary-blue); margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-calendar-day"></i>
            <span>Today</span>
            <?php if ($today_count > 0): ?>
                <span class="badge" style="background: var(--primary-blue); color: white; padding: 0.125rem 0.5rem; border-radius: 10px; font-size: 0.75rem;"><?= $today_count ?></span>
            <?php endif; ?>
        </a>
        <a href="/doctor/appointments" class="tab-link" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-th-large"></i>
            <span>All</span>
        </a>
        <a href="/doctor/appointments/future" class="tab-link" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-calendar-check"></i>
            <span>Upcoming</span>
        </a>
        <a href="/doctor/appointments/previous" class="tab-link" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-history"></i>
            <span>Past</span>
        </a>
    </div>
</div>

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
                    <i class="fas fa-history"></i>
                    <span>Past Appointments</span>
                </div>
                <div class="stat-value"><?= $past_count ?></div>
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
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-content">
                <div class="stat-label">
                    <i class="fas fa-calendar-check"></i>
                    <span>Future Appointments</span>
                </div>
                <div class="stat-value"><?= $future_count ?></div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>Upcoming</span>
                </div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- Today's Appointments Table -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 class="card-title" style="margin: 0;">Appointments for <?= date('F d, Y') ?></h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>

    <!-- Filter Bar (Hidden by default) -->
    <div id="tableFilterBar" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Appointments
            </h3>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-sm" onclick="applyTableFilters()" style="padding: 0.5rem 1rem; background: var(--primary-blue); border: 1px solid var(--primary-blue); border-radius: var(--radius-md); color: white; cursor: pointer; font-size: 0.875rem;">
                    <i class="fas fa-check"></i>
                    <span>Apply Filters</span>
                </button>
                <button type="button" class="btn btn-sm" onclick="resetTableFilters()" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
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
                <input type="text" id="filterPatient" class="filter-input" placeholder="Search patient..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-flask" style="margin-right: 0.25rem;"></i>Service
                </label>
                <input type="text" id="filterService" class="filter-input" placeholder="Search service..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>Time
                </label>
                <input type="time" id="filterTime" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
        </div>
    </div>
    <?php if (empty($appointments)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
            <div class="empty-state-text">No appointments scheduled for today.</div>
        </div>
    <?php else: ?>
        <table class="patient-table">
            <thead>
                <tr>
                    <?php
                    $current_sort = $_GET['sort'] ?? 'appointment_time';
                    $current_order = $_GET['order'] ?? 'ASC';
                    ?>
                    <th class="sortable <?= $current_sort === 'appointment_id' ? 'sort-' . strtolower($current_order) : '' ?>" 
                        onclick="sortTable('appointment_id')">
                        ID
                        <span class="sort-indicator">
                            <i class="fas fa-arrow-up"></i>
                            <i class="fas fa-arrow-down"></i>
                        </span>
                    </th>
                    <th>Patient</th>
                    <th class="sortable <?= $current_sort === 'appointment_time' ? 'sort-' . strtolower($current_order) : '' ?>" 
                        onclick="sortTable('appointment_time')">
                        Time
                        <span class="sort-indicator">
                            <i class="fas fa-arrow-up"></i>
                            <i class="fas fa-arrow-down"></i>
                        </span>
                    </th>
                    <th>Service</th>
                    <th>Contact</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th style="width: 50px;">
                        <i class="fas fa-sticky-note notes-header-icon" title="Notes - Hover over rows to view"></i>
                    </th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php 
                $currentTime = time();
                $firstUpcoming = true;
                foreach ($appointments as $index => $apt): 
                    $patInitial = strtoupper(substr($apt['pat_first_name'] ?? 'P', 0, 1));
                    $patName = htmlspecialchars(($apt['pat_first_name'] ?? '') . ' ' . ($apt['pat_last_name'] ?? ''));
                    $statusName = strtolower($apt['status_name'] ?? 'scheduled');
                    $isCompleted = $statusName === 'completed';
                    $isCanceled = $statusName === 'canceled' || $statusName === 'cancelled';
                    $statusClass = $isCompleted ? 'badge-success' : ($isCanceled ? 'badge-error' : 'badge-warning');
                    $appointmentTime = isset($apt['appointment_time']) ? date('g:i A', strtotime($apt['appointment_time'])) : 'N/A';
                    $appointmentTimeStr = isset($apt['appointment_time']) ? $apt['appointment_time'] : '';
                    $notes = !empty($apt['appointment_notes']) ? htmlspecialchars($apt['appointment_notes']) : 'No notes available';
                    
                    // Priority indicators
                    $isNext = false;
                    $isOverdue = false;
                    if (!$isCompleted && !$isCanceled && !empty($appointmentTimeStr)) {
                        $appointmentTimestamp = strtotime(date('Y-m-d') . ' ' . $appointmentTimeStr);
                        $timeDiff = $appointmentTimestamp - $currentTime;
                        
                        // Check if overdue (past time and not completed)
                        if ($timeDiff < 0 && $timeDiff > -3600) { // Within last hour
                            $isOverdue = true;
                        }
                        
                        // Mark next upcoming appointment
                        if ($firstUpcoming && $timeDiff > 0) {
                            $isNext = true;
                            $firstUpcoming = false;
                        }
                    }
                    
                    $rowClass = '';
                    if ($isNext) $rowClass = 'next-appointment';
                    if ($isOverdue) $rowClass = 'overdue-appointment';
                ?>
                    <tr class="patient-row table-row <?= $rowClass ?>" 
                        data-appointment-id="<?= htmlspecialchars($apt['appointment_id'] ?? '') ?>"
                        data-patient="<?= htmlspecialchars(strtolower(($apt['pat_first_name'] ?? '') . ' ' . ($apt['pat_last_name'] ?? ''))) ?>"
                        data-service="<?= htmlspecialchars(strtolower($apt['service_name'] ?? '')) ?>"
                        data-time="<?= isset($apt['appointment_time']) ? date('H:i', strtotime($apt['appointment_time'])) : '' ?>"
                        data-status="<?= htmlspecialchars(strtolower($statusName)) ?>"
                        style="<?= $isNext ? 'background: #fef3c7; border-left: 4px solid #f59e0b;' : ($isOverdue ? 'background: #fee2e2; border-left: 4px solid #ef4444;' : '') ?>">
                        <td><strong style="color: var(--text-primary);"><?= htmlspecialchars($apt['appointment_id'] ?? 'N/A') ?></strong></td>
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
                                    <?php if ($isNext): ?>
                                        <span style="font-size: 0.75rem; color: #f59e0b; font-weight: 600;">
                                            <i class="fas fa-clock"></i> Next Appointment
                                        </span>
                                    <?php elseif ($isOverdue): ?>
                                        <span style="font-size: 0.75rem; color: #ef4444; font-weight: 600;">
                                            <i class="fas fa-exclamation-triangle"></i> Overdue
                                        </span>
                                    <?php endif; ?>
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
                        <td class="notes-cell" data-notes="<?= htmlspecialchars($notes) ?>">
                            <?php if (!empty($apt['appointment_notes'])): ?>
                                <i class="fas fa-sticky-note" style="color: var(--primary-blue); cursor: help;"></i>
                            <?php else: ?>
                                <span style="color: var(--text-secondary);">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <button onclick="viewAppointmentDetails('<?= htmlspecialchars($apt['appointment_id'] ?? '') ?>')" 
                                        class="btn-action btn-sm" 
                                        style="padding: 0.375rem 0.75rem; background: var(--primary-blue); color: white; border: none; border-radius: var(--radius-md); cursor: pointer; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                    <span>View</span>
                                </button>
                                <?php if (!$isCompleted && !$isCanceled): ?>
                                    <button onclick="startAppointment('<?= htmlspecialchars($apt['appointment_id'] ?? '') ?>')" 
                                            class="btn-action btn-sm" 
                                            style="padding: 0.375rem 0.75rem; background: var(--status-success); color: white; border: none; border-radius: var(--radius-md); cursor: pointer; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;"
                                            title="Start Appointment">
                                        <i class="fas fa-play"></i>
                                        <span>Start</span>
                                    </button>
                                    <button onclick="completeAppointment('<?= htmlspecialchars($apt['appointment_id'] ?? '') ?>')" 
                                            class="btn-action btn-sm" 
                                            style="padding: 0.375rem 0.75rem; background: #10b981; color: white; border: none; border-radius: var(--radius-md); cursor: pointer; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;"
                                            title="Complete Appointment">
                                        <i class="fas fa-check"></i>
                                        <span>Complete</span>
                                    </button>
                                <?php endif; ?>
                                <button onclick="viewPatientRecord('<?= htmlspecialchars($apt['pat_id'] ?? '') ?>')" 
                                        class="btn-action btn-sm" 
                                        style="padding: 0.375rem 0.75rem; background: #8b5cf6; color: white; border: none; border-radius: var(--radius-md); cursor: pointer; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;"
                                        title="View Patient Record">
                                    <i class="fas fa-file-medical"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Appointment Detail Modal -->
<div id="appointmentDetailModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-light); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 1;">
            <h2 style="margin: 0; color: var(--text-primary);">Appointment Details</h2>
            <button onclick="closeAppointmentModal()" style="background: none; border: none; font-size: 1.5rem; color: var(--text-secondary); cursor: pointer; padding: 0.5rem; line-height: 1;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="appointmentDetailContent" style="padding: 1.5rem;">
            <div style="text-align: center; padding: 2rem;">
                <div class="spinner" style="border: 3px solid #f3f4f6; border-top: 3px solid var(--primary-blue); border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto;"></div>
                <p style="margin-top: 1rem; color: var(--text-secondary);">Loading appointment details...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Appointment Detail Modal Functions
function viewAppointmentDetails(appointmentId) {
    const modal = document.getElementById('appointmentDetailModal');
    const content = document.getElementById('appointmentDetailContent');
    
    modal.style.display = 'flex';
    content.innerHTML = `
        <div style="text-align: center; padding: 2rem;">
            <div class="spinner" style="border: 3px solid #f3f4f6; border-top: 3px solid var(--primary-blue); border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto;"></div>
            <p style="margin-top: 1rem; color: var(--text-secondary);">Loading appointment details...</p>
        </div>
    `;
    
    fetch(`/doctor/appointment-actions?action=get_details&appointment_id=${appointmentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const apt = data.appointment;
                const patName = (apt.pat_first_name || '') + ' ' + (apt.pat_last_name || '');
                const appointmentDate = apt.appointment_date ? new Date(apt.appointment_date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : 'N/A';
                const appointmentTime = apt.appointment_time ? new Date('2000-01-01 ' + apt.appointment_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) : 'N/A';
                
                content.innerHTML = `
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="background: #f9fafb; border-radius: 12px; padding: 1.5rem; text-align: center;">
                            <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem; font-weight: 700;">
                                ${(apt.pat_first_name || '').charAt(0).toUpperCase()}
                            </div>
                            <h4 style="margin: 0 0 0.5rem; color: var(--text-primary);">Patient</h4>
                            <p style="margin: 0; color: var(--text-secondary); font-weight: 600;">${patName}</p>
                            ${apt.pat_phone ? `<p style="margin: 0.5rem 0 0; color: var(--text-secondary);"><a href="tel:${apt.pat_phone}" style="color: var(--primary-blue); text-decoration: none;"><i class="fas fa-phone"></i> ${apt.pat_phone}</a></p>` : ''}
                        </div>
                        <div style="background: #f9fafb; border-radius: 12px; padding: 1.5rem;">
                            <h4 style="margin: 0 0 1rem; color: var(--text-primary);">Appointment Information</h4>
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                <div><strong>ID:</strong> ${apt.appointment_id || 'N/A'}</div>
                                <div><strong>Date:</strong> ${appointmentDate}</div>
                                <div><strong>Time:</strong> ${appointmentTime}</div>
                                <div><strong>Duration:</strong> ${apt.appointment_duration || 30} minutes</div>
                                <div><strong>Service:</strong> ${apt.service_name || 'N/A'}</div>
                                <div><strong>Status:</strong> 
                                    <span class="badge" style="background: ${apt.status_color || '#3B82F6'}; color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">
                                        ${apt.status_name || 'N/A'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    ${apt.appointment_notes ? `
                        <div style="background: #f9fafb; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                            <h4 style="margin: 0 0 1rem; color: var(--text-primary);"><i class="fas fa-sticky-note" style="margin-right: 0.5rem;"></i>Notes</h4>
                            <p style="margin: 0; color: var(--text-secondary); white-space: pre-wrap;">${apt.appointment_notes || 'No notes available'}</p>
                        </div>
                    ` : ''}
                    ${data.recent_records && data.recent_records.length > 0 ? `
                        <div style="background: #f9fafb; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                            <h4 style="margin: 0 0 1rem; color: var(--text-primary);"><i class="fas fa-file-medical" style="margin-right: 0.5rem;"></i>Recent Medical Records</h4>
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                ${data.recent_records.slice(0, 3).map(record => `
                                    <div style="padding: 0.75rem; background: white; border-radius: 8px; border: 1px solid var(--border-light);">
                                        <div style="font-weight: 600; color: var(--text-primary);">${new Date(record.record_date).toLocaleDateString()}</div>
                                        ${record.diagnosis ? `<div style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">${record.diagnosis.substring(0, 100)}...</div>` : ''}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                    <div style="display: flex; gap: 0.75rem; justify-content: flex-end; padding-top: 1.5rem; border-top: 1px solid var(--border-light);">
                        <button onclick="viewPatientRecord('${apt.pat_id}')" style="padding: 0.75rem 1.5rem; background: #8b5cf6; color: white; border: none; border-radius: var(--radius-md); cursor: pointer; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-file-medical"></i>
                            <span>View Patient Record</span>
                        </button>
                        ${apt.status_name && apt.status_name.toLowerCase() !== 'completed' && apt.status_name.toLowerCase() !== 'canceled' && apt.status_name.toLowerCase() !== 'cancelled' ? `
                            <button onclick="startAppointment('${apt.appointment_id}')" style="padding: 0.75rem 1.5rem; background: var(--status-success); color: white; border: none; border-radius: var(--radius-md); cursor: pointer; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-play"></i>
                                <span>Start Appointment</span>
                            </button>
                        ` : ''}
                        <button onclick="closeAppointmentModal()" style="padding: 0.75rem 1.5rem; background: var(--bg-light); color: var(--text-primary); border: 1px solid var(--border-light); border-radius: var(--radius-md); cursor: pointer; font-weight: 500;">
                            Close
                        </button>
                    </div>
                `;
            } else {
                content.innerHTML = `
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ef4444; margin-bottom: 1rem;"></i>
                        <p style="color: var(--text-secondary);">${data.error || 'Failed to load appointment details'}</p>
                        <button onclick="closeAppointmentModal()" style="padding: 0.75rem 1.5rem; background: var(--primary-blue); color: white; border: none; border-radius: var(--radius-md); cursor: pointer; margin-top: 1rem;">
                            Close
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            content.innerHTML = `
                <div style="text-align: center; padding: 2rem;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ef4444; margin-bottom: 1rem;"></i>
                    <p style="color: var(--text-secondary);">An error occurred while loading appointment details.</p>
                    <button onclick="closeAppointmentModal()" style="padding: 0.75rem 1.5rem; background: var(--primary-blue); color: white; border: none; border-radius: var(--radius-md); cursor: pointer; margin-top: 1rem;">
                        Close
                    </button>
                </div>
            `;
        });
}

function closeAppointmentModal() {
    document.getElementById('appointmentDetailModal').style.display = 'none';
}

function startAppointment(appointmentId) {
    // Note: In the default schema, there's no "In Progress" status, so we'll keep it as Scheduled
    // If you have an "In Progress" status, update this status_id accordingly
    if (confirm('Start this appointment? The appointment will remain in "Scheduled" status.')) {
        // Optionally, you could create an "In Progress" status and use its ID here
        // For now, we'll just show a message that the appointment has started
        alert('Appointment started! You can now proceed with the consultation.');
        // If you want to update to a specific status, uncomment the line below and set the correct status_id
        // updateAppointmentStatus(appointmentId, statusIdForInProgress, 'Appointment started');
    }
}

function completeAppointment(appointmentId) {
    // Completed status_id is typically 2 based on default schema
    if (confirm('Mark this appointment as completed?')) {
        updateAppointmentStatus(appointmentId, 2, 'Appointment completed');
    }
}

function updateAppointmentStatus(appointmentId, statusId, message = '') {
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('appointment_id', appointmentId);
    formData.append('status_id', statusId);
    
    fetch('/doctor/appointment-actions', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(message || 'Appointment status updated successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to update appointment status'));
        }
    })
    .catch(error => {
        alert('An error occurred while updating the appointment status.');
    });
}

function viewPatientRecord(patientId) {
    window.location.href = `/doctor/medical-records?patient_id=${patientId}`;
}

// Close modal when clicking outside
document.getElementById('appointmentDetailModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAppointmentModal();
    }
});

// Table Filtering Functions
function applyTableFilters() {
    filterTable();
}

function filterTable() {
    const patientFilter = document.getElementById('filterPatient')?.value.toLowerCase().trim() || '';
    const serviceFilter = document.getElementById('filterService')?.value.toLowerCase().trim() || '';
    const timeFilter = document.getElementById('filterTime')?.value || '';
    
    const rows = document.querySelectorAll('.table-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const patient = row.getAttribute('data-patient') || '';
        const service = row.getAttribute('data-service') || '';
        const time = row.getAttribute('data-time') || '';
        
        const matchesPatient = !patientFilter || patient.includes(patientFilter);
        const matchesService = !serviceFilter || service.includes(serviceFilter);
        const matchesTime = !timeFilter || time.startsWith(timeFilter);
        
        if (matchesPatient && matchesService && matchesTime) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    const hasActiveFilters = patientFilter || serviceFilter || timeFilter;
    const tableBody = document.getElementById('tableBody');
    const noResultsMsg = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0 && rows.length > 0 && hasActiveFilters) {
        if (!noResultsMsg) {
            const msg = document.createElement('tr');
            msg.id = 'noResultsMessage';
            const colCount = document.querySelector('thead tr')?.querySelectorAll('th').length || 8;
            msg.innerHTML = `<td colspan="${colCount}" style="padding: 3rem; text-align: center; color: var(--text-secondary);"><i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i><p style="margin: 0;">No appointments match the current filters.</p></td>`;
            tableBody.appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

function resetTableFilters() {
    const inputs = ['filterPatient', 'filterService', 'filterTime'];
    inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    filterTable();
}

function toggleTableFilters() {
    const filterBar = document.getElementById('tableFilterBar');
    const toggleBtn = document.getElementById('toggleFilterBtn');
    
    if (filterBar && toggleBtn) {
        if (filterBar.style.display === 'none') {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
            toggleBtn.innerHTML = '<i class="fas fa-filter"></i>';
        } else {
            filterBar.style.display = 'none';
            toggleBtn.classList.remove('active');
            toggleBtn.innerHTML = '<i class="fas fa-filter"></i>';
        }
    }
}

// Initialize filtering
document.addEventListener('DOMContentLoaded', function() {
    // Filters only apply when "Apply Filters" button is clicked
});

// Table Sorting Function
function sortTable(column) {
    const url = new URL(window.location.href);
    const currentSort = url.searchParams.get('sort');
    const currentOrder = url.searchParams.get('order') || 'ASC';
    
    // Toggle order if clicking the same column, otherwise default to ASC
    if (currentSort === column) {
        url.searchParams.set('order', currentOrder === 'ASC' ? 'DESC' : 'ASC');
    } else {
        url.searchParams.set('order', 'ASC');
    }
    
    url.searchParams.set('sort', column);
    
    window.location.href = url.toString();
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
