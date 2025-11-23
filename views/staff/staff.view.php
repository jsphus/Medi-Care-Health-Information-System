<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">All Staff</h1>
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
            <span style="font-size: 0.875rem; color: var(--text-secondary);">New This Month</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total_this_month'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Active Staff</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['active'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Inactive Staff</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['inactive'] ?? 0 ?></div>
    </div>
</div>

<!-- Table Container -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header with Add Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">All Staff Members</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddStaffModal()" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i>
            <span>Add Staff Member</span>
        </button>
    </div>

    <!-- Filter Bar (Hidden by default) -->
    <div id="tableFilterBar" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Staff
            </h3>
            <button type="button" class="btn btn-sm" onclick="resetTableFilters()" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
                <i class="fas fa-redo"></i>
                <span>Reset Filters</span>
            </button>
        </div>
        <div class="filter-controls-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-user" style="margin-right: 0.25rem;"></i>Staff Name
                </label>
                <input type="text" id="filterName" class="filter-input" placeholder="Search name..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-envelope" style="margin-right: 0.25rem;"></i>Email
                </label>
                <input type="text" id="filterEmail" class="filter-input" placeholder="Search email..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-phone" style="margin-right: 0.25rem;"></i>Phone
                </label>
                <input type="text" id="filterPhone" class="filter-input" placeholder="Search phone..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-briefcase" style="margin-right: 0.25rem;"></i>Position
                </label>
                <select id="filterPosition" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
                    <option value="">All Positions</option>
                    <?php foreach ($filter_positions as $position): ?>
                        <option value="<?= htmlspecialchars($position) ?>"><?= htmlspecialchars($position) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>Date Created - From
                </label>
                <input type="date" id="filterDateFrom" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>Date Created - To
                </label>
                <input type="date" id="filterDateTo" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
            <button type="button" class="btn btn-primary" onclick="applyTableFilters()" style="padding: 0.625rem 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-filter"></i>
                <span>Apply Filters</span>
            </button>
        </div>
    </div>

    <?php if (empty($staff_members)): ?>
        <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
            <i class="fas fa-user-tie" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p style="margin: 0;">No staff members found.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid var(--border-light);">
                        <?php
                        $current_sort = $_GET['sort'] ?? 'created_at';
                        $current_order = $_GET['order'] ?? 'DESC';
                        ?>
                        <th class="sortable <?= $current_sort === 'staff_first_name' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('staff_first_name')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            Staff Name
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'staff_email' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('staff_email')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            Email
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'staff_phone' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('staff_phone')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            Phone
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Position</th>
                        <th class="sortable <?= $current_sort === 'staff_salary' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('staff_salary')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            Salary
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'created_at' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('created_at')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            Date Created
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'updated_at' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('updated_at')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            Date Updated
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($staff_members as $staff): ?>
                        <tr class="table-row" 
                            data-name="<?= htmlspecialchars(strtolower(formatFullName($staff['staff_first_name'] ?? '', $staff['staff_middle_initial'] ?? null, $staff['staff_last_name'] ?? ''))) ?>"
                            data-email="<?= htmlspecialchars(strtolower($staff['staff_email'] ?? '')) ?>"
                            data-phone="<?= htmlspecialchars(strtolower($staff['staff_phone'] ?? '')) ?>"
                            data-position="<?= htmlspecialchars(strtolower($staff['staff_position'] ?? '')) ?>"
                            data-date-day="<?= !empty($staff['created_at']) ? date('d', strtotime($staff['created_at'])) : '' ?>"
                            data-date-month="<?= !empty($staff['created_at']) ? date('m', strtotime($staff['created_at'])) : '' ?>"
                            data-date-year="<?= !empty($staff['created_at']) ? date('Y', strtotime($staff['created_at'])) : '' ?>"
                            style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; overflow: hidden; flex-shrink: 0;">
                                        <?php if (!empty($staff['profile_picture_url'])): ?>
                                            <img src="<?= htmlspecialchars($staff['profile_picture_url']) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?= strtoupper(substr($staff['staff_first_name'] ?? 'S', 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars(formatFullName($staff['staff_first_name'] ?? '', $staff['staff_middle_initial'] ?? null, $staff['staff_last_name'] ?? '')) ?></strong>
                                </div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($staff['staff_email']) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($staff['staff_phone'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($staff['staff_position'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary); font-weight: 600;">₱<?= number_format($staff['staff_salary'] ?? 0, 2) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= !empty($staff['created_at']) ? date('d M Y', strtotime($staff['created_at'])) : 'N/A' ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= !empty($staff['updated_at']) ? date('d M Y', strtotime($staff['updated_at'])) : 'N/A' ?></td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button class="btn btn-sm edit-staff-btn" data-staff="<?= htmlspecialchars(base64_encode(json_encode($staff))) ?>" title="Edit" style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm view-staff-btn" data-staff="<?= htmlspecialchars(base64_encode(json_encode($staff))) ?>" title="View" style="padding: 0.5rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

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

<!-- Add Staff Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add New Staff Member</h2>
            <button type="button" class="modal-close" onclick="closeAddStaffModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name: <span style="color: var(--status-error);">*</span></label>
                    <input type="text" name="first_name" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Middle Initial:</label>
                    <input type="text" name="middle_initial" maxlength="1" class="form-control">
                </div>
                <div class="form-group">
                    <label>Last Name: <span style="color: var(--status-error);">*</span></label>
                    <input type="text" name="last_name" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Email: <span style="color: var(--status-error);">*</span></label>
                    <input type="email" name="email" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="form-group">
                    <label>Position:</label>
                    <input type="text" name="position" placeholder="e.g., Receptionist, Nurse" class="form-control">
                </div>
                <div class="form-group">
                    <label>Salary:</label>
                    <input type="number" name="salary" step="0.01" min="0" class="form-control">
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    <span>Add Staff Member</span>
                </button>
                <button type="button" onclick="closeAddStaffModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Staff Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Staff Member</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name: <span style="color: var(--status-error);">*</span></label>
                    <input type="text" name="first_name" id="edit_first_name" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Middle Initial:</label>
                    <input type="text" name="middle_initial" id="edit_middle_initial" maxlength="1" class="form-control">
                </div>
                <div class="form-group">
                    <label>Last Name: <span style="color: var(--status-error);">*</span></label>
                    <input type="text" name="last_name" id="edit_last_name" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Email: <span style="color: var(--status-error);">*</span></label>
                    <input type="email" name="email" id="edit_email" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="text" name="phone" id="edit_phone" class="form-control">
                </div>
                <div class="form-group">
                    <label>Position:</label>
                    <input type="text" name="position" id="edit_position" class="form-control">
                </div>
                <div class="form-group">
                    <label>Salary:</label>
                    <input type="number" name="salary" id="edit_salary" step="0.01" min="0" class="form-control">
                </div>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    <span>Update Staff Member</span>
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
function openAddStaffModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddStaffModal() {
    document.getElementById('addModal').classList.remove('active');
    document.querySelector('#addModal form').reset();
}

function formatPhoneNumber(value) {
    if (!value) return '';
    let digits = value.toString().replace(/\D/g, '');
    if (digits.length > 11) digits = digits.substring(0, 11);
    if (digits.length >= 7) {
        return digits.substring(0, 4) + '-' + digits.substring(4, 7) + '-' + digits.substring(7);
    } else if (digits.length >= 4) {
        return digits.substring(0, 4) + '-' + digits.substring(4);
    }
    return digits;
}

function formatPhoneInput(inputId) {
    const input = document.getElementById(inputId);
    if (input && !input.hasAttribute('data-phone-formatted')) {
        input.setAttribute('data-phone-formatted', 'true');
        input.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const formatted = formatPhoneNumber(e.target.value);
            if (oldValue !== formatted) {
                e.target.value = formatted;
                const newCursorPosition = cursorPosition + (formatted.length - oldValue.length);
                setTimeout(() => e.target.setSelectionRange(newCursorPosition, newCursorPosition), 0);
            }
        });
        input.addEventListener('blur', function(e) {
            if (e.target.value) e.target.value = formatPhoneNumber(e.target.value);
        });
        if (input.value) input.value = formatPhoneNumber(input.value);
    }
}

function editStaff(staff) {
    document.getElementById('edit_id').value = staff.staff_id;
    document.getElementById('edit_first_name').value = staff.staff_first_name || '';
    document.getElementById('edit_middle_initial').value = staff.staff_middle_initial || '';
    document.getElementById('edit_last_name').value = staff.staff_last_name || '';
    document.getElementById('edit_email').value = staff.staff_email || '';
    document.getElementById('edit_phone').value = staff.staff_phone ? formatPhoneNumber(staff.staff_phone) : '';
    document.getElementById('edit_position').value = staff.staff_position || '';
    document.getElementById('edit_salary').value = staff.staff_salary || '';
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-staff-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-staff');
                const decodedJson = atob(encodedData);
                const staffData = JSON.parse(decodedJson);
                editStaff(staffData);
            } catch (e) {
                console.error('Error parsing staff data:', e);
                alert('Error loading staff data. Please check the console for details.');
            }
        });
    });
    
    formatPhoneInput('edit_phone');
    
    const positionSearch = document.getElementById('positionSearch');
    if (positionSearch) {
        positionSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const positionItems = document.querySelectorAll('#positionList .filter-radio-item');
            positionItems.forEach(item => {
                const label = item.querySelector('label');
                if (label) {
                    const text = label.textContent.toLowerCase();
                    item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
                }
            });
        });
    }
    
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

function filterByCategory(category) {
    if (category === 'all') {
        window.location.href = '/staff/staff';
    } else {
        window.location.href = '/staff/staff?status=' + category;
    }
}

function applyStaffFilters() {
    const filters = {
        position: document.querySelector('input[name="filter_position"]:checked')?.value || ''
    };
    const params = new URLSearchParams();
    if (filters.position) params.append('position', filters.position);
    const url = '/staff/staff' + (params.toString() ? '?' + params.toString() : '');
    window.location.href = url;
}

function clearAllFilters() {
    document.querySelectorAll('.filter-sidebar input[type="radio"]').forEach(radio => {
        radio.checked = false;
    });
    const positionSearch = document.getElementById('positionSearch');
    if (positionSearch) positionSearch.value = '';
    resetTableFilters();
}

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
    const url = new URL(window.location.href);
    
    // Get filter values
    const filterName = document.getElementById('filterName')?.value.trim() || '';
    const filterEmail = document.getElementById('filterEmail')?.value.trim() || '';
    const filterPhone = document.getElementById('filterPhone')?.value.trim() || '';
    const filterPosition = document.getElementById('filterPosition')?.value || '';
    const filterDateFrom = document.getElementById('filterDateFrom')?.value || '';
    const filterDateTo = document.getElementById('filterDateTo')?.value || '';
    
    // Remove existing filter parameters
    url.searchParams.delete('filter_name');
    url.searchParams.delete('filter_email');
    url.searchParams.delete('filter_phone');
    url.searchParams.delete('filter_position');
    url.searchParams.delete('filter_date_from');
    url.searchParams.delete('filter_date_to');
    url.searchParams.delete('page'); // Reset to page 1 when filtering
    
    // Add new filter parameters only if they have values
    if (filterName) {
        url.searchParams.set('filter_name', filterName);
    }
    if (filterEmail) {
        url.searchParams.set('filter_email', filterEmail);
    }
    if (filterPhone) {
        url.searchParams.set('filter_phone', filterPhone);
    }
    if (filterPosition) {
        url.searchParams.set('filter_position', filterPosition);
    }
    if (filterDateFrom) {
        url.searchParams.set('filter_date_from', filterDateFrom);
    }
    if (filterDateTo) {
        url.searchParams.set('filter_date_to', filterDateTo);
    }
    
    window.location.href = url.toString();
}

function resetTableFilters() {
    const url = new URL(window.location.href);
    
    // Remove all filter parameters
    url.searchParams.delete('filter_name');
    url.searchParams.delete('filter_email');
    url.searchParams.delete('filter_phone');
    url.searchParams.delete('filter_position');
    url.searchParams.delete('filter_date_from');
    url.searchParams.delete('filter_date_to');
    url.searchParams.delete('page');
    
    window.location.href = url.toString();
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
    }
}

// Initialize filter values from URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Restore filter values from URL
    const filterName = document.getElementById('filterName');
    const filterEmail = document.getElementById('filterEmail');
    const filterPhone = document.getElementById('filterPhone');
    const filterPosition = document.getElementById('filterPosition');
    const filterDateFrom = document.getElementById('filterDateFrom');
    const filterDateTo = document.getElementById('filterDateTo');
    
    if (filterName && urlParams.get('filter_name')) {
        filterName.value = urlParams.get('filter_name');
    }
    if (filterEmail && urlParams.get('filter_email')) {
        filterEmail.value = urlParams.get('filter_email');
    }
    if (filterPhone && urlParams.get('filter_phone')) {
        filterPhone.value = urlParams.get('filter_phone');
    }
    if (filterPosition && urlParams.get('filter_position')) {
        filterPosition.value = urlParams.get('filter_position');
    }
    if (filterDateFrom && urlParams.get('filter_date_from')) {
        filterDateFrom.value = urlParams.get('filter_date_from');
    }
    if (filterDateTo && urlParams.get('filter_date_to')) {
        filterDateTo.value = urlParams.get('filter_date_to');
    }
    
    // Show filter bar if any filters are active
    const hasFilters = urlParams.get('filter_name') || urlParams.get('filter_email') || 
                       urlParams.get('filter_phone') || urlParams.get('filter_position') || 
                       urlParams.get('filter_date_from') || urlParams.get('filter_date_to');
    
    if (hasFilters) {
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

<style>
.sortable {
    position: relative;
    user-select: none;
}

.sortable:hover {
    background: #f3f4f6 !important;
}

.sort-indicator {
    display: inline-flex;
    flex-direction: column;
    margin-left: 0.5rem;
    font-size: 0.625rem;
    opacity: 0.5;
}

.sort-indicator i {
    line-height: 0.5;
}

.sortable.sort-asc .sort-indicator i.fa-arrow-up {
    opacity: 1;
    color: var(--primary-blue);
}

.sortable.sort-desc .sort-indicator i.fa-arrow-down {
    opacity: 1;
    color: var(--primary-blue);
}
</style>

<!-- Filter Sidebar -->
<div class="filter-sidebar" id="filterSidebar">
    <div class="filter-sidebar-header">
        <h3>Filters</h3>
        <button type="button" class="filter-sidebar-close" onclick="toggleFilterSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <?php if (!empty($filter_positions)): ?>
    <div class="filter-section">
        <div class="filter-section-header" onclick="toggleFilterSection('position')">
            <h4 class="filter-section-title">Position</h4>
            <button type="button" class="filter-section-toggle" id="positionToggle">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
        <div class="filter-section-content" id="positionContent">
            <input type="text" class="filter-search-input" placeholder="Search Position" id="positionSearch">
            <div class="filter-radio-group" id="positionList">
                <?php foreach ($filter_positions as $position): ?>
                    <div class="filter-radio-item">
                        <input type="radio" name="filter_position" id="position_<?= htmlspecialchars(strtolower(str_replace(' ', '_', $position))) ?>" value="<?= htmlspecialchars($position) ?>">
                        <label for="position_<?= htmlspecialchars(strtolower(str_replace(' ', '_', $position))) ?>"><?= htmlspecialchars($position) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="filter-sidebar-actions">
        <button type="button" class="filter-clear-btn" onclick="clearAllFilters()">Clear all</button>
        <button type="button" class="filter-apply-btn" onclick="applyStaffFilters()">Apply all filter</button>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

