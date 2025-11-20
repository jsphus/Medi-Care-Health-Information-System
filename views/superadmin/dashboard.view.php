<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
.dashboard-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.dashboard-header-left {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.dashboard-welcome {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.dashboard-date {
    font-size: 0.875rem;
    color: #6b7280;
}

.dashboard-header-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}



.add-patient-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
}

.add-patient-btn:hover {
    background: #2563eb;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.kpi-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #f3f4f6;
}

.kpi-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.75rem;
    font-weight: 500;
}

.kpi-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.kpi-trend {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
}

.kpi-trend.positive {
    color: #10b981;
}

.kpi-trend.negative {
    color: #ef4444;
}

.kpi-trend-text {
    color: #6b7280;
}

.charts-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.chart-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #f3f4f6;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.chart-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.chart-wrapper {
    height: 250px;
    position: relative;
}

.bottom-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr;
    gap: 1.5rem;
}

.service-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.service-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.service-bar-wrapper {
    flex: 1;
    height: 8px;
    background: #f3f4f6;
    border-radius: 4px;
    overflow: hidden;
}

.service-bar {
    height: 100%;
    border-radius: 4px;
}

.service-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    margin-bottom: 0.25rem;
}

.service-name {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1f2937;
}

.service-stats {
    font-size: 0.875rem;
    color: #6b7280;
}

.staff-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.staff-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 0.5rem;
    transition: background 0.2s;
    cursor: pointer;
}

.staff-item:hover {
    background: #f9fafb;
}

.staff-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.staff-info {
    flex: 1;
}

.staff-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.125rem;
}

.staff-role {
    font-size: 0.75rem;
    color: #6b7280;
}

.staff-arrow {
    color: #9ca3af;
}

.view-all-btn {
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background: #f3f4f6;
    border: none;
    border-radius: 0.5rem;
    color: #374151;
    font-size: 0.875rem;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    display: block;
}

.view-all-btn:hover {
    background: #e5e7eb;
}

.satisfaction-description {
    margin-top: 1rem;
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.6;
}

.payments-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.today-appointments-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #f3f4f6;
    margin-bottom: 2rem;
}

.appointments-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.appointment-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    transition: all 0.2s;
}

.appointment-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.appointment-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.appointment-info {
    flex: 1;
}

.appointment-patient-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.appointment-details {
    font-size: 0.75rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.appointment-time {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.appointment-status {
    padding: 0.25rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}
</style>

<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="dashboard-header-left">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.5rem; overflow: hidden; flex-shrink: 0;">
                    <?php if (!empty($profile_picture_url)): ?>
                        <img src="<?= htmlspecialchars($profile_picture_url) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <?= strtoupper(substr($user_name ?? 'A', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <h1 class="dashboard-welcome">Welcome back, <?= htmlspecialchars($user_name) ?>! 👋</h1>
                </div>
            </div>
            <div class="dashboard-date"><?= date('l, F d, Y') ?></div>
        </div>
        <div class="dashboard-header-right">
            <a href="/superadmin/patients" class="add-patient-btn">
                <i class="fas fa-plus"></i>
                <span>Add New Patient</span>
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Total Patient</div>
            <div class="kpi-value"><?= number_format($current_patients) ?></div>
            <div class="kpi-trend <?= $patients_change >= 0 ? 'positive' : 'negative' ?>">
                <i class="fas fa-arrow-<?= $patients_change >= 0 ? 'up' : 'down' ?>"></i>
                <span><?= abs($patients_change) ?>%</span>
                <span class="kpi-trend-text">from Last month</span>
            </div>
        </div>
        
        <div class="kpi-card">
            <div class="kpi-label">New Appointments</div>
            <div class="kpi-value"><?= number_format($current_appointments) ?></div>
            <div class="kpi-trend <?= $appointments_change >= 0 ? 'positive' : 'negative' ?>">
                <i class="fas fa-arrow-<?= $appointments_change >= 0 ? 'up' : 'down' ?>"></i>
                <span><?= abs($appointments_change) ?>%</span>
                <span class="kpi-trend-text">from Last month</span>
            </div>
        </div>
        
        <div class="kpi-card">
            <div class="kpi-label">Medical Records</div>
            <div class="kpi-value"><?= number_format($current_records) ?></div>
            <div class="kpi-trend <?= $records_change >= 0 ? 'positive' : 'negative' ?>">
                <i class="fas fa-arrow-<?= $records_change >= 0 ? 'up' : 'down' ?>"></i>
                <span><?= abs($records_change) ?>%</span>
                <span class="kpi-trend-text">from Last month</span>
            </div>
        </div>
        
        <div class="kpi-card">
            <div class="kpi-label">Patients Today</div>
            <div class="kpi-value"><?= number_format($patients_today) ?></div>
            <div class="kpi-trend-text" style="margin-top: 0.5rem; color: #6b7280;">
                Appointments scheduled today
            </div>
        </div>
    </div>

    <!-- Payment Cards -->
    <div class="payments-grid">
        <div class="kpi-card">
            <div class="kpi-label">Payments This Month</div>
            <div class="kpi-value"><?= number_format($payments_this_month) ?></div>
            <div class="kpi-trend <?= $payments_change >= 0 ? 'positive' : 'negative' ?>">
                <i class="fas fa-arrow-<?= $payments_change >= 0 ? 'up' : 'down' ?>"></i>
                <span><?= abs($payments_change) ?>%</span>
                <span class="kpi-trend-text">from Last month</span>
            </div>
        </div>
        
        <div class="kpi-card">
            <div class="kpi-label">Total Amount</div>
            <div class="kpi-value">₱<?= number_format($total_amount_this_month, 0) ?></div>
            <div class="kpi-trend-text" style="margin-top: 0.5rem; color: #6b7280;">
                This month
            </div>
        </div>
        
        <div class="kpi-card">
            <div class="kpi-label">Paid</div>
            <div class="kpi-value"><?= number_format($paid_this_month) ?></div>
            <div class="kpi-trend positive">
                <i class="fas fa-check-circle"></i>
                <span class="kpi-trend-text">Completed payments</span>
            </div>
        </div>
        
        <div class="kpi-card">
            <div class="kpi-label">Pending</div>
            <div class="kpi-value"><?= number_format($pending_this_month) ?></div>
            <div class="kpi-trend negative">
                <i class="fas fa-clock"></i>
                <span class="kpi-trend-text">Awaiting payment</span>
            </div>
        </div>
    </div>

    <!-- Today's Appointments -->
    <div class="today-appointments-card">
        <div class="chart-header">
            <h2 class="chart-title">Today's Appointments</h2>
            <a href="/superadmin/appointments" style="font-size: 0.875rem; color: #3b82f6; text-decoration: none;">View All</a>
        </div>
        <?php if (empty($today_appointments)): ?>
            <div style="text-align: center; padding: 2rem; color: #6b7280;">
                <i class="fas fa-calendar-times" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                <div>No appointments scheduled for today</div>
            </div>
        <?php else: ?>
            <div class="appointments-list">
                <?php foreach ($today_appointments as $apt): ?>
                    <?php
                    $patInitial = strtoupper(substr($apt['pat_first_name'] ?? 'P', 0, 1));
                    $patName = htmlspecialchars(formatFullName($apt['pat_first_name'] ?? '', $apt['pat_middle_initial'] ?? null, $apt['pat_last_name'] ?? ''));
                    $docName = htmlspecialchars(formatFullName($apt['doc_first_name'] ?? '', $apt['doc_middle_initial'] ?? null, $apt['doc_last_name'] ?? ''));
                    $statusName = strtolower($apt['status_name'] ?? 'scheduled');
                    $statusColor = $apt['status_color'] ?? '#3b82f6';
                    $appointmentTime = isset($apt['appointment_time']) ? date('g:i A', strtotime($apt['appointment_time'])) : 'N/A';
                    $serviceName = htmlspecialchars($apt['service_name'] ?? 'General Consultation');
                    ?>
                    <div class="appointment-item">
                        <div class="appointment-avatar"><?= $patInitial ?></div>
                        <div class="appointment-info">
                            <div class="appointment-patient-name"><?= $patName ?></div>
                            <div class="appointment-details">
                                <span><i class="fas fa-user-md"></i> Dr. <?= $docName ?></span>
                                <span><i class="fas fa-stethoscope"></i> <?= $serviceName ?></span>
                            </div>
                        </div>
                        <div class="appointment-time"><?= $appointmentTime ?></div>
                        <span class="appointment-status" style="background: <?= $statusColor ?>20; color: <?= $statusColor ?>;">
                            <?= htmlspecialchars($apt['status_name'] ?? 'Scheduled') ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Charts Row -->
    <div class="charts-grid">
        <!-- Patient Statistics Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <h2 class="chart-title">Patient Statistic</h2>
            </div>
            <div class="chart-wrapper">
                <canvas id="patientStatisticChart"></canvas>
            </div>
        </div>
        
        <!-- Users by Role Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <h2 class="chart-title">Users by Role</h2>
            </div>
            <div class="chart-wrapper">
                <canvas id="usersByRoleChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="bottom-grid">
        <!-- Top Services -->
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h2 class="chart-title">Top Services</h2>
                    <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">
                        Total: <?= number_format($total_service_appointments) ?> 
                        <span style="color: #10b981; margin-left: 0.5rem;">
                            <i class="fas fa-arrow-up"></i> +<?= count($top_services) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="service-list">
                <?php 
                $colors = ['#3b82f6', '#f59e0b', '#10b981', '#8b5cf6', '#ef4444'];
                foreach ($top_services as $index => $service): 
                    $percentage = $total_service_appointments > 0 ? round(($service['appointment_count'] / $total_service_appointments) * 100) : 0;
                ?>
                    <div>
                        <div class="service-info">
                            <span class="service-name"><?= htmlspecialchars($service['service_name']) ?></span>
                            <span class="service-stats"><?= $service['appointment_count'] ?> Appointments, <?= $percentage ?>%</span>
                        </div>
                        <div class="service-item">
                            <div class="service-bar-wrapper">
                                <div class="service-bar" style="width: <?= $percentage ?>%; background: <?= $colors[$index % count($colors)] ?>;"></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Staff -->
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h2 class="chart-title">Staff</h2>
                    <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">
                        Total: <?= number_format($total_staff_count) ?> 
                        <span style="color: #10b981; margin-left: 0.5rem;">
                            <i class="fas fa-user-md"></i> +<?= count($top_staff) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="staff-list">
                <?php foreach ($top_staff as $staff): ?>
                    <div class="staff-item">
                        <div class="staff-avatar">
                            <?= strtoupper(substr($staff['doc_first_name'] ?? 'D', 0, 1)) ?>
                        </div>
                        <div class="staff-info">
                            <div class="staff-name">Dr. <?= htmlspecialchars(($staff['doc_first_name'] ?? '') . ' ' . ($staff['doc_last_name'] ?? '')) ?></div>
                            <div class="staff-role"><?= htmlspecialchars($staff['spec_name'] ?? 'General Practice') ?></div>
                        </div>
                        <i class="fas fa-chevron-right staff-arrow"></i>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="/superadmin/doctors" class="view-all-btn">View All</a>
        </div>
        
        <!-- Completion Rate -->
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h2 class="chart-title">Completion Rate</h2>
                    <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.5rem;">
                        <?= $completion_rate ?>%
                        <?php if ($completion_change >= 0): ?>
                            <span style="color: #10b981;">
                                <i class="fas fa-arrow-up"></i> +<?= abs($completion_change) ?>%
                            </span>
                        <?php else: ?>
                            <span style="color: #ef4444;">
                                <i class="fas fa-arrow-down"></i> <?= $completion_change ?>%
                            </span>
                        <?php endif; ?>
                        <i class="fas fa-smile" style="color: #10b981;"></i>
                    </div>
                </div>
            </div>
            <div class="chart-wrapper">
                <canvas id="completionRateChart"></canvas>
            </div>
            <div class="satisfaction-description">
                Appointment completion rate is at <?= $completion_rate ?>%. This indicates the efficiency of appointment processing and patient follow-through.
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Patient Statistic Chart (Line Chart)
const patientCtx = document.getElementById('patientStatisticChart').getContext('2d');
const patientChart = new Chart(patientCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Appointments',
            data: <?= json_encode($patient_chart_data) ?>,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 13 },
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 50,
                    font: { size: 11 },
                    color: '#6b7280'
                },
                grid: {
                    color: '#f3f4f6'
                }
            },
            x: {
                ticks: {
                    font: { size: 11 },
                    color: '#6b7280'
                },
                grid: {
                    display: false
                }
            }
        }
    }
});

// Users by Role Chart (Donut Chart)
const roleCtx = document.getElementById('usersByRoleChart').getContext('2d');
const roleChart = new Chart(roleCtx, {
    type: 'doughnut',
    data: {
        labels: ['Patient', 'Doctor', 'Staff', 'Admin'],
        datasets: [{
            data: [
                <?= $users_by_role['Patient'] ?? 0 ?>,
                <?= $users_by_role['Doctor'] ?? 0 ?>,
                <?= $users_by_role['Staff'] ?? 0 ?>,
                <?= $users_by_role['Admin'] ?? 0 ?>
            ],
            backgroundColor: [
                '#60a5fa',
                '#f59e0b',
                '#3b82f6',
                '#4b5563'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 12 },
                    color: '#374151',
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 13 },
                cornerRadius: 8
            }
        },
        cutout: '70%'
    },
    plugins: [{
        id: 'centerText',
        beforeDraw: function(chart) {
            const ctx = chart.ctx;
            const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
            const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
            
            ctx.save();
            ctx.font = 'bold 24px Arial';
            ctx.fillStyle = '#1f2937';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            const total = <?= array_sum($users_by_role) ?>;
            ctx.fillText(total, centerX, centerY - 10);
            
            ctx.font = '12px Arial';
            ctx.fillStyle = '#6b7280';
            ctx.fillText('Total', centerX, centerY + 10);
            ctx.restore();
        }
    }]
});

// Completion Rate Chart (Line Chart)
const completionCtx = document.getElementById('completionRateChart').getContext('2d');
const completionChart = new Chart(completionCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Completion Rate',
            data: <?= json_encode($completion_chart_data) ?>,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            pointHoverRadius: 5,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 13 },
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return 'Completion: ' + context.parsed.y + '%';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    stepSize: 25,
                    font: { size: 11 },
                    color: '#6b7280',
                    callback: function(value) {
                        return value + '%';
                    }
                },
                grid: {
                    color: '#f3f4f6'
                }
            },
            x: {
                ticks: {
                    font: { size: 11 },
                    color: '#6b7280'
                },
                grid: {
                    display: false
                }
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
