<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <h1 class="page-title">My Schedules</h1>
    <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
        <a href="/doctor/appointments/today" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Dashboard</span>
        </a>
        <a href="/doctor/schedules/manage" class="btn btn-primary">
            <i class="fas fa-calendar-alt"></i>
            <span>Manage All Doctor Schedules</span>
        </a>
        <a href="/doctor/doctors" class="btn btn-success">
            <i class="fas fa-user-md"></i>
            <span>Manage Doctors</span>
        </a>
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
            <h2 class="card-title">Today's Schedule (<?= date('F d, Y') ?>)</h2>
        </div>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($today_schedules as $sched): ?>
                        <tr>
                            <td><?= htmlspecialchars($sched['start_time']) ?></td>
                            <td><?= htmlspecialchars($sched['end_time']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="stat-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Today's Appointments Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Today's Appointments</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['today_appointments'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Scheduled for today
                </div>
            </div>
        </div>
    </div>

    <!-- Available Slots Today Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Available Slots Today</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['available_slots_today'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Open appointment slots
                </div>
            </div>
        </div>
    </div>

    <!-- This Week's Schedules Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">This Week's Schedules</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['this_week_schedules'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Next 7 days
                </div>
            </div>
        </div>
    </div>

    <!-- Next Schedule Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Next Schedule</div>
                <?php if ($stats['next_schedule']): ?>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                        <?= date('M d', strtotime($stats['next_schedule']['schedule_date'])) ?>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">
                        <?= date('g:i A', strtotime($stats['next_schedule']['start_time'])) ?>
                    </div>
                <?php else: ?>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.5rem;">
                        No upcoming
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">
                        No future schedules
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- All Schedules -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">All My Schedules</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-primary" onclick="openAddScheduleModal()" style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-plus"></i>
                <span>Add Schedule</span>
            </button>
            <button type="button" class="btn btn-success" onclick="openBatchCreateModal()" style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calendar-plus"></i>
                <span>Batch Create</span>
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
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>Date
                </label>
                <input type="date" id="filterDate" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
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
        </div>
    </div>

    <?php if (empty($schedules)): ?>
        <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
            <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p style="margin: 0;">No schedules found.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid var(--border-light);">
                        <?php
                        $current_sort = $_GET['sort'] ?? 'schedule_date';
                        $current_order = $_GET['order'] ?? 'DESC';
                        ?>
                        <th class="sortable <?= $current_sort === 'schedule_date' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('schedule_date')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Date
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'start_time' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('start_time')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Start Time
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'end_time' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('end_time')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            End Time
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Max Appointments
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Available
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($schedules as $sched): ?>
                        <tr class="table-row" 
                            data-date="<?= !empty($sched['schedule_date']) ? date('Y-m-d', strtotime($sched['schedule_date'])) : '' ?>"
                            data-start-time="<?= htmlspecialchars($sched['start_time'] ?? '') ?>"
                            data-end-time="<?= htmlspecialchars($sched['end_time'] ?? '') ?>"
                            style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <strong style="color: var(--text-primary);"><?= $sched['schedule_date'] ? date('d M Y', strtotime($sched['schedule_date'])) : 'N/A' ?></strong>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($sched['start_time']) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($sched['end_time']) ?></td>
                            <td style="padding: 1rem;">
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
    <?php endif; ?>
</div>

<!-- Add Schedule Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add New Schedule</h2>
            <button type="button" class="modal-close" onclick="closeAddScheduleModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-grid">
                <div class="form-group">
                    <label>Date: <span style="color: var(--status-error);">*</span></label>
                    <input type="date" name="schedule_date" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Start Time: <span style="color: var(--status-error);">*</span></label>
                    <input type="time" name="start_time" required class="form-control">
                </div>
                <div class="form-group">
                    <label>End Time: <span style="color: var(--status-error);">*</span></label>
                    <input type="time" name="end_time" required class="form-control">
                </div>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    <span>Add Schedule</span>
                </button>
                <button type="button" onclick="closeAddScheduleModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
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

<!-- Batch Create Schedule Modal -->
<div id="batchCreateModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2 class="modal-title">Batch Create Schedules</h2>
            <button type="button" class="modal-close" onclick="closeBatchCreateModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="batch_create">
            <div class="form-grid">
                <div class="form-group">
                    <label>Start Date: <span style="color: var(--status-error);">*</span></label>
                    <input type="date" name="start_date" id="batch_start_date" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>End Date: <span style="color: var(--status-error);">*</span></label>
                    <input type="date" name="end_date" id="batch_end_date" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Start Time: <span style="color: var(--status-error);">*</span></label>
                    <input type="time" name="start_time" id="batch_start_time" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>End Time: <span style="color: var(--status-error);">*</span></label>
                    <input type="time" name="end_time" id="batch_end_time" required class="form-control">
                </div>
                
                <div class="form-group">
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Days of Week: <span style="color: var(--status-error);">*</span></label>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="monday" style="margin-right: 0.5rem; width: auto;">
                        <span>Monday</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="tuesday" style="margin-right: 0.5rem; width: auto;">
                        <span>Tuesday</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="wednesday" style="margin-right: 0.5rem; width: auto;">
                        <span>Wednesday</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="thursday" style="margin-right: 0.5rem; width: auto;">
                        <span>Thursday</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="friday" style="margin-right: 0.5rem; width: auto;">
                        <span>Friday</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="saturday" style="margin-right: 0.5rem; width: auto;">
                        <span>Saturday</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="sunday" style="margin-right: 0.5rem; width: auto;">
                        <span>Sunday</span>
                    </label>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 1rem;">
                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                </label>
            </div>
            
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Create Schedules</span>
                </button>
                <button type="button" onclick="closeBatchCreateModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddScheduleModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddScheduleModal() {
    const modal = document.getElementById('addModal');
    modal.classList.remove('active');
    modal.querySelector('form').reset();
}

// Batch Create Modal Functions
function openBatchCreateModal() {
    document.getElementById('batchCreateModal').classList.add('active');
}

function closeBatchCreateModal() {
    const modal = document.getElementById('batchCreateModal');
    modal.classList.remove('active');
    modal.querySelector('form').reset();
}

function editSchedule(sched) {
    document.getElementById('edit_id').value = sched.schedule_id;
    document.getElementById('edit_schedule_date').value = sched.schedule_date;
    document.getElementById('edit_start_time').value = sched.start_time;
    document.getElementById('edit_end_time').value = sched.end_time;
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

// Close modals on outside click and Escape key
document.addEventListener('DOMContentLoaded', function() {
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
    
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
    
    // Reset add form when modal closes
    const addModal = document.getElementById('addModal');
    if (addModal) {
        addModal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.querySelector('form').reset();
            }
        });
    }
    
    // Reset batch create form when modal closes
    const batchCreateModal = document.getElementById('batchCreateModal');
    if (batchCreateModal) {
        batchCreateModal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.querySelector('form').reset();
            }
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                modal.classList.remove('active');
                // Reset forms if it's the add or batch create modal
                if (modal.id === 'addModal' || modal.id === 'batchCreateModal') {
                    modal.querySelector('form').reset();
                }
            });
        }
    });
});
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
    
    window.location.href = url.toString();
}

// Filtering Functions
function applyTableFilters() {
    filterTable();
}

function filterTable() {
    const tbody = document.getElementById('tableBody');
    if (!tbody) return;
    
    const rows = tbody.querySelectorAll('.table-row');
    const filterDate = document.getElementById('filterDate')?.value || '';
    const filterStartTime = document.getElementById('filterStartTime')?.value || '';
    const filterEndTime = document.getElementById('filterEndTime')?.value || '';
    let visibleCount = 0;
    let hasActiveFilters = filterDate || filterStartTime || filterEndTime;
    
    rows.forEach(row => {
        const date = row.getAttribute('data-date') || '';
        const startTime = row.getAttribute('data-start-time') || '';
        const endTime = row.getAttribute('data-end-time') || '';
        const matchesDate = !filterDate || date === filterDate;
        const matchesStartTime = !filterStartTime || startTime === filterStartTime;
        const matchesEndTime = !filterEndTime || endTime === filterEndTime;
        
        if (matchesDate && matchesStartTime && matchesEndTime) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide filter message
    let filterActiveMessage = document.getElementById('filterActiveMessage');
    
    if (hasActiveFilters) {
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
        if (filterActiveMessage) filterActiveMessage.style.display = 'none';
    }
}

function resetTableFilters() {
    document.getElementById('filterDate').value = '';
    document.getElementById('filterStartTime').value = '';
    document.getElementById('filterEndTime').value = '';
    document.getElementById('filterMinAppointments').value = '';
    document.getElementById('filterAvailable').value = '';
    
    filterTable();
}

function toggleTableFilters() {
    const filterBar = document.getElementById('tableFilterBar');
    const toggleBtn = document.getElementById('toggleFilterBtn');
    
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
        resetTableFilters();
    }
}

// Initialize filter event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Filters only apply when "Apply Filters" button is clicked
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
