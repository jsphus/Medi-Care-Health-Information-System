<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header" style="display: flex; align-items: center; gap: 1rem;">
    <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.5rem; overflow: hidden; flex-shrink: 0;">
        <?php if (!empty($profile_picture_url)): ?>
            <img src="<?= htmlspecialchars($profile_picture_url) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
        <?php else: ?>
            <?= strtoupper(substr($staff_name ?? 'S', 0, 1)) ?>
        <?php endif; ?>
    </div>
    <div>
        <h1 class="page-title">Welcome back, <?= htmlspecialchars($staff_name) ?>! 👋</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem; font-size: 0.95rem;">Here's what's happening in your dashboard today.</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-content">
                <div class="stat-label">
                    <i class="fas fa-user-tie"></i>
                    <span>Total Staff</span>
                </div>
                <div class="stat-value"><?= $stats['total_staff'] ?></div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span><?= $stats['total_staff'] > 0 ? round($stats['total_staff'] * 0.1) : 0 ?> staff</span>
                </div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-user-tie"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-content">
                <div class="stat-label">
                    <i class="fas fa-flask"></i>
                    <span>Services</span>
                </div>
                <div class="stat-value"><?= $stats['total_services'] ?></div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span><?= $stats['total_services'] > 0 ? round($stats['total_services'] * 0.12) : 0 ?> services</span>
                </div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-flask"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-content">
                <div class="stat-label">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Specializations</span>
                </div>
                <div class="stat-value"><?= $stats['total_specializations'] ?></div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span><?= $stats['total_specializations'] > 0 ? round($stats['total_specializations'] * 0.08) : 0 ?> specializations</span>
                </div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-content">
                <div class="stat-label">
                    <i class="fas fa-credit-card"></i>
                    <span>Payment Methods</span>
                </div>
                <div class="stat-value"><?= $stats['total_payment_methods'] ?></div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span><?= $stats['total_payment_methods'] > 0 ? round($stats['total_payment_methods'] * 0.15) : 0 ?> methods</span>
                </div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-credit-card"></i>
            </div>
        </div>
    </div>
</div>

<!-- Chart and Quick Stats -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Services Overview Chart -->
    <div class="chart-container">
        <div class="chart-header">
            <h2 class="chart-title">Services Overview</h2>
            <div class="chart-legend">
                <div class="legend-item">
                    <div class="legend-dot blue"></div>
                    <span><?= date('Y') ?> (Most Booked)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot light-blue"></div>
                    <span><?= date('Y') - 1 ?> (Most Booked)</span>
                </div>
            </div>
        </div>
        <div class="chart-wrapper">
            <canvas id="servicesChart"></canvas>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Quick Stats</h2>
        </div>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div>
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Most Booked Service</div>
                <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">
                    <?= htmlspecialchars($most_booked_service['service_name'] ?? 'N/A') ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    <?= number_format($most_booked_service['booking_count'] ?? 0) ?> bookings
                </div>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Payment Methods</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total_payment_methods'] ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Recently Added Services Table -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 class="card-title">Recently Added Services</h2>
        <a href="/staff/services" style="font-size: 0.875rem; color: #3b82f6; text-decoration: none; font-weight: 500;">Show All</a>
    </div>
    <?php if (empty($recent_services)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-flask"></i></div>
            <div class="empty-state-text">No services found.</div>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Date Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_services as $service): ?>
                    <tr>
                        <td><?= htmlspecialchars($service['service_name']) ?></td>
                        <td><?= htmlspecialchars($service['service_category'] ?? 'N/A') ?></td>
                        <td>₱<?= number_format($service['service_price'] ?? 0, 2) ?></td>
                        <td>
                            <?php if (!empty($service['created_at'])): ?>
                                <?= date('M d, Y', strtotime($service['created_at'])) ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Services Overview Chart
const ctx = document.getElementById('servicesChart').getContext('2d');
const servicesChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chart_data['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']) ?>,
        datasets: [
            {
                label: '<?= date('Y') ?> (Most Booked)',
                data: <?= json_encode($chart_data['current_year'] ?? [0, 0, 0, 0, 0, 0, 0]) ?>,
                backgroundColor: '#3b82f6',
                borderRadius: 4
            },
            {
                label: '<?= date('Y') - 1 ?> (Most Booked)',
                data: <?= json_encode($chart_data['last_year'] ?? [0, 0, 0, 0, 0, 0, 0]) ?>,
                backgroundColor: '#60a5fa',
                borderRadius: 4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y + ' bookings';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    callback: function(value) {
                        return value;
                    }
                },
                grid: {
                    color: '#e5e7eb'
                },
                title: {
                    display: true,
                    text: 'Bookings'
                }
            },
            x: {
                grid: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Month'
                }
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
