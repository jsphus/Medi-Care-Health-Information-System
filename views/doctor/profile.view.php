<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-top">
        <h1 class="page-title">My Profile</h1>
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

<?php if (!empty($doctor)): ?>
    <!-- Update Profile Form -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Update Profile Information</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name: <span style="color: var(--status-error);">*</span></label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($doctor['doc_first_name']) ?>" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Middle Initial:</label>
                        <input type="text" name="middle_initial" value="<?= htmlspecialchars($doctor['doc_middle_initial'] ?? '') ?>" maxlength="1" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Last Name: <span style="color: var(--status-error);">*</span></label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($doctor['doc_last_name']) ?>" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Email: <span style="color: var(--status-error);">*</span></label>
                        <input type="email" name="email" value="<?= htmlspecialchars($doctor['doc_email']) ?>" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Phone:</label>
                        <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($doctor['doc_phone'] ?? '') ?>" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Specialization:</label>
                        <select name="specialization_id" class="form-control">
                            <option value="">Select Specialization</option>
                            <?php foreach ($specializations as $spec): ?>
                                <option value="<?= $spec['spec_id'] ?>" <?= $doctor['doc_specialization_id'] == $spec['spec_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($spec['spec_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>License Number:</label>
                        <input type="text" name="license_number" value="<?= htmlspecialchars($doctor['doc_license_number'] ?? '') ?>" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Experience (Years):</label>
                        <input type="number" name="experience_years" min="0" value="<?= htmlspecialchars($doctor['doc_experience_years'] ?? '') ?>" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Consultation Fee:</label>
                        <input type="number" name="consultation_fee" step="0.01" min="0" value="<?= htmlspecialchars($doctor['doc_consultation_fee'] ?? '') ?>" class="form-control">
                    </div>
                </div>
                
                <div class="form-group form-grid-full">
                    <label>Qualification:</label>
                    <textarea name="qualification" rows="3" class="form-control"><?= htmlspecialchars($doctor['doc_qualification'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group form-grid-full">
                    <label>Bio:</label>
                    <textarea name="bio" rows="5" class="form-control"><?= htmlspecialchars($doctor['doc_bio'] ?? '') ?></textarea>
                </div>
                
                <div class="action-buttons" style="margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        <span>Update Profile</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Profile Summary -->
    <div class="card" style="border-left: 4px solid var(--primary-blue);">
        <div class="card-header">
            <h2 class="card-title">Current Profile Summary</h2>
        </div>
        <div class="card-body">
            <div class="form-grid">
                <div>
                    <p style="margin: 0.5rem 0;"><strong>Name:</strong> Dr. <?= htmlspecialchars($doctor['doc_first_name'] . ' ' . $doctor['doc_last_name']) ?></p>
                    <p style="margin: 0.5rem 0;"><strong>Specialization:</strong> <?= htmlspecialchars($doctor['spec_name'] ?? 'Not specified') ?></p>
                </div>
                <div>
                    <p style="margin: 0.5rem 0;"><strong>License:</strong> <?= htmlspecialchars($doctor['doc_license_number'] ?? 'Not specified') ?></p>
                    <p style="margin: 0.5rem 0;"><strong>Experience:</strong> <?= htmlspecialchars($doctor['doc_experience_years'] ?? '0') ?> years</p>
                </div>
                <div>
                    <p style="margin: 0.5rem 0;"><strong>Consultation Fee:</strong> <strong style="color: var(--status-success);">₱<?= number_format($doctor['doc_consultation_fee'] ?? 0, 2) ?></strong></p>
                    <p style="margin: 0.5rem 0;"><strong>Status:</strong> 
                        <span class="status-badge <?= ($doctor['doc_status'] ?? 'active') === 'active' ? 'active' : 'inactive' ?>">
                            <?= ucfirst($doctor['doc_status'] ?? 'active') ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

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

document.addEventListener('DOMContentLoaded', function() {
    formatPhoneInput('phone');
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
