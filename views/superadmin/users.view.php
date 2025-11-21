<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">All Users</h1>
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
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Users</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #3498db;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Staff</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['staff'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #2ecc71;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Doctors</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['doctor'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #9b59b6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Patients</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['patient'] ?? 0 ?></div>
    </div>
</div>

<!-- Table Container -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header with Add Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">All Users</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddUserModal()" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i>
            <span>Add User</span>
        </button>
    </div>

    <!-- Filter Bar (Hidden by default) -->
    <div id="tableFilterBar" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Users
            </h3>
            <button type="button" class="btn btn-sm" onclick="resetTableFilters()" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
                <i class="fas fa-redo"></i>
                <span>Reset Filters</span>
            </button>
        </div>
        <div class="filter-controls-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-user" style="margin-right: 0.25rem;"></i>Full Name
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
                    <i class="fas fa-phone" style="margin-right: 0.25rem;"></i>Phone Number
                </label>
                <input type="text" id="filterPhone" class="filter-input" placeholder="Search phone..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-user-tag" style="margin-right: 0.25rem;"></i>Role
                </label>
                <select id="filterRole" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
                    <option value="">All Roles</option>
                    <option value="super admin">Super Admin</option>
                    <option value="staff">Staff</option>
                    <option value="doctor">Doctor</option>
                    <option value="patient">Patient</option>
                </select>
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>Date Created
                </label>
                <input type="date" id="filterDate" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
        </div>
    </div>

    <?php if (empty($users)): ?>
        <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
            <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p style="margin: 0;">No users found.</p>
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
                        <th class="sortable <?= $current_sort === 'full_name' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('full_name')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Full Name
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'user_email' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('user_email')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Email
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Phone Number
                        </th>
                        <th class="sortable <?= $current_sort === 'created_at' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('created_at')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Date Created
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'role' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('role')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Role
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($users as $user): ?>
                        <?php
                            // Determine role
                            $role = 'None';
                            $roleColor = '#999';
                            
                            if ($user['user_is_superadmin']) {
                                $role = 'Super Admin';
                                $roleColor = '#e74c3c';
                            } elseif ($user['staff_id']) {
                                $role = 'Staff';
                                $roleColor = '#3498db';
                            } elseif ($user['doc_id']) {
                                $role = 'Doctor';
                                $roleColor = '#2ecc71';
                            } elseif ($user['pat_id']) {
                                $role = 'Patient';
                                $roleColor = '#9b59b6';
                            }
                            
                            // Format phone number
                            $phone_display = !empty($user['phone_number']) ? htmlspecialchars($user['phone_number']) : 'N/A';
                            
                            // Format date
                            $date_created = !empty($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : 'N/A';
                            
                            // Get first letter for avatar
                            $firstLetter = strtoupper(substr($user['full_name'] ?? 'U', 0, 1));
                        ?>
                        <tr class="table-row" 
                            data-name="<?= htmlspecialchars(strtolower($user['full_name'] ?? '')) ?>"
                            data-email="<?= htmlspecialchars(strtolower($user['user_email'] ?? '')) ?>"
                            data-phone="<?= htmlspecialchars(strtolower($phone_display)) ?>"
                            data-role="<?= htmlspecialchars(strtolower($role)) ?>"
                            data-date="<?= !empty($user['created_at']) ? date('Y-m-d', strtotime($user['created_at'])) : '' ?>"
                            style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; overflow: hidden; flex-shrink: 0;">
                                        <?php if (!empty($user['profile_picture_url'])): ?>
                                            <img src="<?= htmlspecialchars($user['profile_picture_url']) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?= $firstLetter ?>
                                        <?php endif; ?>
                                    </div>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars($user['full_name'] ?? 'N/A') ?></strong>
                                </div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($user['user_email']) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $phone_display ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $date_created ?></td>
                            <td style="padding: 1rem;">
                                <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: <?= $roleColor ?>20; color: <?= $roleColor ?>;">
                                    <?= htmlspecialchars($role) ?>
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button class="btn btn-sm edit-user-btn" 
                                            data-user="<?= base64_encode(json_encode($user)) ?>" 
                                            title="Edit"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm view-user-btn" 
                                            data-user="<?= base64_encode(json_encode($user)) ?>" 
                                            title="View"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return handleDelete(event, 'Are you sure you want to delete this user?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $user['user_id'] ?>">
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

<!-- Add User Modal -->
<div id="addModal" class="modal">
    <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h2 class="modal-title">Create New User</h2>
            <button type="button" class="modal-close" onclick="closeAddUserModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem; padding: 0 2rem;">Select a role to create the user account. You will be redirected to create the corresponding profile.</p>
        <form id="createUserForm" onsubmit="return redirectToRoleCreation(event)">
            <div class="form-group" style="padding: 0 2rem;">
                <label>Select Role: <span style="color: var(--status-error);">*</span></label>
                <select id="role_select" required class="form-control">
                    <option value="">-- Select Role --</option>
                    <option value="superadmin">Super Admin</option>
                    <option value="staff">Staff</option>
                    <option value="doctor">Doctor</option>
                    <option value="patient">Patient</option>
                </select>
            </div>
            
            <div class="action-buttons" style="margin-top: 1.5rem; padding: 0 2rem 2rem 2rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-arrow-right"></i>
                    <span>Continue to Create Profile</span>
                </button>
                <button type="button" onclick="closeAddUserModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="modal">
    <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h2 class="modal-title">Edit User</h2>
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
                            <span id="edit_profile_picture_initials">U</span>
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
            
            <!-- User Account Section -->
            <div style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem; color: var(--primary-blue); border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem;">User Account</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Email: <span style="color: var(--status-error);">*</span></label>
                        <input type="email" name="email" id="edit_email" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Password (leave blank to keep current):</label>
                        <input type="password" name="password" id="edit_password" class="form-control">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Change Role:</label>
                    <select name="role" id="edit_role" class="form-control">
                        <option value="none">No Role</option>
                        <option value="superadmin">Super Admin</option>
                        <option value="staff">Staff</option>
                        <option value="doctor">Doctor</option>
                        <option value="patient">Patient</option>
                    </select>
                    <small style="color: var(--text-secondary); display: block; margin-top: 0.5rem;">Current role will be displayed when you open this form</small>
                </div>
            </div>
            
            <!-- Patient Profile Fields -->
            <div id="patient_fields" style="display: none; margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem; color: var(--primary-blue); border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem;">Patient Profile</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name: <span style="color: var(--status-error);">*</span></label>
                        <input type="text" name="pat_first_name" id="edit_pat_first_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Middle Initial:</label>
                        <input type="text" name="pat_middle_initial" id="edit_pat_middle_initial" maxlength="1" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Last Name: <span style="color: var(--status-error);">*</span></label>
                        <input type="text" name="pat_last_name" id="edit_pat_last_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Phone:</label>
                        <input type="text" name="pat_phone" id="edit_pat_phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Date of Birth:</label>
                        <input type="date" name="date_of_birth" id="edit_pat_date_of_birth" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Gender:</label>
                        <select name="gender" id="edit_pat_gender" class="form-control">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group form-grid-full">
                    <label>Address:</label>
                    <textarea name="address" id="edit_pat_address" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Emergency Contact:</label>
                        <input type="text" name="emergency_contact" id="edit_pat_emergency_contact" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Emergency Phone:</label>
                        <input type="text" name="emergency_phone" id="edit_pat_emergency_phone" class="form-control">
                    </div>
                </div>
                <div class="form-group form-grid-full">
                    <label>Medical History:</label>
                    <textarea name="medical_history" id="edit_pat_medical_history" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-group form-grid-full">
                    <label>Allergies:</label>
                    <textarea name="allergies" id="edit_pat_allergies" rows="2" class="form-control"></textarea>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Insurance Provider:</label>
                        <input type="text" name="insurance_provider" id="edit_pat_insurance_provider" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Insurance Number:</label>
                        <input type="text" name="insurance_number" id="edit_pat_insurance_number" class="form-control">
                    </div>
                </div>
            </div>
            
            <!-- Staff Profile Fields -->
            <div id="staff_fields" style="display: none; margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem; color: var(--primary-blue); border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem;">Staff Profile</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name: <span style="color: var(--status-error);">*</span></label>
                        <input type="text" name="staff_first_name" id="edit_staff_first_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Middle Initial:</label>
                        <input type="text" name="staff_middle_initial" id="edit_staff_middle_initial" maxlength="1" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Last Name: <span style="color: var(--status-error);">*</span></label>
                        <input type="text" name="staff_last_name" id="edit_staff_last_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Phone:</label>
                        <input type="text" name="staff_phone" id="edit_staff_phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Position:</label>
                        <input type="text" name="position" id="edit_staff_position" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Hire Date:</label>
                        <input type="date" name="hire_date" id="edit_staff_hire_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Salary:</label>
                        <input type="number" name="salary" id="edit_staff_salary" step="0.01" min="0" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status:</label>
                        <select name="status" id="edit_staff_status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Doctor Profile Fields -->
            <div id="doctor_fields" style="display: none; margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem; color: var(--primary-blue); border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem;">Doctor Profile</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name: <span style="color: var(--status-error);">*</span></label>
                        <input type="text" name="doc_first_name" id="edit_doc_first_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Middle Initial:</label>
                        <input type="text" name="doc_middle_initial" id="edit_doc_middle_initial" maxlength="1" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Last Name: <span style="color: var(--status-error);">*</span></label>
                        <input type="text" name="doc_last_name" id="edit_doc_last_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Phone:</label>
                        <input type="text" name="doc_phone" id="edit_doc_phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Specialization:</label>
                        <select name="specialization_id" id="edit_doc_specialization_id" class="form-control">
                            <option value="">Select Specialization</option>
                            <?php if (isset($specializations)): ?>
                                <?php foreach ($specializations as $spec): ?>
                                    <option value="<?= $spec['spec_id'] ?>"><?= htmlspecialchars($spec['spec_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>License Number:</label>
                        <input type="text" name="license_number" id="edit_doc_license_number" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Experience (Years):</label>
                        <input type="number" name="experience_years" id="edit_doc_experience_years" min="0" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Consultation Fee:</label>
                        <input type="number" name="consultation_fee" id="edit_doc_consultation_fee" step="0.01" min="0" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status:</label>
                        <select name="status" id="edit_doc_status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-group form-grid-full">
                    <label>Qualification:</label>
                    <textarea name="qualification" id="edit_doc_qualification" rows="2" class="form-control"></textarea>
                </div>
                <div class="form-group form-grid-full">
                    <label>Bio:</label>
                    <textarea name="bio" id="edit_doc_bio" rows="3" class="form-control"></textarea>
                </div>
            </div>
            
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    <span>Update User</span>
                </button>
                <button type="button" onclick="closeEditModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View User Profile Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2 class="modal-title">User Profile</h2>
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

<script>
function openAddUserModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddUserModal() {
    document.getElementById('addModal').classList.remove('active');
    document.getElementById('role_select').value = '';
}

function redirectToRoleCreation(event) {
    event.preventDefault();
    const role = document.getElementById('role_select').value;
    
    if (!role) {
        alert('Please select a role');
        return false;
    }
    
    // Redirect to appropriate creation page
    if (role === 'superadmin') {
        showConfirm(
            'Create a Super Admin account? This will have full system access.',
            'Create Super Admin',
            'Yes, Create',
            'Cancel',
            'warning'
        ).then(confirmed => {
            if (confirmed) {
                const email = prompt('Enter email for Super Admin:');
                if (!email) return;
                
                const password = prompt('Enter password for Super Admin:');
                if (!password) return;
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                form.innerHTML = `
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="email" value="${email}">
                    <input type="hidden" name="password" value="${password}">
                    <input type="hidden" name="is_superadmin" value="1">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
        return false;
    } else if (role === 'staff') {
        window.location.href = '/superadmin/staff?create_user=1';
        return false;
    } else if (role === 'doctor') {
        window.location.href = '/superadmin/doctors?create_user=1';
        return false;
    } else if (role === 'patient') {
        window.location.href = '/superadmin/patients?create_user=1';
        return false;
    }
    
    return false;
}

function viewUserProfile(user) {
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
    
    // Determine role and profile link
    let role = 'None';
    let profileLink = '';
    
    if (user.user_is_superadmin == true || user.user_is_superadmin == 1) {
        role = 'Super Admin';
    } else if (user.staff_id) {
        role = 'Staff';
        profileLink = `/superadmin/staff?id=${user.staff_id}`;
    } else if (user.doc_id) {
        role = 'Doctor';
        profileLink = `/superadmin/doctors?id=${user.doc_id}`;
    } else if (user.pat_id) {
        role = 'Patient';
        profileLink = `/superadmin/patients?id=${user.pat_id}`;
    }
    
    // Get profile picture or generate initials
    const profilePicture = user.profile_picture_url || '';
    const firstName = user.full_name ? user.full_name.split(' ')[0] : 'U';
    const firstLetter = firstName.charAt(0).toUpperCase();
    
    const content = `
        <div style="padding: 2rem;">
            <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-light);">
                <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 3rem; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    ${profilePicture ? `<img src="${profilePicture}" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">` : firstLetter}
                </div>
                <div>
                    <h3 style="margin: 0 0 0.5rem 0; color: var(--text-primary); font-size: 1.5rem;">${user.full_name || 'N/A'}</h3>
                    <p style="margin: 0; color: var(--text-secondary);">${user.user_email || 'N/A'}</p>
                    <div style="margin-top: 0.5rem;">
                        <span class="badge" style="background: ${getRoleColor(role)}20; color: ${getRoleColor(role)}; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">${role}</span>
                    </div>
                </div>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <h3 style="margin-bottom: 1rem; color: var(--text-primary);">User Information</h3>
                <div style="display: grid; gap: 1rem;">
                    <div>
                        <strong>User ID:</strong> ${user.user_id}
                    </div>
                    <div>
                        <strong>Email:</strong> ${user.user_email}
                    </div>
                    <div>
                        <strong>Full Name:</strong> ${user.full_name || 'N/A'}
                    </div>
                    <div>
                        <strong>Phone:</strong> ${user.phone_number || 'N/A'}
                    </div>
                    <div>
                        <strong>Date Created:</strong> ${formatDate(user.created_at)}
                    </div>
                    <div>
                        <strong>Date Updated:</strong> ${formatDate(user.updated_at)}
                    </div>
                </div>
            </div>
            ${profileLink ? `
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-light);">
                <a href="${profileLink}" class="btn btn-primary" style="text-decoration: none;">
                    <i class="fas fa-external-link-alt"></i>
                    <span>View ${role} Profile</span>
                </a>
            </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('viewContent').innerHTML = content;
    document.getElementById('viewModal').classList.add('active');
}

function getRoleColor(role) {
    const colors = {
        'Super Admin': '#e74c3c',
        'Staff': '#3498db',
        'Doctor': '#2ecc71',
        'Patient': '#9b59b6',
        'None': '#999'
    };
    return colors[role] || '#999';
}

function editUser(user) {
    // Reset form
    document.getElementById('edit_id').value = user.user_id;
    document.getElementById('edit_email').value = user.user_email || '';
    document.getElementById('edit_password').value = '';
    
    // Update profile picture preview
    updateProfilePicturePreview('edit', user.profile_picture_url || '', user.full_name || 'U');
    
    // Hide all profile sections first
    document.getElementById('patient_fields').style.display = 'none';
    document.getElementById('staff_fields').style.display = 'none';
    document.getElementById('doctor_fields').style.display = 'none';
    
    // Determine current role
    let currentRole = 'none';
    if (user.user_is_superadmin == true || user.user_is_superadmin == 1) {
        currentRole = 'superadmin';
    } else if (user.staff_id) {
        currentRole = 'staff';
        
        // Populate staff fields
        document.getElementById('edit_staff_first_name').value = user.staff_first_name || '';
        document.getElementById('edit_staff_middle_initial').value = user.staff_middle_initial || '';
        document.getElementById('edit_staff_last_name').value = user.staff_last_name || '';
        document.getElementById('edit_staff_phone').value = user.staff_phone ? formatPhoneNumber(user.staff_phone) : '';
        document.getElementById('edit_staff_position').value = user.staff_position || '';
        document.getElementById('edit_staff_hire_date').value = user.staff_hire_date || '';
        document.getElementById('edit_staff_salary').value = user.staff_salary || '';
        document.getElementById('edit_staff_status').value = user.staff_status || 'active';
        document.getElementById('staff_fields').style.display = 'block';
    } else if (user.doc_id) {
        currentRole = 'doctor';
        
        // Populate doctor fields
        document.getElementById('edit_doc_first_name').value = user.doc_first_name || '';
        document.getElementById('edit_doc_middle_initial').value = user.doc_middle_initial || '';
        document.getElementById('edit_doc_last_name').value = user.doc_last_name || '';
        document.getElementById('edit_doc_phone').value = user.doc_phone ? formatPhoneNumber(user.doc_phone) : '';
        document.getElementById('edit_doc_specialization_id').value = user.doc_specialization_id || '';
        document.getElementById('edit_doc_license_number').value = user.doc_license_number || '';
        document.getElementById('edit_doc_experience_years').value = user.doc_experience_years || '';
        document.getElementById('edit_doc_consultation_fee').value = user.doc_consultation_fee || '';
        document.getElementById('edit_doc_qualification').value = user.doc_qualification || '';
        document.getElementById('edit_doc_bio').value = user.doc_bio || '';
        document.getElementById('edit_doc_status').value = user.doc_status || 'active';
        document.getElementById('doctor_fields').style.display = 'block';
    } else if (user.pat_id) {
        currentRole = 'patient';
        
        // Populate patient fields
        document.getElementById('edit_pat_first_name').value = user.pat_first_name || '';
        document.getElementById('edit_pat_middle_initial').value = user.pat_middle_initial || '';
        document.getElementById('edit_pat_last_name').value = user.pat_last_name || '';
        document.getElementById('edit_pat_phone').value = user.pat_phone ? formatPhoneNumber(user.pat_phone) : '';
        document.getElementById('edit_pat_date_of_birth').value = user.pat_date_of_birth || '';
        document.getElementById('edit_pat_gender').value = user.pat_gender || '';
        document.getElementById('edit_pat_address').value = user.pat_address || '';
        document.getElementById('edit_pat_emergency_contact').value = user.pat_emergency_contact || '';
        document.getElementById('edit_pat_emergency_phone').value = user.pat_emergency_phone ? formatPhoneNumber(user.pat_emergency_phone) : '';
        document.getElementById('edit_pat_medical_history').value = user.pat_medical_history || '';
        document.getElementById('edit_pat_allergies').value = user.pat_allergies || '';
        document.getElementById('edit_pat_insurance_provider').value = user.pat_insurance_provider || '';
        document.getElementById('edit_pat_insurance_number').value = user.pat_insurance_number || '';
        document.getElementById('patient_fields').style.display = 'block';
    }
    
    document.getElementById('edit_role').value = currentRole;
    toggleRoleLinkSection();
    
    document.getElementById('editModal').classList.add('active');
    
    // Format phone numbers when loading into form
    formatPhoneInput('edit_pat_phone');
    formatPhoneInput('edit_pat_emergency_phone');
    formatPhoneInput('edit_staff_phone');
    formatPhoneInput('edit_doc_phone');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.remove('active');
}

// Show/hide role link section based on selected role
document.addEventListener('DOMContentLoaded', function() {
    const editRoleSelect = document.getElementById('edit_role');
    if (editRoleSelect) {
        editRoleSelect.addEventListener('change', toggleRoleLinkSection);
    }
    
    // Add event listeners for edit and view buttons
    document.querySelectorAll('.edit-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-user');
                const decodedJson = atob(encodedData);
                const userData = JSON.parse(decodedJson);
                editUser(userData);
            } catch (e) {
                console.error('Error parsing user data:', e);
                alert('Error loading user data. Please check the console for details.');
            }
        });
    });
    
    document.querySelectorAll('.view-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-user');
                const decodedJson = atob(encodedData);
                const userData = JSON.parse(decodedJson);
                viewUserProfile(userData);
            } catch (e) {
                console.error('Error parsing user data:', e);
                alert('Error loading user data. Please check the console for details.');
            }
        });
    });
});

// Phone number formatting function (Philippine format: XXXX-XXX-XXXX)
function formatPhoneNumber(value) {
    // Remove all non-digit characters
    let digits = value.replace(/\D/g, '');
    
    // Limit to 11 digits (Philippine format)
    if (digits.length > 11) {
        digits = digits.substring(0, 11);
    }
    
    // Format as XXXX-XXX-XXXX
    if (digits.length >= 7) {
        return digits.substring(0, 4) + '-' + digits.substring(4, 7) + '-' + digits.substring(7);
    } else if (digits.length >= 4) {
        return digits.substring(0, 4) + '-' + digits.substring(4);
    }
    return digits;
}

// Format phone input on input event
function formatPhoneInput(inputId) {
    const input = document.getElementById(inputId);
    if (input && !input.hasAttribute('data-phone-formatted')) {
        // Mark as formatted to avoid duplicate listeners
        input.setAttribute('data-phone-formatted', 'true');
        
        input.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const formatted = formatPhoneNumber(e.target.value);
            
            if (oldValue !== formatted) {
                e.target.value = formatted;
                // Restore cursor position
                const newCursorPosition = cursorPosition + (formatted.length - oldValue.length);
                setTimeout(() => {
                    e.target.setSelectionRange(newCursorPosition, newCursorPosition);
                }, 0);
            }
        });
        
        // Format on blur (when user leaves the field)
        input.addEventListener('blur', function(e) {
            if (e.target.value) {
                e.target.value = formatPhoneNumber(e.target.value);
            }
        });
        
        // Format existing value if present
        if (input.value) {
            input.value = formatPhoneNumber(input.value);
        }
    }
}

function toggleRoleLinkSection() {
    const role = document.getElementById('edit_role').value;
    
    // Hide all profile sections
    document.getElementById('patient_fields').style.display = 'none';
    document.getElementById('staff_fields').style.display = 'none';
    document.getElementById('doctor_fields').style.display = 'none';
    
    if (role === 'staff') {
        document.getElementById('staff_fields').style.display = 'block';
    } else if (role === 'doctor') {
        document.getElementById('doctor_fields').style.display = 'block';
    } else if (role === 'patient') {
        document.getElementById('patient_fields').style.display = 'block';
    }
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
    
    // Initialize phone number formatting for all phone inputs
    formatPhoneInput('edit_pat_phone');
    formatPhoneInput('edit_pat_emergency_phone');
    formatPhoneInput('edit_staff_phone');
    formatPhoneInput('edit_doc_phone');
});

function filterByCategory(category) {
    if (category === 'all') {
        window.location.href = '/superadmin/users';
    } else {
        window.location.href = '/superadmin/users?role=' + category;
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
    
    <!-- Role Filter -->
    <div class="filter-section">
        <div class="filter-section-header" onclick="toggleFilterSection('role')">
            <h4 class="filter-section-title">Role</h4>
            <button type="button" class="filter-section-toggle" id="roleToggle">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
        <div class="filter-section-content" id="roleContent">
            <div class="filter-radio-group">
                <div class="filter-radio-item">
                    <input type="radio" name="filter_role" id="role_all" value="all" <?= empty($filter_role) ? 'checked' : '' ?>>
                    <label for="role_all">All</label>
                </div>
                <div class="filter-radio-item">
                    <input type="radio" name="filter_role" id="role_superadmin" value="superadmin" <?= $filter_role === 'superadmin' ? 'checked' : '' ?>>
                    <label for="role_superadmin">Super Admin</label>
                </div>
                <div class="filter-radio-item">
                    <input type="radio" name="filter_role" id="role_staff" value="staff" <?= $filter_role === 'staff' ? 'checked' : '' ?>>
                    <label for="role_staff">Staff</label>
                </div>
                <div class="filter-radio-item">
                    <input type="radio" name="filter_role" id="role_doctor" value="doctor" <?= $filter_role === 'doctor' ? 'checked' : '' ?>>
                    <label for="role_doctor">Doctor</label>
                </div>
                <div class="filter-radio-item">
                    <input type="radio" name="filter_role" id="role_patient" value="patient" <?= $filter_role === 'patient' ? 'checked' : '' ?>>
                    <label for="role_patient">Patient</label>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter Actions -->
    <div class="filter-sidebar-actions">
        <button type="button" class="filter-clear-btn" onclick="clearAllFilters()">Clear all</button>
        <button type="button" class="filter-apply-btn" onclick="applyUserFilters()">Apply all filter</button>
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
    document.getElementById('role_all').checked = true;
}

function applyUserFilters() {
    const role = document.querySelector('input[name="filter_role"]:checked')?.value;
    const search = document.querySelector('input[name="search"]')?.value || '';
    
    const params = new URLSearchParams();
    if (search) {
        params.append('search', search);
    }
    if (role && role !== 'all') {
        params.append('role', role);
    }
    
    window.location.href = '/superadmin/users' + (params.toString() ? '?' + params.toString() : '');
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

// Table Filtering Functions
function filterTable() {
    const nameFilter = document.getElementById('filterName')?.value.toLowerCase().trim() || '';
    const emailFilter = document.getElementById('filterEmail')?.value.toLowerCase().trim() || '';
    const phoneFilter = document.getElementById('filterPhone')?.value.toLowerCase().trim() || '';
    const roleFilter = document.getElementById('filterRole')?.value.toLowerCase().trim() || '';
    const dateFilter = document.getElementById('filterDate')?.value || '';
    
    const rows = document.querySelectorAll('tbody .table-row');
    const paginationContainer = document.getElementById('paginationContainer');
    let visibleCount = 0;
    
    // Check if any filters are active
    const hasActiveFilters = nameFilter || emailFilter || phoneFilter || roleFilter || dateFilter;
    
    rows.forEach(row => {
        const name = row.getAttribute('data-name') || '';
        const email = row.getAttribute('data-email') || '';
        const phone = row.getAttribute('data-phone') || '';
        const role = row.getAttribute('data-role') || '';
        const date = row.getAttribute('data-date') || '';
        
        const matchesName = !nameFilter || name.includes(nameFilter);
        const matchesEmail = !emailFilter || email.includes(emailFilter);
        const matchesPhone = !phoneFilter || phone.includes(phoneFilter);
        const matchesRole = !roleFilter || role === roleFilter;
        const matchesDate = !dateFilter || date === dateFilter;
        
        if (matchesName && matchesEmail && matchesPhone && matchesRole && matchesDate) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide pagination based on filter activity
    if (hasActiveFilters) {
        if (paginationContainer) {
            paginationContainer.style.display = 'none';
        }
        
        // Show filter active message
        let filterActiveMessage = document.getElementById('filterActiveMessage');
        if (!filterActiveMessage) {
            filterActiveMessage = document.createElement('div');
            filterActiveMessage.id = 'filterActiveMessage';
            filterActiveMessage.style.cssText = 'padding: 1.5rem; border-top: 1px solid var(--border-light); background: var(--primary-blue-bg); display: flex; align-items: center; gap: 0.75rem; color: var(--primary-blue-dark); font-size: 0.875rem;';
            filterActiveMessage.innerHTML = '<i class="fas fa-info-circle"></i><span>Filters are active. Showing ' + visibleCount + ' result(s) from all pages.</span>';
            const tableContainer = document.getElementById('tableBody').parentElement.parentElement;
            tableContainer.parentElement.insertBefore(filterActiveMessage, paginationContainer || tableContainer.nextSibling);
        } else {
            filterActiveMessage.querySelector('span').textContent = 'Filters are active. Showing ' + visibleCount + ' result(s) from all pages.';
        }
    } else {
        if (paginationContainer) {
            paginationContainer.style.display = 'flex';
        }
        const filterActiveMessage = document.getElementById('filterActiveMessage');
        if (filterActiveMessage) {
            filterActiveMessage.remove();
        }
    }
    
    // Show "no results" message if needed
    const tbody = document.getElementById('tableBody');
    if (visibleCount === 0 && hasActiveFilters) {
        let noResultsMsg = tbody.querySelector('.no-results-message');
        if (!noResultsMsg) {
            noResultsMsg = document.createElement('tr');
            noResultsMsg.className = 'no-results-message';
            const colCount = document.querySelectorAll('thead th').length;
            noResultsMsg.innerHTML = `<td colspan="${colCount}" style="padding: 3rem; text-align: center; color: var(--text-secondary);"><i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i><p style="margin: 0;">No users match the current filters.</p></td>`;
            tbody.appendChild(noResultsMsg);
        }
    } else {
        const noResultsMsg = tbody.querySelector('.no-results-message');
        if (noResultsMsg) {
            noResultsMsg.remove();
        }
    }
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
            resetToPaginatedView();
        }
    }
}

function resetTableFilters() {
    document.getElementById('filterName').value = '';
    document.getElementById('filterEmail').value = '';
    document.getElementById('filterPhone').value = '';
    document.getElementById('filterRole').value = '';
    document.getElementById('filterDate').value = '';
    filterTable();
}

function loadAllResults() {
    const url = new URL(window.location.href);
    if (url.searchParams.get('all_results') !== '1') {
        url.searchParams.set('all_results', '1');
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }
}

function resetToPaginatedView() {
    const url = new URL(window.location.href);
    if (url.searchParams.get('all_results') === '1') {
        url.searchParams.delete('all_results');
        url.searchParams.delete('page');
        window.location.href = url.toString();
    } else {
        filterTable();
    }
}

// Initialize filtering
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = ['filterName', 'filterEmail', 'filterPhone', 'filterRole', 'filterDate'];
    filterInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', filterTable);
            input.addEventListener('change', filterTable);
        }
    });
    
    // Check if we're in all_results mode
    const url = new URL(window.location.href);
    if (url.searchParams.get('all_results') === '1') {
        const filterBar = document.getElementById('tableFilterBar');
        const toggleBtn = document.getElementById('toggleFilterBtn');
        if (filterBar && toggleBtn) {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
        }
    }
    
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
            const firstLetter = initials.textContent || 'U';
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
