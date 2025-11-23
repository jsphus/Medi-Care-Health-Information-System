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
        <h1 class="page-title">Past Appointments</h1>
        <p style="color: var(--text-secondary); font-size: 0.9375rem; margin-top: 0.5rem;">
            View your completed and past appointments
        </p>
    </div>
    <!-- Navigation Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem; border-bottom: 2px solid var(--border-light);">
        <a href="/doctor/appointments/today" class="tab-link" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-calendar-day"></i>
            <span>Today</span>
        </a>
        <a href="/doctor/appointments" class="tab-link" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-th-large"></i>
            <span>All</span>
        </a>
        <a href="/doctor/appointments/future" class="tab-link" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--text-secondary); font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-calendar-check"></i>
            <span>Upcoming</span>
        </a>
        <a href="/doctor/appointments/previous" class="tab-link active" style="padding: 0.75rem 1.5rem; text-decoration: none; color: var(--primary-blue); font-weight: 600; border-bottom: 2px solid var(--primary-blue); margin-bottom: -2px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-history"></i>
            <span>Past</span>
            <?php if (count($appointments) > 0): ?>
                <span class="badge" style="background: var(--text-secondary); color: white; padding: 0.125rem 0.5rem; border-radius: 10px; font-size: 0.75rem;"><?= count($appointments) ?></span>
            <?php endif; ?>
        </a>
    </div>
</div>

<?php if (isset($error) && $error): ?>
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
                    <i class="fas fa-history"></i>
                    <span>Previous Appointments</span>
                </div>
                <div class="stat-value"><?= count($appointments) ?></div>
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

<!-- Previous Appointments Section -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 class="card-title" style="margin: 0;">
                <i class="fas fa-history" style="margin-right: 0.5rem; color: var(--text-secondary);"></i>
                Previous Appointments
            </h2>
            <span class="badge" style="background: var(--text-secondary); color: white; padding: 0.25rem 0.75rem; border-radius: var(--radius-md); font-size: 0.75rem;">
                <?= count($appointments) ?>
            </span>
        </div>
        <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
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
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>From Date
                </label>
                <input type="date" id="filterDateFrom" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>To Date
                </label>
                <input type="date" id="filterDateTo" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-info-circle" style="margin-right: 0.25rem;"></i>Status
                </label>
                <select id="filterStatus" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem; background: white; cursor: pointer;">
                    <option value="">All Statuses</option>
                    <?php
                    require_once __DIR__ . '/../../config/Database.php';
                    try {
                        $db = Database::getInstance();
                        $status_stmt = $db->query("SELECT status_name FROM appointment_statuses ORDER BY status_name");
                        $statuses = $status_stmt->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($statuses as $status): ?>
                            <option value="<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($status) ?></option>
                        <?php endforeach;
                    } catch (PDOException $e) {
                        // Keep default options
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
    <?php if (empty($appointments)): ?>
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
                    <th>Status</th>
                    <th style="width: 50px;">
                        <i class="fas fa-sticky-note notes-header-icon" title="Notes - Hover over rows to view"></i>
                    </th>
                </tr>
            </thead>
            <tbody id="tableBodyPrevious">
                <?php foreach ($appointments as $apt): ?>
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
        
        <!-- Pagination -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
        <div id="paginationContainer" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-top: 1px solid var(--border-light);">
            <div style="color: var(--text-secondary); font-size: 0.875rem;">
                Showing <?= $offset + 1 ?>-<?= min($offset + $items_per_page, $total_items) ?> of <?= $total_items ?> entries
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => max(1, $page - 1)])) ?>" 
                   class="btn btn-sm" 
                   style="<?= $page <= 1 ? 'opacity: 0.5; pointer-events: none;' : '' ?>">
                    < Previous
                </a>
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                if ($start_page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="btn btn-sm">1</a>
                    <?php if ($start_page > 2): ?>
                        <span style="padding: 0.5rem;">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                       class="btn btn-sm <?= $i == $page ? 'btn-primary' : '' ?>" 
                       style="<?= $i == $page ? 'background: var(--primary-blue); color: white;' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                <?php if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <span style="padding: 0.5rem;">...</span>
                    <?php endif; ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" class="btn btn-sm"><?= $total_pages ?></a>
                <?php endif; ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => min($total_pages, $page + 1)])) ?>" 
                   class="btn btn-sm" 
                   style="<?= $page >= $total_pages ? 'opacity: 0.5; pointer-events: none;' : '' ?>">
                    Next >
                </a>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
// URL-based Filtering Functions
function applyTableFilters() {
    const params = new URLSearchParams();
    
    const filterPatient = document.getElementById('filterPatient')?.value.trim() || '';
    const filterService = document.getElementById('filterService')?.value.trim() || '';
    const filterDateFrom = document.getElementById('filterDateFrom')?.value || '';
    const filterDateTo = document.getElementById('filterDateTo')?.value || '';
    const filterStatus = document.getElementById('filterStatus')?.value || '';
    
    // Preserve sort parameters
    const urlParams = new URLSearchParams(window.location.search);
    const sort = urlParams.get('sort');
    const order = urlParams.get('order');
    
    if (filterPatient) params.set('filter_patient', filterPatient);
    if (filterService) params.set('filter_service', filterService);
    if (filterDateFrom) params.set('filter_date_from', filterDateFrom);
    if (filterDateTo) params.set('filter_date_to', filterDateTo);
    if (filterStatus) params.set('filter_status', filterStatus);
    if (sort) params.set('sort', sort);
    if (order) params.set('order', order);
    
    // Reset to page 1 when applying filters
    params.set('page', '1');
    
    window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
}

function resetTableFilters() {
    // Preserve sort parameters
    const urlParams = new URLSearchParams(window.location.search);
    const sort = urlParams.get('sort');
    const order = urlParams.get('order');
    
    const params = new URLSearchParams();
    if (sort) params.set('sort', sort);
    if (order) params.set('order', order);
    
    window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
}

function toggleTableFilters() {
    const filterBar = document.getElementById('tableFilterBar');
    const toggleBtn = document.getElementById('toggleFilterBtn');
    
    if (filterBar && toggleBtn) {
        if (filterBar.style.display === 'none' || !filterBar.style.display) {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
            toggleBtn.style.background = 'var(--primary-blue)';
            toggleBtn.style.color = 'white';
        } else {
            filterBar.style.display = 'none';
            toggleBtn.classList.remove('active');
            toggleBtn.style.background = 'var(--bg-light)';
            toggleBtn.style.color = 'var(--text-secondary)';
        }
    }
}

// Initialize filter values from URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('filter_patient') && document.getElementById('filterPatient')) {
        document.getElementById('filterPatient').value = urlParams.get('filter_patient');
    }
    if (urlParams.get('filter_service') && document.getElementById('filterService')) {
        document.getElementById('filterService').value = urlParams.get('filter_service');
    }
    if (urlParams.get('filter_date_from') && document.getElementById('filterDateFrom')) {
        document.getElementById('filterDateFrom').value = urlParams.get('filter_date_from');
    }
    if (urlParams.get('filter_date_to') && document.getElementById('filterDateTo')) {
        document.getElementById('filterDateTo').value = urlParams.get('filter_date_to');
    }
    if (urlParams.get('filter_status') && document.getElementById('filterStatus')) {
        document.getElementById('filterStatus').value = urlParams.get('filter_status');
    }
    
    // Show filter bar if any filters are active
    if (urlParams.get('filter_patient') || urlParams.get('filter_service') || urlParams.get('filter_date_from') || urlParams.get('filter_date_to') || urlParams.get('filter_status')) {
        const filterBar = document.getElementById('tableFilterBar');
        const toggleBtn = document.getElementById('toggleFilterBtn');
        if (filterBar) {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
        }
    }
    
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
