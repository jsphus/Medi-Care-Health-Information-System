<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
    .book-page {
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .page-header {
        margin-bottom: 2rem;
    }
    
    .page-header-top {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    
    .page-subtitle {
        color: var(--text-secondary);
        font-size: 0.95rem;
        margin: 0;
    }
    
    .doctors-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .doctor-card {
        background: #f9fafb;
        border: 1px solid var(--border-light);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .doctor-card:hover,
    .doctor-card:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }
    
    .doctor-avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.75rem;
        margin: 0 auto 1rem;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .doctor-avatar-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .doctor-name {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }
    
    .doctor-specialization {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }
    
    .doctor-fee {
        font-size: 1rem;
        font-weight: 600;
        color: #3b82f6;
        margin-top: 0.5rem;
    }
    
    .search-input-modern:focus {
        outline: none;
        border-color: var(--status-success);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        background: var(--bg-white);
    }
    
    .category-tab:hover {
        border-color: var(--status-success);
        color: var(--status-success);
    }
    
    .category-tab.active {
        background: var(--status-success);
        border-color: var(--status-success);
        color: var(--text-white);
    }
    
    .btn-secondary:hover {
        background: var(--border-light);
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #f3f4f6;
    }
    
    .empty-state-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    .empty-state-text {
        font-size: 1.125rem;
        color: var(--text-secondary);
    }
    
    .alert-modern {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .alert-modern.error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }
    
    @media (max-width: 768px) {
        .book-page {
            padding: 1rem;
        }
        
        .doctors-grid {
            grid-template-columns: 1fr;
        }
        
        .search-filter-bar-modern {
            flex-direction: column;
            gap: 1rem;
        }
        
        .category-tabs {
            overflow-x: auto;
            flex-wrap: nowrap;
            -webkit-overflow-scrolling: touch;
        }
    }
    
    /* Modal Styles */
    .doctor-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        overflow-y: auto;
        padding: 2rem 1rem;
    }
    
    .doctor-modal-overlay.active {
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }
    
    .doctor-modal {
        background: white;
        border-radius: 12px;
        max-width: 800px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        position: relative;
        margin: auto;
    }
    
    .doctor-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border-light);
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
        border-radius: 12px 12px 0 0;
    }
    
    .doctor-modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    
    .doctor-modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-secondary);
        cursor: pointer;
        padding: 0.5rem;
        line-height: 1;
        transition: color 0.2s;
    }
    
    .doctor-modal-close:hover {
        color: var(--text-primary);
    }
    
    .doctor-modal-body {
        padding: 2rem;
    }
    
    .doctor-modal-header-section {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid var(--border-light);
    }
    
    .doctor-modal-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 2.5rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    
    .doctor-modal-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .doctor-modal-info {
        flex: 1;
    }
    
    .doctor-modal-name {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .doctor-modal-specialization {
        font-size: 1.125rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }
    
    .doctor-modal-fee {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-blue);
    }
    
    .doctor-modal-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .doctor-modal-stat {
        text-align: center;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 0.5rem;
    }
    
    .doctor-modal-stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }
    
    .doctor-modal-stat-label {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .doctor-modal-section {
        margin-bottom: 2rem;
    }
    
    .doctor-modal-section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .doctor-modal-section-title i {
        color: var(--primary-blue);
    }
    
    .doctor-modal-section-content {
        color: #4b5563;
        line-height: 1.7;
        font-size: 0.9375rem;
    }
    
    .doctor-modal-section-content p {
        margin-bottom: 1rem;
    }
    
    .doctor-modal-actions {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border-light);
        text-align: center;
    }
    
    .btn-book-modal {
        background: var(--primary-blue);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-book-modal:hover {
        background: var(--primary-blue-dark);
    }
    
    @media (max-width: 768px) {
        .doctor-modal-header-section {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .doctor-modal-stats {
            grid-template-columns: 1fr;
        }
        
        .doctor-modal-body {
            padding: 1.5rem;
        }
    }
</style>

<div class="book-page">
    <div class="page-header">
        <div class="page-header-top">
            <h1 class="page-title">Book Appointment</h1>
            <p class="page-subtitle">Browse our doctors and select one to book an appointment</p>
        </div>
    </div>
    
    <?php if ($error): ?>
        <div class="alert-modern error">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>
    
    <!-- Search and Filter Bar -->
    <div class="search-filter-bar-modern" style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem; border: 1px solid var(--border-light); display: flex; flex-direction: column; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <form method="GET" id="searchForm" style="flex: 1; display: flex; align-items: center; gap: 0.75rem; min-width: 250px;" onsubmit="handleSearchSubmit(event);">
                <div class="search-input-wrapper" style="position: relative; flex: 1; min-width: 250px;">
                    <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-secondary); font-size: 0.875rem;"></i>
                    <input type="text" 
                           name="search" 
                           id="searchInput"
                           class="search-input-modern"
                           placeholder="Search doctors by name or specialization..." 
                           value="<?= htmlspecialchars($search_query) ?>"
                           aria-label="Search doctors"
                           style="width: 100%; padding: 0.625rem 1rem 0.625rem 2.5rem; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.875rem; transition: var(--transition); background: var(--bg-light);">
                </div>
                <?php if ($search_query || $filter_specialization): ?>
                <a href="/patient/book" class="btn-action btn-secondary" style="padding: 0.625rem 1rem; text-decoration: none; white-space: nowrap; background: var(--bg-light); border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.875rem; font-weight: 500; color: var(--text-primary); cursor: pointer; transition: var(--transition); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-times"></i> Clear
                </a>
                <?php endif; ?>
            </form>
        </div>
        <div class="category-tabs" style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; padding-top: 0.75rem; border-top: 1px solid var(--border-light);">
            <button type="button" class="category-tab <?= empty($filter_specialization) ? 'active' : '' ?>" data-specialization="all" onclick="filterBySpecialization('all')" style="padding: 0.5rem 1rem; background: var(--bg-white); border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.875rem; font-weight: 500; color: var(--text-secondary); cursor: pointer; transition: var(--transition); white-space: nowrap;">All</button>
            <?php foreach ($specializations as $spec): ?>
                <button type="button" class="category-tab <?= ($filter_specialization == $spec['spec_id']) ? 'active' : '' ?>" data-specialization="<?= $spec['spec_id'] ?>" onclick="filterBySpecialization('<?= $spec['spec_id'] ?>')" style="padding: 0.5rem 1rem; background: var(--bg-white); border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.875rem; font-weight: 500; color: var(--text-secondary); cursor: pointer; transition: var(--transition); white-space: nowrap;">
                    <?= htmlspecialchars($spec['spec_name']) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php if (empty($available_doctors) && empty($unavailable_doctors)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-user-md"></i></div>
            <div class="empty-state-text">No doctors found</div>
            <?php if ($search_query || $filter_specialization): ?>
                <p style="color: #9ca3af; margin-top: 0.5rem;">Try adjusting your search or filter</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Available Doctors Section -->
        <?php if (!empty($available_doctors)): ?>
            <div style="margin-bottom: 3rem;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                    Available Doctors
                </h2>
                <div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 1.5rem;">
                    <div class="doctors-grid">
                        <?php foreach ($available_doctors as $doctor): ?>
                            <?php
                            $initials = strtoupper(substr($doctor['doc_first_name'] ?? 'D', 0, 1) . substr($doctor['doc_last_name'] ?? 'D', 0, 1));
                            $doctorName = 'Dr. ' . htmlspecialchars(formatFullName($doctor['doc_first_name'] ?? '', $doctor['doc_middle_initial'] ?? null, $doctor['doc_last_name'] ?? ''));
                            $specialization = htmlspecialchars($doctor['spec_name'] ?? 'General Practice');
                            $fee = $doctor['doc_consultation_fee'] ?? 0;
                            $schedules = $doctor_schedules[$doctor['doc_id']] ?? [];
                            ?>
                            <div class="doctor-card" 
                                 data-doctor='<?= json_encode($doctor, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
                                 data-schedules='<?= json_encode($schedules, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
                                 onclick="openDoctorModalFromCard(this)" 
                                 style="cursor: pointer; position: relative;"
                                 aria-label="View details for <?= $doctorName ?>">
                                <div class="doctor-avatar-large">
                                    <?php if (!empty($doctor['profile_picture_url'])): ?>
                                        <img src="<?= htmlspecialchars($doctor['profile_picture_url']) ?>" alt="Doctor">
                                    <?php else: ?>
                                        <?= $initials ?>
                                    <?php endif; ?>
                                </div>
                                <div class="doctor-name"><?= $doctorName ?></div>
                                <div class="doctor-specialization"><?= $specialization ?></div>
                                <?php if ($fee > 0): ?>
                                    <div class="doctor-fee">₱<?= number_format($fee, 2) ?>/consultation</div>
                                <?php endif; ?>
                                <?php if (!empty($schedules)): ?>
                                    <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e5e7eb; width: 100%;">
                                        <div style="font-size: 0.75rem; color: #6b7280; font-weight: 600; margin-bottom: 0.5rem;">Available Times:</div>
                                        <div style="display: flex; flex-direction: column; gap: 0.25rem; max-height: 80px; overflow-y: auto;">
                                            <?php 
                                            $displayed_count = 0;
                                            foreach (array_slice($schedules, 0, 3) as $schedule): 
                                                $displayed_count++;
                                                $schedule_date = date('M j', strtotime($schedule['schedule_date']));
                                                $start_time = date('g:i A', strtotime($schedule['start_time']));
                                                $end_time = date('g:i A', strtotime($schedule['end_time']));
                                            ?>
                                                <div style="font-size: 0.7rem; color: #10b981; display: flex; align-items: center; gap: 0.25rem;">
                                                    <i class="fas fa-calendar-alt" style="font-size: 0.6rem;"></i>
                                                    <span><?= $schedule_date ?>: <?= $start_time ?> - <?= $end_time ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                            <?php if (count($schedules) > 3): ?>
                                                <div style="font-size: 0.7rem; color: #6b7280; font-style: italic;">
                                                    +<?= count($schedules) - 3 ?> more
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Unavailable Doctors Section -->
        <?php if (!empty($unavailable_doctors)): ?>
            <div style="margin-bottom: 2rem;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-clock" style="color: #f59e0b;"></i>
                    Currently Unavailable
                </h2>
                <div style="background: #f9fafb; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 1.5rem; border: 1px solid #e5e7eb;">
                    <p style="color: #6b7280; margin-bottom: 1rem; font-size: 0.875rem;">
                        These doctors don't have available schedules at the moment. Check back later or contact them directly.
                    </p>
                    <div class="doctors-grid">
                        <?php foreach ($unavailable_doctors as $doctor): ?>
                            <?php
                            $initials = strtoupper(substr($doctor['doc_first_name'] ?? 'D', 0, 1) . substr($doctor['doc_last_name'] ?? 'D', 0, 1));
                            $doctorName = 'Dr. ' . htmlspecialchars(formatFullName($doctor['doc_first_name'] ?? '', $doctor['doc_middle_initial'] ?? null, $doctor['doc_last_name'] ?? ''));
                            $specialization = htmlspecialchars($doctor['spec_name'] ?? 'General Practice');
                            $fee = $doctor['doc_consultation_fee'] ?? 0;
                            ?>
                            <div class="doctor-card" 
                                 data-doctor='<?= json_encode($doctor, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
                                 onclick="openDoctorModalFromCard(this)" 
                                 style="cursor: pointer; opacity: 0.7;"
                                 aria-label="View details for <?= $doctorName ?>">
                                <div class="doctor-avatar-large" style="opacity: 0.6;">
                                    <?php if (!empty($doctor['profile_picture_url'])): ?>
                                        <img src="<?= htmlspecialchars($doctor['profile_picture_url']) ?>" alt="Doctor">
                                    <?php else: ?>
                                        <?= $initials ?>
                                    <?php endif; ?>
                                </div>
                                <div class="doctor-name"><?= $doctorName ?></div>
                                <div class="doctor-specialization"><?= $specialization ?></div>
                                <?php if ($fee > 0): ?>
                                    <div class="doctor-fee">₱<?= number_format($fee, 2) ?>/consultation</div>
                                <?php endif; ?>
                                <div style="margin-top: 0.5rem; padding: 0.25rem 0.5rem; background: #fef3c7; border-radius: 4px; font-size: 0.75rem; color: #92400e; font-weight: 600;">
                                    <i class="fas fa-exclamation-triangle"></i> Not Available
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Doctor Detail Modal -->
<div class="doctor-modal-overlay" id="doctorModal" onclick="closeDoctorModalOnOverlay(event)">
    <div class="doctor-modal" onclick="event.stopPropagation()">
        <div class="doctor-modal-header">
            <h2 class="doctor-modal-title">Doctor Details</h2>
            <button class="doctor-modal-close" onclick="closeDoctorModal()" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="doctor-modal-body" id="doctorModalBody">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<script>
// Handle search form submission - reload page with search query
function handleSearchSubmit(event) {
    event.preventDefault();
    const params = new URLSearchParams();
    const search = document.getElementById('searchInput')?.value.trim();
    
    if (search) {
        params.set('search', search);
    }
    
    // Preserve specialization filter if it exists
    const currentParams = new URLSearchParams(window.location.search);
    const specialization = currentParams.get('specialization');
    if (specialization) {
        params.set('specialization', specialization);
    }
    
    window.location.href = '/patient/book' + (params.toString() ? '?' + params.toString() : '');
}

// Category tab functionality for specializations - reload page on filter click
function filterBySpecialization(specialization) {
    const params = new URLSearchParams();
    
    if (specialization !== 'all') {
        params.set('specialization', specialization);
    }
    
    // Preserve search query if it exists
    const search = document.getElementById('searchInput')?.value.trim();
    if (search) {
        params.set('search', search);
    }
    
    // Update active tab
    const categoryTabs = document.querySelectorAll('.category-tab');
    categoryTabs.forEach(tab => {
        tab.classList.remove('active');
        if (tab.dataset.specialization === specialization) {
            tab.classList.add('active');
        }
    });
    
    // Reload page with new filter
    window.location.href = '/patient/book' + (params.toString() ? '?' + params.toString() : '');
}

// Doctor Modal Functions
function openDoctorModalFromCard(cardElement) {
    const doctorData = cardElement.getAttribute('data-doctor');
    const schedulesData = cardElement.getAttribute('data-schedules');
    if (doctorData) {
        try {
            const doctor = JSON.parse(doctorData);
            const schedules = schedulesData ? JSON.parse(schedulesData) : [];
            openDoctorModal(doctor, schedules);
        } catch (e) {
            console.error('Error parsing doctor data:', e);
        }
    }
}

function openDoctorModal(doctor, schedules = []) {
    const modal = document.getElementById('doctorModal');
    const modalBody = document.getElementById('doctorModalBody');
    
    // Prepare doctor data
    const initials = (doctor.doc_first_name ? doctor.doc_first_name.charAt(0) : 'D') + 
                    (doctor.doc_last_name ? doctor.doc_last_name.charAt(0) : 'D');
    const doctorName = 'Dr. ' + (doctor.doc_first_name || '') + (doctor.doc_middle_initial ? ' ' + doctor.doc_middle_initial.toUpperCase() + '.' : '') + ' ' + (doctor.doc_last_name || '');
    const specialization = doctor.spec_name || 'General Practice';
    const fee = parseFloat(doctor.doc_consultation_fee || 0);
    const profilePicture = doctor.profile_picture_url || '';
    
    // Build modal content
    let content = `
        <div class="doctor-modal-header-section">
            <div class="doctor-modal-avatar">
                ${profilePicture ? 
                    `<img src="${escapeHtml(profilePicture)}" alt="${escapeHtml(doctorName)}">` : 
                    initials.toUpperCase()
                }
            </div>
            <div class="doctor-modal-info">
                <h1 class="doctor-modal-name">${escapeHtml(doctorName)}</h1>
                <div class="doctor-modal-specialization">${escapeHtml(specialization)}</div>
                ${fee > 0 ? `<div class="doctor-modal-fee">₱${fee.toFixed(2)} per consultation</div>` : ''}
            </div>
        </div>
        
        <div class="doctor-modal-stats">
            ${doctor.doc_experience_years ? `
            <div class="doctor-modal-stat">
                <div class="doctor-modal-stat-value">${doctor.doc_experience_years}</div>
                <div class="doctor-modal-stat-label">Years of Experience</div>
            </div>
            ` : ''}
            <div class="doctor-modal-stat">
                <div class="doctor-modal-stat-value">
                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                </div>
                <div class="doctor-modal-stat-label">Verified Doctor</div>
            </div>
            ${doctor.doc_license_number ? `
            <div class="doctor-modal-stat">
                <div class="doctor-modal-stat-value">
                    <i class="fas fa-certificate" style="color: var(--primary-blue);"></i>
                </div>
                <div class="doctor-modal-stat-label">Licensed</div>
            </div>
            ` : ''}
        </div>
    `;
    
    // Add schedules section
    if (schedules && schedules.length > 0) {
        content += `
            <div class="doctor-modal-section">
                <h2 class="doctor-modal-section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Available Schedules
                </h2>
                <div class="doctor-modal-section-content">
                    <div style="display: grid; gap: 0.75rem; max-height: 300px; overflow-y: auto; padding-right: 0.5rem;">
        `;
        
        // Group schedules by date
        const schedulesByDate = {};
        schedules.forEach(schedule => {
            const date = schedule.schedule_date;
            if (!schedulesByDate[date]) {
                schedulesByDate[date] = [];
            }
            schedulesByDate[date].push(schedule);
        });
        
        // Display schedules grouped by date
        Object.keys(schedulesByDate).sort().forEach(date => {
            const dateSchedules = schedulesByDate[date];
            const dateFormatted = new Date(date + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            content += `
                <div style="background: #f9fafb; border-radius: 8px; padding: 1rem; border-left: 3px solid #10b981;">
                    <div style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem;">
                        <i class="fas fa-calendar" style="color: #10b981; margin-right: 0.5rem;"></i>
                        ${dateFormatted}
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            `;
            
            dateSchedules.forEach(schedule => {
                const startTime = new Date('2000-01-01T' + schedule.start_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                const endTime = new Date('2000-01-01T' + schedule.end_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                content += `
                    <div style="background: white; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 0.875rem;">
                        <i class="fas fa-clock" style="color: #6b7280; margin-right: 0.25rem;"></i>
                        <strong>${startTime}</strong> - ${endTime}
                    </div>
                `;
            });
            
            content += `
                    </div>
                </div>
            `;
        });
        
        content += `
                    </div>
                </div>
            </div>
        `;
    } else {
        content += `
            <div class="doctor-modal-section">
                <h2 class="doctor-modal-section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Available Schedules
                </h2>
                <div class="doctor-modal-section-content">
                    <p style="color: #6b7280; font-style: italic;">No available schedules at the moment. Please check back later.</p>
                </div>
            </div>
        `;
    }
    
    if (doctor.doc_bio) {
        content += `
            <div class="doctor-modal-section">
                <h2 class="doctor-modal-section-title">
                    <i class="fas fa-user-md"></i>
                    About
                </h2>
                <div class="doctor-modal-section-content">
                    <p>${escapeHtml(doctor.doc_bio).replace(/\n/g, '<br>')}</p>
                </div>
            </div>
        `;
    }
    
    if (doctor.doc_qualification) {
        content += `
            <div class="doctor-modal-section">
                <h2 class="doctor-modal-section-title">
                    <i class="fas fa-graduation-cap"></i>
                    Qualifications
                </h2>
                <div class="doctor-modal-section-content">
                    <p>${escapeHtml(doctor.doc_qualification).replace(/\n/g, '<br>')}</p>
                </div>
            </div>
        `;
    }
    
    if (specialization && doctor.spec_description) {
        content += `
            <div class="doctor-modal-section">
                <h2 class="doctor-modal-section-title">
                    <i class="fas fa-stethoscope"></i>
                    Specialization
                </h2>
                <div class="doctor-modal-section-content">
                    <p><strong>${escapeHtml(specialization)}</strong></p>
                    <p>${escapeHtml(doctor.spec_description).replace(/\n/g, '<br>')}</p>
                </div>
            </div>
        `;
    }
    
    content += `
        <div class="doctor-modal-actions">
            <a href="/patient/appointments/create?doctor_id=${doctor.doc_id}" class="btn-book-modal">
                <i class="fas fa-calendar-check"></i>
                Book Appointment
            </a>
        </div>
    `;
    
    modalBody.innerHTML = content;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDoctorModal() {
    const modal = document.getElementById('doctorModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

function closeDoctorModalOnOverlay(event) {
    if (event.target === event.currentTarget) {
        closeDoctorModal();
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDoctorModal();
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

