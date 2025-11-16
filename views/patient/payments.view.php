<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
    .payments-page {
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .page-header {
        margin-bottom: 2rem;
    }
    
    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .page-subtitle {
        color: #6b7280;
        font-size: 1rem;
    }
    
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .summary-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid var(--primary-blue);
    }
    
    .summary-card.paid {
        border-left-color: #10b981;
    }
    
    .summary-card.pending {
        border-left-color: #f59e0b;
    }
    
    .summary-card-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .summary-card-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
    }
    
    .search-bar {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        background: white;
        padding: 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .search-input-wrapper {
        flex: 1;
        position: relative;
    }
    
    .search-input-wrapper i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
    }
    
    .search-input-wrapper input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }
    
    .payments-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .payment-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }
    
    .payment-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        border-color: var(--primary-blue);
    }
    
    .payment-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .payment-amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
    }
    
    .payment-status {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-paid {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-refunded {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .payment-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .detail-item i {
        color: #9ca3af;
        width: 16px;
    }
    
    .detail-item strong {
        color: #374151;
        font-weight: 600;
    }
    
    .payment-actions {
        display: flex;
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-primary {
        background: var(--primary-blue);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--primary-blue-dark);
    }
    
    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }
    
    .btn-secondary:hover {
        background: #e5e7eb;
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .empty-state-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    .empty-state-text {
        font-size: 1.125rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
</style>

<div class="payments-page">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title" style="margin: 0;">My Payments</h1>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success" style="margin-bottom: 1.5rem; background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem;">
            <i class="fas fa-check-circle"></i>
            <span><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>
    
    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: #8b5cf6;"></div>
                <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Payments</span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total'] ?? 0 ?></div>
        </div>
        <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></div>
                <span style="font-size: 0.875rem; color: var(--text-secondary);">Paid</span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['paid'] ?? 0 ?></div>
        </div>
        <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></div>
                <span style="font-size: 0.875rem; color: var(--text-secondary);">Pending</span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['pending'] ?? 0 ?></div>
        </div>
        <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></div>
                <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Amount</span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);">₱<?= number_format($stats['total_amount'] ?? 0, 0) ?></div>
        </div>
    </div>
    
    <!-- Unpaid Appointments Section -->
    <?php if (!empty($unpaid_appointments)): ?>
        <div style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">Unpaid Appointments</h2>
            <div class="payments-list">
                <?php foreach ($unpaid_appointments as $appointment): ?>
                    <?php
                    $amount = 0;
                    if (!empty($appointment['service_price'])) {
                        $amount = floatval($appointment['service_price']);
                    } elseif (!empty($appointment['doc_consultation_fee'])) {
                        $amount = floatval($appointment['doc_consultation_fee']);
                    }
                    ?>
                    <div class="payment-card" style="border-left: 4px solid #f59e0b;">
                        <div class="payment-header">
                            <div>
                                <div class="payment-amount">₱<?= number_format($amount, 2) ?></div>
                                <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">
                                    Appointment: <?= htmlspecialchars($appointment['appointment_id']) ?>
                                </div>
                            </div>
                            <span class="payment-status status-pending">Payment Required</span>
                        </div>
                        
                        <div class="payment-details">
                            <div class="detail-item">
                                <i class="fas fa-calendar"></i>
                                <span><strong>Date:</strong> <?= date('M j, Y', strtotime($appointment['appointment_date'])) ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span><strong>Time:</strong> <?= date('g:i A', strtotime($appointment['appointment_time'])) ?></span>
                            </div>
                            <?php if ($appointment['service_name']): ?>
                            <div class="detail-item">
                                <i class="fas fa-stethoscope"></i>
                                <span><strong>Service:</strong> <?= htmlspecialchars($appointment['service_name']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($appointment['doc_first_name']): ?>
                            <div class="detail-item">
                                <i class="fas fa-user-md"></i>
                                <span><strong>Doctor:</strong> Dr. <?= htmlspecialchars($appointment['doc_first_name'] . ' ' . $appointment['doc_last_name']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="payment-actions">
                            <button class="btn-action btn-primary" onclick="openPaymentModal('<?= htmlspecialchars($appointment['appointment_id']) ?>', <?= $amount ?>)">
                                <i class="fas fa-credit-card"></i> Pay Now
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Payment Records Section -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">Payment Records</h2>
        <?php if (empty($payments)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-credit-card"></i></div>
                <div class="empty-state-text">No payment records found</div>
            </div>
        <?php else: ?>
        <div class="payments-list">
            <?php foreach ($payments as $payment): ?>
                <?php
                $statusName = strtolower($payment['status_name'] ?? 'pending');
                $statusClass = 'status-' . $statusName;
                ?>
                <div class="payment-card">
                    <div class="payment-header">
                        <div class="payment-amount">₱<?= number_format($payment['payment_amount'], 2) ?></div>
                        <span class="payment-status <?= $statusClass ?>"><?= htmlspecialchars($payment['status_name'] ?? 'Pending') ?></span>
                    </div>
                    
                    <div class="payment-details">
                        <div class="detail-item">
                            <i class="fas fa-calendar"></i>
                            <span><strong>Date:</strong> <?= date('M j, Y', strtotime($payment['payment_date'])) ?></span>
                        </div>
                        <?php if ($payment['appointment_id']): ?>
                        <div class="detail-item">
                            <i class="fas fa-file-alt"></i>
                            <span><strong>Appointment:</strong> <?= htmlspecialchars($payment['appointment_id']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($payment['method_name']): ?>
                        <div class="detail-item">
                            <i class="fas fa-credit-card"></i>
                            <span><strong>Method:</strong> <?= htmlspecialchars($payment['method_name']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($payment['payment_reference']): ?>
                        <div class="detail-item">
                            <i class="fas fa-hashtag"></i>
                            <span><strong>Reference:</strong> <?= htmlspecialchars($payment['payment_reference']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($payment['payment_notes']): ?>
                    <div style="margin-top: 1rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                        <strong>Notes:</strong> <?= htmlspecialchars($payment['payment_notes']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="payment-actions">
                        <?php if ($statusName === 'paid'): ?>
                        <button class="btn-action btn-primary" onclick="downloadReceipt(<?= $payment['payment_id'] ?>)">
                            <i class="fas fa-download"></i> Download Receipt
                        </button>
                        <button class="btn-action btn-secondary" onclick="printReceipt(<?= $payment['payment_id'] ?>)">
                            <i class="fas fa-print"></i> Print Receipt
                        </button>
                        <?php elseif ($statusName === 'pending'): ?>
                        <button class="btn-action btn-primary" onclick="makePayment(<?= $payment['payment_id'] ?>)">
                            <i class="fas fa-credit-card"></i> Pay Now
                        </button>
                        <?php endif; ?>
                        <button class="btn-action btn-secondary" onclick="viewDetails(<?= $payment['payment_id'] ?>)">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function downloadReceipt(paymentId) {
    // TODO: Implement receipt download
    window.location.href = '/patient/payments?receipt=' + paymentId + '&download=1';
}

function printReceipt(paymentId) {
    // TODO: Implement receipt printing
    window.location.href = '/patient/payments?receipt=' + paymentId + '&print=1';
}

function openPaymentModal(appointmentId, amount) {
    document.getElementById('payment_appointment_id').value = appointmentId;
    document.getElementById('payment_amount_display').textContent = '₱' + amount.toFixed(2);
    document.getElementById('payment_appointment_id_display').textContent = appointmentId;
    document.getElementById('paymentModal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
    document.getElementById('paymentForm').reset();
}

function viewDetails(paymentId) {
    // TODO: Implement view details modal
    alert('View details for payment ID: ' + paymentId);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('paymentModal');
    if (event.target === modal) {
        closePaymentModal();
    }
}
</script>

<!-- Payment Modal -->
<div id="paymentModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2 class="modal-title">Make Payment</h2>
            <button type="button" class="modal-close" onclick="closePaymentModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="paymentForm" method="POST" action="">
            <input type="hidden" name="action" value="create_payment">
            <input type="hidden" name="appointment_id" id="payment_appointment_id">
            
            <div style="padding: 2rem;">
                <div style="background: #f9fafb; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #6b7280; font-size: 0.875rem;">Appointment ID:</span>
                        <strong id="payment_appointment_id_display"></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #6b7280; font-size: 0.875rem;">Amount to Pay:</span>
                        <strong style="font-size: 1.5rem; color: #1f2937;" id="payment_amount_display"></strong>
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                        Payment Method: <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="payment_method_id" required class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
                        <option value="">-- Select Payment Method --</option>
                        <?php foreach ($payment_methods as $method): ?>
                            <option value="<?= $method['method_id'] ?>"><?= htmlspecialchars($method['method_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                        Payment Reference (Optional)
                    </label>
                    <input type="text" name="payment_reference" class="form-control" 
                           placeholder="e.g., Transaction ID, Receipt Number" 
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
                    <small style="color: #6b7280; font-size: 0.875rem; display: block; margin-top: 0.25rem;">
                        Provide a reference number if paying via mobile payment or bank transfer
                    </small>
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151;">
                        Notes (Optional)
                    </label>
                    <textarea name="payment_notes" rows="3" class="form-control" 
                              placeholder="Any additional information about your payment"
                              style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; resize: vertical;"></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn-action btn-primary" style="flex: 1; justify-content: center;">
                        <i class="fas fa-credit-card"></i> Submit Payment
                    </button>
                    <button type="button" onclick="closePaymentModal()" class="btn-action btn-secondary" style="flex: 1; justify-content: center;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
                
                <div style="margin-top: 1rem; padding: 1rem; background: #fef3c7; border-radius: 0.5rem; border-left: 4px solid #f59e0b;">
                    <p style="margin: 0; font-size: 0.875rem; color: #92400e;">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Note:</strong> Your payment will be reviewed by the clinic. You will be notified once it's confirmed.
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

