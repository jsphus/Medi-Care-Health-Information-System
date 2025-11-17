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
        <div class="breadcrumbs">
            <?php if ($selected_doctor_id): ?>
                <a href="/patient/book">
                    <i class="fas fa-book"></i>
                    <span>Book</span>
                </a>
                <i class="fas fa-chevron-right"></i>
                <span>Book Appointment</span>
            <?php else: ?>
                <a href="/patient/appointments">
                    <i class="fas fa-calendar"></i>
                    <span>My Appointments</span>
                </a>
                <i class="fas fa-chevron-right"></i>
                <span>Book Appointment</span>
            <?php endif; ?>
        </div>
        <h1 class="page-title">
            <?php if ($reschedule_id && $existing_appointment): ?>
                Reschedule Appointment
            <?php elseif ($selected_doctor_id): ?>
                Book Appointment
            <?php else: ?>
                Book New Appointment
            <?php endif; ?>
        </h1>
    </div>
</div>

<?php if ($reschedule_id && $existing_appointment): ?>
    <div class="alert" style="background: #dbeafe; border-left: 4px solid #3b82f6; padding: 1rem; margin-bottom: 1.5rem; border-radius: 0.5rem;">
        <i class="fas fa-info-circle" style="color: #3b82f6;"></i>
        <span style="color: #1e40af;">
            <strong>Rescheduling:</strong> You are rescheduling your appointment with 
            Dr. <?= htmlspecialchars($existing_appointment['doc_first_name'] . ' ' . $existing_appointment['doc_last_name']) ?> 
            scheduled for <?= date('M j, Y', strtotime($existing_appointment['appointment_date'])) ?> 
            at <?= date('g:i A', strtotime($existing_appointment['appointment_time'])) ?>.
        </span>
    </div>
<?php endif; ?>

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

<?php if ($reschedule_id && $existing_appointment): ?>
    <?php
    // Get service details for reschedule
    $reschedule_service = null;
    if ($existing_appointment['service_id']) {
        foreach ($services as $svc) {
            if ($svc['service_id'] == $existing_appointment['service_id']) {
                $reschedule_service = $svc;
                break;
            }
        }
    }
    $doctor_initials_reschedule = strtoupper(substr($existing_appointment['doc_first_name'] ?? 'D', 0, 1) . substr($existing_appointment['doc_last_name'] ?? 'D', 0, 1));
    $doctor_name_reschedule = 'Dr. ' . htmlspecialchars($existing_appointment['doc_first_name'] . ' ' . $existing_appointment['doc_last_name']);
    $doctor_spec_reschedule = htmlspecialchars($existing_appointment['spec_name'] ?? 'General Practice');
    $doctor_fee_reschedule = $existing_appointment['doc_consultation_fee'] ?? 0;
    $doctor_profile_reschedule = $existing_appointment['profile_picture_url'] ?? '';
    $total_amount_reschedule = $reschedule_service ? ($reschedule_service['service_price'] ?? 0) : $doctor_fee_reschedule;
    ?>
    
    <style>
        .reschedule-summary-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid var(--border-light);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .summary-section {
            padding: 2rem;
            border-bottom: 1px solid var(--border-light);
        }
        
        .summary-section:last-child {
            border-bottom: none;
        }
        
        .summary-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .summary-header i {
            color: var(--primary-blue);
            font-size: 1.25rem;
        }
        
        .summary-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }
        
        .doctor-summary {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .doctor-avatar-summary {
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
        
        .doctor-avatar-summary img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .doctor-info-summary {
            flex: 1;
        }
        
        .doctor-name-summary {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        
        .doctor-spec-summary {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        
        .info-row-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .info-row-summary:last-child {
            border-bottom: none;
        }
        
        .info-label-summary {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
        }
        
        .info-value-summary {
            font-size: 0.875rem;
            color: #1f2937;
            font-weight: 600;
        }
        
        .editable-field {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .editable-field label {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
        }
        
        .amount-summary-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 0.75rem;
            padding: 1.5rem;
        }
        
        .amount-row-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        
        .amount-row-summary:last-child {
            margin-bottom: 0;
            padding-top: 0.75rem;
            border-top: 2px solid #3b82f6;
            margin-top: 0.75rem;
        }
        
        .amount-label-summary {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .amount-value-summary {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .total-amount-summary {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        .notes-summary {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            color: #1f2937;
            line-height: 1.6;
        }
        
        .submit-section-reschedule {
            padding: 1.5rem 2rem;
            background: #f9fafb;
            border-top: 1px solid var(--border-light);
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }
    </style>
    
    <div class="reschedule-summary-card">
        <form method="POST" id="appointmentForm">
            <input type="hidden" name="reschedule_id" value="<?= htmlspecialchars($reschedule_id) ?>">
            <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($existing_appointment['doc_id']) ?>">
            <input type="hidden" name="service_id" value="<?= htmlspecialchars($existing_appointment['service_id'] ?? '') ?>">
            <input type="hidden" name="action" value="reschedule">
            
            <!-- Doctor Information -->
            <div class="summary-section">
                <div class="summary-header">
                    <i class="fas fa-user-md"></i>
                    <h3 class="summary-title">Doctor Information</h3>
                </div>
                <div class="doctor-summary">
                    <div class="doctor-avatar-summary">
                        <?php if (!empty($doctor_profile_reschedule)): ?>
                            <img src="<?= htmlspecialchars($doctor_profile_reschedule) ?>" alt="<?= $doctor_name_reschedule ?>">
                        <?php else: ?>
                            <?= $doctor_initials_reschedule ?>
                        <?php endif; ?>
                    </div>
                    <div class="doctor-info-summary">
                        <div class="doctor-name-summary"><?= $doctor_name_reschedule ?></div>
                        <div class="doctor-spec-summary"><?= $doctor_spec_reschedule ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Appointment Details -->
            <div class="summary-section">
                <div class="summary-header">
                    <i class="fas fa-calendar-check"></i>
                    <h3 class="summary-title">Appointment Details</h3>
                </div>
                
                <div class="editable-field">
                    <label>Appointment Date: <span style="color: var(--status-error);">*</span></label>
                    <input type="date" name="appointment_date" id="appointment_date"
                           value="<?= htmlspecialchars($existing_appointment['appointment_date']) ?>"
                           min="<?= date('Y-m-d') ?>" required class="form-control">
                </div>
                
                <div class="editable-field" style="margin-top: 1rem;">
                    <label>Preferred Time: <span style="color: var(--status-error);">*</span></label>
                    <div class="time-slots" id="timeSlots">
                        <button type="button" class="time-slot-btn" data-time="09:00">9:00 AM</button>
                        <button type="button" class="time-slot-btn" data-time="10:00">10:00 AM</button>
                        <button type="button" class="time-slot-btn" data-time="11:00">11:00 AM</button>
                        <button type="button" class="time-slot-btn" data-time="13:00">1:00 PM</button>
                        <button type="button" class="time-slot-btn" data-time="14:00">2:00 PM</button>
                        <button type="button" class="time-slot-btn" data-time="15:00">3:00 PM</button>
                        <button type="button" class="time-slot-btn" data-time="16:00">4:00 PM</button>
                        <button type="button" class="time-slot-btn" data-time="17:00">5:00 PM</button>
                    </div>
                    
                    <div class="custom-time-toggle">
                        <button type="button" class="custom-time-toggle-btn" onclick="toggleCustomTime()">
                            <i class="fas fa-clock"></i>
                            <span id="customTimeToggleText">Select custom time</span>
                        </button>
                    </div>
                    
                    <div class="custom-time-input" id="customTimeInput">
                        <input type="time" name="appointment_time" id="appointment_time" 
                               value="<?= date('H:i', strtotime($existing_appointment['appointment_time'])) ?>"
                               class="form-control" style="margin-top: 1rem;">
                    </div>
                </div>
                
                <?php if ($reschedule_service): ?>
                <div class="info-row-summary" style="margin-top: 1.5rem;">
                    <span class="info-label-summary">Service</span>
                    <span class="info-value-summary"><?= htmlspecialchars($reschedule_service['service_name']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Payment Summary -->
            <div class="summary-section">
                <div class="summary-header">
                    <i class="fas fa-credit-card"></i>
                    <h3 class="summary-title">Payment Summary</h3>
                </div>
                <div class="amount-summary-box">
                    <?php if ($reschedule_service): ?>
                    <div class="amount-row-summary">
                        <span class="amount-label-summary">Service Fee</span>
                        <span class="amount-value-summary">₱<?= number_format($reschedule_service['service_price'] ?? 0, 2) ?></span>
                    </div>
                    <?php else: ?>
                    <div class="amount-row-summary">
                        <span class="amount-label-summary">Consultation Fee</span>
                        <span class="amount-value-summary">₱<?= number_format($doctor_fee_reschedule, 2) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="amount-row-summary">
                        <span class="amount-label-summary total-amount-summary">Total Amount</span>
                        <span class="amount-value-summary total-amount-summary">₱<?= number_format($total_amount_reschedule, 2) ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            <?php if (!empty($existing_appointment['appointment_notes'])): ?>
            <div class="summary-section">
                <div class="summary-header">
                    <i class="fas fa-sticky-note"></i>
                    <h3 class="summary-title">Notes</h3>
                </div>
                <div class="notes-summary">
                    <?= nl2br(htmlspecialchars($existing_appointment['appointment_notes'])) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Submit Section -->
            <div class="submit-section-reschedule">
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
<?php else: ?>
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
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= $doctor['doc_id'] ?>" 
                                    data-name="<?= htmlspecialchars($doctor['doc_first_name'] . ' ' . $doctor['doc_last_name']) ?>"
                                    data-spec="<?= htmlspecialchars($doctor['spec_name'] ?? 'General') ?>"
                                    data-fee="<?= number_format($doctor['doc_consultation_fee'] ?? 0, 2) ?>"
                                    data-profile="<?= htmlspecialchars($doctor['profile_picture_url'] ?? '') ?>"
                                    data-initials="<?= strtoupper(substr($doctor['doc_first_name'] ?? 'D', 0, 1) . substr($doctor['doc_last_name'] ?? 'D', 0, 1)) ?>">
                                Dr. <?= htmlspecialchars($doctor['doc_first_name'] . ' ' . $doctor['doc_last_name']) ?> 
                                - <?= htmlspecialchars($doctor['spec_name'] ?? 'General') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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
        
        <!-- Section 3: Date & Time -->
        <div class="appointment-section">
            <div class="section-header">
                <i class="fas fa-calendar-alt"></i>
                <h3 class="section-title">Date & Time</h3>
            </div>
            
            <div class="form-group">
                <label>Appointment Date: <span style="color: var(--status-error);">*</span></label>
                <?php
                $appointment_date_value = '';
                if ($session_data && isset($session_data['appointment_date'])) {
                    $appointment_date_value = htmlspecialchars($session_data['appointment_date']);
                } elseif ($existing_appointment && isset($existing_appointment['appointment_date'])) {
                    $appointment_date_value = htmlspecialchars($existing_appointment['appointment_date']);
                }
                ?>
                <input type="date" name="appointment_date" id="appointment_date"
                       value="<?= $appointment_date_value ?>"
                       min="<?= date('Y-m-d') ?>" required class="form-control">
            </div>
            
            <div class="form-group">
                <label>Preferred Time: <span style="color: var(--status-error);">*</span></label>
                <div class="time-slots" id="timeSlots">
                    <button type="button" class="time-slot-btn" data-time="09:00">9:00 AM</button>
                    <button type="button" class="time-slot-btn" data-time="10:00">10:00 AM</button>
                    <button type="button" class="time-slot-btn" data-time="11:00">11:00 AM</button>
                    <button type="button" class="time-slot-btn" data-time="13:00">1:00 PM</button>
                    <button type="button" class="time-slot-btn" data-time="14:00">2:00 PM</button>
                    <button type="button" class="time-slot-btn" data-time="15:00">3:00 PM</button>
                    <button type="button" class="time-slot-btn" data-time="16:00">4:00 PM</button>
                    <button type="button" class="time-slot-btn" data-time="17:00">5:00 PM</button>
                </div>
                
                <div class="custom-time-toggle">
                    <button type="button" class="custom-time-toggle-btn" onclick="toggleCustomTime()">
                        <i class="fas fa-clock"></i>
                        <span id="customTimeToggleText">Select custom time</span>
                    </button>
                </div>
                
                <div class="custom-time-input" id="customTimeInput">
                    <?php
                    $appointment_time_value = '';
                    if ($session_data && isset($session_data['appointment_time'])) {
                        $appointment_time_value = date('H:i', strtotime($session_data['appointment_time']));
                    } elseif ($existing_appointment && isset($existing_appointment['appointment_time'])) {
                        $appointment_time_value = date('H:i', strtotime($existing_appointment['appointment_time']));
                    }
                    ?>
                    <input type="time" name="appointment_time" id="appointment_time" 
                           value="<?= $appointment_time_value ?>"
                           class="form-control" style="margin-top: 1rem;">
                </div>
            </div>
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
<?php endif; ?>

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
        
        // Clear time selection when doctor changes
        clearTimeSelection();
        if (timeInput) {
            timeInput.value = '';
        }
    } else if (display) {
        display.style.display = 'none';
    }
}

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
            
            // Set the hidden time input
            if (timeInput) {
                timeInput.value = selectedTimeSlot;
            }
            
            // Hide custom time if it was active
            if (customTimeActive) {
                toggleCustomTime();
            }
        });
    });
    
    // Handle custom time input change
    if (timeInput) {
        timeInput.addEventListener('change', function() {
            // Clear time slot selection
            timeSlots.forEach(b => b.classList.remove('active'));
            selectedTimeSlot = null;
            customTimeActive = true;
        });
        
        // If there's an existing time value (from session or existing appointment), try to match it to a slot or show in custom
        <?php 
        $time_to_check = '';
        if ($reschedule_id && $existing_appointment && isset($existing_appointment['appointment_time'])) {
            $time_to_check = date('H:i', strtotime($existing_appointment['appointment_time']));
        } elseif ($session_data && isset($session_data['appointment_time'])) {
            $time_to_check = date('H:i', strtotime($session_data['appointment_time']));
        } elseif ($existing_appointment && isset($existing_appointment['appointment_time'])) {
            $time_to_check = date('H:i', strtotime($existing_appointment['appointment_time']));
        }
        ?>
        <?php if ($time_to_check): ?>
        const existingTime = '<?= $time_to_check ?>';
        const matchingSlot = Array.from(timeSlots).find(btn => btn.dataset.time === existingTime);
        if (matchingSlot) {
            matchingSlot.click();
        } else if (existingTime) {
            timeInput.value = existingTime;
            toggleCustomTime();
        }
        <?php endif; ?>
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
            if (timeInput) {
                timeInput.value = '';
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
            
            // Focus on time input
            if (timeInput) {
                setTimeout(() => timeInput.focus(), 100);
            }
        }
    }
}

// Clear time selection
function clearTimeSelection() {
    const timeSlots = document.querySelectorAll('.time-slot-btn');
    timeSlots.forEach(b => b.classList.remove('active'));
    selectedTimeSlot = null;
    customTimeActive = false;
}

// Form validation
document.getElementById('appointmentForm')?.addEventListener('submit', function(e) {
    const timeInput = document.getElementById('appointment_time');
    const dateInput = document.getElementById('appointment_date');
    
    if (!timeInput || !timeInput.value) {
        e.preventDefault();
        alert('Please select a time for your appointment.');
        return false;
    }
    
    if (!dateInput || !dateInput.value) {
        e.preventDefault();
        alert('Please select a date for your appointment.');
        return false;
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
