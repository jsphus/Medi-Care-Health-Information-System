<?php require_once __DIR__ . '/../partials/header.php'; ?>

<!-- Dashboard Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.5rem; overflow: hidden; flex-shrink: 0;">
            <?php if (!empty($profile_picture_url)): ?>
                <img src="<?= htmlspecialchars($profile_picture_url) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <?= strtoupper(substr($staff_name ?? 'S', 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div>
            <h1 class="page-title" style="margin-bottom: 0.5rem;">Welcome back, <?= htmlspecialchars($staff_name) ?>! 👋</h1>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.95rem;">Here's your day at a glance - <?= date('l, F d, Y') ?></p>
        </div>
    </div>
    <div style="display: flex; gap: 1rem; align-items: center;">
        <a href="/staff/payments" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-credit-card"></i>
            <span>Manage Payments</span>
        </a>
        <a href="/staff/services" class="btn btn-secondary" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-flask"></i>
            <span>Manage Services</span>
        </a>
    </div>
</div>

<!-- Statistics Cards - Payment & Service Focused -->
<div class="stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Pending Payments Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer;" onclick="window.location.href='/staff/payments'">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-clock" style="color: #f59e0b;"></i>
                    <span>Pending Payments</span>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['pending_payments'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    ₱<?= number_format($stats['pending_amount'] ?? 0, 2) ?> total
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-clock" style="font-size: 1.5rem; color: #f59e0b;"></i>
            </div>
        </div>
    </div>

    <!-- Next Payment Widget (Larger if exists) -->
    <?php if ($next_payment): ?>
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; grid-column: span 2;" onclick="window.location.href='/staff/payments'">
            <div style="display: flex; justify-content: space-between; align-items: center; color: white;">
                <div style="flex: 1;">
                    <div style="font-size: 0.875rem; margin-bottom: 0.5rem; font-weight: 500; opacity: 0.9;">
                        <i class="fas fa-exclamation-circle"></i> Next Payment to Process
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">
                        <?= htmlspecialchars(($next_payment['pat_first_name'] ?? '') . ' ' . ($next_payment['pat_last_name'] ?? 'Patient')) ?>
                    </div>
                    <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.25rem;">
                        ₱<?= number_format($next_payment['payment_amount'] ?? 0, 2) ?> - <?= htmlspecialchars($next_payment['method_name'] ?? 'N/A') ?>
                    </div>
                    <div style="font-size: 0.75rem; font-weight: 600; opacity: 0.95;">
                        <?= isset($next_payment['payment_date']) ? date('M d, Y', strtotime($next_payment['payment_date'])) : 'N/A' ?>
                    </div>
                </div>
                <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <?php if (!empty($next_payment['patient_profile_picture'])): ?>
                        <img src="<?= htmlspecialchars($next_payment['patient_profile_picture']) ?>" alt="Patient" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <span style="font-size: 1.5rem; font-weight: 700;"><?= strtoupper(substr($next_payment['pat_first_name'] ?? 'P', 0, 1)) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Today's Payments Card (if no next payment) -->
        <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer;" onclick="window.location.href='/staff/payments'">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-credit-card" style="color: var(--primary-blue);"></i>
                        <span>Today's Payments</span>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                        <?= number_format($stats['today_payments'] ?? 0) ?>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">
                        ₱<?= number_format($stats['today_amount'] ?? 0, 2) ?> processed
                    </div>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-credit-card" style="font-size: 1.5rem; color: var(--primary-blue);"></i>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Today's Payments Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer;" onclick="window.location.href='/staff/payments'">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-credit-card" style="color: var(--primary-blue);"></i>
                    <span>Today's Payments</span>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['today_payments'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    ₱<?= number_format($stats['today_amount'] ?? 0, 2) ?> processed
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-credit-card" style="font-size: 1.5rem; color: var(--primary-blue);"></i>
            </div>
        </div>
    </div>

    <!-- This Week Overview Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-calendar-week" style="color: #8b5cf6;"></i>
                    <span>This Week</span>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['this_week_payments'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    ₱<?= number_format($stats['this_week_amount'] ?? 0, 2) ?> revenue
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(139, 92, 246, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-calendar-week" style="font-size: 1.5rem; color: #8b5cf6;"></i>
            </div>
        </div>
    </div>

    <!-- Total Services Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer;" onclick="window.location.href='/staff/services'">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-flask" style="color: #10b981;"></i>
                    <span>Total Services</span>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['total_services'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Active services
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-flask" style="font-size: 1.5rem; color: #10b981;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Left Column: Pending Payments -->
    <div class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-clock" style="margin-right: 0.5rem; color: #f59e0b;"></i>
                Pending Payments
            </h2>
            <a href="/staff/payments" style="color: var(--primary-blue); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                View All <i class="fas fa-arrow-right" style="margin-left: 0.25rem;"></i>
            </a>
        </div>
        
        <?php if (empty($pending_payments)): ?>
            <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--status-success); opacity: 0.5;"></i>
                <p style="margin: 0; font-size: 1rem; font-weight: 500;">No pending payments</p>
                <a href="/staff/payments" style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: var(--primary-blue); color: white; border-radius: 8px; text-decoration: none; font-size: 0.875rem;">
                    View All Payments
                </a>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($pending_payments as $payment): 
                    $patName = htmlspecialchars(($payment['pat_first_name'] ?? '') . ' ' . ($payment['pat_last_name'] ?? ''));
                    $paymentDate = isset($payment['payment_date']) ? date('M d, Y', strtotime($payment['payment_date'])) : 'N/A';
                    $isUrgent = isset($payment['payment_date']) && strtotime($payment['payment_date']) < strtotime('today');
                ?>
                    <div style="padding: 1rem; background: #f9fafb; border-radius: 8px; border: 1px solid var(--border-light); <?= $isUrgent ? 'border-left: 4px solid #ef4444; background: #fee2e2;' : '' ?>">
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <div style="text-align: center; min-width: 80px;">
                                <div style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary);">
                                    ₱<?= number_format($payment['payment_amount'] ?? 0, 2) ?>
                                </div>
                                <?php if ($isUrgent): ?>
                                    <span style="font-size: 0.75rem; color: #ef4444; font-weight: 600;">
                                        <i class="fas fa-exclamation-triangle"></i> Urgent
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; flex-shrink: 0;">
                                <?php if (!empty($payment['patient_profile_picture'])): ?>
                                    <img src="<?= htmlspecialchars($payment['patient_profile_picture']) ?>" alt="Patient" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                <?php else: ?>
                                    <?= strtoupper(substr($payment['pat_first_name'] ?? 'P', 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">
                                    <?= $patName ?: 'Patient' ?>
                                </div>
                                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">
                                    <?= htmlspecialchars($payment['method_name'] ?? 'N/A') ?> • <?= $paymentDate ?>
                                </div>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <span class="badge" style="background: <?= $payment['status_color'] ?? '#f59e0b' ?>; color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.75rem;">
                                        <?= htmlspecialchars($payment['status_name'] ?? 'Pending') ?>
                                    </span>
                                    <a href="/staff/payments" style="color: var(--primary-blue); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                                        Process <i class="fas fa-arrow-right" style="margin-left: 0.25rem; font-size: 0.75rem;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Quick Actions & Recent Activity -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Quick Actions Panel -->
        <div class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 1.5rem 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-bolt" style="margin-right: 0.5rem; color: #f59e0b;"></i>
                Quick Actions
            </h3>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="/staff/payments" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f9fafb; border-radius: 8px; text-decoration: none; color: var(--text-primary); transition: background 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
                    <i class="fas fa-credit-card" style="color: var(--primary-blue); font-size: 1.25rem; width: 24px;"></i>
                    <div>
                        <div style="font-weight: 600; font-size: 0.875rem;">Manage Payments</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">Process and view payments</div>
                    </div>
                </a>
                <a href="/staff/services" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f9fafb; border-radius: 8px; text-decoration: none; color: var(--text-primary); transition: background 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
                    <i class="fas fa-flask" style="color: #10b981; font-size: 1.25rem; width: 24px;"></i>
                    <div>
                        <div style="font-weight: 600; font-size: 0.875rem;">Manage Services</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">Add or update services</div>
                    </div>
                </a>
                <a href="/staff/payment-methods" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f9fafb; border-radius: 8px; text-decoration: none; color: var(--text-primary); transition: background 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
                    <i class="fas fa-wallet" style="color: #8b5cf6; font-size: 1.25rem; width: 24px;"></i>
                    <div>
                        <div style="font-weight: 600; font-size: 0.875rem;">Payment Methods</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">Manage payment options</div>
                    </div>
                </a>
                <a href="/staff/medical-records" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f9fafb; border-radius: 8px; text-decoration: none; color: var(--text-primary); transition: background 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
                    <i class="fas fa-file-medical" style="color: #8b5cf6; font-size: 1.25rem; width: 24px;"></i>
                    <div>
                        <div style="font-weight: 600; font-size: 0.875rem;">Medical Records</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">View records (read-only)</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 1.5rem 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-clock" style="margin-right: 0.5rem; color: var(--primary-blue);"></i>
                Recent Activity
            </h3>
            
            <?php if (empty($recent_activity)): ?>
                <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                    <i class="fas fa-history" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                    <p style="margin: 0; font-size: 0.875rem;">No recent activity</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach (array_slice($recent_activity, 0, 5) as $activity): ?>
                        <div style="display: flex; gap: 0.75rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-light);">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <?php if ($activity['activity_type'] === 'payment'): ?>
                                    <i class="fas fa-credit-card" style="color: var(--primary-blue); font-size: 0.875rem;"></i>
                                <?php else: ?>
                                    <i class="fas fa-flask" style="color: #10b981; font-size: 0.875rem;"></i>
                                <?php endif; ?>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.875rem; color: var(--text-primary); margin-bottom: 0.25rem;">
                                    <?= htmlspecialchars($activity['activity_description'] ?? 'Activity') ?>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                    <?= isset($activity['updated_at']) ? date('M d, g:i A', strtotime($activity['updated_at'])) : 'N/A' ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Services Overview -->
        <div class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                    <i class="fas fa-flask" style="margin-right: 0.5rem; color: #10b981;"></i>
                    Recent Services
                </h3>
                <a href="/staff/services" style="color: var(--primary-blue); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                    View All
                </a>
            </div>
            
            <?php if (empty($recent_services)): ?>
                <div style="text-align: center; padding: 1rem; color: var(--text-secondary);">
                    <i class="fas fa-flask" style="font-size: 1.5rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                    <p style="margin: 0; font-size: 0.875rem;">No services found</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php foreach ($recent_services as $service): ?>
                        <div style="padding: 0.75rem; background: #f9fafb; border-radius: 8px;">
                            <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-primary); margin-bottom: 0.25rem;">
                                <?= htmlspecialchars($service['service_name']) ?>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                ₱<?= number_format($service['service_price'] ?? 0, 2) ?> • <?= htmlspecialchars($service['service_category'] ?? 'N/A') ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
