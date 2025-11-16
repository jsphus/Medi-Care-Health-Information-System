<?php require_once __DIR__ . '/../partials/header.php'; ?>

<!-- Dashboard Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.5rem; overflow: hidden; flex-shrink: 0;">
            <?php if (!empty($profile_picture_url)): ?>
                <img src="<?= htmlspecialchars($profile_picture_url) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <?= strtoupper(substr($doctor['doc_first_name'] ?? 'D', 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div>
            <h1 class="page-title" style="margin-bottom: 0.5rem;">Welcome back, Dr. <?= htmlspecialchars($doctor['doc_first_name'] ?? 'Doctor') ?>! 👋</h1>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.95rem;">Here's what's happening in your medical practice today.</p>
        </div>
    </div>
    <div style="display: flex; gap: 1rem; align-items: center;">
        <a href="/doctor/schedules" class="btn btn-secondary" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-calendar"></i>
            <span>View Schedule</span>
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stat-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Total Patients Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
            <div>
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Total Patients</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?php 
                    $total_patients = $stats['total_patients'];
                    if ($total_patients >= 1000) {
                        echo number_format($total_patients / 1000, 1) . 'k';
                    } else {
                        echo $total_patients;
                    }
                    ?>
                </div>
                <div style="display: flex; align-items: center; gap: 0.25rem; color: var(--status-success); font-size: 0.875rem;">
                    <i class="fas fa-arrow-up"></i>
                    <span>+<?= $total_patients > 0 ? round(($total_patients * 0.23)) : 0 ?>%</span>
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users" style="color: var(--primary-blue); font-size: 1.5rem;"></i>
            </div>
        </div>
    </div>

    <!-- Active Doctors Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
            <div>
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Active Doctors</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;"><?= $stats['active_doctors'] ?></div>
                <div style="display: flex; align-items: center; gap: 0.25rem; color: var(--status-success); font-size: 0.875rem;">
                    <i class="fas fa-arrow-up"></i>
                    <span>+<?= $stats['active_doctors'] > 0 ? round(($stats['active_doctors'] * 0.10)) : 0 ?>%</span>
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-md" style="color: var(--status-success); font-size: 1.5rem;"></i>
            </div>
        </div>
    </div>

    <!-- Today Appointments Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
            <div>
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Today Appointments</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;"><?= $stats['today_appointments'] ?></div>
                <div style="display: flex; align-items: center; gap: 0.25rem; color: var(--status-success); font-size: 0.875rem;">
                    <i class="fas fa-arrow-up"></i>
                    <span>+<?= $stats['today_appointments'] > 0 ? round(($stats['today_appointments'] * 0.13)) : 0 ?>%</span>
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-calendar-check" style="color: #f59e0b; font-size: 1.5rem;"></i>
            </div>
        </div>
    </div>

    <!-- Pending Lab Results Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
            <div>
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Pending lab results</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;"><?= $stats['pending_lab_results'] ?></div>
                <div style="display: flex; align-items: center; gap: 0.25rem; color: #f59e0b; font-size: 0.875rem;">
                    <i class="fas fa-sync-alt" style="font-size: 0.75rem;"></i>
                    <span>+<?= $stats['pending_lab_results'] > 0 ? round(($stats['pending_lab_results'] * 0.64)) : 0 ?>%</span>
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(251, 191, 36, 0.1); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-flask" style="color: #fbbf24; font-size: 1.5rem;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Recent Appointment Section -->
    <div class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">Recent Appointment</h2>
            <select class="form-control" style="width: auto; padding: 0.5rem 1rem; font-size: 0.875rem;">
                <option>Today</option>
                <option>This Week</option>
                <option>This Month</option>
            </select>
        </div>
        
        <?php if (empty($recent_appointments)): ?>
            <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                <i class="fas fa-calendar" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                <p style="margin: 0;">No recent appointments</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($recent_appointments as $apt): ?>
                    <?php
                    $patName = htmlspecialchars(($apt['pat_first_name'] ?? '') . ' ' . ($apt['pat_last_name'] ?? ''));
                    $docName = 'Dr. ' . htmlspecialchars(($apt['doc_first_name'] ?? '') . ' ' . ($apt['doc_last_name'] ?? ''));
                    $appointmentTime = isset($apt['appointment_time']) ? date('g:i A', strtotime($apt['appointment_time'])) : 'N/A';
                    $statusName = strtolower($apt['status_name'] ?? 'scheduled');
                    $serviceName = htmlspecialchars($apt['service_name'] ?? 'Consultation');
                    
                    // Determine status badge color
                    $statusColor = '#10b981'; // green for completed/active
                    $statusText = 'Completed';
                    if (strpos($statusName, 'pending') !== false || strpos($statusName, 'scheduled') !== false) {
                        $statusColor = '#f59e0b'; // yellow for pending
                        $statusText = 'Pending';
                    } elseif (strpos($statusName, 'active') !== false || strpos($statusName, 'confirmed') !== false) {
                        $statusColor = '#10b981'; // green for active
                        $statusText = 'Active';
                    }
                    ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;"><?= $patName ?></div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary);">
                                <?= $docName ?> - <?= $appointmentTime ?>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                            <span style="font-size: 0.75rem; color: var(--text-secondary);"><?= $serviceName ?></span>
                            <span style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: <?= $statusColor ?>20; color: <?= $statusColor ?>;">
                                <?= $statusText ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Quick Actions Section -->
        <div class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin: 0 0 1.5rem 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">Quick Actions</h2>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="/doctor/appointments/today" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #f9fafb; border-radius: 8px; text-decoration: none; color: var(--text-primary);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-calendar-plus" style="color: var(--primary-blue);"></i>
                        <span style="font-weight: 500;">Schedule Appointment</span>
                    </div>
                    <button class="btn btn-sm" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Add</button>
                </a>
                <a href="/doctor/appointments/today" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #f9fafb; border-radius: 8px; text-decoration: none; color: var(--text-primary);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-calendar-check" style="color: var(--primary-blue);"></i>
                        <span style="font-weight: 500;">View Appointments</span>
                    </div>
                    <button class="btn btn-sm" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">View</button>
                </a>
                <a href="/doctor/doctors" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #f9fafb; border-radius: 8px; text-decoration: none; color: var(--text-primary);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-user-md" style="color: var(--primary-blue);"></i>
                        <span style="font-weight: 500;">View Doctors</span>
                    </div>
                    <button class="btn btn-sm" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">View</button>
                </a>
                <a href="/doctor/medical-records" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #f9fafb; border-radius: 8px; text-decoration: none; color: var(--text-primary);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-file-medical" style="color: var(--primary-blue);"></i>
                        <span style="font-weight: 500;">Create Medical Record</span>
                    </div>
                    <button class="btn btn-sm" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Create</button>
                </a>
            </div>
        </div>

        <!-- Patient Analytics Section -->
        <div class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin: 0 0 1.5rem 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">Patient Analytics</h2>
            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem;">
                <button class="btn btn-sm btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">7 Days</button>
                <button class="btn btn-sm" style="padding: 0.5rem 1rem; font-size: 0.875rem; background: #f3f4f6; color: var(--text-secondary);">30 Days</button>
                <button class="btn btn-sm" style="padding: 0.5rem 1rem; font-size: 0.875rem; background: #f3f4f6; color: var(--text-secondary);">90 Days</button>
            </div>
            <div style="min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f9fafb; border-radius: 8px; padding: 2rem;">
                <p style="color: var(--text-secondary); margin: 0; text-align: center; font-size: 0.875rem;">
                    Chart component will be integrated here<br>
                    <span style="font-size: 0.75rem; opacity: 0.7;">Patient registration and appointment trends.</span>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
