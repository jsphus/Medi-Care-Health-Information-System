<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-top">
        <div class="breadcrumbs">
            <a href="/doctor/dashboard">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <i class="fas fa-chevron-right"></i>
            <span>Future Appointments</span>
        </div>
        <h1 class="page-title">Future Appointments</h1>
    </div>
    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
        <a href="/doctor/appointments/today" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Today</span>
        </a>
        <a href="/doctor/appointments/previous" class="btn btn-secondary">
            <i class="fas fa-history"></i>
            <span>View Previous</span>
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<!-- Statistics -->
<div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-content">
                <div class="stat-label">
                    <i class="fas fa-calendar-check"></i>
                    <span>Total Upcoming</span>
                </div>
                <div class="stat-value"><?= count($appointments) ?></div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- Future Appointments Table -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 class="card-title" style="margin: 0;">Upcoming Appointments</h2>
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
            <button type="button" class="btn btn-sm" onclick="resetTableFilters()" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
                <i class="fas fa-redo"></i>
                <span>Reset Filters</span>
            </button>
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
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>Date
                </label>
                <input type="date" id="filterDate" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
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
            <div class="empty-state-text">No future appointments scheduled.</div>
        </div>
    <?php else: ?>
        <table class="patient-table">
            <thead>
                <tr>
                    <?php
                    $current_sort = $_GET['sort'] ?? 'appointment_date';
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
                    <th class="sortable <?= $current_sort === 'appointment_date' ? 'sort-' . strtolower($current_order) : '' ?>" 
                        onclick="sortTable('appointment_date')">
                        Date
                        <span class="sort-indicator">
                            <i class="fas fa-arrow-up"></i>
                            <i class="fas fa-arrow-down"></i>
                        </span>
                    </th>
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
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php foreach ($appointments as $apt): ?>
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
                    ?>
                    <tr class="patient-row table-row" 
                        data-patient="<?= htmlspecialchars(strtolower(($apt['pat_first_name'] ?? '') . ' ' . ($apt['pat_last_name'] ?? ''))) ?>"
                        data-service="<?= htmlspecialchars(strtolower($apt['service_name'] ?? '')) ?>"
                        data-date="<?= isset($apt['appointment_date']) ? date('Y-m-d', strtotime($apt['appointment_date'])) : '' ?>"
                        data-time="<?= isset($apt['appointment_time']) ? date('H:i', strtotime($apt['appointment_time'])) : '' ?>"
                        data-status="<?= htmlspecialchars(strtolower($statusName)) ?>">
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
function filterTable() {
    const patientFilter = document.getElementById('filterPatient')?.value.toLowerCase().trim() || '';
    const serviceFilter = document.getElementById('filterService')?.value.toLowerCase().trim() || '';
    const dateFilter = document.getElementById('filterDate')?.value || '';
    const timeFilter = document.getElementById('filterTime')?.value || '';
    
    const rows = document.querySelectorAll('.table-row');
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
    const tableBody = document.getElementById('tableBody');
    const noResultsMsg = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0 && rows.length > 0 && hasActiveFilters) {
        if (!noResultsMsg) {
            const msg = document.createElement('tr');
            msg.id = 'noResultsMessage';
            const colCount = document.querySelector('thead tr')?.querySelectorAll('th').length || 9;
            msg.innerHTML = `<td colspan="${colCount}" style="padding: 3rem; text-align: center; color: var(--text-secondary);"><i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i><p style="margin: 0;">No appointments match the current filters.</p></td>`;
            tableBody.appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

function resetTableFilters() {
    const inputs = ['filterPatient', 'filterService', 'filterDate', 'filterTime'];
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
    const filterInputs = ['filterPatient', 'filterService', 'filterDate', 'filterTime'];
    filterInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', filterTable);
            input.addEventListener('change', filterTable);
        }
    });
    filterTable();
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
