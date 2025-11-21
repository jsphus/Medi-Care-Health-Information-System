<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
.account-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.account-header {
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.account-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.account-subtitle {
    color: var(--text-secondary);
    font-size: 0.95rem;
}

.account-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #f3f4f6;
}

.account-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f3f4f6;
}

.account-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.125rem;
}

.account-card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.form-group-modern {
    margin-bottom: 1.5rem;
}

.form-label-modern {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.form-label-modern .required {
    color: var(--status-error);
    margin-left: 0.25rem;
}

.form-control-modern {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.2s;
    background: white;
}

.form-control-modern:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn-save {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-save:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.btn-cancel {
    background: #6b7280;
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-cancel:hover {
    background: #4b5563;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.alert-modern {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-modern.success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.alert-modern.error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}
</style>

<div class="account-container">
    <div class="account-header">
        <div>
            <h1 class="account-title">Edit Profile</h1>
            <p class="account-subtitle">Update your account information</p>
        </div>
        <a href="/patient/account" class="btn-cancel">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Account</span>
        </a>
    </div>

    <?php if (isset($error) && $error): ?>
        <div class="alert-modern error">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($success) && $success): ?>
        <div class="alert-modern success">
            <i class="fas fa-check-circle"></i>
            <span><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>

    <?php 
    // Debug output
    if (!isset($patient)) {
        echo '<div style="background: #fee2e2; padding: 1rem; margin-bottom: 1rem; border-radius: 8px;">';
        echo '<strong>DEBUG:</strong> $patient is not set';
        echo '</div>';
    } elseif (!is_array($patient)) {
        echo '<div style="background: #fee2e2; padding: 1rem; margin-bottom: 1rem; border-radius: 8px;">';
        echo '<strong>DEBUG:</strong> $patient is not an array. Type: ' . gettype($patient);
        echo '</div>';
    } else {
        echo '<div style="background: #d1fae5; padding: 1rem; margin-bottom: 1rem; border-radius: 8px; font-size: 0.875rem;">';
        echo '<strong>DEBUG:</strong> Patient data available. Keys: ' . implode(', ', array_keys($patient)) . '<br>';
        echo 'Email: ' . ($patient['pat_email'] ?? 'NULL') . '<br>';
        echo 'First Name: ' . ($patient['pat_first_name'] ?? 'NULL');
        echo '</div>';
    }
    ?>
    <?php if (isset($patient) && is_array($patient)): ?>
    <div class="account-card">
        <div class="account-card-header">
            <div class="account-card-icon">
                <i class="fas fa-user-edit"></i>
            </div>
            <div>
                <h2 class="account-card-title">Profile Information</h2>
            </div>
        </div>

        <form method="POST">
            <input type="hidden" name="action" value="update_profile">
            <div class="form-group-modern">
                <label class="form-label-modern">
                    First Name <span class="required">*</span>
                </label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($patient['pat_first_name'] ?? '') ?>" required class="form-control-modern">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">
                    Middle Initial
                </label>
                <input type="text" name="middle_initial" value="<?= htmlspecialchars($patient['pat_middle_initial'] ?? '') ?>" maxlength="1" class="form-control-modern">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">
                    Last Name <span class="required">*</span>
                </label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($patient['pat_last_name'] ?? '') ?>" required class="form-control-modern">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">
                    Email Address <span class="required">*</span>
                </label>
                <input type="email" name="email" value="<?= htmlspecialchars($patient['pat_email'] ?? '') ?>" required class="form-control-modern">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">Phone Number</label>
                <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($patient['pat_phone'] ?? '') ?>" class="form-control-modern" placeholder="XXXX-XXX-XXXX">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">Date of Birth</label>
                <input type="date" name="date_of_birth" value="<?= htmlspecialchars($patient['pat_date_of_birth'] ?? '') ?>" class="form-control-modern">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">Gender</label>
                <select name="gender" class="form-control-modern">
                    <option value="">Select Gender</option>
                    <option value="Male" <?= ($patient['pat_gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($patient['pat_gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= ($patient['pat_gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">Address</label>
                <textarea name="address" rows="3" class="form-control-modern"><?= htmlspecialchars($patient['pat_address'] ?? '') ?></textarea>
            </div>
            <div style="margin-top: 2rem; margin-bottom: 1rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">Emergency Contact</h3>
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">Emergency Contact Name</label>
                <input type="text" name="emergency_contact" value="<?= htmlspecialchars($patient['pat_emergency_contact'] ?? '') ?>" class="form-control-modern">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">Emergency Contact Phone</label>
                <input type="text" name="emergency_phone" id="emergency_phone" value="<?= htmlspecialchars($patient['pat_emergency_phone'] ?? '') ?>" class="form-control-modern" placeholder="XXXX-XXX-XXXX">
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
                <a href="/patient/account" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
            </div>
        </form>
    </div>
    <?php endif; ?>
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

document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const formatted = formatPhoneNumber(e.target.value);
            if (oldValue !== formatted) {
                e.target.value = formatted;
                const newCursorPosition = cursorPosition + (formatted.length - oldValue.length);
                setTimeout(() => e.target.setSelectionRange(newCursorPosition, newCursorPosition), 0);
            }
        });
        phoneInput.addEventListener('blur', function(e) {
            if (e.target.value) e.target.value = formatPhoneNumber(e.target.value);
        });
        if (phoneInput.value) phoneInput.value = formatPhoneNumber(phoneInput.value);
    }
    
    const emergencyPhoneInput = document.getElementById('emergency_phone');
    if (emergencyPhoneInput) {
        emergencyPhoneInput.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const formatted = formatPhoneNumber(e.target.value);
            if (oldValue !== formatted) {
                e.target.value = formatted;
                const newCursorPosition = cursorPosition + (formatted.length - oldValue.length);
                setTimeout(() => e.target.setSelectionRange(newCursorPosition, newCursorPosition), 0);
            }
        });
        emergencyPhoneInput.addEventListener('blur', function(e) {
            if (e.target.value) e.target.value = formatPhoneNumber(e.target.value);
        });
        if (emergencyPhoneInput.value) emergencyPhoneInput.value = formatPhoneNumber(emergencyPhoneInput.value);
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

