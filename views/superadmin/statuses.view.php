<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">All Appointment Statuses</h1>
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
<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #8b5cf6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Statuses</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Appointments</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total_appointments'] ?? 0 ?></div>
    </div>
</div>

<!-- Table Container -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header with Add Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">All Appointment Statuses</h2>
        <button type="button" class="btn btn-primary" onclick="openAddStatusModal()" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i>
            <span>Add Status</span>
        </button>
    </div>

    <?php if (empty($statuses)): ?>
        <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
            <i class="fas fa-clipboard-list" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p style="margin: 0;">No statuses found.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid var(--border-light);">
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Status Name
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Description
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Preview
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Appointments
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Date Created
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Date Updated
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statuses as $status): ?>
                        <tr style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <strong style="color: var(--text-primary);"><?= htmlspecialchars($status['status_name']) ?></strong>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($status['status_description'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem;">
                                <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: <?= htmlspecialchars($status['status_color'] ?? '#3B82F6') ?>; color: white;">
                                    <?= htmlspecialchars($status['status_name']) ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= isset($status['appointment_count']) ? $status['appointment_count'] : 0 ?> appointment(s)</td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= isset($status['created_at']) && $status['created_at'] ? date('d M Y', strtotime($status['created_at'])) : 'N/A' ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= isset($status['updated_at']) && $status['updated_at'] ? date('d M Y', strtotime($status['updated_at'])) : 'N/A' ?></td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button class="btn btn-sm edit-status-btn" 
                                            data-status="<?= base64_encode(json_encode($status)) ?>" 
                                            title="Edit"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm view-status-btn" 
                                            data-status="<?= base64_encode(json_encode($status)) ?>" 
                                            title="View"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return handleDelete(event, 'Are you sure? This will affect <?= isset($status['appointment_count']) ? $status['appointment_count'] : 0 ?> appointment(s).');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $status['status_id'] ?>">
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

<!-- Add Status Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add New Status</h2>
            <button type="button" class="modal-close" onclick="closeAddStatusModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-grid">
                <div class="form-group">
                    <label>Status Name: <span style="color: var(--status-error);">*</span></label>
                    <input type="text" name="status_name" required placeholder="e.g., Scheduled, Completed, Cancelled" class="form-control">
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <input type="text" name="status_description" placeholder="Brief description" class="form-control">
                </div>
                <div class="form-group">
                    <label>Color:</label>
                    <input type="color" name="status_color" value="#3B82F6" style="width: 100%; height: 42px; cursor: pointer; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                </div>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    <span>Add Status</span>
                </button>
                <button type="button" onclick="closeAddStatusModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Status Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Status</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-grid">
                <div class="form-group">
                    <label>Status Name: <span style="color: var(--status-error);">*</span></label>
                    <input type="text" name="status_name" id="edit_status_name" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <input type="text" name="status_description" id="edit_status_description" class="form-control">
                </div>
                <div class="form-group">
                    <label>Color:</label>
                    <input type="color" name="status_color" id="edit_status_color" style="width: 100%; height: 42px; cursor: pointer; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                </div>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    <span>Update Status</span>
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
function openAddStatusModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddStatusModal() {
    document.getElementById('addModal').classList.remove('active');
    document.querySelector('#addModal form').reset();
}

function editStatus(status) {
    document.getElementById('edit_id').value = status.status_id;
    document.getElementById('edit_status_name').value = status.status_name;
    document.getElementById('edit_status_description').value = status.status_description || '';
    document.getElementById('edit_status_color').value = status.status_color || '#3B82F6';
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

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
    
    // Add event listeners for edit buttons
    document.querySelectorAll('.edit-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-status');
                const decodedJson = atob(encodedData);
                const statusData = JSON.parse(decodedJson);
                editStatus(statusData);
            } catch (e) {
                console.error('Error parsing status data:', e);
                alert('Error loading status data. Please check the console for details.');
            }
        });
    });
    
    // Close modals on outside click
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
    
    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                modal.classList.remove('active');
            });
        }
    });
});

function toggleFilterSidebar() {
    // Filter sidebar not implemented for statuses page
    alert('Filter sidebar not available for this page');
}

function filterByCategory(category) {
    if (category === 'all') {
        window.location.href = '/superadmin/statuses';
    }
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
