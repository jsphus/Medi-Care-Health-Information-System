<?php require_once __DIR__ . '/../partials/header.php'; ?>

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
    background: #3b82f620;
    color: #3b82f6;
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
        <?php if (!empty($user)): ?>
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
                        <?= strtoupper(substr($user['user_email'] ?? 'A', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <div class="profile-name"><?= htmlspecialchars(explode('@', $user['user_email'] ?? 'Admin')[0]) ?></div>
                    <div class="profile-email"><?= htmlspecialchars($user['user_email'] ?? '') ?></div>
                    <span class="profile-role">Super Admin</span>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="update_profile">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        Email Address <span class="required">*</span>
                    </label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['user_email'] ?? '') ?>" required class="form-control-modern">
                </div>
                <div class="form-group-modern">
                    <label class="form-label-modern">Role</label>
                    <input type="text" value="Super Admin" disabled class="form-control-modern">
                </div>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
            </form>
        </div>
        <?php endif; ?>

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

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
