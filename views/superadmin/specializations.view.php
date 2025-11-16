<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">All Specializations</h1>
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
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #8b5cf6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Specializations</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">With Doctors</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['with_doctors'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Doctors</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total_doctors'] ?? 0 ?></div>
    </div>
</div>

<!-- Table Container -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header with Add Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">All Specializations</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddSpecializationModal()" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i>
            <span>Add Specialization</span>
        </button>
    </div>

    <!-- Filter Bar (Hidden by default) -->
    <div id="tableFilterBar" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Specializations
            </h3>
            <button type="button" class="btn btn-sm" onclick="resetTableFilters()" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
                <i class="fas fa-redo"></i>
                <span>Reset Filters</span>
            </button>
        </div>
        <div class="filter-controls-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-graduation-cap" style="margin-right: 0.25rem;"></i>Specialization Name
                </label>
                <input type="text" id="filterName" class="filter-input" placeholder="Search name..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-align-left" style="margin-right: 0.25rem;"></i>Description
                </label>
                <input type="text" id="filterDescription" class="filter-input" placeholder="Search description..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-user-md" style="margin-right: 0.25rem;"></i>Min Doctors
                </label>
                <input type="number" id="filterMinDoctors" class="filter-input" placeholder="Min doctors..." min="0" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>Date Registered
                </label>
                <input type="date" id="filterDate" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
        </div>
    </div>

    <?php if (empty($specializations)): ?>
        <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
            <i class="fas fa-graduation-cap" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p style="margin: 0;">No specializations found.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid var(--border-light);">
                        <?php
                        $current_sort = $_GET['sort'] ?? 'spec_name';
                        $current_order = $_GET['order'] ?? 'ASC';
                        ?>
                        <th class="sortable <?= $current_sort === 'spec_name' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('spec_name')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Specialization Name
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Description
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Doctors
                        </th>
                        <th class="sortable <?= $current_sort === 'created_at' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('created_at')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Date Registered
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($specializations as $spec): ?>
                        <tr class="table-row" 
                            data-name="<?= htmlspecialchars(strtolower($spec['spec_name'] ?? '')) ?>"
                            data-description="<?= htmlspecialchars(strtolower($spec['spec_description'] ?? '')) ?>"
                            data-doctors="<?= (int)($spec['doctor_count'] ?? 0) ?>"
                            data-date="<?= !empty($spec['created_at']) ? date('Y-m-d', strtotime($spec['created_at'])) : '' ?>"
                            style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <strong style="color: var(--text-primary);"><?= htmlspecialchars($spec['spec_name']) ?></strong>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($spec['spec_description'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem;">
                                <?php if (isset($spec['doctor_count']) && $spec['doctor_count'] > 0): ?>
                                    <button type="button" class="btn btn-sm view-doctors-btn" 
                                            data-spec-id="<?= $spec['spec_id'] ?>"
                                            data-spec-name="<?= htmlspecialchars($spec['spec_name']) ?>"
                                            onclick="viewDoctors(<?= $spec['spec_id'] ?>, '<?= htmlspecialchars($spec['spec_name']) ?>')"
                                            style="padding: 0.25rem 0.75rem; background: var(--primary-blue); color: white; border: none; border-radius: var(--radius-md); cursor: pointer; font-size: 0.875rem; font-weight: 600;">
                                        <?= $spec['doctor_count'] ?> Doctor(s)
                                    </button>
                                <?php else: ?>
                                    <span style="color: var(--text-secondary);">0 Doctors</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $spec['created_at'] ? date('d M Y', strtotime($spec['created_at'])) : 'N/A' ?></td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button class="btn btn-sm edit-spec-btn" 
                                            data-spec="<?= base64_encode(json_encode($spec)) ?>" 
                                            title="Edit"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return handleDelete(event, 'Are you sure you want to delete this specialization?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $spec['spec_id'] ?>">
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

<!-- Add Specialization Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add New Specialization</h2>
            <button type="button" class="modal-close" onclick="closeAddSpecializationModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label>Specialization Name: <span style="color: var(--status-error);">*</span></label>
                <input type="text" name="spec_name" required placeholder="e.g., Family Medicine, Cardiology" class="form-control">
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea name="spec_description" rows="3" placeholder="Brief description of this specialization" class="form-control"></textarea>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    <span>Add Specialization</span>
                </button>
                <button type="button" onclick="closeAddSpecializationModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Specialization Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Specialization</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group">
                <label>Specialization Name: <span style="color: var(--status-error);">*</span></label>
                <input type="text" name="spec_name" id="edit_spec_name" required class="form-control">
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea name="spec_description" id="edit_spec_description" rows="3" class="form-control"></textarea>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    <span>Update Specialization</span>
                </button>
                <button type="button" onclick="closeEditModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Doctors Modal -->
<div id="viewDoctorsModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2 class="modal-title" id="viewDoctorsTitle">Doctors</h2>
            <button type="button" class="modal-close" onclick="closeViewDoctorsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="viewDoctorsContent" style="padding: 1.5rem;">
            <p style="text-align: center; color: var(--text-secondary);">Loading doctors...</p>
        </div>
        <div class="modal-footer" style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-light); display: flex; justify-content: flex-end;">
            <button type="button" onclick="closeViewDoctorsModal()" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                <span>Close</span>
            </button>
        </div>
    </div>
</div>

<script>
function openAddSpecializationModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddSpecializationModal() {
    document.getElementById('addModal').classList.remove('active');
    document.querySelector('#addModal form').reset();
}

function editSpecialization(spec) {
    document.getElementById('edit_id').value = spec.spec_id;
    document.getElementById('edit_spec_name').value = spec.spec_name;
    document.getElementById('edit_spec_description').value = spec.spec_description || '';
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
    document.querySelectorAll('.edit-spec-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-spec');
                const decodedJson = atob(encodedData);
                const specData = JSON.parse(decodedJson);
                editSpecialization(specData);
            } catch (e) {
                console.error('Error parsing specialization data:', e);
                alert('Error loading specialization data. Please check the console for details.');
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
    // Filter sidebar not implemented for specializations page
    alert('Filter sidebar not available for this page');
}

function filterByCategory(category) {
    if (category === 'all') {
        window.location.href = '/superadmin/specializations';
    }
}

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
    url.searchParams.delete('page'); // Reset to page 1 when sorting
    
    window.location.href = url.toString();
}

// Filtering Functions
function filterTable() {
    const tbody = document.getElementById('tableBody');
    if (!tbody) return;
    
    const rows = tbody.querySelectorAll('.table-row');
    const filterName = document.getElementById('filterName')?.value.toLowerCase().trim() || '';
    const filterDescription = document.getElementById('filterDescription')?.value.toLowerCase().trim() || '';
    const filterMinDoctors = document.getElementById('filterMinDoctors')?.value ? parseInt(document.getElementById('filterMinDoctors').value) : null;
    const filterDate = document.getElementById('filterDate')?.value || '';
    
    let visibleCount = 0;
    let hasActiveFilters = filterName || filterDescription || filterMinDoctors !== null || filterDate;
    
    rows.forEach(row => {
        const name = row.getAttribute('data-name') || '';
        const description = row.getAttribute('data-description') || '';
        const doctors = parseInt(row.getAttribute('data-doctors') || '0');
        const date = row.getAttribute('data-date') || '';
        
        const matchesName = !filterName || name.includes(filterName);
        const matchesDescription = !filterDescription || description.includes(filterDescription);
        const matchesMinDoctors = filterMinDoctors === null || doctors >= filterMinDoctors;
        const matchesDate = !filterDate || date === filterDate;
        
        if (matchesName && matchesDescription && matchesMinDoctors && matchesDate) {
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
            filterActiveMessage.innerHTML = '<i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>No specializations match the applied filters.';
        } else {
            filterActiveMessage.innerHTML = `<i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Showing ${visibleCount} specialization(s) matching your filters. <a href="javascript:void(0)" onclick="resetTableFilters()" style="color: var(--primary-blue); text-decoration: underline; margin-left: 0.5rem;">Clear filters</a>`;
        }
        filterActiveMessage.style.display = 'block';
    } else {
        if (filterActiveMessage) filterActiveMessage.style.display = 'none';
    }
}

function resetTableFilters() {
    document.getElementById('filterName').value = '';
    document.getElementById('filterDescription').value = '';
    document.getElementById('filterMinDoctors').value = '';
    document.getElementById('filterDate').value = '';
    
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

// View Doctors Function
function viewDoctors(specId, specName) {
    // Fetch doctors for this specialization
    const url = new URL(window.location.origin + '/superadmin/specializations');
    url.searchParams.set('action', 'get_doctors');
    url.searchParams.set('spec_id', specId);
    
    fetch(url.toString())
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            
            const doctors = data.doctors || [];
            let doctorsHtml = '';
            
            if (doctors.length === 0) {
                doctorsHtml = '<p style="text-align: center; color: var(--text-secondary); padding: 2rem;">No doctors found for this specialization.</p>';
            } else {
                doctorsHtml = '<div style="max-height: 400px; overflow-y: auto;"><table style="width: 100%; border-collapse: collapse;"><thead><tr style="background: #f9fafb; border-bottom: 1px solid var(--border-light);"><th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Doctor Name</th><th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Email</th><th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Phone</th><th style="padding: 0.75rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Status</th></tr></thead><tbody>';
                
                doctors.forEach(doctor => {
                    const statusColor = (doctor.doc_status || 'active') === 'active' ? '#10b981' : '#ef4444';
                    const firstName = (doctor.doc_first_name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    const lastName = (doctor.doc_last_name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    const email = (doctor.doc_email || 'N/A').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    const phone = (doctor.doc_phone || 'N/A').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    const status = (doctor.doc_status || 'active').charAt(0).toUpperCase() + (doctor.doc_status || 'active').slice(1);
                    doctorsHtml += `<tr style="border-bottom: 1px solid var(--border-light);"><td style="padding: 0.75rem;"><strong>${firstName} ${lastName}</strong></td><td style="padding: 0.75rem; color: var(--text-secondary);">${email}</td><td style="padding: 0.75rem; color: var(--text-secondary);">${phone}</td><td style="padding: 0.75rem;"><span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: ${statusColor}; color: white;">${status}</span></td></tr>`;
                });
                
                doctorsHtml += '</tbody></table></div>';
            }
            
            document.getElementById('viewDoctorsContent').innerHTML = doctorsHtml;
            document.getElementById('viewDoctorsTitle').textContent = 'Doctors - ' + specName;
            document.getElementById('viewDoctorsModal').classList.add('active');
        })
        .catch(error => {
            console.error('Error fetching doctors:', error);
            alert('Error loading doctors. Please try again.');
        });
}

function closeViewDoctorsModal() {
    document.getElementById('viewDoctorsModal').classList.remove('active');
}

// Initialize filter event listeners
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = ['filterName', 'filterDescription', 'filterMinDoctors', 'filterDate'];
    filterInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', filterTable);
            input.addEventListener('change', filterTable);
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
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
