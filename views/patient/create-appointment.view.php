<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-top">
        <div class="breadcrumbs">
            <?php if ($selected_doctor_id): ?>
                <a href="/patient/book">
                    <i class="fas fa-book"></i>
                    <span>Book</span>
                </a>
                <i class="fas fa-chevron-right"></i>
                <span>Book Appointment</span>
            <?php else: ?>
                <a href="/patient/appointments">
                    <i class="fas fa-calendar"></i>
                    <span>My Appointments</span>
                </a>
                <i class="fas fa-chevron-right"></i>
                <span>Book Appointment</span>
            <?php endif; ?>
        </div>
        <h1 class="page-title">
            <?php if ($reschedule_id && $existing_appointment): ?>
                Reschedule Appointment
            <?php elseif ($selected_doctor_id): ?>
                Book Appointment
            <?php else: ?>
                Book New Appointment
            <?php endif; ?>
        </h1>
    </div>
</div>

<?php if ($reschedule_id && $existing_appointment): ?>
    <div class="alert" style="background: #dbeafe; border-left: 4px solid #3b82f6; padding: 1rem; margin-bottom: 1.5rem; border-radius: 0.5rem;">
        <i class="fas fa-info-circle" style="color: #3b82f6;"></i>
        <span style="color: #1e40af;">
            <strong>Rescheduling:</strong> You are rescheduling your appointment with 
            Dr. <?= htmlspecialchars($existing_appointment['doc_first_name'] . ' ' . $existing_appointment['doc_last_name']) ?> 
            scheduled for <?= date('M j, Y', strtotime($existing_appointment['appointment_date'])) ?> 
            at <?= date('g:i A', strtotime($existing_appointment['appointment_time'])) ?>.
        </span>
    </div>
<?php endif; ?>

<?php if (isset($error) && $error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<?php if (isset($success) && $success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <div>
            <p style="margin-bottom: 1rem;"><?= $success ?></p>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="/patient/appointments" class="btn btn-success">
                    <i class="fas fa-calendar"></i>
                    <span>View My Appointments</span>
                </a>
                <a href="/patient/appointments/create" class="btn btn-secondary">
                    <i class="fas fa-plus"></i>
                    <span>Book Another</span>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Appointment Details</h2>
    </div>
    <div class="card-body">
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
            <?php if ($reschedule_id && $existing_appointment): ?>
                Please select a new date and time for your appointment. You can also change the doctor or service if needed.
            <?php else: ?>
                Please fill in the details below to book your appointment. You will receive an appointment ID for your reference.
            <?php endif; ?>
        </p>
        
        <form method="POST">
            <?php if ($reschedule_id && $existing_appointment): ?>
                <input type="hidden" name="reschedule_id" value="<?= htmlspecialchars($reschedule_id) ?>">
                <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($existing_appointment['doc_id']) ?>">
                <input type="hidden" name="service_id" value="<?= htmlspecialchars($existing_appointment['service_id'] ?? '') ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Select Doctor: <span style="color: var(--status-error);">*</span></label>
                <?php if ($reschedule_id && $existing_appointment): ?>
                    <input type="text" 
                           value="Dr. <?= htmlspecialchars($existing_appointment['doc_first_name'] . ' ' . $existing_appointment['doc_last_name']) ?> - <?= htmlspecialchars($existing_appointment['spec_name'] ?? 'General') ?>" 
                           class="form-control" 
                           disabled 
                           style="background-color: #f3f4f6; cursor: not-allowed;">
                    <small style="color: #6b7280; display: block; margin-top: 0.25rem;">
                        <i class="fas fa-info-circle"></i> Doctor cannot be changed when rescheduling
                    </small>
                <?php elseif ($selected_doctor_id): ?>
                    <?php
                    $selected_doctor = null;
                    foreach ($doctors as $doc) {
                        if ($doc['doc_id'] == $selected_doctor_id) {
                            $selected_doctor = $doc;
                            break;
                        }
                    }
                    ?>
                    <?php if ($selected_doctor): ?>
                        <input type="hidden" name="doctor_id" value="<?= $selected_doctor_id ?>">
                        <input type="text" 
                               value="Dr. <?= htmlspecialchars($selected_doctor['doc_first_name'] . ' ' . $selected_doctor['doc_last_name']) ?> - <?= htmlspecialchars($selected_doctor['spec_name'] ?? 'General') ?>" 
                               class="form-control" 
                               disabled 
                               style="background-color: #f3f4f6; cursor: not-allowed;">
                        <small style="color: #6b7280; display: block; margin-top: 0.25rem;">
                            <i class="fas fa-info-circle"></i> Doctor selected from directory
                        </small>
                    <?php else: ?>
                        <select name="doctor_id" id="doctor_id" required onchange="showDoctorInfo()" class="form-control">
                            <option value="">Choose a doctor...</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= $doctor['doc_id'] ?>" 
                                        data-spec="<?= htmlspecialchars($doctor['spec_name'] ?? 'General') ?>"
                                        data-fee="<?= number_format($doctor['doc_consultation_fee'] ?? 0, 2) ?>"
                                        data-bio="<?= htmlspecialchars($doctor['doc_bio'] ?? '') ?>">
                                    Dr. <?= htmlspecialchars($doctor['doc_first_name'] . ' ' . $doctor['doc_last_name']) ?> 
                                    - <?= htmlspecialchars($doctor['spec_name'] ?? 'General') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                <?php else: ?>
                    <select name="doctor_id" id="doctor_id" required onchange="showDoctorInfo()" class="form-control">
                        <option value="">Choose a doctor...</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= $doctor['doc_id'] ?>" 
                                    data-spec="<?= htmlspecialchars($doctor['spec_name'] ?? 'General') ?>"
                                    data-fee="<?= number_format($doctor['doc_consultation_fee'] ?? 0, 2) ?>"
                                    data-bio="<?= htmlspecialchars($doctor['doc_bio'] ?? '') ?>">
                                Dr. <?= htmlspecialchars($doctor['doc_first_name'] . ' ' . $doctor['doc_last_name']) ?> 
                                - <?= htmlspecialchars($doctor['spec_name'] ?? 'General') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
            
            <div id="doctorInfo" style="display: none; background: var(--primary-blue-bg); padding: 1rem; border-radius: var(--radius-md); margin: 1rem 0; border-left: 4px solid var(--primary-blue);">
                <h4 style="margin: 0 0 0.75rem 0; color: var(--text-primary);">Doctor Information</h4>
                <p style="margin: 0.5rem 0; color: var(--text-primary);">
                    <strong>Specialization:</strong> <span id="docSpec"></span>
                </p>
                <p style="margin: 0.5rem 0; color: var(--text-primary);">
                    <strong>Consultation Fee:</strong> ₱<span id="docFee"></span>
                </p>
                <p style="margin: 0.5rem 0; color: var(--text-primary);" id="docBioContainer">
                    <strong>About:</strong> <span id="docBio"></span>
                </p>
            </div>
            
            <div class="form-group">
                <label>Service (Optional):</label>
                <?php if ($reschedule_id && $existing_appointment): ?>
                    <?php
                    $service_name = 'None';
                    if ($existing_appointment['service_id']) {
                        foreach ($services as $service) {
                            if ($service['service_id'] == $existing_appointment['service_id']) {
                                $service_name = htmlspecialchars($service['service_name']) . ' - ₱' . number_format($service['service_price'] ?? 0, 2) . ' (' . ($service['service_duration_minutes'] ?? 30) . ' min)';
                                break;
                            }
                        }
                    }
                    ?>
                    <input type="text" 
                           value="<?= $service_name ?>" 
                           class="form-control" 
                           disabled 
                           style="background-color: #f3f4f6; cursor: not-allowed;">
                    <small style="color: #6b7280; display: block; margin-top: 0.25rem;">
                        <i class="fas fa-info-circle"></i> Service cannot be changed when rescheduling
                    </small>
                <?php else: ?>
                    <select name="service_id" class="form-control">
                        <option value="">Select a service...</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= $service['service_id'] ?>">
                                <?= htmlspecialchars($service['service_name']) ?> 
                                - ₱<?= number_format($service['service_price'] ?? 0, 2) ?>
                                (<?= $service['service_duration_minutes'] ?? 30 ?> min)
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Appointment Date: <span style="color: var(--status-error);">*</span></label>
                    <input type="date" name="appointment_date" 
                           value="<?= $existing_appointment ? htmlspecialchars($existing_appointment['appointment_date']) : '' ?>"
                           min="<?= date('Y-m-d') ?>" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Preferred Time: <span style="color: var(--status-error);">*</span></label>
                    <input type="time" name="appointment_time" 
                           value="<?= $existing_appointment ? date('H:i', strtotime($existing_appointment['appointment_time'])) : '' ?>"
                           required class="form-control">
                </div>
            </div>
            
            <div class="form-group form-grid-full">
                <label>Notes/Reason for Visit:</label>
                <textarea name="notes" rows="4" placeholder="Please describe your symptoms or reason for visit..." class="form-control"><?= $existing_appointment ? htmlspecialchars($existing_appointment['appointment_notes'] ?? '') : '' ?></textarea>
            </div>
            
            <div class="info-box" style="margin-top: 1.5rem;">
                <i class="fas fa-info-circle"></i>
                <p><strong>Note:</strong> Your appointment request will be reviewed. You will receive an appointment ID immediately after submission. Please keep this ID for your reference.</p>
            </div>
            
            <input type="hidden" name="action" value="review">
            
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success" style="font-size: 1rem; padding: 0.75rem 2rem;">
                    <i class="fas fa-arrow-right"></i>
                    <span><?= $reschedule_id && $existing_appointment ? 'Review Reschedule' : 'Review Appointment' ?></span>
                </button>
                <?php if ($reschedule_id && $existing_appointment): ?>
                <a href="/patient/appointments" class="btn btn-secondary" style="font-size: 1rem; padding: 0.75rem 2rem; margin-left: 0.5rem;">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
function showDoctorInfo() {
    const select = document.getElementById('doctor_id');
    const option = select.options[select.selectedIndex];
    
    if (option.value) {
        document.getElementById('docSpec').textContent = option.dataset.spec;
        document.getElementById('docFee').textContent = option.dataset.fee;
        
        const bio = option.dataset.bio;
        if (bio) {
            document.getElementById('docBio').textContent = bio;
            document.getElementById('docBioContainer').style.display = 'block';
        } else {
            document.getElementById('docBioContainer').style.display = 'none';
        }
        
        document.getElementById('doctorInfo').style.display = 'block';
    } else {
        document.getElementById('doctorInfo').style.display = 'none';
    }
}

// Show doctor info on page load if doctor is already selected (for rescheduling or from directory)
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('doctor_id');
    if (select && select.value) {
        showDoctorInfo();
    }
    
    // If doctor is pre-selected from directory, show their info
    <?php if ($selected_doctor_id && $selected_doctor): ?>
    document.getElementById('doctorInfo').style.display = 'block';
    document.getElementById('docSpec').textContent = '<?= htmlspecialchars($selected_doctor['spec_name'] ?? 'General') ?>';
    document.getElementById('docFee').textContent = '<?= number_format($selected_doctor['doc_consultation_fee'] ?? 0, 2) ?>';
    <?php if ($selected_doctor['doc_bio']): ?>
    document.getElementById('docBio').textContent = '<?= htmlspecialchars(addslashes($selected_doctor['doc_bio'])) ?>';
    document.getElementById('docBioContainer').style.display = 'block';
    <?php else: ?>
    document.getElementById('docBioContainer').style.display = 'none';
    <?php endif; ?>
    <?php endif; ?>
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
