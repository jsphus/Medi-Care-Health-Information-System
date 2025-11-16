<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
    .confirmation-page {
        padding: 2rem;
        max-width: 700px;
        margin: 0 auto;
        text-align: center;
    }
    
    .success-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        margin: 0 auto 1.5rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .confirmation-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .confirmation-subtitle {
        color: #6b7280;
        font-size: 1rem;
        margin-bottom: 2rem;
    }
    
    .confirmation-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
        text-align: left;
    }
    
    .info-section {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .info-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .section-title i {
        color: var(--primary-blue);
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
    }
    
    .info-label {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .info-value {
        font-size: 0.875rem;
        color: #1f2937;
        font-weight: 600;
        text-align: right;
    }
    
    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        justify-content: center;
    }
    
    .btn {
        padding: 0.875rem 2rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    
    .btn-primary {
        background: var(--primary-blue);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--primary-blue-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }
    
    .btn-secondary:hover {
        background: #e5e7eb;
    }
</style>

<div class="confirmation-page">
    <?php if ($appointment && !$error): ?>
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1 class="confirmation-title">Payment Submitted Successfully!</h1>
        <p class="confirmation-subtitle">Your appointment has been booked and payment is pending review</p>
        
        <div class="confirmation-card">
            <!-- Appointment Information -->
            <div class="info-section">
                <h3 class="section-title">
                    <i class="fas fa-calendar-check"></i>
                    Appointment Details
                </h3>
                <div class="info-row">
                    <span class="info-label">Appointment ID</span>
                    <span class="info-value"><?= htmlspecialchars($appointment['appointment_id']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Doctor</span>
                    <span class="info-value">Dr. <?= htmlspecialchars($appointment['doc_first_name'] . ' ' . $appointment['doc_last_name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date</span>
                    <span class="info-value"><?= date('l, F j, Y', strtotime($appointment['appointment_date'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Time</span>
                    <span class="info-value"><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></span>
                </div>
            </div>
            
            <!-- Payment Information -->
            <?php if ($appointment['payment_id']): ?>
            <div class="info-section">
                <h3 class="section-title">
                    <i class="fas fa-credit-card"></i>
                    Payment Information
                </h3>
                <div class="info-row">
                    <span class="info-label">Amount</span>
                    <span class="info-value">₱<?= number_format($appointment['payment_amount'], 2) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method</span>
                    <span class="info-value"><?= htmlspecialchars($appointment['method_name'] ?? 'N/A') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value" style="color: <?= $appointment['status_color'] ?? '#f59e0b' ?>;">
                        <?= htmlspecialchars($appointment['status_name'] ?? 'Pending') ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem; text-align: left;">
            <p style="margin: 0; font-size: 0.875rem; color: #92400e;">
                <i class="fas fa-info-circle"></i>
                <strong>Next Steps:</strong> Your payment is being reviewed by the clinic. You will receive a notification once it's confirmed. You can view your appointment and payment status in your dashboard.
            </p>
        </div>
        
        <div class="action-buttons">
            <a href="/patient/dashboard" class="btn btn-primary">
                <i class="fas fa-home"></i>
                <span>Go to Dashboard</span>
            </a>
            <a href="/patient/appointments" class="btn btn-secondary">
                <i class="fas fa-calendar"></i>
                <span>View Appointments</span>
            </a>
        </div>
    <?php else: ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?= htmlspecialchars($error ?: 'Appointment not found') ?></span>
        </div>
        <a href="/patient/appointments" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Appointments</span>
        </a>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

