<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
.success-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 2rem;
    text-align: center;
}

.success-card {
    background: white;
    border-radius: 12px;
    padding: 3rem 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #f3f4f6;
}

.success-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    margin: 0 auto 1.5rem;
}

.success-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
}

.success-message {
    font-size: 1rem;
    color: var(--text-secondary);
    margin-bottom: 2rem;
    line-height: 1.6;
}

.email-display {
    background: #f0fdf4;
    border: 1px solid #10b981;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 2rem;
    display: inline-block;
}

.email-label {
    font-size: 0.875rem;
    color: #059669;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.email-value {
    font-size: 1.125rem;
    font-weight: 600;
    color: #065f46;
}

.btn-primary {
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
    text-decoration: none;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.info-note {
    background: #eff6ff;
    border: 1px solid #3b82f6;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 2rem;
    font-size: 0.875rem;
    color: #1e40af;
    text-align: left;
}
</style>

<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1 class="success-title">Email Address Changed Successfully!</h1>
        
        <p class="success-message">
            Your email address has been successfully updated. You can now use your new email address to log in and receive notifications.
        </p>

        <?php
        // Fetch updated patient data to show new email
        try {
            require_once __DIR__ . '/../../classes/Auth.php';
            require_once __DIR__ . '/../../classes/Patient.php';
            
            $auth = new Auth();
            $auth->requirePatient();
            $patient_id = $auth->getPatientId();
            
            if ($patient_id) {
                $patientModel = new Patient();
                $patient = $patientModel->getById($patient_id);
                $new_email = $patient['pat_email'] ?? null;
            } else {
                $new_email = null;
            }
        } catch (Exception $e) {
            $new_email = null;
        }
        ?>

        <?php if ($new_email): ?>
            <div class="email-display">
                <div class="email-label">Your New Email Address</div>
                <div class="email-value"><?= htmlspecialchars($new_email) ?></div>
            </div>
        <?php endif; ?>

        <a href="/patient/account" class="btn-primary">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Account</span>
        </a>

        <div class="info-note">
            <i class="fas fa-info-circle"></i>
            <strong>Note:</strong> Please make sure to update your email address in any external services or applications that may be using your old email address.
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

