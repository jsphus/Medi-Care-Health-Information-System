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

<!-- Today's Schedules -->
<div class="card" style="border-left: 4px solid var(--primary-blue);">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-calendar-day"></i>
            Today's Schedules (<?= date('l, F j, Y') ?>)
        </h2>
    </div>
    <?php if (empty($today_schedules)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
            <div class="empty-state-text">No schedules for today.</div>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Available</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($today_schedules as $schedule): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($schedule['doctor_name']) ?></strong></td>
                            <td><?= htmlspecialchars($schedule['spec_name'] ?? 'N/A') ?></td>
                            <td><?= date('g:i A', strtotime($schedule['start_time'])) ?></td>
                            <td><?= date('g:i A', strtotime($schedule['end_time'])) ?></td>
                            <td>
                                <span class="status-badge <?= $schedule['is_available'] ? 'active' : 'inactive' ?>">
                                    <?= $schedule['is_available'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Add New Schedule -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Add New Schedule</h2>
    </div>
    <div class="card-body">
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
            </div>
        </form>
    </div>
</div>

<!-- All Schedules -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">All Schedules</h2>
    </div>
    <?php if (empty($all_schedules)): ?>
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
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th class="sortable <?= $current_sort === 'schedule_date' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('schedule_date')">
                            Date
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
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
                        <th>Max Appts</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_schedules as $schedule): ?>
                        <tr>
                            <td><?= htmlspecialchars($schedule['doctor_name']) ?></td>
                            <td><?= htmlspecialchars($schedule['spec_name'] ?? 'N/A') ?></td>
                            <td><?= date('M j, Y', strtotime($schedule['schedule_date'])) ?></td>
                            <td><?= date('g:i A', strtotime($schedule['start_time'])) ?></td>
                            <td><?= date('g:i A', strtotime($schedule['end_time'])) ?></td>
                            <td><?= $schedule['max_appointments'] ?? 10 ?></td>
                            <td>
                                <span class="status-badge <?= $schedule['is_available'] ? 'active' : 'inactive' ?>">
                                    <?= $schedule['is_available'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <button onclick='editSchedule(<?= json_encode($schedule) ?>)' class="btn btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return handleDelete(event, 'Delete this schedule?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $schedule['schedule_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
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

// Close modals on outside click and Escape key
document.addEventListener('DOMContentLoaded', function() {
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
