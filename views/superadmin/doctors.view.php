<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">All Doctors</h1>
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
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Doctors</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Active Doctors</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['active'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Inactive Doctors</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['inactive'] ?? 0 ?></div>
    </div>
</div>

<!-- Active Doctors Cards Section -->
<?php if (!empty($active_doctors)): ?>
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">Active Doctors</h2>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem;">
        <?php foreach ($active_doctors as $doctor): ?>
            <?php
            $initials = strtoupper(substr($doctor['doc_first_name'] ?? 'D', 0, 1) . substr($doctor['doc_last_name'] ?? 'D', 0, 1));
            $doctorName = 'Dr. ' . htmlspecialchars(formatFullName($doctor['doc_first_name'] ?? '', $doctor['doc_middle_initial'] ?? null, $doctor['doc_last_name'] ?? ''));
            $specialization = htmlspecialchars($doctor['spec_name'] ?? 'General Practice');
            ?>
            <div style="background: #f9fafb; border-radius: 12px; padding: 1.5rem; text-align: center; border: 1px solid var(--border-light); transition: all 0.2s;" 
                 onmouseover="this.style.borderColor='var(--primary-blue)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.15)';" 
                 onmouseout="this.style.borderColor='var(--border-light)'; this.style.boxShadow='none';">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.75rem; margin: 0 auto 1rem; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <?php if (!empty($doctor['profile_picture_url'])): ?>
                        <img src="<?= htmlspecialchars($doctor['profile_picture_url']) ?>" alt="Doctor" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <?= $initials ?>
                    <?php endif; ?>
                </div>
                <div style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= $doctorName ?>
                </div>
                <div style="font-size: 0.875rem; color: var(--text-secondary);">
                    <?= $specialization ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Table Container -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header with Add Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">All Doctors</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddDoctorModal()" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i>
            <span>Add Doctor</span>
        </button>
    </div>

    <!-- Filter Bar (Hidden by default) -->
    <div id="tableFilterBar" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Doctors
            </h3>
            <button type="button" class="btn btn-sm" onclick="resetTableFilters()" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
                <i class="fas fa-redo"></i>
                <span>Reset Filters</span>
            </button>
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
                <input type="text" id="filterSpecialization" class="filter-input" placeholder="Search specialization..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
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
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Doctor Name
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'doc_email' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('doc_email')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Email
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'doc_phone' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('doc_phone')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Phone
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'doc_specialization_id' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('doc_specialization_id')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Specialization
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'doc_license_number' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('doc_license_number')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            License
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'doc_consultation_fee' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('doc_consultation_fee')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Fee
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'doc_status' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('doc_status')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Status
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
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
                    <?php foreach ($doctors as $doctor): ?>
                        <tr class="table-row" 
                            data-name="<?= htmlspecialchars(strtolower(($doctor['doc_first_name'] ?? '') . ' ' . ($doctor['doc_last_name'] ?? ''))) ?>"
                            data-email="<?= htmlspecialchars(strtolower($doctor['doc_email'] ?? '')) ?>"
                            data-specialization="<?= htmlspecialchars(strtolower($doctor['spec_name'] ?? '')) ?>"
                            data-status="<?= htmlspecialchars(strtolower($doctor['doc_status'] ?? '')) ?>"
                            style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; overflow: hidden; flex-shrink: 0;">
                                        <?php if (!empty($doctor['profile_picture_url'])): ?>
                                            <img src="<?= htmlspecialchars($doctor['profile_picture_url']) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?= strtoupper(substr($doctor['doc_first_name'] ?? 'D', 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars(formatFullName($doctor['doc_first_name'] ?? '', $doctor['doc_middle_initial'] ?? null, $doctor['doc_last_name'] ?? '')) ?></strong>
                                </div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($doctor['doc_email']) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($doctor['doc_phone'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($doctor['spec_name'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($doctor['doc_license_number'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);">₱<?= number_format($doctor['doc_consultation_fee'] ?? 0, 2) ?></td>
                            <td style="padding: 1rem;">
                                <?php
                                $status = $doctor['doc_status'] ?? 'active';
                                $statusColor = $status === 'active' ? '#10b981' : '#ef4444';
                                ?>
                                <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: <?= $statusColor ?>; color: white;">
                                    <?= htmlspecialchars(ucfirst($status)) ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $doctor['created_at'] ? date('d M Y', strtotime($doctor['created_at'])) : 'N/A' ?></td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button class="btn btn-sm edit-doctor-btn" 
                                            data-doctor="<?= base64_encode(json_encode($doctor)) ?>" 
                                            title="Edit"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm view-doctor-btn" 
                                            data-doctor="<?= base64_encode(json_encode($doctor)) ?>" 
                                            title="View"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return handleDelete(event, 'Are you sure you want to delete this doctor?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $doctor['doc_id'] ?>">
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
        <div id="paginationContainer" style="display: <?= isset($_GET['all_results']) && $_GET['all_results'] == '1' ? 'none' : 'flex' ?>; justify-content: space-between; align-items: center; padding: 1.5rem; border-top: 1px solid var(--border-light);">
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
        <!-- Filter Active Message -->
        <div id="filterActiveMessage" style="display: <?= isset($_GET['all_results']) && $_GET['all_results'] == '1' ? 'block' : 'none' ?>; padding: 1rem 1.5rem; border-top: 1px solid var(--border-light); background: var(--primary-blue-bg);">
            <div style="display: flex; align-items: center; gap: 0.75rem; color: var(--primary-blue-dark); font-size: 0.875rem;">
                <i class="fas fa-info-circle"></i>
                <span>All results loaded for filtering. Filters work across all <?= $total_items ?? 0 ?> records.</span>
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
        <div class="action-buttons" style="margin-top: 1.5rem;">
            <button type="button" onclick="closeViewModal()" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                <span>Close</span>
            </button>
        </div>
    </div>
</div>

<!-- Add Doctor Modal -->
<div id="addModal" class="modal">
    <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
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
            </div>
            
            <div class="form-group form-grid-full">
                <label>Qualification:</label>
                <textarea name="qualification" rows="2" class="form-control"></textarea>
            </div>
            
            <div class="form-group form-grid-full">
                <label>Bio:</label>
                <textarea name="bio" rows="3" class="form-control"></textarea>
            </div>
            
            <div class="form-group">
                <label>Status:</label>
                <select name="status" class="form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <div class="info-box" style="margin-top: 1.5rem;">
                <i class="fas fa-lock"></i>
                <p><strong>User Account (Login Credentials):</strong> Check the box below to create a user account for this doctor to login to the system.</p>
            </div>
            
            <div class="form-group" style="margin-top: 1rem;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="create_user" value="1" id="add_create_user_checkbox" onchange="toggleAddPasswordField()" style="margin-right: 10px; width: auto;">
                    <span>Create user account for login</span>
                </label>
            </div>
            
            <div class="form-group" id="add_password_field" style="display: none;">
                <label>Password: <span style="color: var(--status-error);">*</span></label>
                <input type="password" name="password" id="add_password_input" minlength="6" placeholder="Minimum 6 characters" class="form-control">
                <small style="display: block; margin-top: 0.5rem; color: var(--text-secondary);">The doctor will use their email and this password to login.</small>
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

<!-- Edit Doctor Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Doctor</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            
            <!-- Profile Picture Section -->
            <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f9fafb; border-radius: 8px;">
                <h3 style="margin-bottom: 1rem; color: var(--primary-blue); border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem;">Profile Picture</h3>
                <div style="display: flex; gap: 1.5rem; align-items: flex-start; flex-wrap: wrap;">
                    <div style="flex-shrink: 0;">
                        <div id="edit_profile_picture_preview" style="width: 120px; height: 120px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 3rem; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            <span id="edit_profile_picture_initials">D</span>
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
            </div>
            
            <div class="form-group form-grid-full">
                <label>Qualification:</label>
                <textarea name="qualification" id="edit_qualification" rows="2" class="form-control"></textarea>
            </div>
            
            <div class="form-group form-grid-full">
                <label>Bio:</label>
                <textarea name="bio" id="edit_bio" rows="3" class="form-control"></textarea>
            </div>
            
            <div class="form-group">
                <label>Status:</label>
                <select name="status" id="edit_status" class="form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
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
function openAddDoctorModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddDoctorModal() {
    document.getElementById('addModal').classList.remove('active');
    document.querySelector('#addModal form').reset();
    document.getElementById('add_password_field').style.display = 'none';
    document.getElementById('add_password_input').required = false;
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

function togglePasswordField() {
    const checkbox = document.getElementById('create_user_checkbox');
    const passwordField = document.getElementById('password_field');
    const passwordInput = document.getElementById('password_input');
    
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

function editDoctor(doctor) {
    document.getElementById('edit_id').value = doctor.doc_id;
    document.getElementById('edit_first_name').value = doctor.doc_first_name;
    document.getElementById('edit_middle_initial').value = doctor.doc_middle_initial || '';
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
    
    // Update profile picture preview
    updateProfilePicturePreview('edit', doctor.profile_picture_url || '', doctor.doc_first_name || 'D');
    
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

function viewDoctorDetails(doctor) {
    // Get profile picture or generate initials
    const profilePicture = doctor.profile_picture_url || '';
    const firstName = doctor.doc_first_name || 'D';
    const lastName = doctor.doc_last_name || '';
    const firstLetter = firstName.charAt(0).toUpperCase();
    const fullName = `${doctor.doc_first_name || ''}${doctor.doc_middle_initial ? ' ' + doctor.doc_middle_initial.toUpperCase() + '.' : ''} ${doctor.doc_last_name || ''}`.trim();
    
    const content = `
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-body">
                <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-light);">
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 3rem; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        ${profilePicture ? `<img src="${profilePicture}" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">` : firstLetter}
                    </div>
                    <div>
                        <h3 style="margin: 0 0 0.5rem 0; color: var(--text-primary); font-size: 1.5rem;">Dr. ${fullName || 'N/A'}</h3>
                        <p style="margin: 0; color: var(--text-secondary);">${doctor.spec_name || 'N/A'}</p>
                        <div style="margin-top: 0.5rem;">
                            <span class="status-badge ${(doctor.doc_status || 'active') === 'active' ? 'active' : 'inactive'}" style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">
                                ${doctor.doc_status || 'active'}
                            </span>
                        </div>
                    </div>
                </div>
                <h3 style="margin-bottom: 1rem; color: var(--text-primary);">Doctor Information</h3>
                <div class="form-grid">
                    <div>
                        <p style="margin: 0.5rem 0;"><strong>Doctor ID:</strong> ${doctor.doc_id || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>First Name:</strong> ${doctor.doc_first_name || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Last Name:</strong> ${doctor.doc_last_name || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Email:</strong> ${doctor.doc_email || 'N/A'}</p>
                    </div>
                    <div>
                        <p style="margin: 0.5rem 0;"><strong>Phone:</strong> ${doctor.doc_phone || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Specialization:</strong> ${doctor.spec_name || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>License Number:</strong> ${doctor.doc_license_number || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Consultation Fee:</strong> <strong style="color: var(--status-success);">₱${parseFloat(doctor.doc_consultation_fee || 0).toFixed(2)}</strong></p>
                    </div>
                </div>
                ${doctor.doc_address ? `<div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light);"><p style="margin: 0;"><strong>Address:</strong> ${doctor.doc_address}</p></div>` : ''}
            </div>
        </div>
    `;
    document.getElementById('viewContent').innerHTML = content;
    document.getElementById('viewModal').classList.add('active');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.remove('active');
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
    
    // Add event listeners for edit and view buttons
    document.querySelectorAll('.edit-doctor-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-doctor');
                const decodedJson = atob(encodedData);
                const doctorData = JSON.parse(decodedJson);
                editDoctor(doctorData);
            } catch (e) {
                console.error('Error parsing doctor data:', e);
                alert('Error loading doctor data. Please check the console for details.');
            }
        });
    });
    
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
});

function filterByCategory(category) {
    if (category === 'all') {
        window.location.href = '/superadmin/doctors';
    } else {
        window.location.href = '/superadmin/doctors?spec_id=' + category;
    }
}
</script>

<!-- Filter Sidebar -->
<div class="filter-sidebar" id="filterSidebar">
    <div class="filter-sidebar-header">
        <h3 class="filter-sidebar-title">Filters</h3>
        <button type="button" class="filter-sidebar-close" onclick="toggleFilterSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <!-- Specialization Filter -->
    <?php if (!empty($specializations)): ?>
    <div class="filter-section">
        <div class="filter-section-header" onclick="toggleFilterSection('specialization')">
            <h4 class="filter-section-title">Specialization</h4>
            <button type="button" class="filter-section-toggle" id="specializationToggle">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
        <div class="filter-section-content" id="specializationContent">
            <input type="text" class="filter-search-input" placeholder="Search Specialization" id="specializationSearch">
            <div class="filter-radio-group" id="specializationList">
                <?php foreach ($specializations as $spec): ?>
                    <div class="filter-radio-item">
                        <input type="radio" name="filter_specialization" id="spec_<?= $spec['spec_id'] ?>" value="<?= $spec['spec_id'] ?>">
                        <label for="spec_<?= $spec['spec_id'] ?>"><?= htmlspecialchars($spec['spec_name']) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Status Filter -->
    <div class="filter-section">
        <div class="filter-section-header" onclick="toggleFilterSection('status')">
            <h4 class="filter-section-title">Status</h4>
            <button type="button" class="filter-section-toggle" id="statusToggle">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
        <div class="filter-section-content" id="statusContent">
            <div class="filter-radio-group">
                <div class="filter-radio-item">
                    <input type="radio" name="filter_status" id="status_active" value="active">
                    <label for="status_active">Active</label>
                </div>
                <div class="filter-radio-item">
                    <input type="radio" name="filter_status" id="status_inactive" value="inactive">
                    <label for="status_inactive">Inactive</label>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter Actions -->
    <div class="filter-sidebar-actions">
        <button type="button" class="filter-clear-btn" onclick="clearAllFilters()">Clear all</button>
        <button type="button" class="filter-apply-btn" onclick="applyDoctorFilters()">Apply all filter</button>
    </div>
</div>

<script>
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

function clearAllFilters() {
    document.querySelectorAll('.filter-sidebar input[type="radio"]').forEach(radio => {
        radio.checked = false;
    });
    const specializationSearch = document.getElementById('specializationSearch');
    if (specializationSearch) {
        specializationSearch.value = '';
    }
}

function applyDoctorFilters() {
    const filters = {
        specialization: document.querySelector('input[name="filter_specialization"]:checked')?.value || '',
        status: document.querySelector('input[name="filter_status"]:checked')?.value || ''
    };
    
    const params = new URLSearchParams();
    if (filters.specialization) params.append('spec_id', filters.specialization);
    if (filters.status) params.append('status', filters.status);
    
    const url = '/superadmin/doctors' + (params.toString() ? '?' + params.toString() : '');
    window.location.href = url;
}

// Specialization search functionality
document.addEventListener('DOMContentLoaded', function() {
    const specializationSearch = document.getElementById('specializationSearch');
    if (specializationSearch) {
        specializationSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const specializationItems = document.querySelectorAll('#specializationList .filter-radio-item');
            specializationItems.forEach(item => {
                const label = item.querySelector('label');
                if (label) {
                    const text = label.textContent.toLowerCase();
                    item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
                }
            });
        });
    }
});

// Table Filtering Functions
function filterTable() {
    const nameFilter = document.getElementById('filterName')?.value.toLowerCase().trim() || '';
    const emailFilter = document.getElementById('filterEmail')?.value.toLowerCase().trim() || '';
    const specFilter = document.getElementById('filterSpecialization')?.value.toLowerCase().trim() || '';
    const statusFilter = document.getElementById('filterStatus')?.value || '';
    
    const rows = document.querySelectorAll('.table-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const name = row.getAttribute('data-name') || '';
        const email = row.getAttribute('data-email') || '';
        const spec = row.getAttribute('data-specialization') || '';
        const status = row.getAttribute('data-status') || '';
        
        const matchesName = !nameFilter || name.includes(nameFilter);
        const matchesEmail = !emailFilter || email.includes(emailFilter);
        const matchesSpec = !specFilter || spec.includes(specFilter);
        const matchesStatus = !statusFilter || status === statusFilter;
        
        if (matchesName && matchesEmail && matchesSpec && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    const hasActiveFilters = nameFilter || emailFilter || specFilter || statusFilter;
    const tableBody = document.getElementById('tableBody');
    const noResultsMsg = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0 && rows.length > 0 && hasActiveFilters) {
        if (!noResultsMsg) {
            const msg = document.createElement('tr');
            msg.id = 'noResultsMessage';
            const colCount = document.querySelector('thead tr')?.querySelectorAll('th').length || 8;
            msg.innerHTML = `<td colspan="${colCount}" style="padding: 3rem; text-align: center; color: var(--text-secondary);"><i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i><p style="margin: 0;">No doctors match the current filters.</p></td>`;
            tableBody.appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

function resetTableFilters() {
    const inputs = ['filterName', 'filterEmail', 'filterSpecialization', 'filterStatus'];
    inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    resetToPaginatedView();
}

function toggleTableFilters() {
    const filterBar = document.getElementById('tableFilterBar');
    const toggleBtn = document.getElementById('toggleFilterBtn');
    
    if (filterBar && toggleBtn) {
        if (filterBar.style.display === 'none') {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
            toggleBtn.innerHTML = '<i class="fas fa-filter"></i>';
            loadAllResults();
        } else {
            filterBar.style.display = 'none';
            toggleBtn.classList.remove('active');
            toggleBtn.innerHTML = '<i class="fas fa-filter"></i>';
            resetToPaginatedView();
        }
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
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
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

// Initialize filtering
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const isAllResultsMode = urlParams.get('all_results') === '1';
    
    if (isAllResultsMode) {
        const filterBar = document.getElementById('tableFilterBar');
        const toggleBtn = document.getElementById('toggleFilterBtn');
        if (filterBar && toggleBtn) {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
            toggleBtn.innerHTML = '<i class="fas fa-filter"></i>';
        }
    }
    
    const filterInputs = ['filterName', 'filterEmail', 'filterSpecialization', 'filterStatus'];
    filterInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', filterTable);
            input.addEventListener('change', filterTable);
        }
    });
    filterTable();
});

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
            const firstLetter = initials.textContent || 'D';
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

