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
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.95rem;">Here's your day at a glance - <?= date('l, F d, Y') ?></p>
        </div>
    </div>
    <div style="display: flex; gap: 1rem; align-items: center;">
        <a href="/doctor/appointments/today" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-calendar-day"></i>
            <span>Today's Schedule</span>
        </a>
        <a href="/doctor/schedules" class="btn btn-secondary" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-calendar"></i>
            <span>Manage Schedule</span>
        </a>
    </div>
</div>

<!-- Statistics Cards - Today Focused -->
<div class="stat-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Today's Appointments Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer;" onclick="window.location.href='/doctor/appointments/today'">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-calendar-day" style="color: var(--primary-blue);"></i>
                    <span>Today's Appointments</span>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['today_appointments']) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    <?= $stats['today_completed'] ?> completed, <?= $stats['today_pending'] ?> pending
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-calendar-day" style="font-size: 1.5rem; color: var(--primary-blue);"></i>
            </div>
        </div>
    </div>

    <!-- Next Appointment Widget (Larger if exists) -->
    <?php if ($next_appointment): ?>
        <?php
        $nextApptTime = strtotime(date('Y-m-d') . ' ' . $next_appointment['appointment_time']);
        $currentTime = time();
        $timeUntil = $nextApptTime - $currentTime;
        $hoursUntil = floor($timeUntil / 3600);
        $minutesUntil = floor(($timeUntil % 3600) / 60);
        $timeText = $timeUntil < 0 ? 'Overdue' : ($hoursUntil > 0 ? "In $hoursUntil hours" : ($minutesUntil > 0 ? "In $minutesUntil minutes" : 'Starting now'));
        $isOverdue = $timeUntil < 0;
        $isSoon = $timeUntil > 0 && $timeUntil < 3600;
        ?>
        <div class="stat-card" style="background: linear-gradient(135deg, <?= $isOverdue ? '#ef4444' : ($isSoon ? '#f59e0b' : '#10b981') ?> 0%, <?= $isOverdue ? '#dc2626' : ($isSoon ? '#d97706' : '#059669') ?> 100%); border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; grid-column: span 2;" onclick="window.location.href='/doctor/appointments/today'">
            <div style="display: flex; justify-content: space-between; align-items: center; color: white;">
                <div style="flex: 1;">
                    <div style="font-size: 0.875rem; margin-bottom: 0.5rem; font-weight: 500; opacity: 0.9;">
                        <i class="fas fa-clock"></i> Next Appointment
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">
                        <?= htmlspecialchars(($next_appointment['pat_first_name'] ?? '') . ' ' . ($next_appointment['pat_last_name'] ?? '')) ?>
                    </div>
                    <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.25rem;">
                        <?= isset($next_appointment['appointment_time']) ? date('g:i A', strtotime($next_appointment['appointment_time'])) : 'N/A' ?> - <?= htmlspecialchars($next_appointment['service_name'] ?? 'Consultation') ?>
                    </div>
                    <div style="font-size: 0.75rem; font-weight: 600; opacity: 0.95;">
                        <?= $timeText ?>
                    </div>
                </div>
                <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <?php if (!empty($next_appointment['patient_profile_picture'])): ?>
                        <img src="<?= htmlspecialchars($next_appointment['patient_profile_picture']) ?>" alt="Patient" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <span style="font-size: 1.5rem; font-weight: 700;"><?= strtoupper(substr($next_appointment['pat_first_name'] ?? 'P', 0, 1)) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Pending Tasks Card (if no next appointment) -->
        <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer;" onclick="document.getElementById('pending-tasks-section').scrollIntoView({behavior: 'smooth'})">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-tasks" style="color: #f59e0b;"></i>
                        <span>Pending Tasks</span>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                        <?= number_format(($stats['pending_records'] ?? 0) + ($stats['follow_up_count'] ?? 0)) ?>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">
                        <?= $stats['pending_records'] ?? 0 ?> records, <?= $stats['follow_up_count'] ?? 0 ?> follow-ups
                    </div>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-tasks" style="font-size: 1.5rem; color: #f59e0b;"></i>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Pending Tasks Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer;" onclick="document.getElementById('pending-tasks-section').scrollIntoView({behavior: 'smooth'})">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-clipboard-list" style="color: #f59e0b;"></i>
                    <span>Pending Tasks</span>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format(($stats['pending_records'] ?? 0) + ($stats['follow_up_count'] ?? 0)) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Records & follow-ups
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-clipboard-list" style="font-size: 1.5rem; color: #f59e0b;"></i>
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
                    <?= number_format($stats['this_week_appointments'] ?? 0) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    <?= $stats['this_week_completed'] ?? 0 ?> completed
                </div>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(139, 92, 246, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-calendar-week" style="font-size: 1.5rem; color: #8b5cf6;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Left Column: Today's Schedule -->
    <div class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-calendar-day" style="margin-right: 0.5rem; color: var(--primary-blue);"></i>
                Today's Schedule
            </h2>
            <a href="/doctor/appointments/today" style="color: var(--primary-blue); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                View All <i class="fas fa-arrow-right" style="margin-left: 0.25rem;"></i>
            </a>
        </div>
        
        <?php if (empty($today_appointments)): ?>
            <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <p style="margin: 0; font-size: 1rem; font-weight: 500;">No appointments scheduled for today</p>
                <a href="/doctor/schedules" style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: var(--primary-blue); color: white; border-radius: 8px; text-decoration: none; font-size: 0.875rem;">
                    Manage Schedule
                </a>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php 
                $currentTime = time();
                foreach ($today_appointments as $index => $apt): 
                    $patName = htmlspecialchars(($apt['pat_first_name'] ?? '') . ' ' . ($apt['pat_last_name'] ?? ''));
                    $appointmentTime = isset($apt['appointment_time']) ? date('g:i A', strtotime($apt['appointment_time'])) : 'N/A';
                    $statusName = strtolower($apt['status_name'] ?? 'scheduled');
                    $isCompleted = $statusName === 'completed';
                    $isCanceled = $statusName === 'canceled' || $statusName === 'cancelled';
                    $appointmentTimeStr = isset($apt['appointment_time']) ? $apt['appointment_time'] : '';
                    
                    // Check if next appointment
                    $isNext = false;
                    $isOverdue = false;
                    if (!$isCompleted && !$isCanceled && !empty($appointmentTimeStr)) {
                        $appointmentTimestamp = strtotime(date('Y-m-d') . ' ' . $appointmentTimeStr);
                        $timeDiff = $appointmentTimestamp - $currentTime;
                        if ($timeDiff < 0 && $timeDiff > -3600) {
                            $isOverdue = true;
                        }
                        if ($index === 0 || ($timeDiff > 0 && ($index === 0 || !isset($today_appointments[$index-1])))) {
                            $isNext = true;
                        }
                    }
                    
                    $rowStyle = '';
                    if ($isNext) $rowStyle = 'border-left: 4px solid #f59e0b; background: #fef3c7;';
                    if ($isOverdue) $rowStyle = 'border-left: 4px solid #ef4444; background: #fee2e2;';
                ?>
                    <div style="padding: 1rem; background: #f9fafb; border-radius: 8px; border: 1px solid var(--border-light); <?= $rowStyle ?>">
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <div style="text-align: center; min-width: 80px;">
                                <div style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary);">
                                    <?= $appointmentTime ?>
                                </div>
                                <?php if ($isNext): ?>
                                    <span style="font-size: 0.75rem; color: #f59e0b; font-weight: 600;">
                                        <i class="fas fa-clock"></i> Next
                                    </span>
                                <?php elseif ($isOverdue): ?>
                                    <span style="font-size: 0.75rem; color: #ef4444; font-weight: 600;">
                                        <i class="fas fa-exclamation-triangle"></i> Overdue
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; flex-shrink: 0;">
                                <?= strtoupper(substr($apt['pat_first_name'] ?? 'P', 0, 1)) ?>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">
                                    <?= $patName ?>
                                </div>
                                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">
                                    <?= htmlspecialchars($apt['service_name'] ?? 'Consultation') ?>
                                </div>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <span class="badge" style="background: <?= $apt['status_color'] ?? '#3B82F6' ?>; color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.75rem;">
                                        <?= htmlspecialchars($apt['status_name'] ?? 'N/A') ?>
                                    </span>
                                    <a href="/doctor/appointments/today" style="color: var(--primary-blue); text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                                        View <i class="fas fa-arrow-right" style="margin-left: 0.25rem; font-size: 0.75rem;"></i>
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
                <a href="/doctor/appointments/today" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f9fafb; border-radius: 8px; text-decoration: none; color: var(--text-primary); transition: background 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
                    <i class="fas fa-calendar-day" style="color: var(--primary-blue); font-size: 1.25rem; width: 24px;"></i>
                    <div>
                        <div style="font-weight: 600; font-size: 0.875rem;">View Today's Schedule</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">See all appointments</div>
                    </div>
                </a>
                <a href="/doctor/medical-records" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f9fafb; border-radius: 8px; text-decoration: none; color: var(--text-primary); transition: background 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
                    <i class="fas fa-file-medical" style="color: #8b5cf6; font-size: 1.25rem; width: 24px;"></i>
                    <div>
                        <div style="font-weight: 600; font-size: 0.875rem;">Medical Records</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">View patient records</div>
                    </div>
                </a>
                <a href="/doctor/schedules/manage" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f9fafb; border-radius: 8px; text-decoration: none; color: var(--text-primary); transition: background 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
                    <i class="fas fa-calendar-plus" style="color: var(--status-success); font-size: 1.25rem; width: 24px;"></i>
                    <div>
                        <div style="font-weight: 600; font-size: 0.875rem;">Manage Schedule</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">Update availability</div>
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
                                <i class="fas fa-check-circle" style="color: var(--status-success); font-size: 0.875rem;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.875rem; color: var(--text-primary); margin-bottom: 0.25rem;">
                                    Completed appointment with <strong><?= htmlspecialchars(($activity['pat_first_name'] ?? '') . ' ' . ($activity['pat_last_name'] ?? '')) ?></strong>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                    <?= isset($activity['appointment_time']) ? date('g:i A', strtotime($activity['appointment_time'])) : 'N/A' ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pending Tasks Section -->
        <div id="pending-tasks-section" class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 1.5rem 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-clipboard-list" style="margin-right: 0.5rem; color: #f59e0b;"></i>
                Pending Tasks
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <?php if (($stats['pending_records'] ?? 0) > 0): ?>
                    <a href="/doctor/medical-records" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #fef3c7; border-radius: 8px; text-decoration: none; color: var(--text-primary);">
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem;">Medical Records</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);"><?= $stats['pending_records'] ?> appointments need records</div>
                        </div>
                        <span class="badge" style="background: #f59e0b; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                            <?= $stats['pending_records'] ?>
                        </span>
                    </a>
                <?php endif; ?>
                
                <?php if (($stats['follow_up_count'] ?? 0) > 0): ?>
                    <a href="/doctor/appointments/future" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #fef3c7; border-radius: 8px; text-decoration: none; color: var(--text-primary);">
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem;">Follow-up Appointments</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Scheduled follow-ups</div>
                        </div>
                        <span class="badge" style="background: #f59e0b; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem;">
                            <?= $stats['follow_up_count'] ?>
                        </span>
                    </a>
                <?php endif; ?>
                
                <?php if (($stats['pending_records'] ?? 0) == 0 && ($stats['follow_up_count'] ?? 0) == 0): ?>
                    <div style="text-align: center; padding: 1rem; color: var(--text-secondary);">
                        <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 0.5rem; color: var(--status-success); opacity: 0.5;"></i>
                        <p style="margin: 0; font-size: 0.875rem;">All caught up!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/../partials/footer.php'; ?>
