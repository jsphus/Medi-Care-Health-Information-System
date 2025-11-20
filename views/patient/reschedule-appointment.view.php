<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
.reschedule-container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.reschedule-card {
    background: white;
    border-radius: 0.75rem;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 2rem;
}

.reschedule-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.reschedule-header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.current-appointment-card {
    background: #f9fafb;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border-left: 4px solid #3b82f6;
}

.current-appointment-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 1rem;
}

.current-appointment-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
}

.info-value {
    font-size: 0.875rem;
    color: #1f2937;
    font-weight: 600;
}

.form-section {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    min-height: 100px;
    resize: vertical;
    transition: border-color 0.2s;
}

.form-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.time-slots {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.time-slot-btn {
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    background: white;
    color: #374151;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.time-slot-btn:hover {
    border-color: #3b82f6;
    background: #eff6ff;
    color: #3b82f6;
}

.time-slot-btn.active {
    border-color: #3b82f6;
    background: #3b82f6;
    color: white;
}

.time-slot-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.custom-time-toggle {
    margin-top: 1rem;
    padding: 0.75rem;
    background: #f9fafb;
    border: 1px dashed #d1d5db;
    border-radius: 0.5rem;
    text-align: center;
    cursor: pointer;
    color: #6b7280;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.custom-time-toggle:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.custom-time-input {
    display: none;
    margin-top: 1rem;
}

.custom-time-input.show {
    display: block;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.alert {
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.doctor-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.doctor-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.5rem;
    overflow: hidden;
    flex-shrink: 0;
}

.doctor-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.doctor-details h3 {
    margin: 0;
    font-size: 1.125rem;
    color: #1f2937;
}

.doctor-details p {
    margin: 0.25rem 0 0 0;
    font-size: 0.875rem;
    color: #6b7280;
}

@media (max-width: 768px) {
    .reschedule-container {
        padding: 0 0.5rem;
    }
    
    .reschedule-card {
        padding: 1.5rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="reschedule-container">
    <?php if ($error && !$appointment): ?>
        <div class="reschedule-card">
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <div class="form-actions">
                <a href="/patient/appointments" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Appointments</span>
                </a>
            </div>
        </div>
    <?php elseif ($appointment): ?>
        <div class="reschedule-card">
            <div class="reschedule-header">
                <i class="fas fa-calendar-alt" style="font-size: 2rem; color: #3b82f6;"></i>
                <h1>Reschedule Appointment</h1>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Current Appointment Info -->
            <div class="current-appointment-card">
                <div class="current-appointment-title">Current Appointment Details</div>
                <div class="current-appointment-info">
                    <div class="info-item">
                        <span class="info-label">Appointment ID</span>
                        <span class="info-value"><?= htmlspecialchars($appointment['appointment_id']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Current Date</span>
                        <span class="info-value"><?= date('F d, Y', strtotime($appointment['appointment_date'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Current Time</span>
                        <span class="info-value"><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></span>
                    </div>
                </div>
            </div>

            <!-- Doctor Info -->
            <div class="doctor-info">
                <div class="doctor-avatar">
                    <?php if (!empty($appointment['profile_picture_url'])): ?>
                        <img src="<?= htmlspecialchars($appointment['profile_picture_url']) ?>" alt="Doctor">
                    <?php else: ?>
                        <?= strtoupper(substr($appointment['doc_first_name'] ?? 'D', 0, 1) . substr($appointment['doc_last_name'] ?? 'D', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div class="doctor-details">
                    <h3>Dr. <?= htmlspecialchars(formatFullName($appointment['doc_first_name'] ?? '', $appointment['doc_middle_initial'] ?? null, $appointment['doc_last_name'] ?? '')) ?></h3>
                    <p><?= htmlspecialchars($appointment['spec_name'] ?? 'General Practice') ?></p>
                </div>
            </div>

            <!-- Reschedule Form -->
            <form method="POST" id="rescheduleForm">
                <input type="hidden" name="action" value="reschedule">
                
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="fas fa-calendar"></i>
                        Select New Date & Time
                    </h2>

                    <div class="form-group">
                        <label for="appointment_date" class="form-label">New Appointment Date</label>
                        <input 
                            type="date" 
                            id="appointment_date" 
                            name="appointment_date" 
                            class="form-input" 
                            required
                            min="<?= date('Y-m-d') ?>"
                            value="<?= htmlspecialchars($appointment['appointment_date'] ?? '') ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="appointment_time" class="form-label">New Appointment Time</label>
                        <input 
                            type="time" 
                            id="appointment_time" 
                            name="appointment_time" 
                            class="form-input" 
                            required
                            value="<?= htmlspecialchars(date('H:i', strtotime($appointment['appointment_time'] ?? '09:00'))) ?>"
                        >
                        
                        <!-- Time Slots -->
                        <div class="time-slots" id="timeSlots">
                            <button type="button" class="time-slot-btn" data-time="09:00">9:00 AM</button>
                            <button type="button" class="time-slot-btn" data-time="10:00">10:00 AM</button>
                            <button type="button" class="time-slot-btn" data-time="11:00">11:00 AM</button>
                            <button type="button" class="time-slot-btn" data-time="12:00">12:00 PM</button>
                            <button type="button" class="time-slot-btn" data-time="13:00">1:00 PM</button>
                            <button type="button" class="time-slot-btn" data-time="14:00">2:00 PM</button>
                            <button type="button" class="time-slot-btn" data-time="15:00">3:00 PM</button>
                            <button type="button" class="time-slot-btn" data-time="16:00">4:00 PM</button>
                            <button type="button" class="time-slot-btn" data-time="17:00">5:00 PM</button>
                        </div>
                        
                        <div class="custom-time-toggle" id="customTimeToggle" onclick="toggleCustomTime()">
                            <span id="customTimeToggleText">Select custom time</span>
                        </div>
                        <div class="custom-time-input" id="customTimeInput">
                            <input 
                                type="time" 
                                id="customTime" 
                                class="form-input" 
                                onchange="setCustomTime(this.value)"
                            >
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h2 class="section-title">
                        <i class="fas fa-sticky-note"></i>
                        Additional Notes (Optional)
                    </h2>

                    <div class="form-group">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            class="form-textarea" 
                            placeholder="Add any additional notes or special requests..."
                        ><?= htmlspecialchars($appointment['appointment_notes'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="/patient/appointments" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-check"></i>
                        <span>Confirm Reschedule</span>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
// Time slot selection
let selectedTimeSlot = null;
let customTimeActive = false;

document.addEventListener('DOMContentLoaded', function() {
    const timeSlots = document.querySelectorAll('.time-slot-btn');
    const timeInput = document.getElementById('appointment_time');
    
    // Handle time slot button clicks
    timeSlots.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            timeSlots.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            selectedTimeSlot = this.dataset.time;
            
            // Set the time input
            if (timeInput) {
                timeInput.value = selectedTimeSlot;
            }
            
            // Hide custom time if it was active
            if (customTimeActive) {
                toggleCustomTime();
            }
        });
    });
    
    // Handle time input change
    if (timeInput) {
        timeInput.addEventListener('change', function() {
            // Clear time slot selection
            timeSlots.forEach(b => b.classList.remove('active'));
            selectedTimeSlot = null;
            customTimeActive = true;
        });
        
        // Set initial time if exists
        const existingTime = timeInput.value;
        if (existingTime) {
            const matchingSlot = Array.from(timeSlots).find(btn => btn.dataset.time === existingTime);
            if (matchingSlot) {
                matchingSlot.click();
            }
        }
    }
    
    // Set minimum date to today
    const dateInput = document.getElementById('appointment_date');
    if (dateInput && !dateInput.value) {
        dateInput.min = new Date().toISOString().split('T')[0];
    }
});

// Toggle custom time input
function toggleCustomTime() {
    const customInput = document.getElementById('customTimeInput');
    const toggleText = document.getElementById('customTimeToggleText');
    const timeInput = document.getElementById('appointment_time');
    const timeSlots = document.querySelectorAll('.time-slot-btn');
    
    if (customInput && toggleText) {
        if (customInput.classList.contains('show')) {
            customInput.classList.remove('show');
            toggleText.textContent = 'Select custom time';
            customTimeActive = false;
            
            // Clear custom time input
            const customTime = document.getElementById('customTime');
            if (customTime) {
                customTime.value = '';
            }
            
            // Clear time slot selection
            timeSlots.forEach(b => b.classList.remove('active'));
            selectedTimeSlot = null;
        } else {
            customInput.classList.add('show');
            toggleText.textContent = 'Use preset times';
            customTimeActive = true;
            
            // Clear time slot selection
            timeSlots.forEach(b => b.classList.remove('active'));
            selectedTimeSlot = null;
        }
    }
}

// Set custom time
function setCustomTime(time) {
    const timeInput = document.getElementById('appointment_time');
    if (timeInput) {
        timeInput.value = time;
    }
    
    // Clear time slot selection
    const timeSlots = document.querySelectorAll('.time-slot-btn');
    timeSlots.forEach(b => b.classList.remove('active'));
    selectedTimeSlot = null;
    customTimeActive = true;
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

