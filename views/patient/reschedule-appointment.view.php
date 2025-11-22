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
        
        .submit-section {
            flex-direction: column;
        }
    }
</style>

<div class="page-header">
    <div class="page-header-top">
        <h1 class="page-title">Reschedule Appointment</h1>
    </div>
</div>

<?php if ($error && !$appointment): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
    <div style="margin-top: 1.5rem;">
        <a href="/patient/appointments" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Appointments</span>
        </a>
    </div>
<?php elseif ($appointment): ?>
    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <div class="alert" style="background: #dbeafe; border-left: 4px solid #3b82f6; padding: 1rem; margin-bottom: 1.5rem; border-radius: 0.5rem;">
        <i class="fas fa-info-circle" style="color: #3b82f6;"></i>
        <span style="color: #1e40af;">
            <strong>Rescheduling:</strong> You are rescheduling your appointment with 
            Dr. <?= htmlspecialchars($appointment['doc_first_name'] . ' ' . $appointment['doc_last_name']) ?> 
            scheduled for <?= date('M j, Y', strtotime($appointment['appointment_date'])) ?> 
            at <?= date('g:i A', strtotime($appointment['appointment_time'])) ?>.
        </span>
    </div>

    <div class="appointment-booking-card">
        <form method="POST" id="appointmentForm">
            <input type="hidden" name="action" value="reschedule">
            <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($appointment['doc_id']) ?>">
            <input type="hidden" name="service_id" value="<?= htmlspecialchars($appointment['service_id'] ?? '') ?>">
            
            <!-- Section 1: Doctor Details -->
            <div class="appointment-section">
                <div class="section-header">
                    <i class="fas fa-user-md"></i>
                    <h3 class="section-title">Doctor Details</h3>
                </div>
                
                <?php
                $doctor_initials = strtoupper(substr($appointment['doc_first_name'] ?? 'D', 0, 1) . substr($appointment['doc_last_name'] ?? 'D', 0, 1));
                $doctor_name = 'Dr. ' . htmlspecialchars($appointment['doc_first_name'] . ' ' . $appointment['doc_last_name']);
                $doctor_spec = htmlspecialchars($appointment['spec_name'] ?? 'General Practice');
                $doctor_fee = $appointment['doc_consultation_fee'] ?? 0;
                $doctor_profile = $appointment['profile_picture_url'] ?? '';
                ?>
                
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
            </div>
            
            <!-- Section 2: Appointment Details -->
            <div class="appointment-section">
                <div class="section-header">
                    <i class="fas fa-clipboard-list"></i>
                    <h3 class="section-title">Appointment Details</h3>
                </div>
                
                <div class="form-group">
                    <label>Service:</label>
                    <?php
                    $service_name = 'None';
                    if ($appointment['service_id'] && isset($appointment['service_name'])) {
                        $service_name = htmlspecialchars($appointment['service_name']);
                        if (isset($appointment['service_price'])) {
                            $service_name .= ' - ₱' . number_format($appointment['service_price'], 2);
                        }
                    }
                    ?>
                    <input type="text" 
                           value="<?= $service_name ?>" 
                           class="form-control" 
                           disabled 
                           style="background-color: #f3f4f6; cursor: not-allowed;">
                    <small class="form-text text-muted" style="margin-top: 0.5rem;">
                        <i class="fas fa-info-circle"></i> Service cannot be changed when rescheduling.
                    </small>
                </div>
                
                <div class="form-group">
                    <label>Notes/Reason for Visit:</label>
                    <textarea name="notes" rows="3" placeholder="Please describe your symptoms or reason for visit (optional)..." class="form-control"><?= htmlspecialchars($appointment['appointment_notes'] ?? '') ?></textarea>
                </div>
            </div>
            
            <!-- Section 3: Available Schedules -->
            <div class="appointment-section">
                <div class="section-header">
                    <i class="fas fa-calendar-alt"></i>
                    <h3 class="section-title">Select Available Time Slot</h3>
                </div>
                
                <?php if (empty($doctor_schedules)): ?>
                    <div class="alert" style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 1rem; border-radius: 0.5rem;">
                        <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                        <span style="color: #991b1b;">No available schedules found for this doctor. Please check back later or contact support.</span>
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
            
            <!-- Submit Section -->
            <div class="submit-section">
                <a href="/patient/appointments" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calendar-check"></i>
                    <span>Reschedule Appointment</span>
                </button>
            </div>
        </form>
    </div>
<?php endif; ?>

<script>
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
});

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

// Form validation
document.getElementById('appointmentForm')?.addEventListener('submit', function(e) {
    const timeInput = document.getElementById('appointment_time');
    const dateInput = document.getElementById('appointment_date');
    const customDateInput = document.getElementById('custom_date_input');
    const customTimeInput = document.getElementById('custom_time_input');
    
    // Check if custom time is being used
    const customTimeActiveCheck = customTimeInput && customTimeInput.value && 
                                   customDateInput && customDateInput.value &&
                                   document.getElementById('customTimeInput')?.style.display !== 'none';
    
    if (customTimeActiveCheck) {
        if (!customDateInput || !customDateInput.value || !customTimeInput || !customTimeInput.value) {
            e.preventDefault();
            alert('Please select both date and time for your custom appointment slot.');
            return false;
        }
        
        // Validate custom time one more time before submission
        const isValid = validateCustomTime(customTimeInput.value);
        
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