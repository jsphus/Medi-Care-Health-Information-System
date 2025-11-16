<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">All Patients</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span><?= htmlspecialchars($success) ?></span>
    </div>
<?php endif; ?>

<!-- Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #8b5cf6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Patients</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">New This Month</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total_this_month'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #2ecc71;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Active</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['active'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #9b59b6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Inactive</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['inactive'] ?? 0 ?></div>
    </div>
</div>

<!-- Table Container -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header with Add Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">All Patients</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddPatientModal()" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i>
            <span>Add Patient</span>
        </button>
    </div>

    <!-- Filter Bar (Hidden by default) -->
    <div id="tableFilterBar" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Patients
            </h3>
            <button type="button" class="btn btn-sm" onclick="resetTableFilters()" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
                <i class="fas fa-redo"></i>
                <span>Reset Filters</span>
            </button>
        </div>
        <div class="filter-controls-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-user" style="margin-right: 0.25rem;"></i>Patient Name
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
                    <i class="fas fa-venus-mars" style="margin-right: 0.25rem;"></i>Gender
                </label>
                <select id="filterGender" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem; background: white; cursor: pointer;">
                    <option value="">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>
    </div>

    <?php if (empty($patients)): ?>
        <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
            <i class="fas fa-user-injured" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p style="margin: 0;">No patients found.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid var(--border-light);">
                        <?php
                        $current_sort = $_GET['sort'] ?? 'created_at';
                        $current_order = $_GET['order'] ?? 'DESC';
                        $get_params = $_GET;
                        ?>
                        <th class="sortable <?= ($current_sort === 'pat_first_name' || $current_sort === 'pat_last_name') ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('pat_first_name')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Patient Name
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'pat_email' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('pat_email')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Email
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'pat_phone' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('pat_phone')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Phone
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'pat_gender' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('pat_gender')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Gender
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'pat_date_of_birth' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('pat_date_of_birth')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Date of Birth
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
                    <?php foreach ($patients as $patient): ?>
                        <tr class="table-row" 
                            data-name="<?= htmlspecialchars(strtolower(($patient['pat_first_name'] ?? '') . ' ' . ($patient['pat_last_name'] ?? ''))) ?>"
                            data-email="<?= htmlspecialchars(strtolower($patient['pat_email'] ?? '')) ?>"
                            data-phone="<?= htmlspecialchars($patient['pat_phone'] ?? '') ?>"
                            data-gender="<?= htmlspecialchars(strtolower($patient['pat_gender'] ?? '')) ?>"
                            style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; overflow: hidden; flex-shrink: 0;">
                                        <?php if (!empty($patient['profile_picture_url'])): ?>
                                            <img src="<?= htmlspecialchars($patient['profile_picture_url']) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?= strtoupper(substr($patient['pat_first_name'] ?? 'P', 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars($patient['pat_first_name'] . ' ' . $patient['pat_last_name']) ?></strong>
                                </div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($patient['pat_email']) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($patient['pat_phone'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $patient['pat_gender'] ? htmlspecialchars(ucfirst($patient['pat_gender'])) : 'N/A' ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $patient['pat_date_of_birth'] ? date('d M Y', strtotime($patient['pat_date_of_birth'])) : 'N/A' ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $patient['created_at'] ? date('d M Y', strtotime($patient['created_at'])) : 'N/A' ?></td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button class="btn btn-sm edit-patient-btn" 
                                            data-patient="<?= base64_encode(json_encode($patient)) ?>" 
                                            title="Edit"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm view-patient-btn" 
                                            data-patient="<?= base64_encode(json_encode($patient)) ?>" 
                                            title="View"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return handleDelete(event, 'Are you sure you want to delete this patient?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $patient['pat_id'] ?>">
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

<!-- View Patient Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2 class="modal-title">Patient Details</h2>
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

<!-- Add Patient Modal -->
<div id="addModal" class="modal">
    <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h2 class="modal-title">Add New Patient</h2>
            <button type="button" class="modal-close" onclick="closeAddPatientModal()">
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
                    <label>Date of Birth:</label>
                    <input type="date" name="date_of_birth" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Gender:</label>
                    <select name="gender" class="form-control">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group form-grid-full">
                <label>Address:</label>
                <textarea name="address" rows="3" class="form-control"></textarea>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Emergency Contact:</label>
                    <input type="text" name="emergency_contact" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Emergency Phone:</label>
                    <input type="text" name="emergency_phone" id="add_emergency_phone" class="form-control">
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group form-grid-full">
                    <label>Medical History:</label>
                    <textarea name="medical_history" rows="3" class="form-control"></textarea>
                </div>
                
                <div class="form-group form-grid-full">
                    <label>Allergies:</label>
                    <textarea name="allergies" rows="2" class="form-control"></textarea>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Insurance Provider:</label>
                    <input type="text" name="insurance_provider" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Insurance Number:</label>
                    <input type="text" name="insurance_number" class="form-control">
                </div>
            </div>
            
            <div class="info-box" style="margin-top: 1.5rem;">
                <i class="fas fa-lock"></i>
                <p><strong>User Account (Login Credentials):</strong> Check the box below to create a user account for this patient to login to the system.</p>
            </div>
            
            <div class="form-group" style="margin-top: 1rem;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="create_user" value="1" id="patient_create_user_checkbox" onchange="togglePatientPasswordField()" style="margin-right: 10px; width: auto;">
                    <span>Create user account for login</span>
                </label>
            </div>
            
            <div class="form-group" id="patient_password_field" style="display: none;">
                <label>Password: <span style="color: var(--status-error);">*</span></label>
                <input type="password" name="password" id="patient_password_input" minlength="6" placeholder="Minimum 6 characters" class="form-control">
                <small style="display: block; margin-top: 0.5rem; color: var(--text-secondary);">The patient will use their email and this password to login.</small>
            </div>
            
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    <span>Add Patient</span>
                </button>
                <button type="button" onclick="closeAddPatientModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Patient Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Patient</h2>
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
                            <span id="edit_profile_picture_initials">P</span>
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
                    <label>Date of Birth:</label>
                    <input type="date" name="date_of_birth" id="edit_date_of_birth" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Gender:</label>
                    <select name="gender" id="edit_gender" class="form-control">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group form-grid-full">
                <label>Address:</label>
                <textarea name="address" id="edit_address" rows="3" class="form-control"></textarea>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Emergency Contact:</label>
                    <input type="text" name="emergency_contact" id="edit_emergency_contact" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Emergency Phone:</label>
                    <input type="text" name="emergency_phone" id="edit_emergency_phone" class="form-control">
                </div>
            </div>
            
            <div class="form-group form-grid-full">
                <label>Medical History:</label>
                <textarea name="medical_history" id="edit_medical_history" rows="3" class="form-control"></textarea>
            </div>
            
            <div class="form-group form-grid-full">
                <label>Allergies:</label>
                <textarea name="allergies" id="edit_allergies" rows="2" class="form-control"></textarea>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Insurance Provider:</label>
                    <input type="text" name="insurance_provider" id="edit_insurance_provider" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Insurance Number:</label>
                    <input type="text" name="insurance_number" id="edit_insurance_number" class="form-control">
                </div>
            </div>
            
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    <span>Update Patient</span>
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
function openAddPatientModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddPatientModal() {
    document.getElementById('addModal').classList.remove('active');
    // Reset form
    document.querySelector('#addModal form').reset();
    document.getElementById('patient_password_field').style.display = 'none';
    document.getElementById('patient_password_input').required = false;
}

function togglePatientPasswordField() {
    const checkbox = document.getElementById('patient_create_user_checkbox');
    const passwordField = document.getElementById('patient_password_field');
    const passwordInput = document.getElementById('patient_password_input');
    
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

function editPatient(patient) {
    document.getElementById('edit_id').value = patient.pat_id;
    document.getElementById('edit_first_name').value = patient.pat_first_name;
    document.getElementById('edit_last_name').value = patient.pat_last_name;
    document.getElementById('edit_email').value = patient.pat_email;
    document.getElementById('edit_phone').value = patient.pat_phone ? formatPhoneNumber(patient.pat_phone) : '';
    document.getElementById('edit_date_of_birth').value = patient.pat_date_of_birth || '';
    document.getElementById('edit_gender').value = patient.pat_gender || '';
    document.getElementById('edit_address').value = patient.pat_address || '';
    document.getElementById('edit_emergency_contact').value = patient.pat_emergency_contact || '';
    document.getElementById('edit_emergency_phone').value = patient.pat_emergency_phone ? formatPhoneNumber(patient.pat_emergency_phone) : '';
    document.getElementById('edit_medical_history').value = patient.pat_medical_history || '';
    document.getElementById('edit_allergies').value = patient.pat_allergies || '';
    document.getElementById('edit_insurance_provider').value = patient.pat_insurance_provider || '';
    document.getElementById('edit_insurance_number').value = patient.pat_insurance_number || '';
    
    // Update profile picture preview
    updateProfilePicturePreview('edit', patient.profile_picture_url || '', patient.pat_first_name || 'P');
    
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

function viewPatientDetails(patient) {
    // Get profile picture or generate initials
    const profilePicture = patient.profile_picture_url || '';
    const firstName = patient.pat_first_name || 'P';
    const firstLetter = firstName.charAt(0).toUpperCase();
    const fullName = `${patient.pat_first_name || ''} ${patient.pat_last_name || ''}`.trim();
    
    const content = `
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-body">
                <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-light);">
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 3rem; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        ${profilePicture ? `<img src="${profilePicture}" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">` : firstLetter}
                    </div>
                    <div>
                        <h3 style="margin: 0 0 0.5rem 0; color: var(--text-primary); font-size: 1.5rem;">${fullName || 'N/A'}</h3>
                        <p style="margin: 0; color: var(--text-secondary);">${patient.pat_email || 'N/A'}</p>
                        ${patient.pat_gender ? `<p style="margin: 0.25rem 0 0 0; color: var(--text-secondary);">${patient.pat_gender.charAt(0).toUpperCase() + patient.pat_gender.slice(1)}</p>` : ''}
                    </div>
                </div>
                <h3 style="margin-bottom: 1rem; color: var(--text-primary);">Patient Information</h3>
                <div class="form-grid">
                    <div>
                        <p style="margin: 0.5rem 0;"><strong>Patient ID:</strong> ${patient.pat_id || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>First Name:</strong> ${patient.pat_first_name || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Last Name:</strong> ${patient.pat_last_name || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Email:</strong> ${patient.pat_email || 'N/A'}</p>
                    </div>
                    <div>
                        <p style="margin: 0.5rem 0;"><strong>Phone:</strong> ${patient.pat_phone || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Gender:</strong> ${patient.pat_gender ? patient.pat_gender.charAt(0).toUpperCase() + patient.pat_gender.slice(1) : 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Date of Birth:</strong> ${patient.pat_date_of_birth || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Blood Type:</strong> ${patient.pat_blood_type || 'N/A'}</p>
                    </div>
                </div>
                ${patient.pat_address ? `<div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light);"><p style="margin: 0;"><strong>Address:</strong> ${patient.pat_address}</p></div>` : ''}
                ${patient.pat_medical_history ? `<div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light);"><p style="margin: 0;"><strong>Medical History:</strong> ${patient.pat_medical_history}</p></div>` : ''}
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
            // Filter patients by category
            filterByCategory(category);
        });
    });
    
    // Add event listeners for edit and view buttons
    document.querySelectorAll('.edit-patient-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-patient');
                const decodedJson = atob(encodedData);
                const patientData = JSON.parse(decodedJson);
                editPatient(patientData);
            } catch (e) {
                console.error('Error parsing patient data:', e);
                alert('Error loading patient data. Please check the console for details.');
            }
        });
    });
    
    document.querySelectorAll('.view-patient-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-patient');
                const decodedJson = atob(encodedData);
                const patientData = JSON.parse(decodedJson);
                viewPatientDetails(patientData);
            } catch (e) {
                console.error('Error parsing patient data:', e);
                alert('Error loading patient data. Please check the console for details.');
            }
        });
    });
    
    // Initialize phone number formatting
    formatPhoneInput('edit_phone');
    formatPhoneInput('edit_emergency_phone');
    formatPhoneInput('add_phone');
    formatPhoneInput('add_emergency_phone');
    
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
    // Implement category filtering
    console.log('Filtering by category:', category);
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
    
    <!-- Gender Filter -->
    <div class="filter-section">
        <div class="filter-section-header" onclick="toggleFilterSection('gender')">
            <h4 class="filter-section-title">Gender</h4>
            <button type="button" class="filter-section-toggle" id="genderToggle">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
        <div class="filter-section-content" id="genderContent">
            <div class="filter-radio-group">
                <?php if (!empty($filter_genders)): ?>
                    <?php 
                    $seen_genders = [];
                    foreach ($filter_genders as $gender): 
                        $gender_normalized = strtolower(trim($gender));
                        // Skip if we've already seen this gender (case-insensitive)
                        if (in_array($gender_normalized, $seen_genders)) continue;
                        $seen_genders[] = $gender_normalized;
                    ?>
                        <div class="filter-radio-item">
                            <input type="radio" name="filter_gender" id="gender_<?= htmlspecialchars($gender_normalized) ?>" value="<?= htmlspecialchars($gender_normalized) ?>">
                            <label for="gender_<?= htmlspecialchars($gender_normalized) ?>"><?= htmlspecialchars(ucfirst($gender_normalized)) ?></label>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="filter-radio-item">
                        <input type="radio" name="filter_gender" id="gender_male" value="male">
                        <label for="gender_male">Male</label>
                    </div>
                    <div class="filter-radio-item">
                        <input type="radio" name="filter_gender" id="gender_female" value="female">
                        <label for="gender_female">Female</label>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Insurance Provider Filter -->
    <?php if (!empty($filter_insurance_providers)): ?>
    <div class="filter-section">
        <div class="filter-section-header" onclick="toggleFilterSection('insurance')">
            <h4 class="filter-section-title">Insurance Provider</h4>
            <button type="button" class="filter-section-toggle" id="insuranceToggle">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
        <div class="filter-section-content" id="insuranceContent">
            <input type="text" class="filter-search-input" placeholder="Search Insurance Provider" id="insuranceSearch">
            <div class="filter-radio-group" id="insuranceList">
                <?php foreach ($filter_insurance_providers as $provider): ?>
                    <div class="filter-radio-item">
                        <input type="radio" name="filter_insurance" id="insurance_<?= htmlspecialchars(strtolower(str_replace(' ', '_', $provider))) ?>" value="<?= htmlspecialchars($provider) ?>">
                        <label for="insurance_<?= htmlspecialchars(strtolower(str_replace(' ', '_', $provider))) ?>"><?= htmlspecialchars($provider) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Filter Actions -->
    <div class="filter-sidebar-actions">
        <button type="button" class="filter-clear-btn" onclick="clearAllFilters()">Clear all</button>
        <button type="button" class="filter-apply-btn" onclick="applyPatientFilters()">Apply all filter</button>
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
    const insuranceSearch = document.getElementById('insuranceSearch');
    if (insuranceSearch) {
        insuranceSearch.value = '';
    }
}

function applyPatientFilters() {
    const filters = {
        gender: document.querySelector('input[name="filter_gender"]:checked')?.value || '',
        insurance: document.querySelector('input[name="filter_insurance"]:checked')?.value || ''
    };
    
    // Build URL with filters
    const params = new URLSearchParams();
    if (filters.gender) params.append('gender', filters.gender);
    if (filters.insurance) params.append('insurance', filters.insurance);
    
    const url = '/superadmin/patients' + (params.toString() ? '?' + params.toString() : '');
    window.location.href = url;
}

// Insurance search functionality
document.addEventListener('DOMContentLoaded', function() {
    const insuranceSearch = document.getElementById('insuranceSearch');
    if (insuranceSearch) {
        insuranceSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const insuranceItems = document.querySelectorAll('#insuranceList .filter-radio-item');
            insuranceItems.forEach(item => {
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
    const phoneFilter = document.getElementById('filterPhone')?.value.toLowerCase().trim() || '';
    const genderFilter = document.getElementById('filterGender')?.value || '';
    
    const rows = document.querySelectorAll('.table-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const name = row.getAttribute('data-name') || '';
        const email = row.getAttribute('data-email') || '';
        const phone = row.getAttribute('data-phone') || '';
        const gender = row.getAttribute('data-gender') || '';
        
        const matchesName = !nameFilter || name.includes(nameFilter);
        const matchesEmail = !emailFilter || email.includes(emailFilter);
        const matchesPhone = !phoneFilter || phone.includes(phoneFilter);
        const matchesGender = !genderFilter || gender === genderFilter;
        
        if (matchesName && matchesEmail && matchesPhone && matchesGender) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    const hasActiveFilters = nameFilter || emailFilter || phoneFilter || genderFilter;
    const tableBody = document.getElementById('tableBody');
    const noResultsMsg = document.getElementById('noResultsMessage');
    const paginationContainer = document.getElementById('paginationContainer');
    const filterActiveMessage = document.getElementById('filterActiveMessage');
    
    if (paginationContainer) {
        if (hasActiveFilters) {
            paginationContainer.style.display = 'none';
            if (filterActiveMessage) filterActiveMessage.style.display = 'block';
        } else {
            paginationContainer.style.display = 'flex';
            if (filterActiveMessage) filterActiveMessage.style.display = 'none';
        }
    }
    
    if (visibleCount === 0 && rows.length > 0 && hasActiveFilters) {
        if (!noResultsMsg) {
            const msg = document.createElement('tr');
            msg.id = 'noResultsMessage';
            const colCount = document.querySelector('thead tr')?.querySelectorAll('th').length || 6;
            msg.innerHTML = `<td colspan="${colCount}" style="padding: 3rem; text-align: center; color: var(--text-secondary);"><i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i><p style="margin: 0;">No patients match the current filters on this page.</p><p style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--text-light);">Try clearing filters or navigate to another page.</p></td>`;
            tableBody.appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

function resetTableFilters() {
    const inputs = ['filterName', 'filterEmail', 'filterPhone', 'filterGender'];
    inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    // Reset to paginated view when filters are cleared
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
            // Load all results when filter is opened
            loadAllResults();
        } else {
            filterBar.style.display = 'none';
            toggleBtn.classList.remove('active');
            toggleBtn.innerHTML = '<i class="fas fa-filter"></i>';
            // Reset to paginated view when filter is closed
            resetToPaginatedView();
        }
    }
}

function loadAllResults() {
    const url = new URL(window.location.href);
    url.searchParams.set('all_results', '1');
    url.searchParams.delete('page'); // Reset to page 1
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
    // Check if we're in "all results" mode (for filtering)
    const urlParams = new URLSearchParams(window.location.search);
    const isAllResultsMode = urlParams.get('all_results') === '1';
    
    // If filter bar should be open (all_results mode), open it
    if (isAllResultsMode) {
        const filterBar = document.getElementById('tableFilterBar');
        const toggleBtn = document.getElementById('toggleFilterBtn');
        if (filterBar && toggleBtn) {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
            toggleBtn.innerHTML = '<i class="fas fa-filter"></i>';
        }
    }
    
    const filterInputs = ['filterName', 'filterEmail', 'filterPhone', 'filterGender'];
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
            const firstLetter = initials.textContent || 'P';
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

