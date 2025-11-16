<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
    .payment-page {
        padding: 2rem;
        max-width: 900px;
        margin: 0 auto;
    }
    
    .payment-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .payment-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .payment-subtitle {
        color: #6b7280;
        font-size: 1rem;
    }
    
    .payment-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }
    
    .appointment-summary {
        background: #f9fafb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .summary-row:last-child {
        border-bottom: none;
    }
    
    .summary-label {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .summary-value {
        font-size: 0.875rem;
        color: #1f2937;
        font-weight: 600;
    }
    
    .amount-section {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        text-align: center;
    }
    
    .amount-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    
    .amount-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1f2937;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #374151;
        font-size: 0.875rem;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: border-color 0.2s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .form-help {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }
    
    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }
    
    .btn {
        flex: 1;
        padding: 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
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
    
    .info-box {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-top: 1.5rem;
    }
    
    .info-box p {
        margin: 0;
        font-size: 0.875rem;
        color: #92400e;
    }
</style>

<div class="payment-page">
    <div class="payment-header">
        <h1 class="payment-title">Complete Payment</h1>
        <p class="payment-subtitle">Choose your payment method to complete your appointment booking</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>
    
    <?php if ($appointment): ?>
        <div class="payment-card">
            <!-- Appointment Summary -->
            <div class="appointment-summary">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">
                    <i class="fas fa-calendar-check" style="color: var(--primary-blue);"></i> Appointment Summary
                </h3>
                <div class="summary-row">
                    <span class="summary-label">Appointment ID</span>
                    <span class="summary-value"><?= htmlspecialchars($appointment['appointment_id']) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Doctor</span>
                    <span class="summary-value">Dr. <?= htmlspecialchars($appointment['doc_first_name'] . ' ' . $appointment['doc_last_name']) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Date</span>
                    <span class="summary-value"><?= date('l, F j, Y', strtotime($appointment['appointment_date'])) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Time</span>
                    <span class="summary-value"><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></span>
                </div>
                <?php if ($appointment['service_name']): ?>
                <div class="summary-row">
                    <span class="summary-label">Service</span>
                    <span class="summary-value"><?= htmlspecialchars($appointment['service_name']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Payment Amount -->
            <div class="amount-section">
                <div class="amount-label">Total Amount to Pay</div>
                <div class="amount-value">₱<?= number_format($payment_amount, 2) ?></div>
            </div>
            
            <!-- Payment Form -->
            <form method="POST">
                <input type="hidden" name="action" value="process_payment">
                
                <div class="form-group">
                    <label class="form-label">
                        Payment Method <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="payment_method_id" required class="form-control">
                        <option value="">-- Select Payment Method --</option>
                        <?php foreach ($payment_methods as $method): ?>
                            <option value="<?= $method['method_id'] ?>"><?= htmlspecialchars($method['method_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-help">Select how you would like to pay for this appointment</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Payment Reference (Optional)</label>
                    <input type="text" name="payment_reference" class="form-control" 
                           placeholder="e.g., Transaction ID, Receipt Number">
                    <div class="form-help">Provide a reference number if paying via mobile payment or bank transfer</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea name="payment_notes" rows="3" class="form-control" 
                              placeholder="Any additional information about your payment"></textarea>
                </div>
                
                <div class="info-box">
                    <p>
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Your payment will be reviewed by the clinic. You will be notified once it's confirmed.
                    </p>
                </div>
                
                <div class="action-buttons">
                    <a href="/patient/appointments" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Appointments</span>
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i>
                        <span>Submit Payment</span>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

