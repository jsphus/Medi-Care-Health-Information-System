<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
    .review-page {
        padding: 2rem;
        max-width: 900px;
        margin: 0 auto;
    }
    
    .review-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .review-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .review-subtitle {
        color: #6b7280;
        font-size: 1rem;
    }
    
    .review-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }
    
    .card-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .card-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .section-title {
        font-size: 1.125rem;
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
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .info-value {
        font-size: 0.875rem;
        color: #1f2937;
        font-weight: 600;
        text-align: right;
    }
    
    .doctor-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .doctor-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.25rem;
    }
    
    .doctor-details {
        flex: 1;
    }
    
    .doctor-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }
    
    .doctor-spec {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .amount-summary {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-top: 1rem;
    }
    
    .amount-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    
    .amount-row:last-child {
        margin-bottom: 0;
        padding-top: 0.75rem;
        border-top: 2px solid #3b82f6;
        margin-top: 0.75rem;
    }
    
    .amount-label {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .amount-value {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
    }
    
    .total-amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
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
    
    .notes-section {
        background: #f9fafb;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-top: 1rem;
    }
    
    .notes-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    
    .notes-content {
        font-size: 0.875rem;
        color: #1f2937;
        line-height: 1.6;
    }
</style>

<div class="review-page">
    <div class="review-header">
        <h1 class="review-title">Review Your Appointment</h1>
        <p class="review-subtitle">Please review the details below before confirming</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
        <div style="text-align: center; margin-top: 2rem;">
            <a href="/patient/book" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Browse Doctors</span>
            </a>
        </div>
    <?php elseif ($doctor && $appointment_data): ?>
        <div class="review-card">
            <!-- Doctor Information -->
            <div class="card-section">
                <h2 class="section-title">
                    <i class="fas fa-user-md"></i>
                    Doctor Information
                </h2>
                <div class="doctor-info">
                    <?php
                    $initials = strtoupper(substr($doctor['doc_first_name'], 0, 1) . substr($doctor['doc_last_name'], 0, 1));
                    $doctorName = 'Dr. ' . htmlspecialchars($doctor['doc_first_name'] . ' ' . $doctor['doc_last_name']);
                    $specialization = htmlspecialchars($doctor['spec_name'] ?? 'General Practice');
                    ?>
                    <div class="doctor-avatar"><?= $initials ?></div>
                    <div class="doctor-details">
                        <div class="doctor-name"><?= $doctorName ?></div>
                        <div class="doctor-spec"><?= $specialization ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Appointment Details -->
            <div class="card-section">
                <h2 class="section-title">
                    <i class="fas fa-calendar-check"></i>
                    Appointment Details
                </h2>
                <div class="info-row">
                    <span class="info-label">Date</span>
                    <span class="info-value"><?= !empty($appointment_data['appointment_date']) ? date('l, F j, Y', strtotime($appointment_data['appointment_date'])) : 'N/A' ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Time</span>
                    <span class="info-value"><?= !empty($appointment_data['appointment_time']) ? date('g:i A', strtotime($appointment_data['appointment_time'])) : 'N/A' ?></span>
                </div>
                <?php if ($service): ?>
                <div class="info-row">
                    <span class="info-label">Service</span>
                    <span class="info-value"><?= htmlspecialchars($service['service_name']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Payment Summary -->
            <div class="card-section">
                <h2 class="section-title">
                    <i class="fas fa-credit-card"></i>
                    Payment Summary
                </h2>
                <div class="amount-summary">
                    <?php if ($service): ?>
                    <div class="amount-row">
                        <span class="amount-label">Service Fee</span>
                        <span class="amount-value">₱<?= number_format($service['service_price'] ?? 0, 2) ?></span>
                    </div>
                    <?php else: ?>
                    <div class="amount-row">
                        <span class="amount-label">Consultation Fee</span>
                        <span class="amount-value">₱<?= number_format($doctor['doc_consultation_fee'] ?? 0, 2) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="amount-row">
                        <span class="amount-label total-amount">Total Amount</span>
                        <span class="amount-value total-amount">₱<?= number_format($total_amount, 2) ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            <?php if (!empty($appointment_data['notes'])): ?>
            <div class="card-section">
                <h2 class="section-title">
                    <i class="fas fa-sticky-note"></i>
                    Notes
                </h2>
                <div class="notes-section">
                    <div class="notes-label">Reason for Visit:</div>
                    <div class="notes-content"><?= nl2br(htmlspecialchars($appointment_data['notes'])) ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Action Buttons -->
        <form method="POST" action="/patient/appointments/create">
            <input type="hidden" name="action" value="confirm">
            <input type="hidden" name="doctor_id" value="<?= $appointment_data['doctor_id'] ?? '' ?>">
            <input type="hidden" name="service_id" value="<?= $appointment_data['service_id'] ?? '' ?>">
            <input type="hidden" name="appointment_date" value="<?= htmlspecialchars($appointment_data['appointment_date'] ?? '') ?>">
            <input type="hidden" name="appointment_time" value="<?= htmlspecialchars($appointment_data['appointment_time'] ?? '') ?>">
            <input type="hidden" name="notes" value="<?= htmlspecialchars($appointment_data['notes'] ?? '') ?>">
            
            <div class="action-buttons">
                <a href="/patient/appointments/create" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <span>Edit Details</span>
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i>
                    <span>Confirm & Book Appointment</span>
                </button>
            </div>
        </form>
    <?php else: ?>
        <div style="text-align: center; padding: 3rem;">
            <p style="color: #6b7280; margin-bottom: 1.5rem;">Unable to load appointment details. Please try again.</p>
            <a href="/patient/book" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Browse Doctors</span>
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

