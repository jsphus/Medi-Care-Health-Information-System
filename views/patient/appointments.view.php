<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">My Appointments</h1>
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
        <span><?= htmlspecialchars($success) ?></span>
    </div>
<?php endif; ?>

<!-- Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #8b5cf6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Appointments</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Upcoming</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['upcoming'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Completed</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['completed'] ?? 0 ?></div>
    </div>
</div>

<!-- Appointments List -->
<?php if (empty($appointments) && empty($upcoming_appointments) && empty($past_appointments)): ?>
    <div class="empty-state">
        <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
        <div class="empty-state-text">No appointments found.</div>
        <a href="/patient/appointments/create" class="empty-state-link">Book your first appointment now!</a>
    </div>
<?php else: ?>
    <!-- Upcoming Appointments -->
    <?php if (!empty($upcoming_appointments)): ?>
        <?php foreach ($upcoming_appointments as $apt): ?>
            <?php
            $statusName = strtolower($apt['status_name'] ?? 'scheduled');
            $isCompleted = $statusName === 'completed';
            $isCanceled = $statusName === 'canceled' || $statusName === 'cancelled';
            $statusClass = $isCompleted ? 'badge-success' : ($isCanceled ? 'badge-error' : 'badge-warning');
            
            $docInitial = strtoupper(substr($apt['doc_first_name'] ?? 'D', 0, 1));
            $docName = 'Dr. ' . htmlspecialchars(($apt['doc_first_name'] ?? '') . ' ' . ($apt['doc_last_name'] ?? ''));
            $specName = htmlspecialchars($apt['spec_name'] ?? 'General Practice');
            ?>
            <div class="reception-card">
                <div class="reception-header">
                    <div class="reception-doctor">
                        <div class="doctor-avatar" style="overflow: hidden;">
                            <?php if (!empty($apt['doctor_profile_picture'])): ?>
                                <img src="<?= htmlspecialchars($apt['doctor_profile_picture']) ?>" alt="Doctor" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <?= $docInitial ?>
                            <?php endif; ?>
                        </div>
                        <div class="doctor-info">
                            <h3><?= $docName ?></h3>
                            <p><?= $specName ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="reception-details">
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-check-circle"></i></span>
                        <div>
                            <div class="label">Status</div>
                            <div class="value">
                                <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($apt['status_name'] ?? 'Scheduled') ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-calendar"></i></span>
                        <div>
                            <div class="label">Date</div>
                            <div class="value"><?= date('l, M j, Y', strtotime($apt['appointment_date'])) ?></div>
                        </div>
                    </div>
                    
                    <?php if ($apt['appointment_time']): ?>
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-clock"></i></span>
                        <div>
                            <div class="label">Time</div>
                            <div class="value"><?= date('g:i A', strtotime($apt['appointment_time'])) ?> — <?= date('g:i A', strtotime($apt['appointment_time'] . ' +' . ($apt['appointment_duration'] ?? 30) . ' minutes')) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($apt['address']) && $apt['address']): ?>
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-map-marker-alt"></i></span>
                        <div>
                            <div class="label">Address</div>
                            <div class="value"><?= htmlspecialchars($apt['address']) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($apt['office']) && $apt['office']): ?>
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-building"></i></span>
                        <div>
                            <div class="label">Office</div>
                            <div class="value"><?= htmlspecialchars($apt['office']) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-file-alt"></i></span>
                        <div>
                            <div class="label">Description</div>
                            <div class="value">
                                <?php if ($apt['appointment_notes']): ?>
                                    <a href="#" style="color: var(--primary-blue); text-decoration: none;">Visit Summary <i class="fas fa-eye"></i></a>
                                <?php else: ?>
                                    Not found.
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-pills"></i></span>
                        <div>
                            <div class="label">Prescription</div>
                            <div class="value">
                                <?php if (isset($apt['prescription']) && $apt['prescription']): ?>
                                    <a href="#" style="color: var(--primary-blue); text-decoration: none;"><?= htmlspecialchars($apt['prescription']) ?></a>
                                <?php else: ?>
                                    Not found.
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (!$isCanceled && !$isCompleted): ?>
                <div style="padding-top: 1rem; margin-top: 1rem; border-top: 1px solid #e5e7eb; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="/patient/appointments/create?reschedule=<?= htmlspecialchars($apt['appointment_id']) ?>" class="btn-action btn-secondary" style="padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; background: #f3f4f6; color: #374151; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-calendar-alt"></i> Reschedule
                    </a>
                    <button type="button" onclick="cancelAppointment('<?= htmlspecialchars($apt['appointment_id']) ?>')" class="btn-action btn-danger" style="padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; background: #fee2e2; color: #991b1b; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-times"></i> Cancel Appointment
                    </button>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Past Appointments -->
    <?php if (!empty($past_appointments)): ?>
        <?php foreach ($past_appointments as $apt): ?>
            <?php
            $statusName = strtolower($apt['status_name'] ?? 'completed');
            $isCompleted = $statusName === 'completed';
            $isCanceled = $statusName === 'canceled' || $statusName === 'cancelled';
            $statusClass = $isCompleted ? 'badge-success' : ($isCanceled ? 'badge-error' : 'badge-warning');
            
            $docInitial = strtoupper(substr($apt['doc_first_name'] ?? 'D', 0, 1));
            $docName = 'Dr. ' . htmlspecialchars(($apt['doc_first_name'] ?? '') . ' ' . ($apt['doc_last_name'] ?? ''));
            $specName = htmlspecialchars($apt['spec_name'] ?? 'General Practice');
            ?>
            <div class="reception-card">
                <div class="reception-header">
                    <div class="reception-doctor">
                        <div class="doctor-avatar" style="overflow: hidden;">
                            <?php if (!empty($apt['doctor_profile_picture'])): ?>
                                <img src="<?= htmlspecialchars($apt['doctor_profile_picture']) ?>" alt="Doctor" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <?= $docInitial ?>
                            <?php endif; ?>
                        </div>
                        <div class="doctor-info">
                            <h3><?= $docName ?></h3>
                            <p><?= $specName ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="reception-details">
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-check-circle"></i></span>
                        <div>
                            <div class="label">Status</div>
                            <div class="value">
                                <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($apt['status_name'] ?? 'Completed') ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-calendar"></i></span>
                        <div>
                            <div class="label">Date</div>
                            <div class="value"><?= date('l, M j, Y', strtotime($apt['appointment_date'])) ?></div>
                        </div>
                    </div>
                    
                    <?php if ($apt['appointment_time']): ?>
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-clock"></i></span>
                        <div>
                            <div class="label">Time</div>
                            <div class="value"><?= date('g:i A', strtotime($apt['appointment_time'])) ?> — <?= date('g:i A', strtotime($apt['appointment_time'] . ' +' . ($apt['appointment_duration'] ?? 30) . ' minutes')) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($apt['address']) && $apt['address']): ?>
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-map-marker-alt"></i></span>
                        <div>
                            <div class="label">Address</div>
                            <div class="value"><?= htmlspecialchars($apt['address']) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($apt['office']) && $apt['office']): ?>
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-building"></i></span>
                        <div>
                            <div class="label">Office</div>
                            <div class="value"><?= htmlspecialchars($apt['office']) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-file-alt"></i></span>
                        <div>
                            <div class="label">Description</div>
                            <div class="value">
                                <?php if ($apt['appointment_notes']): ?>
                                    <a href="#" style="color: var(--primary-blue); text-decoration: none;">Visit Summary <i class="fas fa-eye"></i></a>
                                <?php else: ?>
                                    Not found.
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <span class="icon"><i class="fas fa-pills"></i></span>
                        <div>
                            <div class="label">Prescription</div>
                            <div class="value">
                                <?php if (isset($apt['prescription']) && $apt['prescription']): ?>
                                    <a href="#" style="color: var(--primary-blue); text-decoration: none;"><?= htmlspecialchars($apt['prescription']) ?></a>
                                <?php else: ?>
                                    Not found.
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>

<script>
// Category tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const categoryTabs = document.querySelectorAll('.category-tab');
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            categoryTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const category = this.dataset.category;
            filterByCategory(category);
        });
    });
});

function filterByCategory(category) {
    const params = new URLSearchParams(window.location.search);
    if (category === 'all') {
        params.delete('category');
    } else {
        params.set('category', category);
    }
    window.location.href = '/patient/appointments' + (params.toString() ? '?' + params.toString() : '');
}

function applyAppointmentFilters() {
    const filters = {
        status: document.querySelector('input[name="filter_status"]:checked')?.value || ''
    };
    const params = new URLSearchParams(window.location.search);
    if (filters.status) {
        params.set('status', filters.status);
    } else {
        params.delete('status');
    }
    const url = '/patient/appointments' + (params.toString() ? '?' + params.toString() : '');
    window.location.href = url;
}

function clearAllFilters() {
    document.querySelectorAll('.filter-sidebar input[type="radio"]').forEach(radio => {
        radio.checked = false;
    });
}

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

<!-- Filter Sidebar -->
<div class="filter-sidebar" id="filterSidebar">
    <div class="filter-sidebar-header">
        <h3>Filters</h3>
        <button type="button" class="filter-sidebar-close" onclick="toggleFilterSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <!-- Status Filter -->
    <?php if (!empty($filter_statuses)): ?>
    <div class="filter-section">
        <div class="filter-section-header" onclick="toggleFilterSection('status')">
            <h4 class="filter-section-title">Status</h4>
            <button type="button" class="filter-section-toggle" id="statusToggle">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
        <div class="filter-section-content" id="statusContent">
            <div class="filter-radio-group">
                <?php foreach ($filter_statuses as $status): ?>
                    <div class="filter-radio-item">
                        <input type="radio" name="filter_status" id="status_<?= $status['status_id'] ?>" value="<?= $status['status_id'] ?>">
                        <label for="status_<?= $status['status_id'] ?>"><?= htmlspecialchars($status['status_name']) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Filter Actions -->
    <div class="filter-sidebar-actions">
        <button type="button" class="filter-clear-btn" onclick="clearAllFilters()">Clear all</button>
        <button type="button" class="filter-apply-btn" onclick="applyAppointmentFilters()">Apply all filter</button>
    </div>
</div>

<script>
function toggleFilterSidebar() {
    const sidebar = document.getElementById('filterSidebar');
    const mainContent = document.querySelector('.main-content');
    const filterBtn = document.querySelector('.filter-toggle-btn');
    
    sidebar.classList.toggle('active');
    if (mainContent) {
        mainContent.classList.toggle('filter-active');
    }
    if (filterBtn) {
        filterBtn.classList.toggle('active');
    }
}

function toggleFilterSection(sectionId) {
    const content = document.getElementById(sectionId + 'Content');
    const toggle = document.getElementById(sectionId + 'Toggle');
    
    if (content && toggle) {
        content.classList.toggle('collapsed');
        const icon = toggle.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-chevron-up');
            icon.classList.toggle('fa-chevron-down');
        }
    }
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
