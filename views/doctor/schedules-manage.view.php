<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-top">
        <div class="breadcrumbs">
            <a href="/doctor/schedules">
                <i class="fas fa-calendar"></i>
                <span>My Schedules</span>
            </a>
            <i class="fas fa-chevron-right"></i>
            <span>Manage All</span>
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

<!-- Summary Cards -->
<div class="stat-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Total Schedules Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Total Schedules</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['total_schedules'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    All schedules
                </div>
            </div>
        </div>
    </div>

    <!-- Schedules Today Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Schedules Today</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['schedules_today'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Available today
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Schedules Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Upcoming Schedules</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['upcoming_schedules'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Future dates
                </div>
            </div>
        </div>
    </div>

    <!-- Doctors with Schedules Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Doctors Scheduled</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['doctors_with_schedules'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Doctors with schedules
                </div>
            </div>
        </div>
    </div>
</div>

<!-- All Schedules -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">All Schedules</h2>
        <button type="button" class="btn btn-primary" onclick="openAddScheduleModal()" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i>
            <span>Add New Schedule</span>
        </button>
    </div>
    <?php if (empty($all_schedules)): ?>
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
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Doctor</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Specialization</th>
                        <th class="sortable <?= $current_sort === 'schedule_date' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('schedule_date')"
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            Date
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'start_time' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('start_time')"
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            Start Time
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'end_time' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('end_time')"
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            End Time
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Max Appts</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Available</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_schedules as $schedule): ?>
                        <tr style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem; color: var(--text-primary); font-weight: 500;"><?= htmlspecialchars($schedule['doctor_name']) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($schedule['spec_name'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= date('M j, Y', strtotime($schedule['schedule_date'])) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= date('g:i A', strtotime($schedule['start_time'])) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= date('g:i A', strtotime($schedule['end_time'])) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $schedule['max_appointments'] ?? 10 ?></td>
                            <td style="padding: 1rem;">
                                <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: <?= $schedule['is_available'] ? '#10b98120; color: #10b981;' : '#ef444420; color: #ef4444;' ?>">
                                    <?= $schedule['is_available'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button onclick='editSchedule(<?= json_encode($schedule) ?>)' class="btn btn-sm" title="Edit"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick='viewSchedule(<?= json_encode($schedule) ?>)' class="btn btn-sm" title="View"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return handleDelete(event, 'Delete this schedule?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $schedule['schedule_id'] ?>">
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
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            <div class="form-grid">
                <div class="form-group">
                    <label>Doctor: <span style="color: var(--status-error);">*</span></label>
                    <select name="doc_id" required class="form-control">
                        <option value="">Select Doctor</option>
                        <?php foreach ($doctors as $doc): ?>
                            <option value="<?= $doc['doc_id'] ?>"><?= htmlspecialchars($doc['doctor_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Schedule Date: <span style="color: var(--status-error);">*</span></label>
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
                
                <div class="form-group">
                    <label>Max Appointments:</label>
                    <input type="number" name="max_appointments" value="10" min="1" max="50" class="form-control">
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; cursor: pointer; margin-top: 2rem;">
                        <input type="checkbox" name="is_available" value="1" checked style="margin-right: 0.5rem; width: auto;">
                        <span>Available for appointments</span>
                    </label>
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

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Schedule</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Doctor: <span style="color: var(--status-error);">*</span></label>
                    <select name="doc_id" id="edit_doc_id" required class="form-control">
                        <option value="">Select Doctor</option>
                        <?php foreach ($doctors as $doc): ?>
                            <option value="<?= $doc['doc_id'] ?>"><?= htmlspecialchars($doc['doctor_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Schedule Date: <span style="color: var(--status-error);">*</span></label>
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
                    <input type="number" name="max_appointments" id="edit_max_appointments" min="1" max="50" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; cursor: pointer; margin-top: 2rem;">
                        <input type="checkbox" name="is_available" id="edit_is_available" value="1" style="margin-right: 0.5rem; width: auto;">
                        <span>Available for appointments</span>
                    </label>
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

<script>
function openAddScheduleModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddScheduleModal() {
    const modal = document.getElementById('addModal');
    modal.classList.remove('active');
    modal.querySelector('form').reset();
}

function editSchedule(schedule) {
    document.getElementById('edit_id').value = schedule.schedule_id;
    document.getElementById('edit_doc_id').value = schedule.doc_id;
    document.getElementById('edit_schedule_date').value = schedule.schedule_date;
    document.getElementById('edit_start_time').value = schedule.start_time;
    document.getElementById('edit_end_time').value = schedule.end_time;
    document.getElementById('edit_max_appointments').value = schedule.max_appointments || 10;
    document.getElementById('edit_is_available').checked = schedule.is_available == 1;
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

function viewSchedule(schedule) {
    const doctorName = schedule.doctor_name || 'N/A';
    const specName = schedule.spec_name || 'N/A';
    const scheduleDate = schedule.schedule_date ? new Date(schedule.schedule_date).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    }) : 'N/A';
    const startTime = schedule.start_time ? new Date('2000-01-01T' + schedule.start_time).toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit',
        hour12: true 
    }) : 'N/A';
    const endTime = schedule.end_time ? new Date('2000-01-01T' + schedule.end_time).toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit',
        hour12: true 
    }) : 'N/A';
    const maxAppointments = schedule.max_appointments || 0;
    const isAvailable = schedule.is_available == 1 || schedule.is_available === true;
    const created = schedule.created_at ? new Date(schedule.created_at).toLocaleString('en-US') : 'N/A';
    const updated = schedule.updated_at ? new Date(schedule.updated_at).toLocaleString('en-US') : 'N/A';
    
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

// Close modals on outside click and Escape key
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                if (this.id === 'addModal') {
                    closeAddScheduleModal();
                } else {
                    this.classList.remove('active');
                }
            }
        });
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                if (modal.id === 'addModal') {
                    closeAddScheduleModal();
                } else {
                    modal.classList.remove('active');
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
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
