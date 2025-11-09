<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div style="background: white; border: 1px solid #e5e7eb; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 14px; font-weight: 500;">Total Staff</p>
                <h2 style="margin: 0; font-size: 32px; color: #1f2937; font-weight: 700;"><?= $stats['total_staff'] ?></h2>
            </div>
            <div style="background: #eff6ff; padding: 12px; border-radius: 8px;">
                <span class="material-icons" style="color: #3b82f6; font-size: 28px;">badge</span>
            </div>
        </div>
    </div>
    
    <div style="background: white; border: 1px solid #e5e7eb; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 14px; font-weight: 500;">Services</p>
                <h2 style="margin: 0; font-size: 32px; color: #1f2937; font-weight: 700;"><?= $stats['total_services'] ?></h2>
            </div>
            <div style="background: #f0fdf4; padding: 12px; border-radius: 8px;">
                <span class="material-icons" style="color: #10b981; font-size: 28px;">science</span>
            </div>
        </div>
    </div>
    
    <div style="background: white; border: 1px solid #e5e7eb; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 14px; font-weight: 500;">Specializations</p>
                <h2 style="margin: 0; font-size: 32px; color: #1f2937; font-weight: 700;"><?= $stats['total_specializations'] ?></h2>
            </div>
            <div style="background: #fef3c7; padding: 12px; border-radius: 8px;">
                <span class="material-icons" style="color: #f59e0b; font-size: 28px;">school</span>
            </div>
        </div>
    </div>
    
    <div style="background: white; border: 1px solid #e5e7eb; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 14px; font-weight: 500;">Payment Methods</p>
                <h2 style="margin: 0; font-size: 32px; color: #1f2937; font-weight: 700;"><?= $stats['total_payment_methods'] ?></h2>
            </div>
            <div style="background: #f3e8ff; padding: 12px; border-radius: 8px;">
                <span class="material-icons" style="color: #a855f7; font-size: 28px;">credit_card</span>
            </div>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h2 style="margin: 0; display: flex; align-items: center; gap: 8px;">
            <span class="material-icons" style="color: #3b82f6;">list_alt</span>
            Recent Services
        </h2>
    </div>
    <div class="table-responsive">
        <?php if (empty($recent_services)): ?>
            <div style="padding: 40px; text-align: center; color: #6b7280;">
                <span class="material-icons" style="font-size: 48px; color: #d1d5db; margin-bottom: 12px;">inbox</span>
                <p style="margin: 0;">No services found.</p>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service Name</th>
                        <th>Category</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_services as $service): ?>
                        <tr>
                            <td><?= htmlspecialchars($service['service_id']) ?></td>
                            <td><?= htmlspecialchars($service['service_name']) ?></td>
                            <td><?= htmlspecialchars($service['service_category'] ?? 'N/A') ?></td>
                            <td>₱<?= number_format($service['service_price'] ?? 0, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>