<?php 
// Make a local copy to prevent any modification
$view_staff = isset($staff) && is_array($staff) ? $staff : [];

require_once __DIR__ . '/../partials/header.php'; 
?>

<style>
.account-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.account-header {
    margin-bottom: 2rem;
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

.account-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
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

.account-card-description {
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
    background: #10b98120;
    color: #10b981;
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
    .account-container {
        padding: 1rem;
    }
    
    .profile-section {
        flex-direction: column;
        text-align: center;
    }
    
    .account-card {
        padding: 1.5rem;
    }
}
</style>

<div class="account-container">
    <div class="account-header">
        <h1 class="account-title">My Account</h1>
        <p class="account-subtitle">Manage your account information and security</p>
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

    <div class="account-grid">
        <!-- Profile & Account Information -->
        <?php 
        // Use view_staff if available, otherwise fall back to staff
        // Don't overwrite $display_staff if it's already set by controller
        if (!isset($display_staff) || empty($display_staff)) {
            $display_staff = !empty($view_staff) ? $view_staff : ($staff ?? []);
        }
        if (isset($display_staff) && is_array($display_staff) && !empty($display_staff)): 
        ?>
        <div class="account-card">
            <div class="account-card-header">
                <div class="account-card-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h2 class="account-card-title">Profile & Account Information</h2>
                    <p class="account-card-description">Your account details and profile information</p>
                </div>
            </div>

            <div class="profile-section">
                <div class="profile-avatar" style="overflow: hidden;">
                    <?php if (!empty($profile_picture_url)): ?>
                        <img src="<?= htmlspecialchars($profile_picture_url) ?>" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <?= strtoupper(substr($display_staff['staff_first_name'] ?? 'S', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <div class="profile-name"><?= htmlspecialchars(($display_staff['staff_first_name'] ?? '') . ' ' . ($display_staff['staff_last_name'] ?? '')) ?></div>
                    <div class="profile-email"><?= htmlspecialchars($display_staff['staff_email'] ?? '') ?></div>
                    <div style="display: flex; gap: 0.5rem; align-items: center; margin-top: 0.5rem;">
                        <span class="profile-role">Staff</span>
                        <?php if (!empty($display_staff['staff_phone'])): ?>
                            <span style="color: var(--text-secondary); font-size: 0.875rem;">• <?= htmlspecialchars($display_staff['staff_phone']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Account Details (Read-Only) -->
            <div style="margin-bottom: 2rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">First Name</div>
                        <div style="font-size: 1rem; font-weight: 500; color: var(--text-primary);">
                            <?php 
                            $first_name = $display_staff['staff_first_name'] ?? null;
                            echo htmlspecialchars($first_name !== null && $first_name !== '' ? $first_name : 'N/A');
                            ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Middle Initial</div>
                        <div style="font-size: 1rem; font-weight: 500; color: var(--text-primary);">
                            <?php 
                            $middle = $display_staff['staff_middle_initial'] ?? null;
                            echo htmlspecialchars(($middle !== null && $middle !== '') ? $middle : 'N/A');
                            ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Last Name</div>
                        <div style="font-size: 1rem; font-weight: 500; color: var(--text-primary);">
                            <?php 
                            $last_name = $display_staff['staff_last_name'] ?? null;
                            echo htmlspecialchars(($last_name !== null && $last_name !== '') ? $last_name : 'N/A');
                            ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Email Address</div>
                        <div style="font-size: 1rem; font-weight: 500; color: var(--text-primary);">
                            <?php 
                            $email = $display_staff['staff_email'] ?? null;
                            echo htmlspecialchars(($email !== null && $email !== '') ? $email : 'N/A');
                            ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Phone Number</div>
                        <div style="font-size: 1rem; font-weight: 500; color: var(--text-primary);">
                            <?php 
                            $phone = $display_staff['staff_phone'] ?? null;
                            echo htmlspecialchars(($phone !== null && $phone !== '') ? $phone : 'N/A');
                            ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Position</div>
                        <div style="font-size: 1rem; font-weight: 500; color: var(--text-primary);">
                            <?php 
                            $position = $display_staff['staff_position'] ?? null;
                            echo htmlspecialchars(($position !== null && $position !== '') ? $position : 'N/A');
                            ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Role</div>
                        <div style="font-size: 1rem; font-weight: 500; color: var(--text-primary);">Staff</div>
                    </div>
                </div>
                
                <button type="button" onclick="openEditProfileModal()" class="btn-save" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-edit"></i>
                    <span>Edit Profile</span>
                </button>
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
        </div>
        <?php endif; ?>

        <!-- Change Email Address -->
        <div class="account-card">
            <div class="account-card-header">
                <div class="account-card-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <h2 class="account-card-title">Change Email Address</h2>
                    <p class="account-card-description">Update your email address with OTP verification</p>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Current Email Address</div>
                <div style="font-size: 1rem; font-weight: 500; color: var(--text-primary);">
                    <?php 
                    $email = $display_staff['staff_email'] ?? null;
                    echo htmlspecialchars(($email !== null && $email !== '') ? $email : 'N/A');
                    ?>
                </div>
            </div>

            <button type="button" onclick="openChangeEmailModal()" class="btn-save" style="display: inline-flex;">
                <i class="fas fa-envelope"></i>
                <span>Change Email Address</span>
            </button>
        </div>

        <!-- Change Password -->
        <div class="account-card">
            <div class="account-card-header">
                <div class="account-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fas fa-key"></i>
                </div>
                <div>
                    <h2 class="account-card-title">Change Password</h2>
                    <p class="account-card-description">Update your password to keep your account secure</p>
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

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal">
    <div class="modal-content" style="max-width: 700px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h2 class="modal-title">Edit Profile</h2>
            <button type="button" class="modal-close" onclick="closeEditProfileModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" id="editProfileForm">
            <input type="hidden" name="action" value="update_profile">
            <div class="form-group-modern">
                <label class="form-label-modern">
                    First Name <span class="required">*</span>
                </label>
                <input type="text" name="first_name" id="edit_first_name" value="<?= htmlspecialchars($display_staff['staff_first_name'] ?? '') ?>" required class="form-control-modern">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">
                    Middle Initial
                </label>
                <input type="text" name="middle_initial" id="edit_middle_initial" value="<?= htmlspecialchars(isset($display_staff['staff_middle_initial']) && $display_staff['staff_middle_initial'] !== null ? $display_staff['staff_middle_initial'] : '') ?>" maxlength="1" class="form-control-modern">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">
                    Last Name <span class="required">*</span>
                </label>
                <input type="text" name="last_name" id="edit_last_name" value="<?= htmlspecialchars($display_staff['staff_last_name'] ?? '') ?>" required class="form-control-modern">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">Phone Number</label>
                <input type="text" name="phone" id="edit_phone" value="<?= htmlspecialchars($display_staff['staff_phone'] ?? '') ?>" class="form-control-modern" placeholder="XXXX-XXX-XXXX">
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern">Position</label>
                <input type="text" name="position" id="edit_position" value="<?= htmlspecialchars($display_staff['staff_position'] ?? '') ?>" class="form-control-modern">
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
                <button type="button" onclick="closeEditProfileModal()" class="btn-save" style="background: #6b7280;">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Phone number formatting function (Philippine format: XXXX-XXX-XXXX)
function formatPhoneNumber(value) {
    const digits = value.replace(/\D/g, '');
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

function openEditProfileModal() {
    // Populate form fields from display_staff data
    <?php if (isset($display_staff) && is_array($display_staff)): ?>
    document.getElementById('edit_first_name').value = <?= json_encode($display_staff['staff_first_name'] ?? '') ?>;
    document.getElementById('edit_middle_initial').value = <?= json_encode($display_staff['staff_middle_initial'] ?? '') ?>;
    document.getElementById('edit_last_name').value = <?= json_encode($display_staff['staff_last_name'] ?? '') ?>;
    document.getElementById('edit_phone').value = <?= json_encode($display_staff['staff_phone'] ?? '') ?>;
    document.getElementById('edit_position').value = <?= json_encode($display_staff['staff_position'] ?? '') ?>;
    <?php endif; ?>
    document.getElementById('editProfileModal').classList.add('active');
    formatPhoneInput('edit_phone');
}

function closeEditProfileModal() {
    document.getElementById('editProfileModal').classList.remove('active');
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('editProfileModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeEditProfileModal();
            }
        });
    }
    
    // Format phone input on page load
    formatPhoneInput('edit_phone');
});

// Change Email Modal Functions
function openChangeEmailModal() {
    const modal = document.getElementById('changeEmailModal');
    if (modal) {
        modal.classList.add('active');
        resetChangeEmailModal();
    } else {
        console.error('Change email modal not found');
    }
}

function closeChangeEmailModal() {
    const modal = document.getElementById('changeEmailModal');
    if (modal) {
        modal.classList.remove('active');
        resetChangeEmailModal();
    }
}

function resetChangeEmailModal() {
    const step1 = document.getElementById('changeEmailStep1');
    const step2 = document.getElementById('changeEmailStep2');
    const error = document.getElementById('changeEmailError');
    const success = document.getElementById('changeEmailSuccess');
    const form1 = document.getElementById('changeEmailForm1');
    const form2 = document.getElementById('changeEmailForm2');
    const title = document.getElementById('changeEmailModalTitle');
    
    if (step1) step1.style.display = 'block';
    if (step2) step2.style.display = 'none';
    if (error) error.style.display = 'none';
    if (success) success.style.display = 'none';
    if (form1) form1.reset();
    if (form2) form2.reset();
    if (title) title.textContent = 'Change Email Address';
}

// Handle change email form submission - wait for DOM
document.addEventListener('DOMContentLoaded', function() {
    const form1 = document.getElementById('changeEmailForm1');
    const form2 = document.getElementById('changeEmailForm2');
    const otpInput = document.getElementById('otpInput');
    
    if (!form1 || !form2) {
        console.error('Change email forms not found');
        return;
    }
    
    form1.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/staff/account', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            }
            return response.text();
        })
        .then(data => {
            if (typeof data === 'object') {
                // JSON response
                if (data.success) {
                    // Success - move to step 2
                    document.getElementById('otpDisplay').textContent = data.otp;
                    document.getElementById('otpCurrentEmail').textContent = document.getElementById('currentEmailDisplay').value;
                    document.getElementById('otpNewEmail').textContent = data.new_email;
                    document.getElementById('changeEmailStep1').style.display = 'none';
                    document.getElementById('changeEmailStep2').style.display = 'block';
                    document.getElementById('changeEmailModalTitle').textContent = 'Verify OTP';
                    document.getElementById('changeEmailError').style.display = 'none';
                    document.getElementById('otpInput').focus();
                } else {
                    // Error
                    document.getElementById('changeEmailErrorText').textContent = data.error || 'An error occurred';
                    document.getElementById('changeEmailError').style.display = 'flex';
                }
            } else {
                // HTML response - check for errors
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                const errorDiv = doc.querySelector('.alert-modern.error');
                
                if (errorDiv) {
                    document.getElementById('changeEmailErrorText').textContent = errorDiv.textContent.trim();
                    document.getElementById('changeEmailError').style.display = 'flex';
                }
            }
        })
        .catch(error => {
            document.getElementById('changeEmailErrorText').textContent = 'An error occurred. Please try again.';
            document.getElementById('changeEmailError').style.display = 'flex';
        });
    });
    
    // Handle OTP verification
    if (form2) {
        form2.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('/staff/account', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }
                return response.text();
            })
            .then(data => {
                if (typeof data === 'object') {
                    // JSON response
                    if (data.success) {
                        // Success - close modal and reload page
                        closeChangeEmailModal();
                        window.location.href = '/staff/account?email_changed=1';
                    } else {
                        // Error
                        document.getElementById('changeEmailErrorText').textContent = data.error || 'An error occurred';
                        document.getElementById('changeEmailError').style.display = 'flex';
                        document.getElementById('changeEmailSuccess').style.display = 'none';
                    }
                } else {
                    // HTML response - check for errors
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data, 'text/html');
                    const errorDiv = doc.querySelector('.alert-modern.error');
                    
                    if (errorDiv) {
                        document.getElementById('changeEmailErrorText').textContent = errorDiv.textContent.trim();
                        document.getElementById('changeEmailError').style.display = 'flex';
                        document.getElementById('changeEmailSuccess').style.display = 'none';
                    } else {
                        // Success - reload page
                        closeChangeEmailModal();
                        window.location.href = '/staff/account?email_changed=1';
                    }
                }
            })
            .catch(error => {
                document.getElementById('changeEmailErrorText').textContent = 'An error occurred. Please try again.';
                document.getElementById('changeEmailError').style.display = 'flex';
            });
        });
    }
    
    // OTP input formatting
    if (otpInput) {
        otpInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
            if (e.target.value.length > 6) {
                e.target.value = e.target.value.substring(0, 6);
            }
        });
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('changeEmailModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeChangeEmailModal();
            }
        });
    }
});
</script>

<!-- Change Email Modal -->
<div id="changeEmailModal" class="modal">
    <div class="modal-content" style="max-width: 600px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h2 class="modal-title" id="changeEmailModalTitle">Change Email Address</h2>
            <button type="button" class="modal-close" onclick="closeChangeEmailModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div id="changeEmailError" class="alert-modern error" style="display: none; margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-triangle"></i>
            <span id="changeEmailErrorText"></span>
        </div>
        
        <div id="changeEmailSuccess" class="alert-modern success" style="display: none; margin-bottom: 1.5rem;">
            <i class="fas fa-check-circle"></i>
            <span id="changeEmailSuccessText"></span>
        </div>

        <!-- Step 1: Enter New Email -->
        <div id="changeEmailStep1">
            <form method="POST" id="changeEmailForm1">
                <input type="hidden" name="action" value="request_email_change">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        Current Email Address
                    </label>
                    <input type="text" id="currentEmailDisplay" value="<?= htmlspecialchars($display_staff['staff_email'] ?? '') ?>" readonly class="form-control-modern" style="background: #f9fafb;">
                </div>
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        New Email Address <span class="required">*</span>
                    </label>
                    <input type="email" name="new_email" id="newEmailInput" required class="form-control-modern" placeholder="Enter your new email address">
                </div>
                <div style="background: #eff6ff; border: 1px solid #3b82f6; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; font-size: 0.875rem; color: #1e40af;">
                    <i class="fas fa-info-circle"></i>
                    <span>You will receive an OTP code to verify your new email address. The OTP will be valid for 10 minutes.</span>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-arrow-right"></i>
                        <span>Continue</span>
                    </button>
                    <button type="button" onclick="closeChangeEmailModal()" class="btn-save" style="background: #6b7280;">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Step 2: OTP Verification -->
        <div id="changeEmailStep2" style="display: none;">
            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Current Email Address</div>
                <div style="font-size: 1rem; font-weight: 500; color: var(--text-primary);" id="otpCurrentEmail"></div>
            </div>
            <div style="background: #f0fdf4; border: 1px solid #10b981; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
                <div style="font-size: 0.875rem; color: #059669; margin-bottom: 0.5rem; font-weight: 500;">New Email Address</div>
                <div style="font-size: 1rem; font-weight: 600; color: #065f46;" id="otpNewEmail"></div>
            </div>
            <form method="POST" id="changeEmailForm2">
                <input type="hidden" name="action" value="verify_otp">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        Enter OTP Code <span class="required">*</span>
                    </label>
                    <input type="text" name="otp" id="otpInput" required class="form-control-modern" style="font-size: 1.5rem; text-align: center; letter-spacing: 0.5rem; font-weight: 600;" placeholder="000000" maxlength="6" pattern="[0-9]{6}" autocomplete="off">
                    <div style="text-align: center; font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.5rem;">Enter the 6-digit OTP code</div>
                </div>
                <div style="background: #eff6ff; border: 1px solid #3b82f6; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; font-size: 0.875rem; color: #1e40af;">
                    <i class="fas fa-info-circle"></i>
                    <span>For testing purposes, your OTP is: <strong id="otpDisplay"></strong></span>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-check"></i>
                        <span>Verify & Update Email</span>
                    </button>
                    <button type="button" onclick="resetChangeEmailModal()" class="btn-save" style="background: #6b7280;">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

