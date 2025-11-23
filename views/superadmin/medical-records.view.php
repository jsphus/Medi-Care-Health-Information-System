<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">All Medical Records</h1>
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
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Records</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Records This Month</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['this_month'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Pending Follow-up</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['pending_followup'] ?? 0 ?></div>
    </div>
</div>

<!-- Table Container -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header with Add Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">All Medical Records</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <!-- Add Medical Record button removed - medical records are created through appointments -->
    </div>

    <!-- Filter Bar (Hidden by default) -->
    <div id="tableFilterBar" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Medical Records
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
                    <i class="fas fa-user" style="margin-right: 0.25rem;"></i>Patient Name
                </label>
                <input type="text" id="filterPatient" class="filter-input" placeholder="Search patient..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-user-md" style="margin-right: 0.25rem;"></i>Doctor Name
                </label>
                <input type="text" id="filterDoctor" class="filter-input" placeholder="Search doctor..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-stethoscope" style="margin-right: 0.25rem;"></i>Diagnosis
                </label>
                <input type="text" id="filterDiagnosis" class="filter-input" placeholder="Search diagnosis..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
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

    <?php if (empty($records)): ?>
        <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
            <i class="fas fa-file-medical" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p style="margin: 0;">No medical records found.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid var(--border-light);">
                        <?php
                        $current_sort = $_GET['sort'] ?? 'med_rec_created_at';
                        $current_order = $_GET['order'] ?? 'DESC';
                        ?>
                        <th class="sortable <?= $current_sort === 'med_rec_id' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('med_rec_id')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Record ID
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'med_rec_visit_date' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('med_rec_visit_date')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Date
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Patient
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Doctor
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Diagnosis
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Prescription
                        </th>
                        <th class="sortable <?= $current_sort === 'med_rec_created_at' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('med_rec_created_at')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Created At
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'med_rec_updated_at' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('med_rec_updated_at')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Updated At
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($records as $record): ?>
                        <tr class="table-row" 
                            data-patient="<?= htmlspecialchars(strtolower(($record['pat_first_name'] ?? '') . ' ' . ($record['pat_last_name'] ?? ''))) ?>"
                            data-doctor="<?= htmlspecialchars(strtolower(($record['doc_first_name'] ?? '') . ' ' . ($record['doc_last_name'] ?? ''))) ?>"
                            data-diagnosis="<?= htmlspecialchars(strtolower($record['med_rec_diagnosis'] ?? '')) ?>"
                            data-date="<?= $record['med_rec_visit_date'] ? date('Y-m-d', strtotime($record['med_rec_visit_date'])) : '' ?>"
                            style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <strong style="color: var(--text-primary);">#<?= htmlspecialchars($record['med_rec_id']) ?></strong>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $record['med_rec_visit_date'] ? date('d M Y', strtotime($record['med_rec_visit_date'])) : 'N/A' ?></td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; overflow: hidden; flex-shrink: 0;">
                                        <?php if (!empty($record['patient_profile_picture'])): ?>
                                            <img src="<?= htmlspecialchars($record['patient_profile_picture']) ?>" alt="Patient" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?= strtoupper(substr($record['pat_first_name'] ?? 'P', 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars(formatFullName($record['pat_first_name'] ?? '', $record['pat_middle_initial'] ?? null, $record['pat_last_name'] ?? '')) ?></strong>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <?php if (!empty($record['doctor_profile_picture'])): ?>
                                        <div style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; flex-shrink: 0;">
                                            <img src="<?= htmlspecialchars($record['doctor_profile_picture']) ?>" alt="Doctor" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    <?php endif; ?>
                                    <span style="color: var(--text-secondary);">Dr. <?= htmlspecialchars(formatFullName($record['doc_first_name'] ?? '', $record['doc_middle_initial'] ?? null, $record['doc_last_name'] ?? '')) ?></span>
                                </div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars(substr($record['med_rec_diagnosis'] ?? '', 0, 50)) ?><?= strlen($record['med_rec_diagnosis'] ?? '') > 50 ? '...' : '' ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars(substr($record['med_rec_prescription'] ?? '', 0, 50)) ?><?= strlen($record['med_rec_prescription'] ?? '') > 50 ? '...' : '' ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">
                                <?php if (!empty($record['med_rec_created_at'])): ?>
                                    <?= date('M d, Y', strtotime($record['med_rec_created_at'])) ?><br>
                                    <span style="color: var(--text-secondary); font-size: 0.75rem;"><?= date('g:i A', strtotime($record['med_rec_created_at'])) ?></span>
                                <?php else: ?>
                                    <span style="color: var(--text-secondary);">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">
                                <?php if (!empty($record['med_rec_updated_at'])): ?>
                                    <?= date('M d, Y', strtotime($record['med_rec_updated_at'])) ?><br>
                                    <span style="color: var(--text-secondary); font-size: 0.75rem;"><?= date('g:i A', strtotime($record['med_rec_updated_at'])) ?></span>
                                <?php else: ?>
                                    <span style="color: var(--text-secondary);">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button class="btn btn-sm view-record-btn" 
                                            data-record="<?= base64_encode(json_encode($record)) ?>" 
                                            title="View"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm edit-record-btn" 
                                            data-record="<?= base64_encode(json_encode($record)) ?>" 
                                            title="Edit"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return handleDelete(event, 'Are you sure you want to delete this medical record? This action cannot be undone.');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $record['med_rec_id'] ?>">
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
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-top: 1px solid var(--border-light);">
            <div style="color: var(--text-secondary); font-size: 0.875rem;">
                Showing <?= ($offset ?? 0) + 1 ?>-<?= min(($offset ?? 0) + ($items_per_page ?? 10), $total_items ?? count($records)) ?> of <?= $total_items ?? count($records) ?> entries
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => max(1, ($page ?? 1) - 1)])) ?>" 
                   class="btn btn-sm" 
                   style="<?= ($page ?? 1) <= 1 ? 'opacity: 0.5; pointer-events: none;' : '' ?>">
                    < Previous
                </a>
                <?php
                $current_page = $page ?? 1;
                $total_pages = $total_pages ?? 1;
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                if ($start_page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="btn btn-sm">1</a>
                    <?php if ($start_page > 2): ?>
                        <span style="padding: 0.5rem;">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                       class="btn btn-sm <?= $i == $current_page ? 'btn-primary' : '' ?>" 
                       style="<?= $i == $current_page ? 'background: var(--primary-blue); color: white;' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                <?php if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <span style="padding: 0.5rem;">...</span>
                    <?php endif; ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" class="btn btn-sm"><?= $total_pages ?></a>
                <?php endif; ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => min($total_pages, $current_page + 1)])) ?>" 
                   class="btn btn-sm" 
                   style="<?= $current_page >= $total_pages ? 'opacity: 0.5; pointer-events: none;' : '' ?>">
                    Next >
                </a>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- View Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2 class="modal-title">Medical Record Details</h2>
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
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2 class="modal-title">Edit Medical Record</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editRecordForm" method="POST" onsubmit="return handleEditSubmit(event);">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_record_id">
            
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Record ID:</strong> <span id="edit_record_id_display" style="color: var(--text-secondary);"></span></p>
                <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Patient:</strong> <span id="edit_patient_name" style="color: var(--text-secondary);"></span></p>
                <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Doctor:</strong> <span id="edit_doctor_name" style="color: var(--text-secondary);"></span></p>
            </div>
            
            <div class="form-group">
                <label for="edit_visit_date">Visit Date: <span style="color: var(--status-error);">*</span></label>
                <input type="date" name="med_rec_visit_date" id="edit_visit_date" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="edit_diagnosis">Diagnosis: <span style="color: var(--status-error);">*</span></label>
                <textarea name="med_rec_diagnosis" id="edit_diagnosis" rows="6" required class="form-control" placeholder="Enter diagnosis details..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="edit_prescription">Prescription:</label>
                <textarea name="med_rec_prescription" id="edit_prescription" rows="6" class="form-control" placeholder="Enter prescription details..."></textarea>
            </div>
            
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="button" onclick="closeEditModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function viewRecord(record) {
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
    
    // Helper function to format time
    const formatTime = (timeString) => {
        if (!timeString) return 'N/A';
        try {
            const [hours, minutes] = timeString.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            return `${displayHour}:${minutes} ${ampm}`;
        } catch (e) {
            return timeString;
        }
    };
    
    // Helper function to get profile picture or initials
    const getProfilePicture = (profilePic, firstName, lastName) => {
        if (profilePic) {
            return `<img src="${profilePic}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
        }
        const initial = (firstName ? firstName.charAt(0) : '') || (lastName ? lastName.charAt(0) : '') || '?';
        return `<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--primary-blue); color: white; font-weight: bold; font-size: 1.2rem; border-radius: 50%;">${initial.toUpperCase()}</div>`;
    };
    
    const patientPic = getProfilePicture(record.patient_profile_picture, record.pat_first_name, record.pat_last_name);
    const doctorPic = getProfilePicture(record.doctor_profile_picture, record.doc_first_name, record.doc_last_name);
    
    // Format full names
    const patientName = `${record.pat_first_name || ''} ${record.pat_middle_initial ? record.pat_middle_initial + '.' : ''} ${record.pat_last_name || ''}`.trim();
    const doctorName = `Dr. ${record.doc_first_name || ''} ${record.doc_middle_initial ? record.doc_middle_initial + '.' : ''} ${record.doc_last_name || ''}`.trim();
    
    const content = `
        <!-- Appointment Details Section -->
        <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f9fafb; border-radius: 8px; border-left: 4px solid var(--primary-blue);">
            <h3 style="margin-bottom: 1rem; color: var(--primary-blue); font-size: 1.2rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calendar-check"></i>
                Appointment Details
            </h3>
            <div style="display: flex; gap: 1.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; flex-shrink: 0; border: 2px solid var(--border-color);">
                        ${patientPic}
                    </div>
                    <div>
                        <p style="margin: 0.25rem 0; font-weight: 600; color: var(--text-primary);">Patient</p>
                        <p style="margin: 0.25rem 0; color: var(--text-secondary);">${patientName}</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; flex-shrink: 0; border: 2px solid var(--border-color);">
                        ${doctorPic}
                    </div>
                    <div>
                        <p style="margin: 0.25rem 0; font-weight: 600; color: var(--text-primary);">Doctor</p>
                        <p style="margin: 0.25rem 0; color: var(--text-secondary);">${doctorName}</p>
                    </div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div>
                    <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Appointment ID:</strong> <span style="color: var(--text-secondary);">${record.appointment_id || 'N/A'}</span></p>
                    <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Date:</strong> <span style="color: var(--text-secondary);">${record.appointment_date ? new Date(record.appointment_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A'}</span></p>
                    <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Time:</strong> <span style="color: var(--text-secondary);">${formatTime(record.appointment_time)}</span></p>
                </div>
                <div>
                    <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Service:</strong> <span style="color: var(--text-secondary);">${record.service_name || 'N/A'}</span></p>
                    ${record.service_price ? `<p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Service Price:</strong> <span style="color: var(--text-secondary);">₱${parseFloat(record.service_price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span></p>` : ''}
                    <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Duration:</strong> <span style="color: var(--text-secondary);">${record.appointment_duration || 30} minutes</span></p>
                </div>
                <div>
                    <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Status:</strong> 
                        <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; background: ${record.status_color ? record.status_color + '20' : '#3b82f620'}; color: ${record.status_color || '#3b82f6'};">
                            ${record.status_name || 'N/A'}
                        </span>
                    </p>
                    <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Created:</strong> <span style="color: var(--text-secondary);">${formatDate(record.appointment_created_at)}</span></p>
                </div>
            </div>
            ${record.appointment_notes ? `
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Appointment Notes:</strong></p>
                <p style="white-space: pre-wrap; margin: 0.5rem 0; color: var(--text-secondary); font-size: 0.875rem; padding: 0.75rem; background: white; border-radius: 4px;">${record.appointment_notes}</p>
            </div>
            ` : ''}
        </div>
        
        <!-- Medical Record Details Section -->
        <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f0fdf4; border-radius: 8px; border-left: 4px solid #10b981;">
            <h3 style="margin-bottom: 1rem; color: #10b981; font-size: 1.2rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-medical"></i>
                Medical Record Details
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Record ID:</strong> <span style="color: var(--text-secondary);">#${record.med_rec_id || 'N/A'}</span></p>
                    <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Visit Date:</strong> <span style="color: var(--text-secondary);">${record.med_rec_visit_date ? new Date(record.med_rec_visit_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A'}</span></p>
                </div>
                <div>
                    <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Created:</strong> <span style="color: var(--text-secondary);">${formatDate(record.med_rec_created_at)}</span></p>
                    <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong style="color: var(--text-primary);">Updated:</strong> <span style="color: var(--text-secondary);">${formatDate(record.med_rec_updated_at)}</span></p>
                </div>
            </div>
            
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <h4 style="margin-bottom: 0.75rem; color: var(--text-primary); font-size: 1rem; font-weight: 600;">Diagnosis</h4>
                <p style="white-space: pre-wrap; margin: 0; color: var(--text-primary); padding: 0.75rem; background: white; border-radius: 4px; border: 1px solid var(--border-color);">${record.med_rec_diagnosis || 'N/A'}</p>
            </div>
            
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <h4 style="margin-bottom: 0.75rem; color: var(--text-primary); font-size: 1rem; font-weight: 600;">Prescription</h4>
                <p style="white-space: pre-wrap; margin: 0; color: var(--text-primary); padding: 0.75rem; background: white; border-radius: 4px; border: 1px solid var(--border-color);">${record.med_rec_prescription || 'None'}</p>
            </div>
        </div>
    `;
    document.getElementById('viewContent').innerHTML = content;
    document.getElementById('viewModal').classList.add('active');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.remove('active');
}

function editRecord(record) {
    // Populate edit form with record data
    document.getElementById('edit_record_id').value = record.med_rec_id || '';
    document.getElementById('edit_record_id_display').textContent = '#' + (record.med_rec_id || 'N/A');
    
    // Format patient name
    const patientName = `${record.pat_first_name || ''} ${record.pat_middle_initial ? record.pat_middle_initial + '.' : ''} ${record.pat_last_name || ''}`.trim();
    document.getElementById('edit_patient_name').textContent = patientName || 'N/A';
    
    // Format doctor name
    const doctorName = `Dr. ${record.doc_first_name || ''} ${record.doc_middle_initial ? record.doc_middle_initial + '.' : ''} ${record.doc_last_name || ''}`.trim();
    document.getElementById('edit_doctor_name').textContent = doctorName || 'N/A';
    
    // Set form values
    if (record.med_rec_visit_date) {
        const visitDate = new Date(record.med_rec_visit_date);
        document.getElementById('edit_visit_date').value = visitDate.toISOString().split('T')[0];
    } else {
        document.getElementById('edit_visit_date').value = '';
    }
    
    document.getElementById('edit_diagnosis').value = record.med_rec_diagnosis || '';
    document.getElementById('edit_prescription').value = record.med_rec_prescription || '';
    
    // Show modal
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    // Reset form
    document.getElementById('editRecordForm').reset();
}

function handleEditSubmit(event) {
    // Form validation is handled by HTML5 required attributes
    // Just let it submit normally
    return true;
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
    
    // Add event listeners for view buttons
    document.querySelectorAll('.view-record-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-record');
                const decodedJson = atob(encodedData);
                const recordData = JSON.parse(decodedJson);
                viewRecord(recordData);
            } catch (e) {
                console.error('Error parsing record data:', e);
                alert('Error loading record data. Please check the console for details.');
            }
        });
    });
    
    // Add event listeners for edit buttons
    document.querySelectorAll('.edit-record-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-record');
                const decodedJson = atob(encodedData);
                const recordData = JSON.parse(decodedJson);
                editRecord(recordData);
            } catch (e) {
                console.error('Error parsing record data:', e);
                alert('Error loading record data. Please check the console for details.');
            }
        });
    });
});

function filterByCategory(category) {
    const url = new URL(window.location.href);
    if (category === 'all') {
        url.searchParams.delete('filter');
    } else {
        url.searchParams.set('filter', category);
    }
    window.location.href = url.toString();
}

function applyMedicalRecordFilters() {
    const filters = {
        doctor: document.querySelector('input[name="filter_doctor"]:checked')?.value || '',
        patient: document.querySelector('input[name="filter_patient"]:checked')?.value || ''
    };
    const url = new URL(window.location.href);
    url.searchParams.delete('doctor');
    url.searchParams.delete('patient');
    if (filters.doctor) url.searchParams.set('doctor', filters.doctor);
    if (filters.patient) url.searchParams.set('patient', filters.patient);
    url.searchParams.delete('page'); // Reset to page 1 when filtering
    window.location.href = url.toString();
}

function clearAllFilters() {
    document.querySelectorAll('.filter-sidebar input[type="radio"]').forEach(radio => {
        radio.checked = false;
    });
    const doctorSearch = document.getElementById('doctorSearch');
    const patientSearch = document.getElementById('patientSearch');
    if (doctorSearch) doctorSearch.value = '';
    if (patientSearch) patientSearch.value = '';
}
</script>

<!-- Filter Sidebar -->
<div class="filter-sidebar" id="filterSidebar">
    <div class="filter-sidebar-header">
        <h3>Filters</h3>
        <button type="button" class="filter-sidebar-close" onclick="toggleFilterSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <!-- Doctor Filter -->
    <?php if (!empty($filter_doctors)): ?>
    <div class="filter-section">
        <div class="filter-section-header" onclick="toggleFilterSection('doctor')">
            <h4 class="filter-section-title">Doctor</h4>
            <button type="button" class="filter-section-toggle" id="doctorToggle">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
        <div class="filter-section-content" id="doctorContent">
            <input type="text" class="filter-search-input" placeholder="Search Doctor" id="doctorSearch">
            <div class="filter-radio-group" id="doctorList">
                <?php foreach ($filter_doctors as $doctor): ?>
                    <div class="filter-radio-item">
                        <input type="radio" name="filter_doctor" id="doctor_<?= $doctor['doc_id'] ?>" value="<?= $doctor['doc_id'] ?>">
                        <label for="doctor_<?= $doctor['doc_id'] ?>">Dr. <?= htmlspecialchars($doctor['doc_first_name'] . ' ' . $doctor['doc_last_name']) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Patient Filter -->
    <?php if (!empty($filter_patients)): ?>
    <div class="filter-section">
        <div class="filter-section-header" onclick="toggleFilterSection('patient')">
            <h4 class="filter-section-title">Patient</h4>
            <button type="button" class="filter-section-toggle" id="patientToggle">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
        <div class="filter-section-content" id="patientContent">
            <input type="text" class="filter-search-input" placeholder="Search Patient" id="patientSearch">
            <div class="filter-radio-group" id="patientList">
                <?php foreach ($filter_patients as $patient): ?>
                    <div class="filter-radio-item">
                        <input type="radio" name="filter_patient" id="patient_<?= $patient['pat_id'] ?>" value="<?= $patient['pat_id'] ?>">
                        <label for="patient_<?= $patient['pat_id'] ?>"><?= htmlspecialchars($patient['pat_first_name'] . ' ' . $patient['pat_last_name']) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Filter Actions -->
    <div class="filter-sidebar-actions">
        <button type="button" class="filter-clear-btn" onclick="clearAllFilters()">Clear all</button>
        <button type="button" class="filter-apply-btn" onclick="applyMedicalRecordFilters()">Apply all filter</button>
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

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const doctorSearch = document.getElementById('doctorSearch');
    if (doctorSearch) {
        doctorSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const doctorItems = document.querySelectorAll('#doctorList .filter-radio-item');
            doctorItems.forEach(item => {
                const label = item.querySelector('label');
                if (label) {
                    const text = label.textContent.toLowerCase();
                    item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
                }
            });
        });
    }
    
    const patientSearch = document.getElementById('patientSearch');
    if (patientSearch) {
        patientSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const patientItems = document.querySelectorAll('#patientList .filter-radio-item');
            patientItems.forEach(item => {
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

// Table Filtering Functions - Simple URL-based filtering
function applyTableFilters() {
    const url = new URL(window.location.href);
    
    // Get filter values
    const patient = document.getElementById('filterPatient')?.value.trim() || '';
    const doctor = document.getElementById('filterDoctor')?.value.trim() || '';
    const diagnosis = document.getElementById('filterDiagnosis')?.value.trim() || '';
    const dateFrom = document.getElementById('filterDateFrom')?.value.trim() || '';
    const dateTo = document.getElementById('filterDateTo')?.value.trim() || '';
    
    // Remove existing filter parameters
    url.searchParams.delete('patient');
    url.searchParams.delete('doctor');
    url.searchParams.delete('diagnosis');
    url.searchParams.delete('date_from');
    url.searchParams.delete('date_to');
    url.searchParams.delete('page'); // Reset to page 1 when filtering
    
    // Add non-empty filter values to URL
    if (patient) url.searchParams.set('patient', patient);
    if (doctor) url.searchParams.set('doctor', doctor);
    if (diagnosis) url.searchParams.set('diagnosis', diagnosis);
    if (dateFrom) url.searchParams.set('date_from', dateFrom);
    if (dateTo) url.searchParams.set('date_to', dateTo);
    
    // Redirect to filtered URL
    window.location.href = url.toString();
}

function resetTableFilters() {
    const url = new URL(window.location.href);
    
    // Remove all filter parameters
    url.searchParams.delete('patient');
    url.searchParams.delete('doctor');
    url.searchParams.delete('diagnosis');
    url.searchParams.delete('date_from');
    url.searchParams.delete('date_to');
    url.searchParams.delete('page');
    
    // Redirect to clean URL
    window.location.href = url.toString();
}

function toggleTableFilters() {
    const filterBar = document.getElementById('tableFilterBar');
    const toggleBtn = document.getElementById('toggleFilterBtn');
    
    if (filterBar && toggleBtn) {
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
}

// Initialize - restore filter values from URL on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Restore filter values from URL
    if (urlParams.get('patient') && document.getElementById('filterPatient')) {
        document.getElementById('filterPatient').value = urlParams.get('patient');
    }
    if (urlParams.get('doctor') && document.getElementById('filterDoctor')) {
        document.getElementById('filterDoctor').value = urlParams.get('doctor');
    }
    if (urlParams.get('diagnosis') && document.getElementById('filterDiagnosis')) {
        document.getElementById('filterDiagnosis').value = urlParams.get('diagnosis');
    }
    if (urlParams.get('date_from') && document.getElementById('filterDateFrom')) {
        document.getElementById('filterDateFrom').value = urlParams.get('date_from');
    }
    if (urlParams.get('date_to') && document.getElementById('filterDateTo')) {
        document.getElementById('filterDateTo').value = urlParams.get('date_to');
    }
    
    // Show filter bar if any filters are active
    if (urlParams.has('patient') || urlParams.has('doctor') || urlParams.has('diagnosis') || 
        urlParams.has('date_from') || urlParams.has('date_to')) {
        const filterBar = document.getElementById('tableFilterBar');
        const toggleBtn = document.getElementById('toggleFilterBtn');
        if (filterBar && toggleBtn) {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
            toggleBtn.style.background = 'var(--primary-blue)';
            toggleBtn.style.color = 'white';
        }
    }
});

// Table Sorting Function
function sortTable(column) {
    const url = new URL(window.location.href);
    const currentSort = url.searchParams.get('sort');
    const currentOrder = url.searchParams.get('order') || 'DESC';
    
    // Map view column names to database column names
    const columnMap = {
        'med_rec_id': 'med_rec_id',
        'med_rec_visit_date': 'med_rec_visit_date',
        'med_rec_created_at': 'med_rec_created_at',
        'med_rec_updated_at': 'med_rec_updated_at'
    };
    
    // Use mapped column name or fallback to provided column
    const dbColumn = columnMap[column] || column;
    
    // Toggle order if clicking the same column, otherwise default to ASC
    if (currentSort === dbColumn) {
        url.searchParams.set('order', currentOrder === 'ASC' ? 'DESC' : 'ASC');
    } else {
        url.searchParams.set('order', 'ASC');
    }
    
    url.searchParams.set('sort', dbColumn);
    url.searchParams.delete('page'); // Reset to page 1 when sorting
    
    window.location.href = url.toString();
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
