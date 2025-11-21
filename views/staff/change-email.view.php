<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
.account-container {
    max-width: 600px;
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
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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

.otp-input {
    font-size: 1.5rem;
    text-align: center;
    letter-spacing: 0.5rem;
    font-weight: 600;
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

.info-box {
    background: #eff6ff;
    border: 1px solid #3b82f6;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
    color: #1e40af;
}

.current-email-display {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.current-email-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}

.current-email-value {
    font-size: 1rem;
    font-weight: 500;
    color: var(--text-primary);
}

.new-email-display {
    background: #f0fdf4;
    border: 1px solid #10b981;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.new-email-label {
    font-size: 0.875rem;
    color: #059669;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.new-email-value {
    font-size: 1rem;
    font-weight: 600;
    color: #065f46;
}

.otp-hint {
    text-align: center;
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-top: 0.5rem;
}
</style>

<div class="account-container">
    <div class="account-header">
        <div>
            <h1 class="account-title">Change Email Address</h1>
            <p class="account-subtitle"><?= $step === 'confirm' ? 'Verify your new email address' : 'Update your email address with OTP verification' ?></p>
        </div>
        <a href="/staff/account" class="btn-cancel">
            <i class="fas fa-arrow-left"></i>
            <span>Back</span>
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

    <div class="account-card">
        <?php if ($step === 'request'): ?>
            <!-- Step 1: Request Email Change -->
            <div class="account-card-header">
                <div class="account-card-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <h2 class="account-card-title">Enter New Email Address</h2>
                    <p class="account-card-description">We'll send you an OTP to verify your new email</p>
                </div>
            </div>

            <div class="current-email-display">
                <div class="current-email-label">Current Email Address</div>
                <div class="current-email-value"><?= htmlspecialchars($staff['staff_email'] ?? 'N/A') ?></div>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="request_email_change">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        New Email Address <span class="required">*</span>
                    </label>
                    <input type="email" name="new_email" required class="form-control-modern" placeholder="Enter your new email address">
                </div>
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <span>You will receive an OTP code to verify your new email address. The OTP will be valid for 10 minutes.</span>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-arrow-right"></i>
                        <span>Continue</span>
                    </button>
                    <a href="/staff/account" class="btn-cancel">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </a>
                </div>
            </form>

        <?php elseif ($step === 'confirm'): ?>
            <!-- Step 2: OTP Confirmation -->
            <div class="account-card-header">
                <div class="account-card-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <h2 class="account-card-title">Verify OTP</h2>
                    <p class="account-card-description">Enter the OTP code to confirm your email change</p>
                </div>
            </div>

            <div class="current-email-display">
                <div class="current-email-label">Current Email Address</div>
                <div class="current-email-value"><?= htmlspecialchars($staff['staff_email'] ?? 'N/A') ?></div>
            </div>

            <div class="new-email-display">
                <div class="new-email-label">New Email Address</div>
                <div class="new-email-value"><?= htmlspecialchars($new_email ?? 'N/A') ?></div>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="verify_otp">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        Enter OTP Code <span class="required">*</span>
                    </label>
                    <input type="text" name="otp" required class="form-control-modern otp-input" placeholder="000000" maxlength="6" pattern="[0-9]{6}" autocomplete="off">
                    <div class="otp-hint">Enter the 6-digit OTP code</div>
                </div>
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <span>For testing purposes, your OTP is: <strong><?= htmlspecialchars($otp ?? 'N/A') ?></strong></span>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-check"></i>
                        <span>Verify & Update Email</span>
                    </button>
                    <a href="/staff/change-email" class="btn-cancel">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format OTP input
    const otpInput = document.querySelector('input[name="otp"]');
    if (otpInput) {
        otpInput.addEventListener('input', function(e) {
            // Only allow numbers
            e.target.value = e.target.value.replace(/\D/g, '');
            // Limit to 6 digits
            if (e.target.value.length > 6) {
                e.target.value = e.target.value.substring(0, 6);
            }
        });
        
        // Focus on OTP input when page loads
        otpInput.focus();
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

