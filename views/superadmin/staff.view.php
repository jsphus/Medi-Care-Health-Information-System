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
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: relative; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
            <div style="display: flex; flex-direction: column;">
                <span style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Total Staff</span>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= ($stats['active'] ?? 0) + ($stats['inactive'] ?? 0) ?></div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; flex-shrink: 0;">
                <i class="fas fa-user-tie"></i>
            </div>
        </div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: relative; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
            <div style="display: flex; flex-direction: column;">
                <span style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Active Staff</span>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['active'] ?? 0 ?></div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; flex-shrink: 0;">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: relative; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
            <div style="display: flex; flex-direction: column;">
                <span style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Inactive Staff</span>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['inactive'] ?? 0 ?></div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; flex-shrink: 0;">
                <i class="fas fa-user-slash"></i>
            </div>
        </div>
    </div>
</div>

<style>
/* Scrollable sections styling */
.recently-added-scroll-container {
    overflow-x: hidden;
    overflow-y: auto;
    flex: 1;
    max-height: 500px;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
}

.recently-added-scroll-container::-webkit-scrollbar {
    width: 8px;
}

.recently-added-scroll-container::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 4px;
}

.recently-added-scroll-container::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 4px;
}

.recently-added-scroll-container::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}
</style>

<!-- Recently Added Staff Section -->
<?php if (!empty($recently_added_staff)): ?>
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem; display: flex; flex-direction: column;">
    <div style="margin-bottom: 1.5rem; flex-shrink: 0;">
        <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">Recently Added Staff</h2>
        <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem; color: var(--text-secondary);">Staff members added to the system recently</p>
    </div>
    <div class="recently-added-scroll-container">
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($recently_added_staff as $recent_staff): ?>
                <div style="display: flex; gap: 1rem; padding: 1rem; background: #f9fafb; border-radius: 8px; border: 1px solid var(--border-light); transition: all 0.2s; flex-shrink: 0;" 
                     onmouseover="this.style.background='#f3f4f6'; this.style.borderColor='var(--primary-blue)';" 
                     onmouseout="this.style.background='#f9fafb'; this.style.borderColor='var(--border-light)';">
                    <div style="position: relative; flex-shrink: 0;">
                        <div style="width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.25rem; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <?php if (!empty($recent_staff['profile_picture_url'])): ?>
                                <img src="<?= htmlspecialchars($recent_staff['profile_picture_url']) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <?= strtoupper(substr($recent_staff['staff_first_name'] ?? 'S', 0, 1) . substr($recent_staff['staff_last_name'] ?? 'S', 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 0.75rem;">
                        <div>
                            <div style="font-weight: 600; color: var(--text-primary); font-size: 1rem; margin-bottom: 0.25rem;">
                                <?= htmlspecialchars(formatFullName($recent_staff['staff_first_name'] ?? '', $recent_staff['staff_middle_initial'] ?? null, $recent_staff['staff_last_name'] ?? '')) ?>
                            </div>
                            <div style="font-size: 0.8125rem; color: var(--text-secondary);">
                                <?= htmlspecialchars($recent_staff['staff_position'] ?? 'Staff') ?>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem 1rem; font-size: 0.8125rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary);">
                                <i class="fas fa-envelope" style="width: 16px; color: var(--primary-blue); flex-shrink: 0;"></i>
                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($recent_staff['staff_email'] ?? 'N/A') ?></span>
                            </div>
                            <?php if (!empty($recent_staff['staff_phone'])): ?>
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary);">
                                <i class="fas fa-phone" style="width: 16px; color: var(--primary-blue); flex-shrink: 0;"></i>
                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($recent_staff['staff_phone']) ?></span>
                            </div>
                            <?php else: ?>
                            <div></div>
                            <?php endif; ?>
                            <?php if (!empty($recent_staff['staff_salary'])): ?>
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary);">
                                <i class="fas fa-money-bill-wave" style="width: 16px; color: var(--primary-blue); flex-shrink: 0;"></i>
                                <span>₱<?= number_format($recent_staff['staff_salary'], 2) ?></span>
                            </div>
                            <?php else: ?>
                            <div></div>
                            <?php endif; ?>
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary);">
                                <i class="fas fa-calendar-plus" style="width: 16px; color: var(--primary-blue); flex-shrink: 0;"></i>
                                <span><?= !empty($recent_staff['created_at']) ? date('M d, Y', strtotime($recent_staff['created_at'])) : 'Recently' ?></span>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <?php if (!empty($recent_staff['staff_status'])): ?>
                                <div style="display: flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: <?= $recent_staff['staff_status'] === 'active' ? '#10b981' : '#ef4444' ?>; color: white; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                    <i class="fas fa-<?= $recent_staff['staff_status'] === 'active' ? 'check-circle' : 'times-circle' ?>"></i>
                                    <span><?= htmlspecialchars(ucfirst($recent_staff['staff_status'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Table Container -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header with Add Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">All Staff</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddStaffModal()" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i>
            <span>Add Staff</span>
        </button>
    </div>

    <!-- Filter Bar (Hidden by default) -->
    <div id="tableFilterBar" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Staff
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
                <select id="filterPosition" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem; background: white; cursor: pointer;">
                    <option value="">All Positions</option>
                    <?php if (!empty($filter_positions)): ?>
                        <?php foreach ($filter_positions as $position): ?>
                            <option value="<?= htmlspecialchars($position) ?>"><?= htmlspecialchars($position) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-toggle-on" style="margin-right: 0.25rem;"></i>Status
                </label>
                <select id="filterStatus" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>Date Registered
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
        </div>
    </div>

    <?php if (empty($staff)): ?>
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
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Staff Name
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'staff_email' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('staff_email')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Email
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'staff_phone' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('staff_phone')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Phone
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Position
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Status
                        </th>
                        <th class="sortable <?= $current_sort === 'created_at' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('created_at')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Date Joined
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'updated_at' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('updated_at')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Updated On
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($staff as $member): ?>
                        <tr class="table-row" 
                            data-name="<?= htmlspecialchars(strtolower(($member['staff_first_name'] ?? '') . ' ' . ($member['staff_last_name'] ?? ''))) ?>"
                            data-email="<?= htmlspecialchars(strtolower($member['staff_email'] ?? '')) ?>"
                            data-phone="<?= htmlspecialchars(strtolower($member['staff_phone'] ?? '')) ?>"
                            data-position="<?= htmlspecialchars($member['staff_position'] ?? '') ?>"
                            data-status="<?= htmlspecialchars(strtolower($member['staff_status'] ?? '')) ?>"
                            data-date="<?= !empty($member['created_at']) ? date('Y-m-d', strtotime($member['created_at'])) : '' ?>"
                            style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; overflow: hidden; flex-shrink: 0;">
                                        <?php if (!empty($member['profile_picture_url'])): ?>
                                            <img src="<?= htmlspecialchars($member['profile_picture_url']) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?= strtoupper(substr($member['staff_first_name'] ?? 'S', 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars(formatFullName($member['staff_first_name'] ?? '', $member['staff_middle_initial'] ?? null, $member['staff_last_name'] ?? '')) ?></strong>
                                </div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($member['staff_email']) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($member['staff_phone'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($member['staff_position'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem;">
                                <?php
                                $status = $member['staff_status'] ?? 'active';
                                $statusColor = $status === 'active' ? '#10b981' : '#ef4444';
                                ?>
                                <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: <?= $statusColor ?>; color: white;">
                                    <?= htmlspecialchars(ucfirst($status)) ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $member['created_at'] ? date('d M Y', strtotime($member['created_at'])) : 'N/A' ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $member['updated_at'] ? date('d M Y', strtotime($member['updated_at'])) : 'N/A' ?></td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button class="btn btn-sm edit-staff-btn" 
                                            data-staff="<?= base64_encode(json_encode($member)) ?>" 
                                            title="Edit"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm view-staff-btn" 
                                            data-staff="<?= base64_encode(json_encode($member)) ?>" 
                                            title="View"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return handleDelete(event, 'Are you sure you want to delete this staff member?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $member['staff_id'] ?>">
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

<!-- View Staff Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2 class="modal-title">Staff Details</h2>
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

<!-- Add Staff Modal -->
<div id="addModal" class="modal">
    <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
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
                    <input type="text" name="phone" id="add_phone" class="form-control">
                </div>
                <div class="form-group">
                    <label>Position:</label>
                    <input type="text" name="position" class="form-control">
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
            
            <div class="form-group" style="margin-top: 1rem;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="create_user" value="1" id="add_staff_create_user_checkbox" onchange="toggleAddStaffPasswordField()" style="margin-right: 10px; width: auto;">
                    <span>Create user account for login</span>
                </label>
            </div>
            
            <div class="form-group" id="add_staff_password_field" style="display: none;">
                <label>Password: <span style="color: var(--status-error);">*</span></label>
                <input type="password" name="password" id="add_staff_password_input" minlength="6" placeholder="Minimum 6 characters" class="form-control">
                <small style="display: block; margin-top: 0.5rem; color: var(--text-secondary);">The staff member will use their email and this password to login.</small>
            </div>
            
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    <span>Add Staff</span>
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
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            
            <!-- Profile Picture Section -->
            <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f9fafb; border-radius: 8px;">
                <h3 style="margin-bottom: 1rem; color: var(--primary-blue); border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem;">Profile Picture</h3>
                <div style="display: flex; gap: 1.5rem; align-items: flex-start; flex-wrap: wrap;">
                    <div style="flex-shrink: 0;">
                        <div id="edit_profile_picture_preview" style="width: 120px; height: 120px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 3rem; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            <span id="edit_profile_picture_initials">S</span>
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label class="form-label-modern">Upload New Picture</label>
                        <input type="file" name="profile_picture" id="edit_profile_picture_input" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="form-control" style="padding: 0.5rem;">
                        <small style="color: var(--text-secondary); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Max 5MB. Formats: JPG, PNG, GIF, WEBP</small>
                        <button type="button" id="edit_remove_profile_picture_btn" onclick="removeProfilePicture('edit')" class="btn btn-sm" style="margin-top: 0.5rem; display: none; background: #ef4444; color: white;">
                            <i class="fas fa-trash"></i>
                            <span>Remove Picture</span>
                        </button>
                        <input type="hidden" name="remove_profile_picture" id="edit_remove_profile_picture" value="0">
                    </div>
                </div>
            </div>
            
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
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" id="edit_status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    <span>Update Staff</span>
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
    document.getElementById('add_staff_password_field').style.display = 'none';
    document.getElementById('add_staff_password_input').required = false;
}

function toggleAddStaffPasswordField() {
    const checkbox = document.getElementById('add_staff_create_user_checkbox');
    const passwordField = document.getElementById('add_staff_password_field');
    const passwordInput = document.getElementById('add_staff_password_input');
    
    if (checkbox.checked) {
        passwordField.style.display = 'block';
        passwordInput.required = true;
    } else {
        passwordField.style.display = 'none';
        passwordInput.required = false;
        passwordInput.value = '';
    }
}

// Phone number formatting function (Philippine format: XXXX-XXX-XXXX)
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
    document.getElementById('edit_first_name').value = staff.staff_first_name;
    document.getElementById('edit_middle_initial').value = staff.staff_middle_initial || '';
    document.getElementById('edit_last_name').value = staff.staff_last_name;
    document.getElementById('edit_email').value = staff.staff_email;
    document.getElementById('edit_phone').value = staff.staff_phone ? formatPhoneNumber(staff.staff_phone) : '';
    document.getElementById('edit_position').value = staff.staff_position || '';
    document.getElementById('edit_salary').value = staff.staff_salary || '';
    document.getElementById('edit_status').value = staff.staff_status || 'active';
    
    // Update profile picture preview
    updateProfilePicturePreview('edit', staff.profile_picture_url || '', staff.staff_first_name || 'S');
    
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

function viewStaffDetails(staff) {
    // Helper function to format date
    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            return date.toLocaleString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric', 
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
            });
        } catch (e) {
            return dateString;
        }
    };
    
    // Get profile picture or generate initials
    const profilePicture = staff.profile_picture_url || '';
    const firstName = staff.staff_first_name || 'S';
    const firstLetter = firstName.charAt(0).toUpperCase();
    const fullName = `${staff.staff_first_name || ''}${staff.staff_middle_initial ? ' ' + staff.staff_middle_initial.toUpperCase() + '.' : ''} ${staff.staff_last_name || ''}`.trim();
    
    const content = `
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-body">
                <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-light);">
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 3rem; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        ${profilePicture ? `<img src="${profilePicture}" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">` : firstLetter}
                    </div>
                    <div>
                        <h3 style="margin: 0 0 0.5rem 0; color: var(--text-primary); font-size: 1.5rem;">${fullName || 'N/A'}</h3>
                        <p style="margin: 0; color: var(--text-secondary);">${staff.staff_position || 'N/A'}</p>
                        <div style="margin-top: 0.5rem;">
                            <span class="status-badge ${(staff.staff_status || 'active') === 'active' ? 'active' : 'inactive'}" style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">
                                ${staff.staff_status || 'active'}
                            </span>
                        </div>
                    </div>
                </div>
                <h3 style="margin-bottom: 1rem; color: var(--text-primary);">Staff Information</h3>
                <div class="form-grid">
                    <div>
                        <p style="margin: 0.5rem 0;"><strong>Staff ID:</strong> ${staff.staff_id || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>First Name:</strong> ${staff.staff_first_name || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Last Name:</strong> ${staff.staff_last_name || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Email:</strong> ${staff.staff_email || 'N/A'}</p>
                    </div>
                    <div>
                        <p style="margin: 0.5rem 0;"><strong>Phone:</strong> ${staff.staff_phone || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Position:</strong> ${staff.staff_position || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Status:</strong> 
                            <span class="status-badge ${(staff.staff_status || 'active') === 'active' ? 'active' : 'inactive'}">
                                ${staff.staff_status || 'active'}
                            </span>
                        </p>
                    </div>
                </div>
                ${staff.staff_address ? `<div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light);"><p style="margin: 0;"><strong>Address:</strong> ${staff.staff_address}</p></div>` : ''}
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light); display: flex; gap: 2rem; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-plus-circle" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
                        <div>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);"><strong>Created:</strong> ${formatDate(staff.created_at)}</p>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-edit" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
                        <div>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);"><strong>Updated:</strong> ${formatDate(staff.updated_at)}</p>
                        </div>
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

document.addEventListener('DOMContentLoaded', function() {
    // Edit and view buttons
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
    
    document.querySelectorAll('.view-staff-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-staff');
                const decodedJson = atob(encodedData);
                const staffData = JSON.parse(decodedJson);
                viewStaffDetails(staffData);
            } catch (e) {
                console.error('Error parsing staff data:', e);
                alert('Error loading staff data. Please check the console for details.');
            }
        });
    });
    
    // Initialize phone number formatting
    formatPhoneInput('edit_phone');
    formatPhoneInput('add_phone');
    
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
    
    // Filters only apply when "Apply Filters" button is clicked
    
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
                // Restore filter input values
                if (filterValues.filterName && document.getElementById('filterName')) {
                    document.getElementById('filterName').value = filterValues.filterName;
                }
                if (filterValues.filterEmail && document.getElementById('filterEmail')) {
                    document.getElementById('filterEmail').value = filterValues.filterEmail;
                }
                if (filterValues.filterPhone && document.getElementById('filterPhone')) {
                    document.getElementById('filterPhone').value = filterValues.filterPhone;
                }
                if (filterValues.filterPosition && document.getElementById('filterPosition')) {
                    document.getElementById('filterPosition').value = filterValues.filterPosition;
                }
                if (filterValues.filterStatus && document.getElementById('filterStatus')) {
                    document.getElementById('filterStatus').value = filterValues.filterStatus;
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
            filterName: document.getElementById('filterName')?.value || '',
            filterEmail: document.getElementById('filterEmail')?.value || '',
            filterPhone: document.getElementById('filterPhone')?.value || '',
            filterPosition: document.getElementById('filterPosition')?.value || '',
            filterStatus: document.getElementById('filterStatus')?.value || '',
            filterDateMonth: document.getElementById('filterDateMonth')?.value || '',
            filterDateDay: document.getElementById('filterDateDay')?.value || '',
            filterDateYear: document.getElementById('filterDateYear')?.value || ''
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
    const filterName = document.getElementById('filterName')?.value.toLowerCase().trim() || '';
    const filterEmail = document.getElementById('filterEmail')?.value.toLowerCase().trim() || '';
    const filterPhone = document.getElementById('filterPhone')?.value.toLowerCase().trim() || '';
    const filterPosition = document.getElementById('filterPosition')?.value || '';
    const filterStatus = document.getElementById('filterStatus')?.value.toLowerCase().trim() || '';
    const dateMonthFilter = document.getElementById('filterDateMonth')?.value || '';
    const dateDayFilter = document.getElementById('filterDateDay')?.value || '';
    const dateYearFilter = document.getElementById('filterDateYear')?.value || '';
    
    let visibleCount = 0;
    let hasActiveFilters = filterName || filterEmail || filterPhone || filterPosition || filterStatus || dateMonthFilter || dateDayFilter || dateYearFilter;
    
    rows.forEach(row => {
        const name = row.getAttribute('data-name') || '';
        const email = row.getAttribute('data-email') || '';
        const phone = row.getAttribute('data-phone') || '';
        const position = row.getAttribute('data-position') || '';
        const status = row.getAttribute('data-status') || '';
        const dateStr = row.getAttribute('data-date') || '';
        
        const matchesName = !filterName || name.includes(filterName);
        const matchesEmail = !filterEmail || email.includes(filterEmail);
        const matchesPhone = !filterPhone || phone.includes(filterPhone);
        const matchesPosition = !filterPosition || position === filterPosition;
        const matchesStatus = !filterStatus || status === filterStatus;
        
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
        
        if (matchesName && matchesEmail && matchesPhone && matchesPosition && matchesStatus && matchesDate) {
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
            tbody.parentElement.parentElement.parentElement.appendChild(filterActiveMessage);
        }
        
        if (visibleCount === 0) {
            filterActiveMessage.innerHTML = '<i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>No staff members match the applied filters.';
        } else {
            filterActiveMessage.innerHTML = `<i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Showing ${visibleCount} staff member(s) matching your filters. <a href="javascript:void(0)" onclick="resetTableFilters()" style="color: var(--primary-blue); text-decoration: underline; margin-left: 0.5rem;">Clear filters</a>`;
        }
        filterActiveMessage.style.display = 'block';
    } else {
        if (paginationContainer) paginationContainer.style.display = '';
        if (filterActiveMessage) filterActiveMessage.style.display = 'none';
    }
}

function resetTableFilters() {
    document.getElementById('filterName').value = '';
    document.getElementById('filterEmail').value = '';
    document.getElementById('filterPhone').value = '';
    document.getElementById('filterPosition').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterDateMonth').value = '';
    document.getElementById('filterDateDay').value = '';
    document.getElementById('filterDateYear').value = '';
    
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

// Profile picture preview and management
function updateProfilePicturePreview(prefix, imageUrl, name) {
    const preview = document.getElementById(prefix + '_profile_picture_preview');
    const initials = document.getElementById(prefix + '_profile_picture_initials');
    const removeBtn = document.getElementById(prefix + '_remove_profile_picture_btn');
    const removeInput = document.getElementById(prefix + '_remove_profile_picture');
    
    if (preview && initials) {
        if (imageUrl) {
            preview.innerHTML = `<img src="${imageUrl}" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">`;
            if (removeBtn) removeBtn.style.display = 'inline-flex';
        } else {
            const firstLetter = name.charAt(0).toUpperCase();
            preview.innerHTML = `<span id="${prefix}_profile_picture_initials">${firstLetter}</span>`;
            if (removeBtn) removeBtn.style.display = 'none';
        }
        if (removeInput) removeInput.value = '0';
    }
}

function removeProfilePicture(prefix) {
    if (confirm('Are you sure you want to remove the profile picture?')) {
        const preview = document.getElementById(prefix + '_profile_picture_preview');
        const initials = document.getElementById(prefix + '_profile_picture_initials');
        const removeInput = document.getElementById(prefix + '_remove_profile_picture');
        const fileInput = document.getElementById(prefix + '_profile_picture_input');
        const removeBtn = document.getElementById(prefix + '_remove_profile_picture_btn');
        
        if (preview && initials) {
            const firstLetter = initials.textContent || 'S';
            preview.innerHTML = `<span id="${prefix}_profile_picture_initials">${firstLetter}</span>`;
        }
        if (removeInput) removeInput.value = '1';
        if (fileInput) fileInput.value = '';
        if (removeBtn) removeBtn.style.display = 'none';
    }
}

// Profile picture preview on file selection
document.addEventListener('DOMContentLoaded', function() {
    const profilePictureInput = document.getElementById('edit_profile_picture_input');
    if (profilePictureInput) {
        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    e.target.value = '';
                    return;
                }
                
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Invalid file type. Please upload JPG, PNG, GIF, or WEBP image.');
                    e.target.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('edit_profile_picture_preview');
                    const removeBtn = document.getElementById('edit_remove_profile_picture_btn');
                    if (preview) {
                        preview.innerHTML = `<img src="${e.target.result}" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">`;
                    }
                    if (removeBtn) removeBtn.style.display = 'inline-flex';
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
