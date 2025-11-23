<?php require_once __DIR__ . '/../partials/header.php'; ?>

<!-- Page Header with Profile Picture -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.5rem; overflow: hidden; flex-shrink: 0;">
            <?php if (!empty($profile_picture_url)): ?>
                <img src="<?= htmlspecialchars($profile_picture_url) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <?= strtoupper(substr($current_doctor['doc_first_name'] ?? 'D', 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div>
            <h1 class="page-title" style="margin-bottom: 0.5rem;">All Doctors</h1>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.95rem;">View and manage doctor profiles</p>
        </div>
    </div>
    <div style="display: flex; gap: 1rem; align-items: center;">
        <a href="/doctor/dashboard" class="btn btn-secondary" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Dashboard</span>
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

<!-- Summary Cards -->
<div class="stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Total Doctors Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-user-md" style="color: #8b5cf6;"></i>
                    <span>Total Doctors</span>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['total_doctors'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    All registered doctors
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(139, 92, 246, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-user-md" style="font-size: 1.5rem; color: #8b5cf6;"></i>
            </div>
        </div>
    </div>

    <!-- Active Doctors Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                    <span>Active Doctors</span>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['active_doctors'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Currently active
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #10b981;"></i>
            </div>
        </div>
    </div>

    <!-- Scheduled Today Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-calendar-day" style="color: var(--primary-blue);"></i>
                    <span>Scheduled Today</span>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['doctors_with_schedules_today'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    With available schedules
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-calendar-day" style="font-size: 1.5rem; color: var(--primary-blue);"></i>
            </div>
        </div>
    </div>

    <!-- With User Accounts Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-user-check" style="color: #f59e0b;"></i>
                    <span>With User Accounts</span>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['doctors_with_user_accounts'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Can login to system
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-user-check" style="font-size: 1.5rem; color: #f59e0b;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Doctors List -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">All Doctors</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddDoctorModal()" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i>
            <span>Add New Doctor</span>
        </button>
    </div>

    <!-- Filter Bar (Hidden by default) -->
    <div id="tableFilterBar" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Doctors
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
                    <i class="fas fa-stethoscope" style="margin-right: 0.25rem;"></i>Specialization
                </label>
                <select id="filterSpecialization" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem; background: white; cursor: pointer;">
                    <option value="">All Specializations</option>
                    <?php if (!empty($specializations)): ?>
                        <?php foreach ($specializations as $spec): ?>
                            <option value="<?= htmlspecialchars($spec['spec_id']) ?>"><?= htmlspecialchars($spec['spec_name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-check-circle" style="margin-right: 0.25rem;"></i>Status
                </label>
                <select id="filterStatus" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem; background: white; cursor: pointer;">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
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
        </div>
    </div>

    <?php if (empty($doctors)): ?>
        <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
            <i class="fas fa-user-md" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p style="margin: 0;">No doctors found.</p>
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
                        <th class="sortable <?= ($current_sort === 'doc_first_name' || $current_sort === 'doc_last_name') ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('doc_first_name')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            Doctor Name
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'doc_email' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('doc_email')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            Email
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Phone
                        </th>
                        <th class="sortable <?= $current_sort === 'doc_specialization_id' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('doc_specialization_id')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            Specialization
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            License
                        </th>
                        <th class="sortable <?= $current_sort === 'doc_consultation_fee' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('doc_consultation_fee')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            Fee
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'doc_status' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('doc_status')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem; cursor: pointer;">
                            Status
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($doctors as $doctor): ?>
                        <tr class="table-row" 
                            data-name="<?= htmlspecialchars(strtolower(($doctor['doc_first_name'] ?? '') . ' ' . ($doctor['doc_last_name'] ?? ''))) ?>"
                            data-email="<?= htmlspecialchars(strtolower($doctor['doc_email'] ?? '')) ?>"
                            data-specialization="<?= htmlspecialchars($doctor['spec_name'] ?? '') ?>"
                            data-status="<?= htmlspecialchars(strtolower($doctor['doc_status'] ?? '')) ?>"
                            style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; overflow: hidden; flex-shrink: 0;">
                                        <?php if (!empty($doctor['profile_picture_url'])): ?>
                                            <img src="<?= htmlspecialchars($doctor['profile_picture_url']) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?= strtoupper(substr($doctor['doc_first_name'] ?? 'D', 0, 1) . substr($doctor['doc_last_name'] ?? 'D', 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars(($doctor['doc_first_name'] ?? '') . ' ' . ($doctor['doc_last_name'] ?? '')) ?></strong>
                                </div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($doctor['doc_email']) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($doctor['doc_phone'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($doctor['spec_name'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($doctor['doc_license_number'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary); font-weight: 600;">₱<?= number_format($doctor['doc_consultation_fee'] ?? 0, 2) ?></td>
                            <td style="padding: 1rem;">
                                <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: <?= ($doctor['doc_status'] ?? 'active') === 'active' ? '#10b98120; color: #10b981;' : '#ef444420; color: #ef4444;' ?>">
                                    <?= htmlspecialchars(ucfirst($doctor['doc_status'] ?? 'active')) ?>
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button onclick='editDoctor(<?= json_encode($doctor) ?>)' class="btn btn-sm" title="Edit"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm view-doctor-btn" 
                                            data-doctor="<?= base64_encode(json_encode($doctor)) ?>" 
                                            title="View"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">
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

<!-- View Doctor Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2 class="modal-title">Doctor Details</h2>
            <button type="button" class="modal-close" onclick="closeViewModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="viewContent"></div>
        <div class="action-buttons" style="margin-top: 1.5rem; padding: 0 1.5rem 1.5rem;">
            <button type="button" onclick="closeViewModal()" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                <span>Close</span>
            </button>
        </div>
    </div>
</div>

<!-- Add Doctor Modal -->
<div id="addModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2 class="modal-title">Add New Doctor</h2>
            <button type="button" class="modal-close" onclick="closeAddDoctorModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name: <span style="color: var(--status-error);">*</span></label>
                    <input type="text" name="first_name" required class="form-control">
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
                    <label>Specialization:</label>
                    <select name="specialization_id" class="form-control">
                        <option value="">Select Specialization</option>
                        <?php foreach ($specializations as $spec): ?>
                            <option value="<?= $spec['spec_id'] ?>"><?= htmlspecialchars($spec['spec_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>License Number:</label>
                    <input type="text" name="license_number" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Experience (Years):</label>
                    <input type="number" name="experience_years" min="0" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Consultation Fee:</label>
                    <input type="number" name="consultation_fee" step="0.01" min="0" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group form-grid-full">
                <label>Qualification:</label>
                <textarea name="qualification" rows="2" class="form-control"></textarea>
            </div>
            
            <div class="form-group form-grid-full">
                <label>Bio:</label>
                <textarea name="bio" rows="3" class="form-control"></textarea>
            </div>
            
            <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid var(--border-light);">
            
            <div class="info-box" style="margin-bottom: 1.5rem;">
                <i class="fas fa-lock"></i>
                <p><strong>User Account (Login Credentials):</strong> Check the box below to create a user account for this doctor to login to the system.</p>
            </div>
            
            <div class="form-group form-grid-full">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="create_user" value="1" id="add_create_user_checkbox" onchange="toggleAddPasswordField()" style="margin-right: 0.5rem; width: auto;">
                    <span>Create user account for login</span>
                </label>
            </div>
            
            <div class="form-group form-grid-full" id="add_password_field" style="display: none;">
                <label>Password: <span style="color: var(--status-error);">*</span></label>
                <input type="password" name="password" id="add_password_input" minlength="6" placeholder="Minimum 6 characters" class="form-control">
                <small style="display: block; margin-top: 0.5rem; color: var(--text-secondary); font-size: 0.875rem;">The doctor will use their email and this password to login.</small>
            </div>
            
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    <span>Add Doctor</span>
                </button>
                <button type="button" onclick="closeAddDoctorModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2 class="modal-title">Edit Doctor</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name: <span style="color: var(--status-error);">*</span></label>
                    <input type="text" name="first_name" id="edit_first_name" required class="form-control">
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
                    <label>Specialization:</label>
                    <select name="specialization_id" id="edit_specialization_id" class="form-control">
                        <option value="">Select Specialization</option>
                        <?php foreach ($specializations as $spec): ?>
                            <option value="<?= $spec['spec_id'] ?>"><?= htmlspecialchars($spec['spec_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>License Number:</label>
                    <input type="text" name="license_number" id="edit_license_number" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Experience (Years):</label>
                    <input type="number" name="experience_years" id="edit_experience_years" min="0" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Consultation Fee:</label>
                    <input type="number" name="consultation_fee" id="edit_consultation_fee" step="0.01" min="0" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" id="edit_status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group form-grid-full">
                <label>Qualification:</label>
                <textarea name="qualification" id="edit_qualification" rows="2" class="form-control"></textarea>
            </div>
            
            <div class="form-group form-grid-full">
                <label>Bio:</label>
                <textarea name="bio" id="edit_bio" rows="3" class="form-control"></textarea>
            </div>
            
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    <span>Update Doctor</span>
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

function openAddDoctorModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddDoctorModal() {
    const modal = document.getElementById('addModal');
    modal.classList.remove('active');
    modal.querySelector('form').reset();
    document.getElementById('add_password_field').style.display = 'none';
    document.getElementById('add_password_input').required = false;
    document.getElementById('add_create_user_checkbox').checked = false;
}

function toggleAddPasswordField() {
    const checkbox = document.getElementById('add_create_user_checkbox');
    const passwordField = document.getElementById('add_password_field');
    const passwordInput = document.getElementById('add_password_input');
    
    if (checkbox.checked) {
        passwordField.style.display = 'block';
        passwordInput.required = true;
    } else {
        passwordField.style.display = 'none';
        passwordInput.required = false;
        passwordInput.value = '';
    }
}

function editDoctor(doctor) {
    document.getElementById('edit_id').value = doctor.doc_id;
    document.getElementById('edit_first_name').value = doctor.doc_first_name;
    document.getElementById('edit_last_name').value = doctor.doc_last_name;
    document.getElementById('edit_email').value = doctor.doc_email;
    document.getElementById('edit_phone').value = doctor.doc_phone ? formatPhoneNumber(doctor.doc_phone) : '';
    document.getElementById('edit_specialization_id').value = doctor.doc_specialization_id || '';
    document.getElementById('edit_license_number').value = doctor.doc_license_number || '';
    document.getElementById('edit_experience_years').value = doctor.doc_experience_years || '';
    document.getElementById('edit_consultation_fee').value = doctor.doc_consultation_fee || '';
    document.getElementById('edit_qualification').value = doctor.doc_qualification || '';
    document.getElementById('edit_bio').value = doctor.doc_bio || '';
    document.getElementById('edit_status').value = doctor.doc_status || 'active';
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

function viewDoctorDetails(doctor) {
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
    const profilePicture = doctor.profile_picture_url || '';
    const firstName = doctor.doc_first_name || 'D';
    const lastName = doctor.doc_last_name || '';
    const firstLetter = firstName.charAt(0).toUpperCase();
    const lastLetter = lastName.charAt(0).toUpperCase();
    const fullName = `${doctor.doc_first_name || ''} ${doctor.doc_last_name || ''}`.trim();
    
    const content = `
        <div style="padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-light);">
                <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 3rem; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    ${profilePicture ? `<img src="${profilePicture}" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">` : firstLetter + lastLetter}
                </div>
                <div>
                    <h3 style="margin: 0 0 0.5rem 0; color: var(--text-primary); font-size: 1.5rem;">Dr. ${fullName || 'N/A'}</h3>
                    <p style="margin: 0; color: var(--text-secondary); font-size: 1rem;">${doctor.spec_name || 'N/A'}</p>
                    <div style="margin-top: 0.5rem;">
                        <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 500; background: ${(doctor.doc_status || 'active') === 'active' ? '#10b98120; color: #10b981;' : '#ef444420; color: #ef4444;'};">
                            ${(doctor.doc_status || 'active').charAt(0).toUpperCase() + (doctor.doc_status || 'active').slice(1)}
                        </span>
                    </div>
                </div>
            </div>
            <h3 style="margin-bottom: 1rem; color: var(--text-primary);">Doctor Information</h3>
            <div class="form-grid">
                <div>
                    <p style="margin: 0.5rem 0;"><strong>First Name:</strong> ${doctor.doc_first_name || 'N/A'}</p>
                    <p style="margin: 0.5rem 0;"><strong>Last Name:</strong> ${doctor.doc_last_name || 'N/A'}</p>
                    <p style="margin: 0.5rem 0;"><strong>Email:</strong> ${doctor.doc_email || 'N/A'}</p>
                    <p style="margin: 0.5rem 0;"><strong>Phone:</strong> ${doctor.doc_phone || 'N/A'}</p>
                </div>
                <div>
                    <p style="margin: 0.5rem 0;"><strong>Specialization:</strong> ${doctor.spec_name || 'N/A'}</p>
                    <p style="margin: 0.5rem 0;"><strong>License Number:</strong> ${doctor.doc_license_number || 'N/A'}</p>
                    <p style="margin: 0.5rem 0;"><strong>Experience:</strong> ${doctor.doc_experience_years || 'N/A'} ${doctor.doc_experience_years ? 'years' : ''}</p>
                    <p style="margin: 0.5rem 0;"><strong>Consultation Fee:</strong> <strong style="color: var(--status-success);">₱${parseFloat(doctor.doc_consultation_fee || 0).toFixed(2)}</strong></p>
                </div>
            </div>
            ${doctor.doc_qualification ? `<div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light);"><p style="margin: 0;"><strong>Qualification:</strong> ${doctor.doc_qualification}</p></div>` : ''}
            ${doctor.doc_bio ? `<div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light);"><p style="margin: 0;"><strong>Bio:</strong> ${doctor.doc_bio}</p></div>` : ''}
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light); display: flex; gap: 2rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-plus-circle" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);"><strong>Created:</strong> ${formatDate(doctor.created_at)}</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-edit" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);"><strong>Updated:</strong> ${formatDate(doctor.updated_at)}</p>
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

// URL-based Filtering Functions
function applyTableFilters() {
    const params = new URLSearchParams();
    
    const filterName = document.getElementById('filterName')?.value.trim() || '';
    const filterEmail = document.getElementById('filterEmail')?.value.trim() || '';
    const filterSpecialization = document.getElementById('filterSpecialization')?.value || '';
    const filterStatus = document.getElementById('filterStatus')?.value || '';
    const filterDateFrom = document.getElementById('filterDateFrom')?.value || '';
    const filterDateTo = document.getElementById('filterDateTo')?.value || '';
    
    // Preserve sort parameters
    const urlParams = new URLSearchParams(window.location.search);
    const sort = urlParams.get('sort');
    const order = urlParams.get('order');
    
    if (filterName) params.set('filter_name', filterName);
    if (filterEmail) params.set('filter_email', filterEmail);
    if (filterSpecialization) params.set('filter_specialization', filterSpecialization);
    if (filterStatus) params.set('filter_status', filterStatus);
    if (filterDateFrom) params.set('filter_date_from', filterDateFrom);
    if (filterDateTo) params.set('filter_date_to', filterDateTo);
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
    
    if (urlParams.get('filter_name') && document.getElementById('filterName')) {
        document.getElementById('filterName').value = urlParams.get('filter_name');
    }
    if (urlParams.get('filter_email') && document.getElementById('filterEmail')) {
        document.getElementById('filterEmail').value = urlParams.get('filter_email');
    }
    if (urlParams.get('filter_specialization') && document.getElementById('filterSpecialization')) {
        document.getElementById('filterSpecialization').value = urlParams.get('filter_specialization');
    }
    if (urlParams.get('filter_status') && document.getElementById('filterStatus')) {
        document.getElementById('filterStatus').value = urlParams.get('filter_status');
    }
    if (urlParams.get('filter_date_from') && document.getElementById('filterDateFrom')) {
        document.getElementById('filterDateFrom').value = urlParams.get('filter_date_from');
    }
    if (urlParams.get('filter_date_to') && document.getElementById('filterDateTo')) {
        document.getElementById('filterDateTo').value = urlParams.get('filter_date_to');
    }
    
    // Show filter bar if any filters are active
    if (urlParams.get('filter_name') || urlParams.get('filter_email') || urlParams.get('filter_specialization') || urlParams.get('filter_status') || urlParams.get('filter_date_from') || urlParams.get('filter_date_to')) {
        const filterBar = document.getElementById('tableFilterBar');
        const toggleBtn = document.getElementById('toggleFilterBtn');
        if (filterBar) {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
            toggleBtn.style.background = 'var(--primary-blue)';
            toggleBtn.style.color = 'white';
        }
    }
    
    formatPhoneInput('edit_phone');
    formatPhoneInput('add_phone');
    
    // Add event listeners for view buttons
    document.querySelectorAll('.view-doctor-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-doctor');
                const decodedJson = atob(encodedData);
                const doctorData = JSON.parse(decodedJson);
                viewDoctorDetails(doctorData);
            } catch (e) {
                console.error('Error parsing doctor data:', e);
                alert('Error loading doctor data. Please check the console for details.');
            }
        });
    });
    
    // Close modals on outside click and Escape key
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                if (this.id === 'addModal') {
                    closeAddDoctorModal();
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
                    closeAddDoctorModal();
                } else {
                    modal.classList.remove('active');
                }
            });
        }
    });
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
