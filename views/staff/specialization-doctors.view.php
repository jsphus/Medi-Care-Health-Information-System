<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-top">
        <h1 class="page-title"><?= isset($specialization) && $specialization ? htmlspecialchars($specialization['spec_name']) . ' Doctors' : 'Doctors' ?></h1>
    </div>
</div>

<?php if (isset($error) && $error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<?php if (isset($specialization) && $specialization): ?>
    <div class="card" style="border-left: 4px solid var(--primary-blue);">
        <div class="card-header">
            <h2 class="card-title">About <?= htmlspecialchars($specialization['spec_name']) ?></h2>
        </div>
        <div class="card-body">
            <p style="margin: 0; color: var(--text-secondary);">
                <?= htmlspecialchars($specialization['spec_description'] ?? 'No description available') ?>
            </p>
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
                   placeholder="Search Doctor...">
        </div>
    </form>
    <div class="category-tabs">
        <button type="button" class="category-tab active" data-category="all">All</button>
        <button type="button" class="category-tab" data-category="active">Active</button>
        <button type="button" class="category-tab" data-category="inactive">Inactive</button>
    </div>
</div>

<!-- Doctors List -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Doctors Specializing in <?= htmlspecialchars($specialization['spec_name'] ?? 'This Field') ?></h2>
    </div>
    <?php if (empty($doctors)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-user-md"></i></div>
            <div class="empty-state-text">No doctors found for this specialization.</div>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; padding: 1.5rem;">
            <?php foreach ($doctors as $doctor): ?>
                <div class="card" style="margin: 0;">
                    <div class="card-body">
                        <h3 style="margin: 0 0 1rem 0; color: var(--text-primary); font-size: 1.125rem;">
                            Dr. <?= htmlspecialchars($doctor['doc_first_name'] . ' ' . $doctor['doc_last_name']) ?>
                        </h3>
                        
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">
                            <p style="margin: 0.5rem 0;">
                                <strong>Email:</strong> <?= htmlspecialchars($doctor['doc_email']) ?>
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <strong>Phone:</strong> <?= htmlspecialchars($doctor['doc_phone'] ?? 'N/A') ?>
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <strong>License:</strong> <?= htmlspecialchars($doctor['doc_license_number'] ?? 'N/A') ?>
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <strong>Experience:</strong> <?= htmlspecialchars($doctor['doc_experience_years'] ?? '0') ?> years
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <strong>Consultation Fee:</strong> <strong style="color: var(--status-success);">₱<?= number_format($doctor['doc_consultation_fee'] ?? 0, 2) ?></strong>
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <strong>Total Appointments:</strong> <?= isset($doctor['total_appointments']) ? $doctor['total_appointments'] : 0 ?>
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <strong>Status:</strong> 
                                <span class="status-badge <?= ($doctor['doc_status'] ?? 'active') === 'active' ? 'active' : 'inactive' ?>">
                                    <?= ucfirst($doctor['doc_status'] ?? 'active') ?>
                                </span>
                            </p>
                        </div>
                        
                        <?php if (!empty($doctor['doc_bio'])): ?>
                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light);">
                                <p style="margin: 0; font-size: 0.8125rem; color: var(--text-secondary); font-style: italic;">
                                    <?= htmlspecialchars($doctor['doc_bio']) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
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
    // Filter sidebar not implemented for specialization-doctors page
    alert('Filter sidebar not available for this page');
}

function filterByCategory(category) {
    if (category === 'all') {
        window.location.href = window.location.pathname;
    } else {
        window.location.href = window.location.pathname + '?status=' + category;
    }
}
</script>

<?php require_once __DIR__ . '/../partials/filter-sidebar.php'; ?>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
