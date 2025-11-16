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

<!-- Top Summary Cards Row -->
<div class="stat-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Appointments Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Appointments</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['total_appointments']) ?>
                </div>
                <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; color: #ef4444;">
                    <span>-4.3%</span>
                    <span style="color: var(--text-secondary);">then last month</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Patients Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Total Patients</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['total_patients']) ?>
                </div>
                <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; color: var(--status-success);">
                    <span>+6.5%</span>
                    <span style="color: var(--text-secondary);">then last month</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Admitted Patients Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Admitted Patients</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= number_format($stats['admitted_patients']) ?>
                </div>
                <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; color: var(--status-success);">
                    <span>+6.5%</span>
                    <span style="color: var(--text-secondary);">then last month</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Revenue Card -->
    <div class="stat-card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 500;">Today's Revenue</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    $<?= number_format($stats['today_revenue'], 2) ?>
                </div>
                <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; color: var(--status-success);">
                    <span>+6.5%</span>
                    <span style="color: var(--text-secondary);">then last month</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Left Column: Patient List Table -->
    <div class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="margin: 0 0 1.5rem 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">Patient list</h2>
        
        <?php if (empty($patient_list)): ?>
            <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                <p style="margin: 0;">No patient records found</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-light);">
                            <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-secondary);">#</th>
                            <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-secondary);">Date</th>
                            <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-secondary);">Patient list</th>
                            <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-secondary);">Age</th>
                            <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-secondary);">Reason</th>
                            <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-secondary);">Type</th>
                            <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-secondary);">Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patient_list as $index => $patient): ?>
                            <?php
                            $patientName = htmlspecialchars(($patient['pat_first_name'] ?? '') . ' ' . ($patient['pat_last_name'] ?? ''));
                            $appointmentDate = isset($patient['appointment_date']) ? date('n/j/y', strtotime($patient['appointment_date'])) : 'N/A';
                            $age = '';
                            if (!empty($patient['pat_date_of_birth'])) {
                                $birthDate = new DateTime($patient['pat_date_of_birth']);
                                $today = new DateTime();
                                $age = $today->diff($birthDate)->y;
                            }
                            $reason = htmlspecialchars($patient['appointment_notes'] ?? $patient['service_name'] ?? '---');
                            $appointmentType = $patient['appointment_type'] ?? 'First visit';
                            $hasReport = $patient['has_report'] ?? 'No';
                            ?>
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary);"><?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?></td>
                                <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary);"><?= $appointmentDate ?></td>
                                <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary); font-weight: 500;"><?= $patientName ?></td>
                                <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary);"><?= $age ? $age : '---' ?></td>
                                <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary);"><?= $reason ?></td>
                                <td style="padding: 0.75rem; font-size: 0.875rem;">
                                    <?php if ($appointmentType === 'Follow up'): ?>
                                        <span style="color: var(--status-success); font-weight: 500;">Follow up</span>
                                    <?php else: ?>
                                        <span style="color: var(--text-primary);"><?= htmlspecialchars($appointmentType) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.75rem; font-size: 0.875rem; color: var(--text-primary);">
                                    <?php if ($hasReport === 'Yes'): ?>
                                        <span style="color: var(--text-primary);">Yes</span>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary);"><?= $hasReport === 'No' ? 'No' : '---' ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Calendar and New Appointments -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Calendar Widget -->
        <div class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 id="calendar-month-header" style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary); text-transform: uppercase;">
                    <?= strtoupper(date('F Y')) ?>
                </h3>
                <div style="display: flex; gap: 0.5rem;">
                    <button onclick="changeMonth(-1)" style="background: none; border: none; cursor: pointer; color: var(--text-secondary); padding: 0.25rem 0.5rem;">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button onclick="changeMonth(1)" style="background: none; border: none; cursor: pointer; color: var(--text-secondary); padding: 0.25rem 0.5rem;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div id="calendar-widget" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.5rem;">
                <!-- Calendar will be generated by JavaScript -->
            </div>
        </div>

        <!-- New Appointments List -->
        <div class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 1.5rem 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">New Appointments</h3>
            
            <?php if (empty($new_appointments)): ?>
                <div style="text-align: center; padding: 1rem; color: var(--text-secondary);">
                    <i class="fas fa-calendar" style="font-size: 1.5rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                    <p style="margin: 0; font-size: 0.875rem;">No upcoming appointments</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($new_appointments as $apt): ?>
                        <?php
                        $patName = htmlspecialchars(($apt['pat_first_name'] ?? '') . ' ' . ($apt['pat_last_name'] ?? ''));
                        $appointmentTime = isset($apt['appointment_time']) ? date('g:i A', strtotime($apt['appointment_time'])) : 'N/A';
                        $reason = htmlspecialchars($apt['appointment_notes'] ?? $apt['service_name'] ?? 'Consultation');
                        $appointmentType = $apt['appointment_type'] ?? 'First Visit';
                        if (!empty($apt['pat_date_of_birth'])) {
                            $birthDate = new DateTime($apt['pat_date_of_birth']);
                            $today = new DateTime();
                            $age = $today->diff($birthDate)->y;
                        } else {
                            $age = '';
                        }
                        ?>
                        <div style="display: flex; gap: 1rem; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; flex-shrink: 0;">
                                <?= strtoupper(substr($apt['pat_first_name'] ?? 'P', 0, 1)) ?>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; font-size: 0.875rem;">
                                    <?= $patName ?>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">
                                    age: <?= $age ? $age . ' years' : 'N/A' ?>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">
                                    reason: <?= $reason ?>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                        Time: <?= $appointmentTime ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                        Type: <?= htmlspecialchars($appointmentType) ?>
                                    </div>
                                </div>
                                <a href="/doctor/appointments/today" style="display: inline-block; margin-top: 0.5rem; padding: 0.375rem 0.75rem; background: var(--primary-blue); color: white; border-radius: 6px; text-decoration: none; font-size: 0.75rem; font-weight: 500;">
                                    View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bottom Charts Row -->
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Patient Appointment Type Donut Chart -->
    <div class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 1.5rem 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">Patient Appointment Type</h3>
        <div style="position: relative; height: 250px;">
            <canvas id="appointmentTypeChart"></canvas>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 12px; height: 12px; border-radius: 2px; background: var(--status-success);"></div>
                <span style="font-size: 0.875rem; color: var(--text-primary);">Follow up</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 12px; height: 12px; border-radius: 2px; background: #f59e0b;"></div>
                <span style="font-size: 0.875rem; color: var(--text-primary);">Emergency</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 12px; height: 12px; border-radius: 2px; background: var(--primary-blue);"></div>
                <span style="font-size: 0.875rem; color: var(--text-primary);">First visit</span>
            </div>
        </div>
    </div>

    <!-- Monthly Patients Visit Line Graph -->
    <div class="card" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 1.5rem 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">Monthly Patients Visit</h3>
        <div style="position: relative; height: 250px;">
            <canvas id="monthlyVisitChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Calendar Widget
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();

function generateCalendar() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const calendarWidget = document.getElementById('calendar-widget');
    
    // Update header
    const header = document.getElementById('calendar-month-header');
    if (header) {
        header.textContent = monthNames[currentMonth].toUpperCase() + ' ' + currentYear;
    }
    
    // Clear previous calendar
    calendarWidget.innerHTML = '';
    
    // Add day headers
    dayNames.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.style.cssText = 'text-align: center; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); padding: 0.5rem;';
        dayHeader.textContent = day;
        calendarWidget.appendChild(dayHeader);
    });
    
    // Get first day of month and number of days
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const today = new Date();
    
    // Add empty cells for days before month starts
    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.style.cssText = 'padding: 0.5rem; text-align: center;';
        calendarWidget.appendChild(emptyCell);
    }
    
    // Add days of month
    for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement('div');
        dayCell.style.cssText = 'padding: 0.5rem; text-align: center; font-size: 0.875rem; cursor: pointer; border-radius: 6px;';
        
        // Highlight today
        if (day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) {
            dayCell.style.cssText += 'background: var(--primary-blue); color: white; font-weight: 600;';
        } else {
            dayCell.style.cssText += 'color: var(--text-primary);';
        }
        
        dayCell.textContent = day;
        dayCell.onmouseover = function() {
            if (!this.style.background || !this.style.background.includes('var(--primary-blue)')) {
                this.style.background = '#f3f4f6';
            }
        };
        dayCell.onmouseout = function() {
            if (!this.style.background || !this.style.background.includes('var(--primary-blue)')) {
                this.style.background = '';
            }
        };
        calendarWidget.appendChild(dayCell);
    }
}

function changeMonth(direction) {
    currentMonth += direction;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    } else if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    generateCalendar();
}

// Initialize calendar
generateCalendar();

// Appointment Type Donut Chart
const appointmentTypeCtx = document.getElementById('appointmentTypeChart').getContext('2d');
const appointmentTypeChart = new Chart(appointmentTypeCtx, {
    type: 'doughnut',
    data: {
        labels: ['Follow up', 'Emergency', 'First visit'],
        datasets: [{
            data: [
                <?= $appointment_type_chart['Follow up'] ?? 0 ?>,
                <?= $appointment_type_chart['Emergency'] ?? 0 ?>,
                <?= $appointment_type_chart['First visit'] ?? 0 ?>
            ],
            backgroundColor: [
                'var(--status-success)',
                '#f59e0b',
                'var(--primary-blue)'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Monthly Patients Visit Line Chart
const monthlyVisitCtx = document.getElementById('monthlyVisitChart').getContext('2d');
const monthlyVisitChart = new Chart(monthlyVisitCtx, {
    type: 'line',
    data: {
        labels: ['week 1', 'week 2', 'week 3', 'week 4'],
        datasets: [{
            label: 'No. of patients',
            data: <?= json_encode($weekly_visits) ?>,
            borderColor: 'var(--primary-blue)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointBackgroundColor: 'var(--primary-blue)',
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
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 10
                },
                grid: {
                    color: '#f3f4f6'
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
