<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">My Medical Records</h1>
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
<div class="stat-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Records This Month Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Records This Month</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['records_this_month'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Created this month
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Follow-ups Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Pending Follow-ups</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['pending_followups'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Upcoming follow-up dates
                </div>
            </div>
        </div>
    </div>

    <!-- Unique Patients Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Unique Patients</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['unique_patients'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Patients with records
                </div>
            </div>
        </div>
    </div>

    <!-- Records Today Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Records Today</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['records_today'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Created today
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Container -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header with Add Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">My Medical Records</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddMedicalRecordModal()" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i>
            <span>Create Medical Record</span>
        </button>
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
                    <i class="fas fa-stethoscope" style="margin-right: 0.25rem;"></i>Diagnosis
                </label>
                <input type="text" id="filterDiagnosis" class="filter-input" placeholder="Search diagnosis..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>Date
                </label>
                <input type="date" id="filterDate" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
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
                        $current_sort = $_GET['sort'] ?? 'med_rec_visit_date';
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
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Appointment
                        </th>
                        <th class="sortable <?= $current_sort === 'med_rec_visit_date' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('med_rec_visit_date')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Visit Date
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Patient
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Diagnosis
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($records as $record): ?>
                        <tr class="table-row" 
                            data-patient="<?= htmlspecialchars(strtolower(($record['pat_first_name'] ?? '') . ' ' . ($record['pat_last_name'] ?? ''))) ?>"
                            data-diagnosis="<?= htmlspecialchars(strtolower($record['med_rec_diagnosis'] ?? $record['diagnosis'] ?? '')) ?>"
                            data-date="<?= $record['med_rec_visit_date'] ?? $record['record_date'] ? date('Y-m-d', strtotime($record['med_rec_visit_date'] ?? $record['record_date'])) : '' ?>"
                            style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <strong style="color: var(--text-primary);">#<?= htmlspecialchars($record['med_rec_id'] ?? $record['record_id']) ?></strong>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);">
                                <div style="font-size: 0.875rem;">
                                    <strong><?= htmlspecialchars($record['appointment_id'] ?? $record['appt_id'] ?? 'N/A') ?></strong><br>
                                    <span style="color: var(--text-secondary); font-size: 0.8125rem;">
                                        <?= $record['appointment_date'] ? date('d M Y', strtotime($record['appointment_date'])) : 'N/A' ?>
                                    </span>
                                </div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= ($record['med_rec_visit_date'] ?? $record['record_date']) ? date('d M Y', strtotime($record['med_rec_visit_date'] ?? $record['record_date'])) : 'N/A' ?></td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; overflow: hidden; flex-shrink: 0;">
                                        <?php if (!empty($record['patient_profile_picture'])): ?>
                                            <img src="<?= htmlspecialchars($record['patient_profile_picture']) ?>" alt="Patient" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?= strtoupper(substr($record['pat_first_name'] ?? 'P', 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars($record['pat_first_name'] . ' ' . $record['pat_last_name']) ?></strong>
                                </div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars(substr($record['med_rec_diagnosis'] ?? $record['diagnosis'] ?? '', 0, 50)) ?><?= strlen($record['med_rec_diagnosis'] ?? $record['diagnosis'] ?? '') > 50 ? '...' : '' ?></td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button class="btn btn-sm edit-record-btn" 
                                            data-record="<?= base64_encode(json_encode($record)) ?>" 
                                            title="Edit"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm view-record-btn" 
                                            data-record="<?= base64_encode(json_encode($record)) ?>" 
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

<!-- Add Medical Record Modal -->
<div id="addModal" class="modal">
    <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h2 class="modal-title">Create New Medical Record</h2>
            <button type="button" class="modal-close" onclick="closeAddMedicalRecordModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-grid">
                <div class="form-group form-grid-full">
                    <label>Appointment: <span style="color: var(--status-error);">*</span></label>
                    <select name="appointment_id" id="add_appointment_select" required class="form-control" style="width: 100%;">
                        <option value="">Select Appointment</option>
                        <?php if (!empty($appointments)): ?>
                            <?php foreach ($appointments as $appt): ?>
                                <?php 
                                $patientName = htmlspecialchars(trim($appt['pat_first_name'] . ' ' . ($appt['pat_middle_initial'] ?? '') . ' ' . $appt['pat_last_name']));
                                $apptDate = date('M d, Y', strtotime($appt['appointment_date']));
                                $apptTime = date('g:i A', strtotime($appt['appointment_time']));
                                $serviceName = !empty($appt['service_name']) ? ' - ' . htmlspecialchars($appt['service_name']) : '';
                                ?>
                                <option value="<?= htmlspecialchars($appt['appointment_id']) ?>" data-appointment-date="<?= htmlspecialchars($appt['appointment_date']) ?>">
                                    <?= htmlspecialchars($appt['appointment_id']) ?> - <?= $patientName ?> (<?= $apptDate ?> at <?= $apptTime ?>)<?= $serviceName ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No completed appointments available without medical records</option>
                        <?php endif; ?>
                    </select>
                    <small style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                        Only completed appointments without medical records are shown
                    </small>
                </div>
                <div class="form-group">
                    <label>Visit Date: <span style="color: var(--status-error);">*</span></label>
                    <input type="date" name="med_rec_visit_date" id="add_visit_date" value="<?= date('Y-m-d') ?>" required class="form-control">
                </div>
            </div>
            <div class="form-group form-grid-full">
                <label>Diagnosis: <span style="color: var(--status-error);">*</span></label>
                <textarea name="med_rec_diagnosis" id="add_diagnosis" rows="4" required class="form-control" placeholder="Enter diagnosis details..."></textarea>
            </div>
            <div class="form-group form-grid-full">
                <label>Prescription:</label>
                <textarea name="med_rec_prescription" id="add_prescription" rows="4" class="form-control" placeholder="Enter prescription details..."></textarea>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    <span>Create Medical Record</span>
                </button>
                <button type="button" onclick="closeAddMedicalRecordModal()" class="btn btn-secondary">
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
            <h2 class="modal-title">Edit Medical Record</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group form-grid-full">
                <label>Diagnosis: <span style="color: var(--status-error);">*</span></label>
                <textarea name="med_rec_diagnosis" id="edit_diagnosis" rows="4" required class="form-control" placeholder="Enter diagnosis details..."></textarea>
            </div>
            <div class="form-group form-grid-full">
                <label>Prescription:</label>
                <textarea name="med_rec_prescription" id="edit_prescription" rows="4" class="form-control" placeholder="Enter prescription details..."></textarea>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    <span>Update Record</span>
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
function openAddMedicalRecordModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddMedicalRecordModal() {
    document.getElementById('addModal').classList.remove('active');
    document.querySelector('#addModal form').reset();
}

// Auto-populate visit date when appointment is selected
document.addEventListener('DOMContentLoaded', function() {
    const appointmentSelect = document.getElementById('add_appointment_select');
    const visitDateInput = document.getElementById('add_visit_date');
    
    if (appointmentSelect && visitDateInput) {
        appointmentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value && selectedOption.dataset.appointmentDate) {
                // Use the appointment date from data attribute
                visitDateInput.value = selectedOption.dataset.appointmentDate;
            }
        });
    }
});

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
    
    // Helper function to get profile picture or initials
    const getProfilePicture = (profilePic, firstName, lastName) => {
        if (profilePic) {
            return `<img src="${profilePic}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
        }
        const initial = (firstName ? firstName.charAt(0) : '') || (lastName ? lastName.charAt(0) : '') || '?';
        return `<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--primary-blue); color: white; font-weight: bold; font-size: 1.2rem; border-radius: 50%;">${initial.toUpperCase()}</div>`;
    };
    
    const patientPic = getProfilePicture(record.patient_profile_picture, record.pat_first_name, record.pat_last_name);
    
    const content = `
        <div style="margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem; color: var(--primary-blue); font-size: 1.1rem;">Record Information</h3>
            <div style="display: flex; gap: 1.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; flex-shrink: 0; border: 2px solid var(--border-color);">
                        ${patientPic}
                    </div>
                    <div>
                        <p style="margin: 0.25rem 0; font-weight: 600;">Patient</p>
                        <p style="margin: 0.25rem 0; color: var(--text-secondary);">${record.pat_first_name} ${record.pat_last_name}</p>
                    </div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div>
                    <p style="margin: 0.5rem 0;"><strong>Record ID:</strong> ${record.med_rec_id}</p>
                    <p style="margin: 0.5rem 0;"><strong>Visit Date:</strong> ${record.med_rec_visit_date || 'N/A'}</p>
                </div>
                <div>
                    <p style="margin: 0.5rem 0;"><strong>Appointment ID:</strong> ${record.appt_id || record.appointment_id || 'N/A'}</p>
                    <p style="margin: 0.5rem 0;"><strong>Appointment Date:</strong> ${record.appointment_date || 'N/A'}</p>
                </div>
            </div>
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color); display: flex; gap: 2rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-plus-circle" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);"><strong>Created:</strong> ${formatDate(record.med_rec_created_at || record.created_at)}</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-edit" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);"><strong>Updated:</strong> ${formatDate(record.med_rec_updated_at || record.updated_at)}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <hr style="border: none; border-top: 1px solid var(--border-color); margin: 1.5rem 0;">
        
        <div style="margin-bottom: 1.5rem;">
            <h3 style="margin-bottom: 0.75rem; color: var(--primary-blue); font-size: 1.1rem;">Diagnosis</h3>
            <p style="white-space: pre-wrap; margin: 0; color: var(--text-primary);">${record.med_rec_diagnosis || record.diagnosis || 'N/A'}</p>
        </div>
        
        <hr style="border: none; border-top: 1px solid var(--border-color); margin: 1.5rem 0;">
        
        <div style="margin-bottom: 1.5rem;">
            <h3 style="margin-bottom: 0.75rem; color: var(--primary-blue); font-size: 1.1rem;">Prescription</h3>
            <p style="white-space: pre-wrap; margin: 0; color: var(--text-primary);">${record.med_rec_prescription || record.prescription || 'None'}</p>
        </div>
    `;
    document.getElementById('viewContent').innerHTML = content;
    document.getElementById('viewModal').classList.add('active');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.remove('active');
}

function editRecord(record) {
    document.getElementById('edit_id').value = record.med_rec_id;
    document.getElementById('edit_diagnosis').value = record.med_rec_diagnosis || '';
    document.getElementById('edit_prescription').value = record.med_rec_prescription || '';
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

// Close modals on outside click and Escape key
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for view and edit buttons
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

function applyMedicalRecordFilters() {
    const filters = {
        patient: document.querySelector('input[name="filter_patient"]:checked')?.value || ''
    };
    const params = new URLSearchParams();
    if (filters.patient) params.append('patient', filters.patient);
    const url = '/doctor/medical-records' + (params.toString() ? '?' + params.toString() : '');
    window.location.href = url;
}

function clearAllFilters() {
    document.querySelectorAll('.filter-sidebar input[type="radio"]').forEach(radio => {
        radio.checked = false;
    });
    const patientSearch = document.getElementById('patientSearch');
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
function applyTableFilters() {
    filterTable();
}

function filterTable() {
    const patientFilter = document.getElementById('filterPatient')?.value.toLowerCase().trim() || '';
    const diagnosisFilter = document.getElementById('filterDiagnosis')?.value.toLowerCase().trim() || '';
    const dateFilter = document.getElementById('filterDate')?.value || '';
    
    const rows = document.querySelectorAll('.table-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const patient = row.getAttribute('data-patient') || '';
        const diagnosis = row.getAttribute('data-diagnosis') || '';
        const date = row.getAttribute('data-date') || '';
        
        const matchesPatient = !patientFilter || patient.includes(patientFilter);
        const matchesDiagnosis = !diagnosisFilter || diagnosis.includes(diagnosisFilter);
        const matchesDate = !dateFilter || date === dateFilter;
        
        if (matchesPatient && matchesDiagnosis && matchesDate) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    const hasActiveFilters = patientFilter || diagnosisFilter || dateFilter;
    const tableBody = document.getElementById('tableBody');
    const noResultsMsg = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0 && rows.length > 0 && hasActiveFilters) {
        if (!noResultsMsg) {
            const msg = document.createElement('tr');
            msg.id = 'noResultsMessage';
            const colCount = document.querySelector('thead tr')?.querySelectorAll('th').length || 7;
            msg.innerHTML = `<td colspan="${colCount}" style="padding: 3rem; text-align: center; color: var(--text-secondary);"><i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i><p style="margin: 0;">No medical records match the current filters.</p></td>`;
            tableBody.appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

function resetTableFilters() {
    const inputs = ['filterPatient', 'filterDiagnosis', 'filterDate'];
    inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    filterTable();
}

function toggleTableFilters() {
    const filterBar = document.getElementById('tableFilterBar');
    const toggleBtn = document.getElementById('toggleFilterBtn');
    
    if (filterBar && toggleBtn) {
        if (filterBar.style.display === 'none') {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
            toggleBtn.innerHTML = '<i class="fas fa-filter"></i>';
        } else {
            filterBar.style.display = 'none';
            toggleBtn.classList.remove('active');
            toggleBtn.innerHTML = '<i class="fas fa-filter"></i>';
        }
    }
}

// Initialize filtering
document.addEventListener('DOMContentLoaded', function() {
    // Filters only apply when "Apply Filters" button is clicked
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
