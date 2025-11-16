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
                        <div class="toggle-description">Receive reminders about appointments and important updates</div>
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

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
