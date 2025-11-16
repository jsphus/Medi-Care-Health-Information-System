<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
.settings-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.settings-header {
    margin-bottom: 2rem;
}

.settings-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.settings-subtitle {
    color: var(--text-secondary);
    font-size: 0.95rem;
}

.settings-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

.settings-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #f3f4f6;
}

.settings-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f3f4f6;
}

.settings-card-icon {
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

.settings-card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.settings-card-description {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-top: 0.25rem;
}

.profile-section {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #f3f4f6;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    font-weight: 600;
    flex-shrink: 0;
}

.profile-info {
    flex: 1;
}

.profile-name {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.profile-email {
    color: var(--text-secondary);
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
}

.profile-role {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    background: #8b5cf620;
    color: #8b5cf6;
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

.form-control-modern:disabled {
    background: #f9fafb;
    color: var(--text-secondary);
    cursor: not-allowed;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: 0.3s;
    border-radius: 28px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: #3b82f6;
}

input:checked + .toggle-slider:before {
    transform: translateX(24px);
}

.toggle-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.toggle-container:last-child {
    border-bottom: none;
}

.toggle-label {
    flex: 1;
}

.toggle-title {
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.toggle-description {
    font-size: 0.875rem;
    color: var(--text-secondary);
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

@media (max-width: 768px) {
    .settings-container {
        padding: 1rem;
    }
    
    .profile-section {
        flex-direction: column;
        text-align: center;
    }
    
    .settings-card {
        padding: 1.5rem;
    }
}
</style>

<div class="settings-container">
    <div class="settings-header">
        <h1 class="settings-title">Settings</h1>
        <p class="settings-subtitle">Manage your account settings and preferences</p>
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

    <div class="settings-grid">
        <!-- Profile & Account Information -->
        <?php if (!empty($patient)): ?>
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h2 class="settings-card-title">Profile & Account Information</h2>
                    <p class="settings-card-description">Your account details and profile information</p>
                </div>
            </div>

            <div class="profile-section">
                <div class="profile-avatar" id="profileAvatar" style="position: relative; cursor: pointer; overflow: hidden;">
                    <?php if (!empty($profile_picture_url)): ?>
                        <img src="<?= htmlspecialchars($profile_picture_url) ?>" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <?= strtoupper(substr($patient['pat_first_name'] ?? 'P', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <div class="profile-name"><?= htmlspecialchars(($patient['pat_first_name'] ?? '') . ' ' . ($patient['pat_last_name'] ?? '')) ?></div>
                    <div class="profile-email"><?= htmlspecialchars($patient['pat_email'] ?? '') ?></div>
                    <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: 0.5rem; flex-wrap: wrap;">
                        <span class="profile-role">Patient</span>
                        <?php if (!empty($patient['pat_phone'])): ?>
                            <span style="color: var(--text-secondary); font-size: 0.875rem;">• <?= htmlspecialchars($patient['pat_phone']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Profile Picture Upload Section -->
            <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f9fafb; border-radius: 8px;">
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">Profile Picture</h3>
                <form method="POST" enctype="multipart/form-data" id="profilePictureForm">
                    <input type="hidden" name="action" value="update_profile_picture">
                    <div style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 200px;">
                            <label class="form-label-modern">Upload New Picture</label>
                            <input type="file" name="profile_picture" id="profilePictureInput" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="form-control-modern" style="padding: 0.5rem;">
                            <small style="color: var(--text-secondary); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Max 5MB. Formats: JPG, PNG, GIF, WEBP</small>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="submit" class="btn-save" style="padding: 0.75rem 1.5rem;">
                                <i class="fas fa-upload"></i>
                                <span>Upload</span>
                            </button>
                            <?php if (!empty($profile_picture_url)): ?>
                                <button type="button" onclick="deleteProfilePicture()" class="btn-save" style="padding: 0.75rem 1.5rem; background: #ef4444;">
                                    <i class="fas fa-trash"></i>
                                    <span>Remove</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div id="imagePreview" style="margin-top: 1rem; display: none;">
                        <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #e5e7eb;">
                    </div>
                </form>
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
                <div class="form-group-modern">
                    <label class="form-label-modern">Role</label>
                    <input type="text" value="Patient" disabled class="form-control-modern">
                </div>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Change Password -->
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fas fa-key"></i>
                </div>
                <div>
                    <h2 class="settings-card-title">Change Password</h2>
                    <p class="settings-card-description">Update your password to keep your account secure</p>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="change_password">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        Current Password <span class="required">*</span>
                    </label>
                    <input type="password" name="current_password" required class="form-control-modern" placeholder="Enter your current password">
                </div>
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        New Password <span class="required">*</span>
                    </label>
                    <input type="password" name="new_password" required class="form-control-modern" minlength="6" placeholder="Enter new password (min. 6 characters)">
                </div>
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        Confirm New Password <span class="required">*</span>
                    </label>
                    <input type="password" name="confirm_password" required class="form-control-modern" minlength="6" placeholder="Confirm your new password">
                </div>
                <button type="submit" class="btn-save">
                    <i class="fas fa-key"></i>
                    <span>Change Password</span>
                </button>
            </form>
        </div>

        <!-- Notifications -->
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <h2 class="settings-card-title">Notifications</h2>
                    <p class="settings-card-description">Manage how you receive notifications</p>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="update_settings">
                <div class="toggle-container">
                    <div class="toggle-label">
                        <div class="toggle-title">Appointment Reminders</div>
                        <div class="toggle-description">Receive reminders about upcoming appointments</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="notifications" value="1" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="toggle-container">
                    <div class="toggle-label">
                        <div class="toggle-title">Appointment Updates</div>
                        <div class="toggle-description">Receive notifications when appointments are rescheduled or cancelled</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_notifications" value="1" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f3f4f6;">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i>
                        <span>Save Settings</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Display Preferences -->
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <i class="fas fa-palette"></i>
                </div>
                <div>
                    <h2 class="settings-card-title">Display Preferences</h2>
                    <p class="settings-card-description">Customize your interface preferences</p>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="update_settings">
                <div class="form-group-modern">
                    <label class="form-label-modern">Language</label>
                    <select name="language" class="form-control-modern">
                        <option value="en" selected>English</option>
                        <option value="es">Spanish</option>
                        <option value="fr">French</option>
                    </select>
                </div>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i>
                    <span>Save Preferences</span>
                </button>
            </form>
        </div>
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
    
    // Profile picture preview
    const profilePictureInput = document.getElementById('profilePictureInput');
    if (profilePictureInput) {
        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    const previewImg = document.getElementById('previewImg');
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

function deleteProfilePicture() {
    if (confirm('Are you sure you want to remove your profile picture?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="action" value="delete_profile_picture">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
