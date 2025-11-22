<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-top">
        <h1 class="page-title"><?= isset($service) && $service ? htmlspecialchars($service['service_name']) . ' - Appointments' : 'Service Appointments' ?></h1>
    </div>
</div>

<?php if (isset($error) && $error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<?php if (isset($service) && $service): ?>
    <div class="card" style="border-left: 4px solid var(--primary-blue);">
        <div class="card-header">
            <h2 class="card-title">Service Details</h2>
        </div>
        <div class="card-body">
            <div class="form-grid">
                <div>
                    <p style="margin: 0.5rem 0;"><strong>Service:</strong> <?= htmlspecialchars($service['service_name']) ?></p>
                    <p style="margin: 0.5rem 0;"><strong>Category:</strong> <?= htmlspecialchars($service['service_category'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p style="margin: 0.5rem 0;"><strong>Price:</strong> ₱<?= number_format($service['service_price'] ?? 0, 2) ?></p>
                    <p style="margin: 0.5rem 0;"><strong>Duration:</strong> <?= htmlspecialchars($service['service_duration_minutes'] ?? 30) ?> minutes</p>
                </div>
                <div>
                    <p style="margin: 0.5rem 0;"><strong>Total Appointments:</strong> <?= count($appointments) ?></p>
                </div>
            </div>
            <?php if (!empty($service['service_description'])): ?>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light);">
                    <p style="margin: 0; color: var(--text-secondary); font-style: italic;">
                        <?= htmlspecialchars($service['service_description']) ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Search and Filter Bar -->
<div class="search-filter-bar-modern">
    <button type="button" class="filter-toggle-btn" onclick="toggleFilterSidebar()">
        <i class="fas fa-filter"></i>
        <span>Filter</span>
        <i class="fas fa-chevron-down"></i>
    </button>
    <form method="GET" style="flex: 1; display: flex; align-items: center; gap: 0.75rem;">
        <div class="search-input-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" name="search" class="search-input-modern" 
                   value="<?= htmlspecialchars($search_query ?? '') ?>" 
                   placeholder="Search Appointment...">
        </div>
    </form>
    <div class="category-tabs">
        <button type="button" class="category-tab active" data-category="all">All</button>
        <?php if (isset($statuses)): ?>
            <?php foreach (array_slice($statuses, 0, 4) as $status): ?>
                <button type="button" class="category-tab" data-category="<?= $status['status_id'] ?>">
                    <?= htmlspecialchars($status['status_name']) ?>
                </button>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Appointments List -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">All Appointments for <?= htmlspecialchars($service['service_name'] ?? 'This Service') ?></h2>
    </div>
    <?php if (empty($appointments)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
            <div class="empty-state-text">No appointments found for this service.</div>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <?php
                        $current_sort = $_GET['sort'] ?? 'appointment_date';
                        $current_order = $_GET['order'] ?? 'DESC';
                        ?>
                        <th class="sortable <?= $current_sort === 'appointment_id' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('appointment_id')">
                            Appointment ID
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'appointment_date' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('appointment_date')">
                            Date
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'appointment_time' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('appointment_time')">
                            Time
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th>Patient</th>
                        <th>Contact</th>
                        <th>Doctor</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th style="width: 50px;">
                            <i class="fas fa-sticky-note notes-header-icon" title="Notes - Hover over rows to view"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $apt): ?>
                        <?php
                        $notes = !empty($apt['appointment_notes']) ? htmlspecialchars($apt['appointment_notes']) : 'No notes available';
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($apt['appointment_id']) ?></strong></td>
                            <td><?= isset($apt['appointment_date']) ? date('M j, Y', strtotime($apt['appointment_date'])) : 'N/A' ?></td>
                            <td><?= isset($apt['appointment_time']) ? date('g:i A', strtotime($apt['appointment_time'])) : 'N/A' ?></td>
                            <td><?= htmlspecialchars(($apt['pat_first_name'] ?? '') . ' ' . ($apt['pat_last_name'] ?? '')) ?></td>
                            <td>
                                <?php if (!empty($apt['pat_phone'])): ?>
                                    <a href="tel:<?= htmlspecialchars($apt['pat_phone']) ?>" class="text-link">
                                        <i class="fas fa-phone"></i> <?= htmlspecialchars($apt['pat_phone']) ?>
                                    </a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>Dr. <?= htmlspecialchars(($apt['doc_first_name'] ?? '') . ' ' . ($apt['doc_last_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($apt['appointment_duration'] ?? 30) ?> min</td>
                            <td>
                                <span class="badge" style="background: <?= htmlspecialchars($apt['status_color'] ?? '#3B82F6') ?>;">
                                    <?= htmlspecialchars($apt['status_name'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td class="notes-cell" data-notes="<?= htmlspecialchars($notes) ?>">
                                <?php if (!empty($apt['appointment_notes'])): ?>
                                    <i class="fas fa-sticky-note" style="color: var(--primary-blue); cursor: help;"></i>
                                <?php else: ?>
                                    <span style="color: var(--text-secondary);">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="pagination">
            <div class="pagination-controls">
                <button class="pagination-btn" disabled>
                    <i class="fas fa-angle-double-left"></i>
                </button>
                <button class="pagination-btn" disabled>
                    <i class="fas fa-angle-left"></i>
                </button>
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn">3</button>
                <button class="pagination-btn">
                    <i class="fas fa-angle-right"></i>
                </button>
                <button class="pagination-btn">
                    <i class="fas fa-angle-double-right"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
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
});

function toggleFilterSidebar() {
    // Filter sidebar not implemented for service-appointments page
    alert('Filter sidebar not available for this page');
}

function filterByCategory(category) {
    if (category === 'all') {
        window.location.href = window.location.pathname;
    } else {
        window.location.href = window.location.pathname + '?status=' + category;
    }
}

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
    
    window.location.href = url.toString();
}
</script>

<?php require_once __DIR__ . '/../partials/filter-sidebar.php'; ?>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
