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
                    <span>2024 Services</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot light-blue"></div>
                    <span>Active Services</span>
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
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Total Specializations</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total_specializations'] ?></div>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Payment Methods</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total_payment_methods'] ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Services Table -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Recent Services</h2>
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
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_services as $service): ?>
                    <tr>
                        <td><?= htmlspecialchars($service['service_name']) ?></td>
                        <td><?= htmlspecialchars($service['service_category'] ?? 'N/A') ?></td>
                        <td>₱<?= number_format($service['service_price'] ?? 0, 2) ?></td>
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
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        datasets: [
            {
                label: '2024 Services',
                data: [<?= isset($chart_data['services']) ? implode(',', $chart_data['services']) : '5,8,12,10,15,18,16' ?>],
                backgroundColor: '#3b82f6',
                borderRadius: 4
            },
            {
                label: 'Active Services',
                data: [<?= isset($chart_data['active']) ? implode(',', $chart_data['active']) : '4,7,11,9,14,17,15' ?>],
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
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 5,
                    callback: function(value) {
                        return value;
                    }
                },
                grid: {
                    color: '#e5e7eb'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
