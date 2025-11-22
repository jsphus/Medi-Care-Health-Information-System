<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
    .appointment-booking-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: 1px solid var(--border-light);
        overflow: hidden;
    }
    
    .appointment-section {
        padding: 2rem;
        border-bottom: 1px solid var(--border-light);
    }
    
    .appointment-section:last-child {
        border-bottom: none;
    }
    
    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .section-header i {
        color: var(--primary-blue);
        font-size: 1.25rem;
    }
    
    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }
    
    .doctor-display {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.5rem;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid var(--border-light);
    }
    
    .doctor-avatar-select {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.75rem;
        flex-shrink: 0;
        overflow: hidden;
    }
    
    .doctor-avatar-select img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .doctor-info-select {
        flex: 1;
    }
    
    .doctor-name-select {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }
    
    .doctor-spec-select {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }
    
    .doctor-fee-select {
        font-size: 1rem;
        font-weight: 600;
        color: var(--primary-blue);
    }
    
    .time-slots {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    
    .time-slot-btn {
        padding: 0.75rem 1rem;
        background: white;
        border: 2px solid var(--border-medium);
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
    }
    
    .time-slot-btn:hover {
        border-color: var(--primary-blue);
        background: #f0f7ff;
    }
    
    .time-slot-btn.active {
        background: var(--primary-blue);
        border-color: var(--primary-blue);
        color: white;
    }
    
    .schedule-card {
        background: white;
        border: 2px solid var(--border-medium);
        border-radius: 12px;
        padding: 1.25rem;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
        width: 100%;
    }
    
    .schedule-card:hover:not(:disabled) {
        border-color: var(--primary-blue);
        background: #f0f7ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .schedule-card.available.selected {
        background: var(--primary-blue);
        border-color: var(--primary-blue);
        color: white;
    }
    
    .schedule-card.available.selected .schedule-time,
    .schedule-card.available.selected .schedule-availability {
        color: white;
    }
    
    .schedule-card.full {
        opacity: 0.6;
        cursor: not-allowed;
        background: #f3f4f6;
    }
    
    .schedule-card.full:hover {
        transform: none;
        box-shadow: none;
    }
    
    .time-slot-btn-preset {
        padding: 0.875rem 1rem;
        background: white;
        border: 2px solid var(--border-medium);
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
    }
    
    .time-slot-btn-preset:hover {
        border-color: var(--primary-blue);
        background: #f0f7ff;
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .time-slot-btn-preset.selected {
        background: var(--primary-blue);
        border-color: var(--primary-blue);
        color: white;
    }
    
    .custom-time-toggle {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-light);
    }
    
    .custom-time-toggle-btn {
        background: none;
        border: none;
        color: var(--primary-blue);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        padding: 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .custom-time-toggle-btn:hover {
        color: var(--primary-blue-dark);
    }
    
    .custom-time-input {
        margin-top: 1rem;
        display: none;
    }
    
    .custom-time-input.show {
        display: block;
    }
    
    .submit-section {
        padding: 1.5rem 2rem;
        background: #f9fafb;
        border-top: 1px solid var(--border-light);
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }
    
    @media (max-width: 768px) {
        .appointment-section {
            padding: 1.5rem;
        }
        
        .doctor-display {
            flex-direction: column;
            text-align: center;
        }
        
        .time-slots {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .submit-section {
            flex-direction: column;
        }
    }
</style>

<div class="page-header">
    <div class="page-header-top">
        <h1 class="page-title">
            <?php if ($selected_doctor_id): ?>
                Book Appointment
            <?php else: ?>
                Book New Appointment
            <?php endif; ?>
        </h1>
    </div>
</div>

<?php if (isset($error) && $error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<?php if (isset($success) && $success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <div>
            <p style="margin-bottom: 1rem;"><?= $success ?></p>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="/patient/appointments" class="btn btn-success">
                    <i class="fas fa-calendar"></i>
                    <span>View My Appointments</span>
                </a>
                <a href="/patient/appointments/create" class="btn btn-secondary">
                    <i class="fas fa-plus"></i>
                    <span>Book Another</span>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="appointment-booking-card">
    <form method="POST" id="appointmentForm">
        
        <!-- Section 1: Doctor Details -->
        <div class="appointment-section">
            <div class="section-header">
                <i class="fas fa-user-md"></i>
                <h3 class="section-title">Doctor Details</h3>
            </div>
            
            <?php
            $display_doctor = null;
            $doctor_initials = '';
            $doctor_name = '';
            $doctor_spec = '';
            $doctor_fee = 0;
            $doctor_profile = '';
            
            // Get session data for pre-populating form
            $session_data = $session_appointment_data ?? null;
            
            if ($reschedule_id && $existing_appointment) {
                $display_doctor = $existing_appointment;
                $doctor_initials = strtoupper(substr($existing_appointment['doc_first_name'] ?? 'D', 0, 1) . substr($existing_appointment['doc_last_name'] ?? 'D', 0, 1));
                $doctor_name = 'Dr. ' . htmlspecialchars($existing_appointment['doc_first_name'] . ' ' . $existing_appointment['doc_last_name']);
                $doctor_spec = htmlspecialchars($existing_appointment['spec_name'] ?? 'General Practice');
                $doctor_fee = $existing_appointment['doc_consultation_fee'] ?? 0;
                $doctor_profile = $existing_appointment['profile_picture_url'] ?? '';
            } elseif ($selected_doctor_id) {
                foreach ($doctors as $doc) {
                    if ($doc['doc_id'] == $selected_doctor_id) {
                        $display_doctor = $doc;
                        break;
                    }
                }
                if ($display_doctor) {
                    $doctor_initials = strtoupper(substr($display_doctor['doc_first_name'] ?? 'D', 0, 1) . substr($display_doctor['doc_last_name'] ?? 'D', 0, 1));
                    $doctor_name = 'Dr. ' . htmlspecialchars($display_doctor['doc_first_name'] . ' ' . $display_doctor['doc_last_name']);
                    $doctor_spec = htmlspecialchars($display_doctor['spec_name'] ?? 'General Practice');
                    $doctor_fee = $display_doctor['doc_consultation_fee'] ?? 0;
                    $doctor_profile = $display_doctor['profile_picture_url'] ?? '';
                }
            }
            ?>
            
            <?php if ($display_doctor): ?>
                <input type="hidden" name="doctor_id" value="<?= $display_doctor['doc_id'] ?>">
                <div class="doctor-display">
                    <div class="doctor-avatar-select">
                        <?php if (!empty($doctor_profile)): ?>
                            <img src="<?= htmlspecialchars($doctor_profile) ?>" alt="<?= $doctor_name ?>">
                        <?php else: ?>
                            <?= $doctor_initials ?>
                        <?php endif; ?>
                    </div>
                    <div class="doctor-info-select">
                        <div class="doctor-name-select"><?= $doctor_name ?></div>
                        <div class="doctor-spec-select"><?= $doctor_spec ?></div>
                        <div class="doctor-fee-select">₱<?= number_format($doctor_fee, 2) ?> per consultation</div>
                    </div>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <select name="doctor_id" id="doctor_id" required onchange="updateDoctorDisplay()" class="form-control">
                        <option value="">Choose a doctor...</option>
                        <?php if (empty($doctors) && $appointment_date && $appointment_time): ?>
                            <option value="" disabled>No doctors available for the selected date and time</option>
                        <?php else: ?>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= $doctor['doc_id'] ?>" 
                                        <?= ($selected_doctor_id && $doctor['doc_id'] == $selected_doctor_id) ? 'selected' : '' ?>
                                        data-name="<?= htmlspecialchars($doctor['doc_first_name'] . ' ' . $doctor['doc_last_name']) ?>"
                                        data-spec="<?= htmlspecialchars($doctor['spec_name'] ?? 'General') ?>"
                                        data-fee="<?= number_format($doctor['doc_consultation_fee'] ?? 0, 2) ?>"
                                        data-profile="<?= htmlspecialchars($doctor['profile_picture_url'] ?? '') ?>"
                                        data-initials="<?= strtoupper(substr($doctor['doc_first_name'] ?? 'D', 0, 1) . substr($doctor['doc_last_name'] ?? 'D', 0, 1)) ?>">
                                    Dr. <?= htmlspecialchars($doctor['doc_first_name'] . ' ' . $doctor['doc_last_name']) ?> 
                                    - <?= htmlspecialchars($doctor['spec_name'] ?? 'General') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (empty($doctors) && $appointment_date && $appointment_time): ?>
                        <small class="form-text" style="margin-top: 0.5rem; color: #ef4444;">
                            <i class="fas fa-exclamation-circle"></i> No doctors are available for <?= date('M j, Y', strtotime($appointment_date)) ?> at <?= date('g:i A', strtotime($appointment_time)) ?>. Please try a different date or time.
                        </small>
                    <?php elseif ($appointment_date && $appointment_time && !empty($doctors)): ?>
                        <small class="form-text text-muted" style="margin-top: 0.5rem;">
                            <i class="fas fa-info-circle"></i> Showing <?= count($doctors) ?> doctor<?= count($doctors) > 1 ? 's' : '' ?> available for this time slot.
                        </small>
                    <?php else: ?>
                        <small id="doctorFilterMessage" class="form-text text-muted" style="display: none; margin-top: 0.5rem;"></small>
                    <?php endif; ?>
                </div>
                <div id="doctorDisplay" class="doctor-display" style="display: none;">
                    <div class="doctor-avatar-select" id="doctorAvatar">
                        <span id="doctorInitials"></span>
                    </div>
                    <div class="doctor-info-select">
                        <div class="doctor-name-select" id="doctorName"></div>
                        <div class="doctor-spec-select" id="doctorSpec"></div>
                        <div class="doctor-fee-select" id="doctorFee"></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Section 2: Appointment Details -->
        <div class="appointment-section">
            <div class="section-header">
                <i class="fas fa-clipboard-list"></i>
                <h3 class="section-title">Appointment Details</h3>
            </div>
            
            <div class="form-group">
                <label>Service (Optional):</label>
                <?php if ($reschedule_id && $existing_appointment): ?>
                    <?php
                    $service_name = 'None';
                    if ($existing_appointment['service_id']) {
                        foreach ($services as $service) {
                            if ($service['service_id'] == $existing_appointment['service_id']) {
                                $service_name = htmlspecialchars($service['service_name']) . ' - ₱' . number_format($service['service_price'] ?? 0, 2) . ' (' . ($service['service_duration_minutes'] ?? 30) . ' min)';
                                break;
                            }
                        }
                    }
                    ?>
                    <input type="text" 
                           value="<?= $service_name ?>" 
                           class="form-control" 
                           disabled 
                           style="background-color: #f3f4f6; cursor: not-allowed;">
                <?php else: ?>
                    <?php
                    $selected_service_id = null;
                    if ($session_data && isset($session_data['service_id'])) {
                        $selected_service_id = (int)$session_data['service_id'];
                    } elseif ($existing_appointment && isset($existing_appointment['service_id'])) {
                        $selected_service_id = (int)$existing_appointment['service_id'];
                    }
                    ?>
                    <select name="service_id" class="form-control">
                        <option value="">Select a service (optional)...</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= $service['service_id'] ?>" <?= ($selected_service_id && $service['service_id'] == $selected_service_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($service['service_name']) ?> 
                                - ₱<?= number_format($service['service_price'] ?? 0, 2) ?>
                                (<?= $service['service_duration_minutes'] ?? 30 ?> min)
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Notes/Reason for Visit:</label>
                <?php
                $notes_value = '';
                if ($session_data && isset($session_data['notes'])) {
                    $notes_value = htmlspecialchars($session_data['notes']);
                } elseif ($existing_appointment && isset($existing_appointment['appointment_notes'])) {
                    $notes_value = htmlspecialchars($existing_appointment['appointment_notes']);
                }
                ?>
                <textarea name="notes" rows="3" placeholder="Please describe your symptoms or reason for visit (optional)..." class="form-control"><?= $notes_value ?></textarea>
            </div>
        </div>
        
        <!-- Section 3: Available Schedules -->
        <div class="appointment-section">
            <div class="section-header">
                <i class="fas fa-calendar-alt"></i>
                <h3 class="section-title">Select Available Time Slot</h3>
            </div>
            
            <?php if (!$display_doctor): ?>
                <div class="alert" style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem; border-radius: 0.5rem;">
                    <i class="fas fa-info-circle" style="color: #f59e0b;"></i>
                    <span style="color: #92400e;">Please select a doctor first to see available time slots.</span>
                </div>
            <?php elseif (empty($doctor_schedules)): ?>
                <div class="alert" style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 1rem; border-radius: 0.5rem;">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                    <span style="color: #991b1b;">No available schedules found for this doctor. Please check back later or select another doctor.</span>
                </div>
            <?php else: ?>
                <input type="hidden" name="appointment_date" id="appointment_date" required>
                <input type="hidden" name="appointment_time" id="appointment_time" required>
                
                <div class="schedules-container">
                <?php
                    // Group schedules by date
                    $schedules_by_date = [];
                    foreach ($doctor_schedules as $schedule) {
                        $date = $schedule['schedule_date'];
                        if (!isset($schedules_by_date[$date])) {
                            $schedules_by_date[$date] = [];
                        }
                        $schedules_by_date[$date][] = $schedule;
                    }
                    ?>
                    
                        <?php foreach ($schedules_by_date as $date => $schedules): ?>
                            <?php
                            // Get the earliest start time and latest end time for this date
                            $earliest_start = null;
                            $latest_end = null;
                            $schedule_ranges = [];
                            
                            foreach ($schedules as $schedule) {
                                $schedule_id = $schedule['schedule_id'];
                                $current_count = $schedule_appointment_counts[$schedule_id] ?? 0;
                                
                                $start_time_str = $schedule['start_time'];
                                $end_time_str = $schedule['end_time'];
                                
                                if ($earliest_start === null || $start_time_str < $earliest_start) {
                                    $earliest_start = $start_time_str;
                                }
                                if ($latest_end === null || $end_time_str > $latest_end) {
                                    $latest_end = $end_time_str;
                                }
                                
                                $schedule_ranges[] = [
                                    'start' => $start_time_str,
                                    'end' => $end_time_str,
                                    'schedule_id' => $schedule_id
                                ];
                            }
                            
                            $schedule_start_display = $earliest_start ? date('g:i A', strtotime($earliest_start)) : '';
                            $schedule_end_display = $latest_end ? date('g:i A', strtotime($latest_end)) : '';
                            
                            // Generate preset time slots (every 30 minutes) within the schedule range
                            $preset_times = [];
                            if ($earliest_start && $latest_end) {
                                $start_datetime = new DateTime($date . ' ' . $earliest_start);
                                $end_datetime = new DateTime($date . ' ' . $latest_end);
                                $current_time = clone $start_datetime;
                                
                                while ($current_time < $end_datetime) {
                                    $time_str = $current_time->format('H:i:s');
                                    $time_display = $current_time->format('g:i A');
                                    
                                    // Check if this time falls within any schedule range
                                    $is_within_schedule = false;
                                    foreach ($schedule_ranges as $range) {
                                        if ($time_str >= $range['start'] && $time_str < $range['end']) {
                                            $is_within_schedule = true;
                                            break;
                                        }
                                    }
                                    
                                    if ($is_within_schedule) {
                                        $preset_times[] = [
                                            'time' => $time_str,
                                            'display' => $time_display
                                        ];
                                    }
                                    
                                    // Add 30 minutes
                                    $current_time->modify('+30 minutes');
                                }
                }
                ?>
                            <div class="schedule-date-group" style="margin-bottom: 1.5rem; padding: 1.5rem; background: #f9fafb; border-radius: 12px; border: 1px solid var(--border-light);">
                                <div class="schedule-date-header" 
                                     onclick="toggleDateSlots('<?= htmlspecialchars($date) ?>')"
                                     style="margin-bottom: 0; padding-bottom: 1rem; border-bottom: 2px solid var(--border-light); cursor: pointer; transition: all 0.2s;"
                                     onmouseover="this.style.background='#f0f7ff'" 
                                     onmouseout="this.style.background='transparent'"
                                     data-date="<?= htmlspecialchars($date) ?>">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div style="flex: 1;">
                                            <h4 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin: 0 0 0.5rem 0;">
                                                <i class="fas fa-calendar-day" style="margin-right: 0.5rem; color: var(--primary-blue);"></i>
                                                <?= date('l, F j, Y', strtotime($date)) ?>
                                            </h4>
                                            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                                <div style="font-size: 0.875rem; color: var(--text-secondary);">
                                                    <i class="fas fa-clock" style="margin-right: 0.5rem; color: var(--primary-blue);"></i>
                                                    <strong>Available:</strong> <?= $schedule_start_display ?> - <?= $schedule_end_display ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="margin-left: 1rem;">
                                            <i class="fas fa-chevron-down date-toggle-icon" 
                                               id="toggle-icon-<?= htmlspecialchars($date) ?>"
                                               style="color: var(--primary-blue); transition: transform 0.3s;"></i>
                                        </div>
                                    </div>
            </div>
            
                                <div class="date-time-slots" 
                                     id="time-slots-<?= htmlspecialchars($date) ?>"
                                     style="display: none; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-light);">
                                    <div style="margin-bottom: 1rem;">
                                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem; display: block;">
                                            Select a time slot:
                                        </label>
                                        <div class="time-slots-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 0.75rem;">
                                            <?php foreach ($preset_times as $preset): ?>
                                                <button type="button" 
                                                        class="time-slot-btn-preset" 
                                                        data-date="<?= htmlspecialchars($date) ?>"
                                                        data-time="<?= htmlspecialchars($preset['time']) ?>"
                                                        data-schedule-date="<?= htmlspecialchars($date) ?>"
                                                        data-schedule-start="<?= htmlspecialchars($earliest_start) ?>"
                                                        data-schedule-end="<?= htmlspecialchars($latest_end) ?>">
                                                    <?= $preset['display'] ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                </div>
                
                <!-- Custom Time Option -->
                <div style="margin-top: 2rem; padding: 1.5rem; background: #f9fafb; border-radius: 12px; border: 1px solid var(--border-light);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <label style="font-weight: 600; color: var(--text-primary);">
                            <i class="fas fa-clock" style="margin-right: 0.5rem; color: var(--primary-blue);"></i>
                            Or enter a custom time:
                        </label>
                        <button type="button" class="custom-time-toggle-btn" onclick="toggleCustomTime()" style="background: none; border: none; color: var(--primary-blue); font-size: 0.875rem; cursor: pointer; padding: 0.5rem 1rem; border-radius: 6px; transition: all 0.2s;">
                            <i class="fas fa-edit"></i>
                        <span id="customTimeToggleText">Select custom time</span>
                    </button>
                </div>
                
                    <div class="custom-time-input" id="customTimeInput" style="display: none;">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                                    Select Date: <span style="color: var(--status-error);">*</span>
                                </label>
                                <input type="date" 
                                       id="custom_date_input" 
                                       min="<?= date('Y-m-d') ?>" 
                                       class="form-control" 
                                       style="max-width: 200px;">
                </div>
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">
                                    Enter Time: <span style="color: var(--status-error);">*</span>
                                    <small id="scheduleRangeHint" style="display: block; font-weight: normal; color: var(--text-secondary); margin-top: 0.25rem;">
                                        Must be within the doctor's schedule time range for the selected date
                    </small>
                                </label>
                                <input type="time" 
                                       id="custom_time_input" 
                                       class="form-control" 
                                       style="max-width: 200px;"
                                       placeholder="Select time">
            </div>
                        </div>
                        <small id="customTimeValidation" class="form-text" style="margin-top: 0.5rem; display: none;"></small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <input type="hidden" name="action" value="<?= ($reschedule_id && $existing_appointment) ? 'reschedule' : 'review' ?>">
        
        <!-- Submit Section -->
        <div class="submit-section">
            <?php if ($reschedule_id && $existing_appointment): ?>
                <a href="/patient/appointments" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-<?= ($reschedule_id && $existing_appointment) ? 'calendar-check' : 'arrow-right' ?>"></i>
                <span><?= $reschedule_id && $existing_appointment ? 'Reschedule Appointment' : 'Review Appointment' ?></span>
            </button>
        </div>
    </form>
</div>

<script>
// Update doctor display when selected
function updateDoctorDisplay() {
    const select = document.getElementById('doctor_id');
    const display = document.getElementById('doctorDisplay');
    const option = select.options[select.selectedIndex];
    
    if (option.value && display) {
        const avatar = document.getElementById('doctorAvatar');
        const initials = document.getElementById('doctorInitials');
        const name = document.getElementById('doctorName');
        const spec = document.getElementById('doctorSpec');
        const fee = document.getElementById('doctorFee');
        const timeInput = document.getElementById('appointment_time');
        
        // Clear existing image
        const existingImg = avatar.querySelector('img');
        if (existingImg) {
            existingImg.remove();
        }
        
        // Set doctor info
        name.textContent = 'Dr. ' + option.dataset.name;
        spec.textContent = option.dataset.spec;
        fee.textContent = '₱' + option.dataset.fee + ' per consultation';
        
        // Set avatar
        if (option.dataset.profile) {
            const img = document.createElement('img');
            img.src = option.dataset.profile;
            img.alt = 'Dr. ' + option.dataset.name;
            avatar.innerHTML = '';
            avatar.appendChild(img);
        } else {
            initials.textContent = option.dataset.initials;
            avatar.innerHTML = '<span id="doctorInitials">' + option.dataset.initials + '</span>';
        }
        
        display.style.display = 'flex';
        
        // Clear schedule selection when doctor changes
        clearScheduleSelection();
    } else if (display) {
        display.style.display = 'none';
    }
}

// Schedule selection
let selectedSchedule = null;
let customTimeActive = false;
let availableSchedules = [];

// Store available schedules for validation
document.addEventListener('DOMContentLoaded', function() {
    // Collect all schedule data
    <?php if (!empty($doctor_schedules)): ?>
    availableSchedules = [
        <?php 
        $schedule_items = [];
        foreach ($doctor_schedules as $schedule) {
            $schedule_items[] = "{
            date: '" . htmlspecialchars($schedule['schedule_date'], ENT_QUOTES) . "',
            startTime: '" . htmlspecialchars($schedule['start_time'], ENT_QUOTES) . "',
            endTime: '" . htmlspecialchars($schedule['end_time'], ENT_QUOTES) . "',
            scheduleId: " . $schedule['schedule_id'] . "
        }";
        }
        echo implode(",\n        ", $schedule_items);
        ?>
    ];
    <?php endif; ?>
    
    const dateInput = document.getElementById('appointment_date');
    const timeInput = document.getElementById('appointment_time');
    const customTimeInput = document.getElementById('custom_time_input');
    
    // Handle preset time slot button clicks
    const presetTimeButtons = document.querySelectorAll('.time-slot-btn-preset');
    presetTimeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove selected class from all preset buttons
            document.querySelectorAll('.time-slot-btn-preset').forEach(b => b.classList.remove('selected'));
            
            // Hide custom time if active
            if (customTimeActive) {
                toggleCustomTime();
            }
            
            // Add selected class to clicked button
            this.classList.add('selected');
            selectedSchedule = {
                date: this.dataset.date,
                time: this.dataset.time
            };
            
            // Set the hidden inputs
            if (dateInput) {
                dateInput.value = selectedSchedule.date;
            }
            if (timeInput) {
                timeInput.value = selectedSchedule.time;
            }
            
            // Clear custom time input
            if (customTimeInput) {
                customTimeInput.value = '';
            }
            const customDateInput = document.getElementById('custom_date_input');
            if (customDateInput) {
                customDateInput.value = '';
            }
        });
    });
    
    // Handle custom date input
    const customDateInput = document.getElementById('custom_date_input');
    const scheduleRangeHint = document.getElementById('scheduleRangeHint');
    if (customDateInput) {
        customDateInput.addEventListener('change', function() {
            // Clear preset button selections when date changes
            document.querySelectorAll('.time-slot-btn-preset').forEach(b => b.classList.remove('selected'));
            selectedSchedule = null;
            
            // Clear the hidden inputs
            const dateInput = document.getElementById('appointment_date');
            const timeInput = document.getElementById('appointment_time');
            if (dateInput) dateInput.value = '';
            if (timeInput) timeInput.value = '';
        
            // Update schedule range hint
            const selectedDate = this.value;
            const schedulesForDate = availableSchedules.filter(s => s.date === selectedDate);
            if (schedulesForDate.length > 0 && scheduleRangeHint) {
                let earliestStart = null;
                let latestEnd = null;
                for (const schedule of schedulesForDate) {
                    if (earliestStart === null || schedule.startTime < earliestStart) {
                        earliestStart = schedule.startTime;
                    }
                    if (latestEnd === null || schedule.endTime > latestEnd) {
                        latestEnd = schedule.endTime;
                    }
                }
                if (earliestStart && latestEnd) {
                    const startDisplay = new Date('2000-01-01T' + earliestStart).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                    const endDisplay = new Date('2000-01-01T' + latestEnd).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                    scheduleRangeHint.textContent = `Must be within ${startDisplay} - ${endDisplay} for this date`;
                    scheduleRangeHint.style.color = 'var(--primary-blue)';
                }
            } else if (scheduleRangeHint) {
                scheduleRangeHint.textContent = 'Must be within the doctor\'s schedule time range for the selected date';
                scheduleRangeHint.style.color = 'var(--text-secondary)';
            }
            
            // If time is already entered, validate it
            if (customTimeInput && customTimeInput.value) {
                validateCustomTime(customTimeInput.value);
            }
        });
    }
    
    // Handle custom time input
    if (customTimeInput) {
        customTimeInput.addEventListener('change', function() {
            validateCustomTime(this.value);
        });
        
        customTimeInput.addEventListener('input', function() {
            // Clear validation message while typing
            const validationMsg = document.getElementById('customTimeValidation');
            if (validationMsg) {
                validationMsg.style.display = 'none';
            }
        });
    }
    
    // If there's an existing appointment time, try to select matching preset button
        <?php 
    $date_to_check = '';
        $time_to_check = '';
    if ($reschedule_id && $existing_appointment && isset($existing_appointment['appointment_date']) && isset($existing_appointment['appointment_time'])) {
        $date_to_check = $existing_appointment['appointment_date'];
            $time_to_check = date('H:i', strtotime($existing_appointment['appointment_time']));
    } elseif ($session_data && isset($session_data['appointment_date']) && isset($session_data['appointment_time'])) {
        $date_to_check = $session_data['appointment_date'];
            $time_to_check = date('H:i', strtotime($session_data['appointment_time']));
        }
        ?>
    <?php if ($date_to_check && $time_to_check): ?>
    const existingDate = '<?= $date_to_check ?>';
        const existingTime = '<?= $time_to_check ?>';
    const matchingButton = Array.from(presetTimeButtons).find(btn => 
        btn.dataset.date === existingDate && btn.dataset.time === existingTime
    );
    if (matchingButton) {
        matchingButton.click();
    } else {
        // If no matching preset, set custom time
        const customDateInput = document.getElementById('custom_date_input');
        const customTimeInput = document.getElementById('custom_time_input');
        if (customDateInput && customTimeInput) {
            customDateInput.value = existingDate;
            customTimeInput.value = existingTime;
            toggleCustomTime();
            validateCustomTime(existingTime);
        }
        }
        <?php endif; ?>
    
    // Also handle reschedule form preset time buttons
    const reschedulePresetButtons = document.querySelectorAll('#appointmentForm .time-slot-btn-preset');
    const rescheduleDateInput = document.getElementById('appointment_date_reschedule');
    const rescheduleTimeInput = document.getElementById('appointment_time_reschedule');
    
    reschedulePresetButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Hide custom time if active
            const customInputReschedule = document.getElementById('customTimeInputReschedule');
            if (customInputReschedule && customInputReschedule.style.display !== 'none') {
                toggleCustomTimeReschedule();
            }
            
            // Remove selected class from all preset buttons in reschedule form
            document.querySelectorAll('#appointmentForm .time-slot-btn-preset').forEach(b => b.classList.remove('selected'));
            
            // Add selected class to clicked button
            this.classList.add('selected');
            selectedSchedule = {
                date: this.dataset.date,
                time: this.dataset.time
            };
            
            // Set the hidden inputs
            if (rescheduleDateInput) {
                rescheduleDateInput.value = selectedSchedule.date;
            }
            if (rescheduleTimeInput) {
                rescheduleTimeInput.value = selectedSchedule.time;
            }
            
            // Clear custom time inputs
            const customDateInputReschedule = document.getElementById('custom_date_input_reschedule');
            const customTimeInputReschedule = document.getElementById('custom_time_input_reschedule');
            if (customDateInputReschedule) customDateInputReschedule.value = '';
            if (customTimeInputReschedule) customTimeInputReschedule.value = '';
        });
    });
    
    // Handle custom time for reschedule
    const customDateInputReschedule = document.getElementById('custom_date_input_reschedule');
    const customTimeInputReschedule = document.getElementById('custom_time_input_reschedule');
    
    if (customDateInputReschedule) {
        const scheduleRangeHintReschedule = document.getElementById('scheduleRangeHintReschedule');
        customDateInputReschedule.addEventListener('change', function() {
            // Clear preset button selections when date changes
            document.querySelectorAll('#appointmentForm .time-slot-btn-preset').forEach(b => b.classList.remove('selected'));
            selectedSchedule = null;
            
            // Clear the hidden inputs
            if (rescheduleDateInput) rescheduleDateInput.value = '';
            if (rescheduleTimeInput) rescheduleTimeInput.value = '';
            
            // Update schedule range hint
            const selectedDate = this.value;
            const schedulesForDate = availableSchedules.filter(s => s.date === selectedDate);
            if (schedulesForDate.length > 0 && scheduleRangeHintReschedule) {
                let earliestStart = null;
                let latestEnd = null;
                for (const schedule of schedulesForDate) {
                    if (earliestStart === null || schedule.startTime < earliestStart) {
                        earliestStart = schedule.startTime;
                    }
                    if (latestEnd === null || schedule.endTime > latestEnd) {
                        latestEnd = schedule.endTime;
                    }
                }
                if (earliestStart && latestEnd) {
                    const startDisplay = new Date('2000-01-01T' + earliestStart).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                    const endDisplay = new Date('2000-01-01T' + latestEnd).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                    scheduleRangeHintReschedule.textContent = `Must be within ${startDisplay} - ${endDisplay} for this date`;
                    scheduleRangeHintReschedule.style.color = 'var(--primary-blue)';
                }
            } else if (scheduleRangeHintReschedule) {
                scheduleRangeHintReschedule.textContent = 'Must be within the doctor\'s schedule time range for the selected date';
                scheduleRangeHintReschedule.style.color = 'var(--text-secondary)';
            }
            
            if (customTimeInputReschedule && customTimeInputReschedule.value) {
                validateCustomTimeReschedule(customTimeInputReschedule.value);
            }
        });
    }
    
    if (customTimeInputReschedule) {
        customTimeInputReschedule.addEventListener('change', function() {
            validateCustomTimeReschedule(this.value);
        });
        
        customTimeInputReschedule.addEventListener('input', function() {
            const validationMsg = document.getElementById('customTimeValidationReschedule');
            if (validationMsg) {
                validationMsg.style.display = 'none';
            }
        });
    }
});

// Toggle custom time for reschedule
function toggleCustomTimeReschedule() {
    const customInput = document.getElementById('customTimeInputReschedule');
    const toggleText = document.getElementById('customTimeToggleTextReschedule');
    const customDateInput = document.getElementById('custom_date_input_reschedule');
    const customTimeInput = document.getElementById('custom_time_input_reschedule');
    
    if (!customInput || !toggleText) return;
    
    if (customInput.style.display === 'none' || !customInput.style.display) {
        customInput.style.display = 'block';
        toggleText.textContent = 'Use preset slots';
        customTimeActive = true;
        
        // Clear schedule card selection
        document.querySelectorAll('#appointmentForm .schedule-card').forEach(c => c.classList.remove('selected'));
        selectedSchedule = null;
        
        // Clear hidden inputs
        const dateInput = document.getElementById('appointment_date_reschedule');
        const timeInput = document.getElementById('appointment_time_reschedule');
        if (dateInput) dateInput.value = '';
        if (timeInput) timeInput.value = '';
    } else {
        customInput.style.display = 'none';
            toggleText.textContent = 'Select custom time';
            customTimeActive = false;
            
        // Clear custom inputs
        if (customDateInput) customDateInput.value = '';
        if (customTimeInput) customTimeInput.value = '';
        
        // Clear validation message
        const validationMsg = document.getElementById('customTimeValidationReschedule');
        if (validationMsg) {
            validationMsg.style.display = 'none';
        }
    }
}

// Validate custom time for reschedule
function validateCustomTimeReschedule(timeValue) {
    const customDateInput = document.getElementById('custom_date_input_reschedule');
    const customTimeInput = document.getElementById('custom_time_input_reschedule');
    const validationMsg = document.getElementById('customTimeValidationReschedule');
    const dateInput = document.getElementById('appointment_date_reschedule');
    const timeInput = document.getElementById('appointment_time_reschedule');
    
    if (!timeValue || !customDateInput || !customDateInput.value) {
        if (validationMsg) {
            validationMsg.style.display = 'none';
        }
        if (dateInput) dateInput.value = '';
        if (timeInput) timeInput.value = '';
        return false;
    }
    
    const selectedDate = customDateInput.value;
    const selectedTime = timeValue + ':00'; // Add seconds for comparison
    
    // Find schedules for the selected date
    const schedulesForDate = availableSchedules.filter(s => s.date === selectedDate);
    
    if (schedulesForDate.length === 0) {
        if (validationMsg) {
            validationMsg.style.display = 'block';
            validationMsg.style.color = '#ef4444';
            validationMsg.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> No schedules available for this date. Please select a different date.';
        }
        if (dateInput) dateInput.value = '';
        if (timeInput) timeInput.value = '';
        return false;
    }
    
    // Get the earliest start and latest end time for this date
    let earliestStart = null;
    let latestEnd = null;
    
    for (const schedule of schedulesForDate) {
        if (earliestStart === null || schedule.startTime < earliestStart) {
            earliestStart = schedule.startTime;
        }
        if (latestEnd === null || schedule.endTime > latestEnd) {
            latestEnd = schedule.endTime;
        }
    }
    
    // Check if the selected time falls within the overall schedule range
    let isValid = false;
    
    if (selectedTime >= earliestStart && selectedTime < latestEnd) {
        // Check if it falls within any specific schedule range
        for (const schedule of schedulesForDate) {
            if (selectedTime >= schedule.startTime && selectedTime < schedule.endTime) {
                isValid = true;
                break;
            }
        }
    }
    
    if (isValid) {
        if (validationMsg) {
            validationMsg.style.display = 'block';
            validationMsg.style.color = '#10b981';
            validationMsg.innerHTML = '<i class="fas fa-check-circle"></i> Time slot is available!';
        }
        
        // Clear preset button selections
        document.querySelectorAll('#appointmentForm .time-slot-btn-preset').forEach(b => b.classList.remove('selected'));
        
        // Set the hidden inputs
        if (dateInput) {
            dateInput.value = selectedDate;
        }
            if (timeInput) {
            timeInput.value = timeValue;
            }
            
        // Update selectedSchedule
        selectedSchedule = {
            date: selectedDate,
            time: timeValue
        };
        
        return true;
        } else {
        if (validationMsg) {
            validationMsg.style.display = 'block';
            validationMsg.style.color = '#ef4444';
            
            // Format times for display
            const startDisplay = earliestStart ? new Date('2000-01-01T' + earliestStart).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }) : '';
            const endDisplay = latestEnd ? new Date('2000-01-01T' + latestEnd).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }) : '';
            
            validationMsg.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> Selected time is outside the doctor's available schedule range (${startDisplay} - ${endDisplay}) for this date. Please select a time within this range.`;
        }
        if (dateInput) dateInput.value = '';
        if (timeInput) timeInput.value = '';
        selectedSchedule = null;
        return false;
    }
}

// Toggle custom time input
function toggleCustomTime() {
    const customInput = document.getElementById('customTimeInput');
    const toggleText = document.getElementById('customTimeToggleText');
    const customDateInput = document.getElementById('custom_date_input');
    const customTimeInput = document.getElementById('custom_time_input');
    
    if (!customInput || !toggleText) return;
    
    if (customInput.style.display === 'none' || !customInput.style.display) {
        customInput.style.display = 'block';
        toggleText.textContent = 'Use preset slots';
        customTimeActive = true;
        
        // Clear schedule card selection
        document.querySelectorAll('.schedule-card').forEach(c => c.classList.remove('selected'));
        selectedSchedule = null;
        
        // Clear hidden inputs
        const dateInput = document.getElementById('appointment_date');
        const timeInput = document.getElementById('appointment_time');
        if (dateInput) dateInput.value = '';
        if (timeInput) timeInput.value = '';
    } else {
        customInput.style.display = 'none';
        toggleText.textContent = 'Select custom time';
    customTimeActive = false;
        
        // Clear custom inputs
        if (customDateInput) customDateInput.value = '';
        if (customTimeInput) customTimeInput.value = '';
        
        // Clear validation message
        const validationMsg = document.getElementById('customTimeValidation');
        if (validationMsg) {
            validationMsg.style.display = 'none';
        }
    }
}

// Validate custom time against available schedules
function validateCustomTime(timeValue) {
    const customDateInput = document.getElementById('custom_date_input');
    const customTimeInput = document.getElementById('custom_time_input');
    const validationMsg = document.getElementById('customTimeValidation');
    const dateInput = document.getElementById('appointment_date');
    const timeInput = document.getElementById('appointment_time');
    
    if (!timeValue || !customDateInput || !customDateInput.value) {
        if (validationMsg) {
            validationMsg.style.display = 'none';
        }
        if (dateInput) dateInput.value = '';
        if (timeInput) timeInput.value = '';
        return false;
    }
    
    const selectedDate = customDateInput.value;
    const selectedTime = timeValue + ':00'; // Add seconds for comparison
    
    // Find schedules for the selected date
    const schedulesForDate = availableSchedules.filter(s => s.date === selectedDate);
    
    if (schedulesForDate.length === 0) {
        if (validationMsg) {
            validationMsg.style.display = 'block';
            validationMsg.style.color = '#ef4444';
            validationMsg.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> No schedules available for this date. Please select a different date.';
        }
        if (dateInput) dateInput.value = '';
        if (timeInput) timeInput.value = '';
        return false;
    }
    
    // Get the earliest start and latest end time for this date
    let earliestStart = null;
    let latestEnd = null;
    
    for (const schedule of schedulesForDate) {
        if (earliestStart === null || schedule.startTime < earliestStart) {
            earliestStart = schedule.startTime;
        }
        if (latestEnd === null || schedule.endTime > latestEnd) {
            latestEnd = schedule.endTime;
        }
        }
        
    // Check if the selected time falls within the overall schedule range
    let isValid = false;
    
    if (selectedTime >= earliestStart && selectedTime < latestEnd) {
        // Check if it falls within any specific schedule range
        for (const schedule of schedulesForDate) {
            if (selectedTime >= schedule.startTime && selectedTime < schedule.endTime) {
                isValid = true;
                break;
            }
        }
    }
    
    if (isValid) {
        if (validationMsg) {
            validationMsg.style.display = 'block';
            validationMsg.style.color = '#10b981';
            validationMsg.innerHTML = '<i class="fas fa-check-circle"></i> Time slot is available!';
}

        // Clear preset button selections
        document.querySelectorAll('.time-slot-btn-preset').forEach(b => b.classList.remove('selected'));
        
        // Set the hidden inputs
    if (dateInput) {
            dateInput.value = selectedDate;
    }
    if (timeInput) {
            timeInput.value = timeValue;
        }
        
        // Update selectedSchedule
        selectedSchedule = {
            date: selectedDate,
            time: timeValue
        };
        
        return true;
    } else {
        if (validationMsg) {
            validationMsg.style.display = 'block';
            validationMsg.style.color = '#ef4444';
            
            // Format times for display
            const startDisplay = earliestStart ? new Date('2000-01-01T' + earliestStart).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }) : '';
            const endDisplay = latestEnd ? new Date('2000-01-01T' + latestEnd).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }) : '';
            
            validationMsg.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> Selected time is outside the doctor's available schedule range (${startDisplay} - ${endDisplay}) for this date. Please select a time within this range.`;
        }
        if (dateInput) dateInput.value = '';
        if (timeInput) timeInput.value = '';
        selectedSchedule = null;
        return false;
    }
    }
    
// Toggle date slots visibility
function toggleDateSlots(date) {
    const timeSlotsContainer = document.getElementById('time-slots-' + date);
    const toggleIcon = document.getElementById('toggle-icon-' + date);
    
    if (!timeSlotsContainer || !toggleIcon) return;
    
    const isVisible = timeSlotsContainer.style.display !== 'none';
    
    if (isVisible) {
        timeSlotsContainer.style.display = 'none';
        toggleIcon.style.transform = 'rotate(0deg)';
        toggleIcon.classList.remove('fa-chevron-up');
        toggleIcon.classList.add('fa-chevron-down');
    } else {
        timeSlotsContainer.style.display = 'block';
        toggleIcon.style.transform = 'rotate(180deg)';
        toggleIcon.classList.remove('fa-chevron-down');
        toggleIcon.classList.add('fa-chevron-up');
    }
}

// Clear schedule selection when doctor changes
function clearScheduleSelection() {
    document.querySelectorAll('.time-slot-btn-preset').forEach(b => b.classList.remove('selected'));
    selectedSchedule = null;
    customTimeActive = false;
    const dateInput = document.getElementById('appointment_date');
    const timeInput = document.getElementById('appointment_time');
    const customDateInput = document.getElementById('custom_date_input');
    const customTimeInput = document.getElementById('custom_time_input');
    const customInput = document.getElementById('customTimeInput');
    const validationMsg = document.getElementById('customTimeValidation');
    
    if (dateInput) dateInput.value = '';
    if (timeInput) timeInput.value = '';
    if (customDateInput) customDateInput.value = '';
    if (customTimeInput) customTimeInput.value = '';
    if (customInput) customInput.style.display = 'none';
    if (validationMsg) validationMsg.style.display = 'none';
}

// Form validation
document.getElementById('appointmentForm')?.addEventListener('submit', function(e) {
    // Check if this is reschedule form
    const isReschedule = document.getElementById('appointment_date_reschedule') !== null;
    
    let timeInput, dateInput, customDateInput, customTimeInput;
    
    if (isReschedule) {
        timeInput = document.getElementById('appointment_time_reschedule');
        dateInput = document.getElementById('appointment_date_reschedule');
        customDateInput = document.getElementById('custom_date_input_reschedule');
        customTimeInput = document.getElementById('custom_time_input_reschedule');
    } else {
        timeInput = document.getElementById('appointment_time');
        dateInput = document.getElementById('appointment_date');
        customDateInput = document.getElementById('custom_date_input');
        customTimeInput = document.getElementById('custom_time_input');
    }
    
    // Check if custom time is being used
    const customTimeActiveCheck = customTimeInput && customTimeInput.value && 
                                   customDateInput && customDateInput.value &&
                                   (document.getElementById('customTimeInput')?.style.display !== 'none' || 
                                    document.getElementById('customTimeInputReschedule')?.style.display !== 'none');
    
    if (customTimeActiveCheck) {
        if (!customDateInput || !customDateInput.value || !customTimeInput || !customTimeInput.value) {
            e.preventDefault();
            alert('Please select both date and time for your custom appointment slot.');
            return false;
        }
        
        // Validate custom time one more time before submission
        const isValid = isReschedule ? 
            validateCustomTimeReschedule(customTimeInput.value) : 
            validateCustomTime(customTimeInput.value);
        
        if (!isValid) {
            e.preventDefault();
            alert('The selected time is not within the doctor\'s available schedule. Please select a valid time slot.');
            return false;
        }
    } else {
    if (!timeInput || !timeInput.value) {
        e.preventDefault();
            alert('Please select an available time slot for your appointment.');
        return false;
    }
    
    if (!dateInput || !dateInput.value) {
        e.preventDefault();
            alert('Please select an available date for your appointment.');
        return false;
        }
        
        if (!selectedSchedule) {
            e.preventDefault();
            alert('Please select an available time slot by clicking on one of the preset time buttons or use the custom time option.');
            return false;
        }
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
