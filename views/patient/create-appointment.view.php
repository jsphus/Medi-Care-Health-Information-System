<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div style="max-width: 900px; margin: 0 auto; padding: 20px;">
    <h1>Book New Appointment</h1>
    <p><a href="/patient/appointments" class="btn">← Back to My Appointments</a></p>
    
    <?php if ($error): ?>
        <div style="background: #fee; color: #c33; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div style="background: #efe; color: #3c3; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #28a745;">
            <?= $success ?>
            <div style="margin-top: 15px;">
                <a href="/patient/appointments" class="btn btn-success">View My Appointments</a>
                <a href="/patient/appointments/create" class="btn">Book Another</a>
            </div>
        </div>
    <?php endif; ?>
    
    <div style="background: #fff; padding: 30px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2>Appointment Details</h2>
        <p style="color: #666; margin-bottom: 20px;">Please fill in the details below to book your appointment. You will receive an appointment ID for your reference.</p>
        
        <form method="POST">
            <div class="form-group">
                <label>Select Doctor: *</label>
                <select name="doctor_id" id="doctor_id" required onchange="showDoctorInfo()">
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
            </div>
            
            <div id="doctorInfo" style="display: none; background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0;">
                <h4 style="margin: 0 0 10px 0;">Doctor Information</h4>
                <p style="margin: 5px 0;"><strong>Specialization:</strong> <span id="docSpec"></span></p>
                <p style="margin: 5px 0;"><strong>Consultation Fee:</strong> ₱<span id="docFee"></span></p>
                <p style="margin: 5px 0;" id="docBioContainer"><strong>About:</strong> <span id="docBio"></span></p>
            </div>
            
            <div class="form-group">
                <label>Service (Optional):</label>
                <select name="service_id">
                    <option value="">Select a service...</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['service_id'] ?>">
                            <?= htmlspecialchars($service['service_name']) ?> 
                            - ₱<?= number_format($service['service_price'] ?? 0, 2) ?>
                            (<?= $service['service_duration_minutes'] ?? 30 ?> min)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Appointment Date: *</label>
                    <input type="date" name="appointment_date" min="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Preferred Time: *</label>
                    <input type="time" name="appointment_time" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Notes/Reason for Visit:</label>
                <textarea name="notes" rows="4" placeholder="Please describe your symptoms or reason for visit..."></textarea>
            </div>
            
            <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <p style="margin: 0; color: #856404; font-size: 14px;">
                    <strong>Note:</strong> Your appointment request will be reviewed. You will receive an appointment ID immediately after submission. Please keep this ID for your reference.
                </p>
            </div>
            
            <button type="submit" class="btn btn-success" style="font-size: 16px; padding: 12px 30px;">
                Book Appointment
            </button>
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
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
