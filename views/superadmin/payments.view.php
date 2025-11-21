<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">All Payments</h1>
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
        <span><?= htmlspecialchars($success) ?></span>
    </div>
<?php endif; ?>

<!-- Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #8b5cf6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Payments This Month</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total_this_month'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Paid</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['paid'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Pending</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['pending'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Amount</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);">₱<?= number_format($stats['total_amount'] ?? 0, 0) ?></div>
    </div>
</div>

<!-- Table Container -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header with Add Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">All Payment Records</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddPaymentModal()" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i>
            <span>Add Payment</span>
        </button>
    </div>

    <!-- Filter Bar (Hidden by default) -->
    <div id="tableFilterBar" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Payments
            </h3>
            <button type="button" class="btn btn-sm" onclick="resetTableFilters()" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
                <i class="fas fa-redo"></i>
                <span>Reset Filters</span>
            </button>
        </div>
        <div class="filter-controls-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-user" style="margin-right: 0.25rem;"></i>Patient Name
                </label>
                <input type="text" id="filterPatient" class="filter-input" placeholder="Search patient..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-dollar-sign" style="margin-right: 0.25rem;"></i>Amount Range
                </label>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="number" id="filterAmountMin" class="filter-input" placeholder="Min" step="0.01" min="0" style="flex: 1; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
                    <span style="color: var(--text-secondary);">-</span>
                    <input type="number" id="filterAmountMax" class="filter-input" placeholder="Max" step="0.01" min="0" style="flex: 1; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
                </div>
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-credit-card" style="margin-right: 0.25rem;"></i>Payment Method
                </label>
                <input type="text" id="filterMethod" class="filter-input" placeholder="Search method..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>Date
                </label>
                <input type="date" id="filterDate" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
        </div>
    </div>

    <?php if (empty($payments)): ?>
        <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
            <i class="fas fa-money-bill-wave" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p style="margin: 0;">No payment records found.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid var(--border-light);">
                        <?php
                        $current_sort = $_GET['sort'] ?? 'payment_date';
                        $current_order = $_GET['order'] ?? 'DESC';
                        ?>
                        <th class="sortable <?= $current_sort === 'payment_date' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('payment_date')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Date
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Appointment ID
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Patient
                        </th>
                        <th class="sortable <?= $current_sort === 'payment_amount' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('payment_amount')" 
                            style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Amount
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Method
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Status
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($payments as $payment): ?>
                        <tr class="table-row" 
                            data-patient="<?= htmlspecialchars(strtolower(($payment['pat_first_name'] ?? '') . ' ' . ($payment['pat_last_name'] ?? ''))) ?>"
                            data-amount="<?= floatval($payment['payment_amount'] ?? 0) ?>"
                            data-method="<?= htmlspecialchars(strtolower($payment['method_name'] ?? '')) ?>"
                            data-date="<?= $payment['payment_date'] ? date('Y-m-d', strtotime($payment['payment_date'])) : '' ?>"
                            data-status="<?= htmlspecialchars(strtolower($payment['status_name'] ?? '')) ?>"
                            style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $payment['payment_date'] ? date('d M Y', strtotime($payment['payment_date'])) : 'N/A' ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);">#<?= htmlspecialchars($payment['appointment_id']) ?></td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; overflow: hidden; flex-shrink: 0;">
                                        <?php if (!empty($payment['patient_profile_picture'])): ?>
                                            <img src="<?= htmlspecialchars($payment['patient_profile_picture']) ?>" alt="Patient" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?= strtoupper(substr($payment['pat_first_name'] ?? 'P', 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars(($payment['pat_first_name'] ?? '') . ' ' . ($payment['pat_last_name'] ?? '')) ?></strong>
                                </div>
                            </td>
                            <td style="padding: 1rem; color: var(--status-success); font-weight: 600;">₱<?= number_format($payment['payment_amount'] ?? 0, 2) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($payment['method_name'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem;">
                                <form method="POST" class="status-update-form" style="display: inline;" onchange="this.submit()">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="id" value="<?= $payment['payment_id'] ?>">
                                    <select name="payment_status_id" class="status-dropdown" 
                                            style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; background: var(--primary-blue); color: white; border: 1px solid var(--primary-blue); cursor: pointer; min-width: 120px;">
                                        <?php foreach ($payment_statuses as $status): ?>
                                            <option value="<?= $status['payment_status_id'] ?>" 
                                                    <?= ($payment['payment_status_id'] == $status['payment_status_id']) ? 'selected' : '' ?>
                                                    style="background: white; color: var(--text-primary);">
                                                <?= htmlspecialchars($status['status_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <?php if (!empty($payment['appointment_id'])): 
                                        // Prepare appointment and payment data for embedding
                                        $appointmentData = [
                                            // Appointment data
                                            'appointment_id' => $payment['appointment_id'] ?? null,
                                            'appointment_date' => $payment['appointment_date'] ?? null,
                                            'appointment_time' => $payment['appointment_time'] ?? null,
                                            'appointment_notes' => $payment['appointment_notes'] ?? null,
                                            'pat_id' => $payment['pat_id'] ?? null,
                                            'pat_first_name' => $payment['pat_first_name'] ?? null,
                                            'pat_last_name' => $payment['pat_last_name'] ?? null,
                                            'doc_id' => $payment['doc_id'] ?? null,
                                            'doc_first_name' => $payment['doc_first_name'] ?? null,
                                            'doc_last_name' => $payment['doc_last_name'] ?? null,
                                            'service_id' => $payment['service_id'] ?? null,
                                            'service_name' => $payment['service_name'] ?? null,
                                            'service_price' => $payment['service_price'] ?? null,
                                            'spec_name' => $payment['spec_name'] ?? null,
                                            'status_id' => $payment['status_id'] ?? null,
                                            'status_name' => $payment['appointment_status_name'] ?? null,
                                            'status_color' => $payment['appointment_status_color'] ?? null,
                                            'patient_profile_picture' => $payment['patient_profile_picture'] ?? null,
                                            'doctor_profile_picture' => $payment['doctor_profile_picture'] ?? null,
                                            // Payment data
                                            'payment_id' => $payment['payment_id'] ?? null,
                                            'payment_amount' => $payment['payment_amount'] ?? null,
                                            'payment_date' => $payment['payment_date'] ?? null,
                                            'payment_method' => $payment['method_name'] ?? null,
                                            'payment_status' => $payment['status_name'] ?? null,
                                            'payment_notes' => $payment['payment_notes'] ?? null
                                        ];
                                    ?>
                                        <button type="button" class="btn btn-sm view-appointment-btn" 
                                                title="View Appointment Details"
                                                data-appointment="<?= base64_encode(json_encode($appointmentData)) ?>"
                                                style="padding: 0.5rem; background: var(--primary-blue); color: white; border: none; border-radius: 6px; cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    <?php endif; ?>
                                    <form method="POST" style="display: inline;" onsubmit="return handleDelete(event, 'Are you sure you want to delete this payment record?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $payment['payment_id'] ?>">
                                        <button type="submit" class="btn btn-sm" title="Delete"
                                                style="padding: 0.5rem; background: transparent; border: none; color: var(--status-error); cursor: pointer;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-top: 1px solid var(--border-light);">
            <div style="color: var(--text-secondary); font-size: 0.875rem;">
                Showing <?= $offset + 1 ?>-<?= min($offset + $items_per_page, $total_items) ?> of <?= $total_items ?> entries
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => max(1, $page - 1)])) ?>" 
                   class="btn btn-sm" 
                   style="<?= $page <= 1 ? 'opacity: 0.5; pointer-events: none;' : '' ?>">
                    < Previous
                </a>
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                if ($start_page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="btn btn-sm">1</a>
                    <?php if ($start_page > 2): ?>
                        <span style="padding: 0.5rem;">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                       class="btn btn-sm <?= $i == $page ? 'btn-primary' : '' ?>" 
                       style="<?= $i == $page ? 'background: var(--primary-blue); color: white;' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                <?php if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <span style="padding: 0.5rem;">...</span>
                    <?php endif; ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" class="btn btn-sm"><?= $total_pages ?></a>
                <?php endif; ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => min($total_pages, $page + 1)])) ?>" 
                   class="btn btn-sm" 
                   style="<?= $page >= $total_pages ? 'opacity: 0.5; pointer-events: none;' : '' ?>">
                    Next >
                </a>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- View Appointment & Payment Details Modal -->
<div id="viewAppointmentModal" class="modal">
    <div class="modal-content" style="max-width: 900px;">
        <div class="modal-header">
            <h2 class="modal-title">Appointment & Payment Details</h2>
            <button type="button" class="modal-close" onclick="closeViewAppointmentModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="appointmentDetailsContent"></div>
        <div class="action-buttons" style="margin-top: 1.5rem;">
            <button type="button" onclick="closeViewAppointmentModal()" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                <span>Close</span>
            </button>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add New Payment Record</h2>
            <button type="button" class="modal-close" onclick="closeAddPaymentModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-grid">
                <div class="form-group">
                    <label>Appointment ID: <span style="color: var(--status-error);">*</span></label>
                    <input type="text" name="appointment_id" required placeholder="e.g., 2025-10-0000001" class="form-control">
                </div>
                <div class="form-group">
                    <label>Amount (₱): <span style="color: var(--status-error);">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Payment Date: <span style="color: var(--status-error);">*</span></label>
                    <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Payment Method: <span style="color: var(--status-error);">*</span></label>
                    <select name="payment_method_id" required class="form-control">
                        <option value="">Select Method</option>
                        <?php foreach ($payment_methods as $method): ?>
                            <option value="<?= $method['method_id'] ?>"><?= htmlspecialchars($method['method_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Status: <span style="color: var(--status-error);">*</span></label>
                    <select name="payment_status_id" required class="form-control">
                        <option value="">Select Status</option>
                        <?php foreach ($payment_statuses as $status): ?>
                            <option value="<?= $status['payment_status_id'] ?>"><?= htmlspecialchars($status['status_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group form-grid-full">
                <label>Notes:</label>
                <textarea name="notes" rows="2" class="form-control"></textarea>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    <span>Add Payment Record</span>
                </button>
                <button type="button" onclick="closeAddPaymentModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddPaymentModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddPaymentModal() {
    document.getElementById('addModal').classList.remove('active');
    document.querySelector('#addModal form').reset();
}

// Category tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const categoryTabs = document.querySelectorAll('.category-tab');
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            categoryTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const category = this.dataset.category;
            filterByCategory(category);
        });
    });
    
    // Add event listeners for view appointment buttons
    document.querySelectorAll('.view-appointment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-appointment');
                const decodedJson = atob(encodedData);
                const appointmentData = JSON.parse(decodedJson);
                viewAppointmentDetails(appointmentData);
            } catch (e) {
                console.error('Error parsing appointment data:', e);
                alert('Error loading appointment data. Please check the console for details.');
            }
        });
    });
    
    // Close modals on outside click
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
    
    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                modal.classList.remove('active');
            });
        }
    });
});

function toggleFilterSidebar() {
    // Filter sidebar not implemented for payments page
    alert('Filter sidebar not available for this page');
}

function filterByCategory(category) {
    if (category === 'all') {
        window.location.href = '/superadmin/payments';
    } else {
        window.location.href = '/superadmin/payments?status_id=' + category;
    }
}

// Table Filtering Functions
function filterTable() {
    const patientFilter = document.getElementById('filterPatient')?.value.toLowerCase().trim() || '';
    const amountMin = parseFloat(document.getElementById('filterAmountMin')?.value) || 0;
    const amountMax = parseFloat(document.getElementById('filterAmountMax')?.value) || Infinity;
    const methodFilter = document.getElementById('filterMethod')?.value.toLowerCase().trim() || '';
    const dateFilter = document.getElementById('filterDate')?.value || '';
    
    const rows = document.querySelectorAll('.table-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const patient = row.getAttribute('data-patient') || '';
        const amount = parseFloat(row.getAttribute('data-amount')) || 0;
        const method = row.getAttribute('data-method') || '';
        const date = row.getAttribute('data-date') || '';
        
        const matchesPatient = !patientFilter || patient.includes(patientFilter);
        const matchesAmount = amount >= amountMin && amount <= amountMax;
        const matchesMethod = !methodFilter || method.includes(methodFilter);
        const matchesDate = !dateFilter || date === dateFilter;
        
        if (matchesPatient && matchesAmount && matchesMethod && matchesDate) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    const hasActiveFilters = patientFilter || document.getElementById('filterAmountMin')?.value || document.getElementById('filterAmountMax')?.value || methodFilter || dateFilter;
    const tableBody = document.getElementById('tableBody');
    const noResultsMsg = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0 && rows.length > 0 && hasActiveFilters) {
        if (!noResultsMsg) {
            const msg = document.createElement('tr');
            msg.id = 'noResultsMessage';
            const colCount = document.querySelector('thead tr')?.querySelectorAll('th').length || 7;
            msg.innerHTML = `<td colspan="${colCount}" style="padding: 3rem; text-align: center; color: var(--text-secondary);"><i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i><p style="margin: 0;">No payments match the current filters.</p></td>`;
            tableBody.appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

function resetTableFilters() {
    const inputs = ['filterPatient', 'filterAmountMin', 'filterAmountMax', 'filterMethod', 'filterDate'];
    inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    filterTable();
}

function toggleTableFilters() {
    const filterBar = document.getElementById('tableFilterBar');
    const toggleBtn = document.getElementById('toggleFilterBtn');
    
    if (filterBar && toggleBtn) {
        if (filterBar.style.display === 'none') {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
            toggleBtn.innerHTML = '<i class="fas fa-filter"></i>';
        } else {
            filterBar.style.display = 'none';
            toggleBtn.classList.remove('active');
            toggleBtn.innerHTML = '<i class="fas fa-filter"></i>';
        }
    }
}

// Initialize filtering
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = ['filterPatient', 'filterAmountMin', 'filterAmountMax', 'filterMethod', 'filterDate'];
    filterInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', filterTable);
            input.addEventListener('change', filterTable);
        }
    });
    filterTable();
});

// Table Sorting Function
function sortTable(column) {
    const url = new URL(window.location.href);
    const currentSort = url.searchParams.get('sort');
    const currentOrder = url.searchParams.get('order') || 'DESC';
    
    // Toggle order if clicking the same column, otherwise default to ASC
    if (currentSort === column) {
        url.searchParams.set('order', currentOrder === 'ASC' ? 'DESC' : 'ASC');
    } else {
        url.searchParams.set('order', 'ASC');
    }
    
    url.searchParams.set('sort', column);
    url.searchParams.delete('page'); // Reset to page 1 when sorting
    
    window.location.href = url.toString();
}

// View Appointment & Payment Details Functions
function viewAppointmentDetails(data) {
    const appointmentDate = data.appointment_date ? new Date(data.appointment_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A';
    const appointmentTime = data.appointment_time ? new Date('1970-01-01T' + data.appointment_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }) : 'N/A';
    const paymentDate = data.payment_date ? new Date(data.payment_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A';
    
    // Helper function to format date
    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            return date.toLocaleString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric', 
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
            });
        } catch (e) {
            return dateString;
        }
    };
    
    // Helper function to escape HTML
    const escapeHtml = (text) => {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };
    
    // Format currency
    const formatCurrency = (amount) => {
        if (!amount) return '₱0.00';
        return '₱' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };
    
    // Patient profile picture or initial
    const patientInitial = (data.pat_first_name ? data.pat_first_name.charAt(0) : '') || (data.pat_last_name ? data.pat_last_name.charAt(0) : '') || 'P';
    const patientProfilePic = data.patient_profile_picture 
        ? `<img src="${escapeHtml(data.patient_profile_picture)}" alt="Patient" style="width: 100%; height: 100%; object-fit: cover;">`
        : `<span style="font-size: 1.5rem; font-weight: 700;">${patientInitial.toUpperCase()}</span>`;
    
    // Doctor profile picture or initial
    const doctorInitial = (data.doc_first_name ? data.doc_first_name.charAt(0) : '') || (data.doc_last_name ? data.doc_last_name.charAt(0) : '') || 'D';
    const doctorProfilePic = data.doctor_profile_picture 
        ? `<img src="${escapeHtml(data.doctor_profile_picture)}" alt="Doctor" style="width: 100%; height: 100%; object-fit: cover;">`
        : `<span style="font-size: 1.5rem; font-weight: 700;">${doctorInitial.toUpperCase()}</span>`;
    
    const content = `
        <div class="card">
            <div class="card-body">
                <!-- Appointment Information Section -->
                <h3 style="margin-bottom: 1rem; color: var(--text-primary);">Appointment Information</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem;">
                    <!-- Patient Card -->
                    <div style="background: #f9fafb; border-radius: 12px; padding: 1.5rem; text-align: center;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            ${patientProfilePic}
                        </div>
                        <h4 style="margin: 0 0 0.5rem; color: var(--text-primary); font-size: 1rem;">Patient</h4>
                        <p style="margin: 0; color: var(--text-secondary); font-weight: 600;">${escapeHtml((data.pat_first_name || '') + ' ' + (data.pat_last_name || ''))}</p>
                    </div>
                    
                    <!-- Doctor Card -->
                    <div style="background: #f9fafb; border-radius: 12px; padding: 1.5rem; text-align: center;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            ${doctorProfilePic}
                        </div>
                        <h4 style="margin: 0 0 0.5rem; color: var(--text-primary); font-size: 1rem;">Doctor</h4>
                        <p style="margin: 0; color: var(--text-secondary); font-weight: 600;">Dr. ${escapeHtml((data.doc_first_name || '') + ' ' + (data.doc_last_name || ''))}</p>
                        ${data.spec_name ? `<p style="margin: 0.5rem 0 0; color: var(--text-secondary); font-size: 0.875rem;">${escapeHtml(data.spec_name)}</p>` : ''}
                    </div>
                </div>
                <div class="form-grid">
                    <div>
                        <p style="margin: 0.5rem 0;"><strong>Appointment ID:</strong> ${data.appointment_id || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Date:</strong> ${appointmentDate}</p>
                        <p style="margin: 0.5rem 0;"><strong>Time:</strong> ${appointmentTime}</p>
                        <p style="margin: 0.5rem 0;"><strong>Status:</strong> 
                            <span class="badge" style="background: ${data.status_color || '#3B82F6'}; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 500;">
                                ${data.status_name || 'N/A'}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p style="margin: 0.5rem 0;"><strong>Service:</strong> ${data.service_name || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Specialization:</strong> ${data.spec_name || 'N/A'}</p>
                        ${data.service_price ? `<p style="margin: 0.5rem 0;"><strong>Service Price:</strong> <span style="color: var(--status-success); font-weight: 600;">${formatCurrency(data.service_price)}</span></p>` : ''}
                    </div>
                </div>
                ${data.appointment_notes ? `<div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light);"><p style="margin: 0;"><strong>Notes:</strong> ${escapeHtml(data.appointment_notes)}</p></div>` : ''}
                
                <!-- Divider Line -->
                <div style="margin: 2rem 0; border-top: 2px solid var(--border-light);"></div>
                
                <!-- Payment Information Section -->
                <h3 style="margin-bottom: 1rem; color: var(--text-primary);">Payment Information</h3>
                <div class="form-grid">
                    <div>
                        <p style="margin: 0.5rem 0;"><strong>Payment ID:</strong> ${data.payment_id || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Amount Paid:</strong> <span style="color: var(--status-success); font-weight: 700; font-size: 1.125rem;">${formatCurrency(data.payment_amount)}</span></p>
                        <p style="margin: 0.5rem 0;"><strong>Payment Date:</strong> ${paymentDate}</p>
                    </div>
                    <div>
                        <p style="margin: 0.5rem 0;"><strong>Payment Method:</strong> ${data.payment_method || 'N/A'}</p>
                        <p style="margin: 0.5rem 0;"><strong>Payment Status:</strong> 
                            <span class="badge" style="background: var(--primary-blue); color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 500;">
                                ${data.payment_status || 'N/A'}
                            </span>
                        </p>
                    </div>
                </div>
                ${data.payment_notes ? `<div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light);"><p style="margin: 0;"><strong>Payment Notes:</strong> ${escapeHtml(data.payment_notes)}</p></div>` : ''}
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light); display: flex; gap: 2rem; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-plus-circle" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
                        <div>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);"><strong>Payment Created:</strong> ${formatDate(data.payment_created_at || data.created_at)}</p>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-edit" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
                        <div>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);"><strong>Payment Updated:</strong> ${formatDate(data.payment_updated_at || data.updated_at)}</p>
                        </div>
                    </div>
                </div>
                ${data.appointment_created_at || data.appointment_updated_at ? `
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light); display: flex; gap: 2rem; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-plus-circle" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
                        <div>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);"><strong>Appointment Created:</strong> ${formatDate(data.appointment_created_at)}</p>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-edit" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
                        <div>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);"><strong>Appointment Updated:</strong> ${formatDate(data.appointment_updated_at)}</p>
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
        </div>
    `;
    document.getElementById('appointmentDetailsContent').innerHTML = content;
    document.getElementById('viewAppointmentModal').classList.add('active');
}

function closeViewAppointmentModal() {
    document.getElementById('viewAppointmentModal').classList.remove('active');
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
