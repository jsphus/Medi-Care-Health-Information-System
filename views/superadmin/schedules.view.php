<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-top">
        <div class="breadcrumbs">
            <a href="/superadmin/dashboard">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <i class="fas fa-chevron-right"></i>
            <span>Schedules</span>
        </div>
        <h1 class="page-title">Manage All Doctor Schedules</h1>
    </div>
</div>

<?php if (isset($error) && $error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<?php if (isset($success) && $success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span><?= htmlspecialchars($success) ?></span>
    </div>
<?php endif; ?>

<!-- Today's Schedules -->
<?php if (!empty($today_schedules)): ?>
    <div class="card" style="border-left: 4px solid var(--primary-blue);">
        <div class="card-header">
            <h2 class="card-title">Today's Schedules (<?= date('F d, Y') ?>)</h2>
        </div>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Max Appointments</th>
                        <th>Available</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($today_schedules as $sched): ?>
                        <tr>
                            <td><strong>Dr. <?= htmlspecialchars($sched['doc_first_name'] . ' ' . $sched['doc_last_name']) ?></strong></td>
                            <td><?= htmlspecialchars($sched['spec_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($sched['start_time']) ?></td>
                            <td><?= htmlspecialchars($sched['end_time']) ?></td>
                            <td><?= htmlspecialchars($sched['max_appointments']) ?></td>
                            <td>
                                <span class="status-badge <?= $sched['is_available'] ? 'active' : 'inactive' ?>">
                                    <?= $sched['is_available'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- All Schedules -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 class="card-title" style="margin: 0;">All Doctor Schedules</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>

    <!-- Filter Bar (Hidden by default) -->
    <div id="tableFilterBar" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Schedules
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
                    <i class="fas fa-user-md" style="margin-right: 0.25rem;"></i>Doctor Name
                </label>
                <input type="text" id="filterDoctor" class="filter-input" placeholder="Search doctor..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-graduation-cap" style="margin-right: 0.25rem;"></i>Specialization
                </label>
                <input type="text" id="filterSpecialization" class="filter-input" placeholder="Search specialization..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>Date Created
                </label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                    <select id="filterDateMonth" class="filter-input" style="padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem; background: white; cursor: pointer;">
                        <option value="">All Months</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                    <select id="filterDateDay" class="filter-input" style="padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem; background: white; cursor: pointer;">
                        <option value="">All Days</option>
                        <?php for ($i = 1; $i <= 31; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                    <select id="filterDateYear" class="filter-input" style="padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem; background: white; cursor: pointer;">
                        <option value="">All Years</option>
                        <?php 
                        $current_year = (int)date('Y');
                        for ($year = $current_year; $year >= 2020; $year--): 
                        ?>
                            <option value="<?= $year ?>"><?= $year ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>Start Time
                </label>
                <input type="time" id="filterStartTime" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>End Time
                </label>
                <input type="time" id="filterEndTime" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-users" style="margin-right: 0.25rem;"></i>Min Max Appointments
                </label>
                <input type="number" id="filterMinAppointments" class="filter-input" placeholder="Min..." min="0" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-toggle-on" style="margin-right: 0.25rem;"></i>Available
                </label>
                <select id="filterAvailable" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
                    <option value="">All</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
        </div>
    </div>
    <?php if (empty($schedules)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
            <div class="empty-state-text">No schedules found.</div>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <?php
                        $current_sort = $_GET['sort'] ?? 'schedule_date';
                        $current_order = $_GET['order'] ?? 'DESC';
                        ?>
                        <th class="sortable <?= $current_sort === 'schedule_date' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('schedule_date')">
                            Date
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th class="sortable <?= $current_sort === 'start_time' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('start_time')">
                            Start Time
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'end_time' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('end_time')">
                            End Time
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th>Max Appointments</th>
                        <th>Available</th>
                        <th>Date Created</th>
                        <th>Date Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($schedules as $sched): ?>
                        <tr class="table-row" 
                            data-doctor="<?= htmlspecialchars(strtolower(($sched['doc_first_name'] ?? '') . ' ' . ($sched['doc_last_name'] ?? ''))) ?>"
                            data-specialization="<?= htmlspecialchars(strtolower($sched['spec_name'] ?? '')) ?>"
                            data-date="<?= !empty($sched['created_at']) ? date('Y-m-d', strtotime($sched['created_at'])) : '' ?>"
                            data-start-time="<?= htmlspecialchars($sched['start_time'] ?? '') ?>"
                            data-end-time="<?= htmlspecialchars($sched['end_time'] ?? '') ?>"
                            data-max-appointments="<?= (int)($sched['max_appointments'] ?? 0) ?>"
                            data-available="<?= $sched['is_available'] ? 'yes' : 'no' ?>">
                            <td><strong><?= $sched['schedule_date'] ? date('d M Y', strtotime($sched['schedule_date'])) : 'N/A' ?></strong></td>
                            <td>Dr. <?= htmlspecialchars($sched['doc_first_name'] . ' ' . $sched['doc_last_name']) ?></td>
                            <td><?= htmlspecialchars($sched['spec_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($sched['start_time']) ?></td>
                            <td><?= htmlspecialchars($sched['end_time']) ?></td>
                            <td><?= htmlspecialchars($sched['max_appointments']) ?></td>
                            <td>
                                <span class="status-badge <?= $sched['is_available'] ? 'active' : 'inactive' ?>">
                                    <?= $sched['is_available'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                            <td style="color: var(--text-secondary);"><?= $sched['created_at'] ? date('d M Y', strtotime($sched['created_at'])) : 'N/A' ?></td>
                            <td style="color: var(--text-secondary);"><?= $sched['updated_at'] ? date('d M Y', strtotime($sched['updated_at'])) : 'N/A' ?></td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button class="btn btn-sm edit-schedule-btn" 
                                            data-schedule="<?= base64_encode(json_encode($sched)) ?>" 
                                            title="Edit"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm view-schedule-btn" 
                                            data-schedule="<?= base64_encode(json_encode($sched)) ?>" 
                                            title="View"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return handleDelete(event, 'Are you sure you want to delete this schedule?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $sched['schedule_id'] ?>">
                                        <button type="submit" class="btn btn-sm" title="Delete"
                                                style="padding: 0.5rem; background: transparent; border: none; color: var(--status-error); cursor: pointer;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
        <div id="paginationContainer" class="pagination">
            <div class="pagination-controls">
                <button class="pagination-btn" disabled>
                    <i class="fas fa-angle-double-left"></i>
                </button>
                <button class="pagination-btn" disabled>
                    <i class="fas fa-angle-left"></i>
                </button>
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn">3</button>
                <button class="pagination-btn">
                    <i class="fas fa-angle-right"></i>
                </button>
                <button class="pagination-btn">
                    <i class="fas fa-angle-double-right"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Edit Schedule Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Schedule</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-grid">
                <div class="form-group">
                    <label>Date: <span style="color: var(--status-error);">*</span></label>
                    <input type="date" name="schedule_date" id="edit_schedule_date" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Start Time: <span style="color: var(--status-error);">*</span></label>
                    <input type="time" name="start_time" id="edit_start_time" required class="form-control">
                </div>
                <div class="form-group">
                    <label>End Time: <span style="color: var(--status-error);">*</span></label>
                    <input type="time" name="end_time" id="edit_end_time" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Max Appointments:</label>
                    <input type="number" name="max_appointments" id="edit_max_appointments" min="1" class="form-control">
                </div>
            </div>
            <div class="form-group" style="margin-top: 1rem;">
                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="is_available" id="edit_is_available" value="1" style="width: auto;">
                    <span>Available for appointments</span>
                </label>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    <span>Update Schedule</span>
                </button>
                <button type="button" onclick="closeEditModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Schedule Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2 class="modal-title">Schedule Details</h2>
            <button type="button" class="modal-close" onclick="closeViewModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="viewContent"></div>
        <div class="action-buttons" style="margin-top: 1.5rem;">
            <button type="button" onclick="closeViewModal()" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                <span>Close</span>
            </button>
        </div>
    </div>
</div>

<script>
// Table Sorting Function
function sortTable(column) {
    const url = new URL(window.location.href);
    const currentSort = url.searchParams.get('sort');
    const currentOrder = url.searchParams.get('order') || 'DESC';
    
    // Toggle order if clicking the same column, otherwise default to ASC
    if (currentSort === column) {
        url.searchParams.set('order', currentOrder === 'ASC' ? 'DESC' : 'ASC');
    } else {
        url.searchParams.set('order', 'ASC');
    }
    
    url.searchParams.set('sort', column);
    url.searchParams.delete('page'); // Reset to page 1 when sorting
    
    window.location.href = url.toString();
}

// Filtering Functions
function applyTableFilters() {
    // Ensure we're in all_results mode for filtering to work properly
    const url = new URL(window.location.href);
    const isAllResultsMode = url.searchParams.get('all_results') === '1';
    
    if (!isAllResultsMode) {
        // Store filter values before reloading
        const filterValues = {
            filterDoctor: document.getElementById('filterDoctor')?.value || '',
            filterSpecialization: document.getElementById('filterSpecialization')?.value || '',
            filterDateMonth: document.getElementById('filterDateMonth')?.value || '',
            filterDateDay: document.getElementById('filterDateDay')?.value || '',
            filterDateYear: document.getElementById('filterDateYear')?.value || '',
            filterStartTime: document.getElementById('filterStartTime')?.value || '',
            filterEndTime: document.getElementById('filterEndTime')?.value || '',
            filterMinAppointments: document.getElementById('filterMinAppointments')?.value || '',
            filterAvailable: document.getElementById('filterAvailable')?.value || ''
        };
        sessionStorage.setItem('pendingFilters', JSON.stringify(filterValues));
        // Load all results first, then apply filters after page reloads
        loadAllResults();
        return;
    }
    
    // Apply filters if already in all_results mode
    filterTable();
}

function filterTable() {
    const tbody = document.getElementById('tableBody');
    if (!tbody) return;
    
    const rows = tbody.querySelectorAll('.table-row');
    const filterDoctor = document.getElementById('filterDoctor')?.value.toLowerCase().trim() || '';
    const filterSpecialization = document.getElementById('filterSpecialization')?.value.toLowerCase().trim() || '';
    const dateMonthFilter = document.getElementById('filterDateMonth')?.value || '';
    const dateDayFilter = document.getElementById('filterDateDay')?.value || '';
    const dateYearFilter = document.getElementById('filterDateYear')?.value || '';
    const filterStartTime = document.getElementById('filterStartTime')?.value || '';
    const filterEndTime = document.getElementById('filterEndTime')?.value || '';
    const filterMinAppointments = document.getElementById('filterMinAppointments')?.value ? parseInt(document.getElementById('filterMinAppointments').value) : null;
    const filterAvailable = document.getElementById('filterAvailable')?.value.toLowerCase().trim() || '';
    
    let visibleCount = 0;
    let hasActiveFilters = filterDoctor || filterSpecialization || dateMonthFilter || dateDayFilter || dateYearFilter || filterStartTime || filterEndTime || filterMinAppointments !== null || filterAvailable;
    
    rows.forEach(row => {
        const doctor = row.getAttribute('data-doctor') || '';
        const specialization = row.getAttribute('data-specialization') || '';
        const dateStr = row.getAttribute('data-date') || '';
        const startTime = row.getAttribute('data-start-time') || '';
        const endTime = row.getAttribute('data-end-time') || '';
        const maxAppointments = parseInt(row.getAttribute('data-max-appointments') || '0');
        const available = row.getAttribute('data-available') || '';
        
        const matchesDoctor = !filterDoctor || doctor.includes(filterDoctor);
        const matchesSpecialization = !filterSpecialization || specialization.includes(filterSpecialization);
        
        // Date filtering - extract month, day, year from date string (format: YYYY-MM-DD)
        let matchesDate = true;
        if (dateMonthFilter || dateDayFilter || dateYearFilter) {
            if (dateStr) {
                const dateParts = dateStr.split('-');
                if (dateParts.length === 3) {
                    const year = dateParts[0];
                    const month = dateParts[1];
                    const day = dateParts[2];
                    
                    const matchesMonth = !dateMonthFilter || month === String(dateMonthFilter).padStart(2, '0');
                    const matchesDay = !dateDayFilter || day === String(dateDayFilter).padStart(2, '0');
                    const matchesYear = !dateYearFilter || year === dateYearFilter;
                    
                    matchesDate = matchesMonth && matchesDay && matchesYear;
                } else {
                    matchesDate = false;
                }
            } else {
                matchesDate = false;
            }
        }
        
        const matchesStartTime = !filterStartTime || startTime === filterStartTime;
        const matchesEndTime = !filterEndTime || endTime === filterEndTime;
        const matchesMinAppointments = filterMinAppointments === null || maxAppointments >= filterMinAppointments;
        const matchesAvailable = !filterAvailable || available === filterAvailable;
        
        if (matchesDoctor && matchesSpecialization && matchesDate && matchesStartTime && matchesEndTime && matchesMinAppointments && matchesAvailable) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide pagination and filter message
    const paginationContainer = document.getElementById('paginationContainer');
    let filterActiveMessage = document.getElementById('filterActiveMessage');
    
    if (hasActiveFilters) {
        if (paginationContainer) paginationContainer.style.display = 'none';
        
        if (!filterActiveMessage) {
            filterActiveMessage = document.createElement('div');
            filterActiveMessage.id = 'filterActiveMessage';
            filterActiveMessage.style.cssText = 'padding: 1.5rem; text-align: center; color: var(--text-secondary); font-size: 0.875rem; border-top: 1px solid var(--border-light);';
            tbody.parentElement.parentElement.appendChild(filterActiveMessage);
        }
        
        if (visibleCount === 0) {
            filterActiveMessage.innerHTML = '<i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>No schedules match the applied filters.';
        } else {
            filterActiveMessage.innerHTML = `<i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Showing ${visibleCount} schedule(s) matching your filters. <a href="javascript:void(0)" onclick="resetTableFilters()" style="color: var(--primary-blue); text-decoration: underline; margin-left: 0.5rem;">Clear filters</a>`;
        }
        filterActiveMessage.style.display = 'block';
    } else {
        if (paginationContainer) paginationContainer.style.display = '';
        if (filterActiveMessage) filterActiveMessage.style.display = 'none';
    }
}

function resetTableFilters() {
    document.getElementById('filterDoctor').value = '';
    document.getElementById('filterSpecialization').value = '';
    document.getElementById('filterDateMonth').value = '';
    document.getElementById('filterDateDay').value = '';
    document.getElementById('filterDateYear').value = '';
    document.getElementById('filterStartTime').value = '';
    document.getElementById('filterEndTime').value = '';
    document.getElementById('filterMinAppointments').value = '';
    document.getElementById('filterAvailable').value = '';
    
    filterTable();
    resetToPaginatedView();
}

function toggleTableFilters() {
    const filterBar = document.getElementById('tableFilterBar');
    const toggleBtn = document.getElementById('toggleFilterBtn');
    
    if (filterBar.style.display === 'none' || !filterBar.style.display) {
        filterBar.style.display = 'block';
        toggleBtn.classList.add('active');
        toggleBtn.style.background = 'var(--primary-blue)';
        toggleBtn.style.color = 'white';
        loadAllResults();
    } else {
        filterBar.style.display = 'none';
        toggleBtn.classList.remove('active');
        toggleBtn.style.background = 'var(--bg-light)';
        toggleBtn.style.color = 'var(--text-secondary)';
        resetTableFilters();
        resetToPaginatedView();
    }
}

function loadAllResults() {
    const url = new URL(window.location.href);
    url.searchParams.set('all_results', '1');
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

function resetToPaginatedView() {
    const url = new URL(window.location.href);
    url.searchParams.delete('all_results');
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

// Edit and View Schedule Functions
function editSchedule(sched) {
    document.getElementById('edit_id').value = sched.schedule_id;
    document.getElementById('edit_schedule_date').value = sched.schedule_date;
    document.getElementById('edit_start_time').value = sched.start_time;
    document.getElementById('edit_end_time').value = sched.end_time;
    document.getElementById('edit_max_appointments').value = sched.max_appointments;
    document.getElementById('edit_is_available').checked = sched.is_available == 1 || sched.is_available === true;
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

function viewSchedule(sched) {
    const doctorName = sched.doc_first_name && sched.doc_last_name 
        ? `Dr. ${sched.doc_first_name} ${sched.doc_last_name}` 
        : 'N/A';
    const specName = sched.spec_name || 'N/A';
    const scheduleDate = sched.schedule_date ? new Date(sched.schedule_date).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    }) : 'N/A';
    const startTime = sched.start_time || 'N/A';
    const endTime = sched.end_time || 'N/A';
    const maxAppointments = sched.max_appointments || 0;
    const isAvailable = sched.is_available == 1 || sched.is_available === true;
    const created = sched.created_at ? new Date(sched.created_at).toLocaleString('en-US') : 'N/A';
    const updated = sched.updated_at ? new Date(sched.updated_at).toLocaleString('en-US') : 'N/A';
    
    const content = `
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3 class="card-title" style="color: var(--primary-blue);">Schedule Information</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Doctor:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--text-primary);">${doctorName}</p>
                    </div>
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Specialization:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--text-primary);">${specName}</p>
                    </div>
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Schedule Date:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--text-primary);">${scheduleDate}</p>
                    </div>
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Time:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--text-primary);">${startTime} - ${endTime}</p>
                    </div>
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Max Appointments:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--text-primary);">${maxAppointments}</p>
                    </div>
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Available:</strong>
                        <p style="margin: 0.5rem 0 0 0;">
                            <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: ${isAvailable ? '#10b98120; color: #10b981;' : '#ef444420; color: #ef4444;'}">
                                ${isAvailable ? 'Yes' : 'No'}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="color: var(--primary-blue);">Timestamps</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Created At:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--text-primary);">${created}</p>
                    </div>
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Updated At:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--text-primary);">${updated}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.getElementById('viewContent').innerHTML = content;
    document.getElementById('viewModal').classList.add('active');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.remove('active');
}

// Initialize filter event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Filters only apply when "Apply Filters" button is clicked
    
    // Add event listeners for edit buttons
    document.querySelectorAll('.edit-schedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-schedule');
                const decodedJson = atob(encodedData);
                const scheduleData = JSON.parse(decodedJson);
                editSchedule(scheduleData);
            } catch (e) {
                console.error('Error parsing schedule data:', e);
                alert('Error loading schedule data. Please check the console for details.');
            }
        });
    });
    
    // Add event listeners for view buttons
    document.querySelectorAll('.view-schedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-schedule');
                const decodedJson = atob(encodedData);
                const scheduleData = JSON.parse(decodedJson);
                viewSchedule(scheduleData);
            } catch (e) {
                console.error('Error parsing schedule data:', e);
                alert('Error loading schedule data. Please check the console for details.');
            }
        });
    });
    
    // Close modals on outside click and Escape key
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                modal.classList.remove('active');
            });
        }
    });
    
    // Check if filters are active on page load
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('all_results') === '1') {
        const filterBar = document.getElementById('tableFilterBar');
        const toggleBtn = document.getElementById('toggleFilterBtn');
        if (filterBar) {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
            toggleBtn.style.background = 'var(--primary-blue)';
            toggleBtn.style.color = 'white';
        }
        
        // Restore filter values from sessionStorage and apply them
        const pendingFilters = sessionStorage.getItem('pendingFilters');
        if (pendingFilters) {
            try {
                const filterValues = JSON.parse(pendingFilters);
                if (filterValues.filterDoctor && document.getElementById('filterDoctor')) {
                    document.getElementById('filterDoctor').value = filterValues.filterDoctor;
                }
                if (filterValues.filterSpecialization && document.getElementById('filterSpecialization')) {
                    document.getElementById('filterSpecialization').value = filterValues.filterSpecialization;
                }
                if (filterValues.filterDateMonth && document.getElementById('filterDateMonth')) {
                    document.getElementById('filterDateMonth').value = filterValues.filterDateMonth;
                }
                if (filterValues.filterDateDay && document.getElementById('filterDateDay')) {
                    document.getElementById('filterDateDay').value = filterValues.filterDateDay;
                }
                if (filterValues.filterDateYear && document.getElementById('filterDateYear')) {
                    document.getElementById('filterDateYear').value = filterValues.filterDateYear;
                }
                if (filterValues.filterStartTime && document.getElementById('filterStartTime')) {
                    document.getElementById('filterStartTime').value = filterValues.filterStartTime;
                }
                if (filterValues.filterEndTime && document.getElementById('filterEndTime')) {
                    document.getElementById('filterEndTime').value = filterValues.filterEndTime;
                }
                if (filterValues.filterMinAppointments && document.getElementById('filterMinAppointments')) {
                    document.getElementById('filterMinAppointments').value = filterValues.filterMinAppointments;
                }
                if (filterValues.filterAvailable && document.getElementById('filterAvailable')) {
                    document.getElementById('filterAvailable').value = filterValues.filterAvailable;
                }
                // Apply the filters
                filterTable();
                // Clear the stored filters
                sessionStorage.removeItem('pendingFilters');
            } catch (e) {
                console.error('Error restoring filters:', e);
                sessionStorage.removeItem('pendingFilters');
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
