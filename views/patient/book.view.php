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
    
    <?php if (empty($doctors)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-user-md"></i></div>
            <div class="empty-state-text">No doctors found</div>
            <?php if ($search_query || $filter_specialization): ?>
                <p style="color: #9ca3af; margin-top: 0.5rem;">Try adjusting your search or filter</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 1.5rem;">
            <div class="doctors-grid">
                <?php foreach ($doctors as $doctor): ?>
                    <?php
                    $initials = strtoupper(substr($doctor['doc_first_name'] ?? 'D', 0, 1) . substr($doctor['doc_last_name'] ?? 'D', 0, 1));
                    $doctorName = 'Dr. ' . htmlspecialchars(($doctor['doc_first_name'] ?? '') . ' ' . ($doctor['doc_last_name'] ?? ''));
                    $specialization = htmlspecialchars($doctor['spec_name'] ?? 'General Practice');
                    $fee = $doctor['doc_consultation_fee'] ?? 0;
                    ?>
                    <a href="/patient/doctor-detail?id=<?= $doctor['doc_id'] ?>" class="doctor-card" aria-label="View details for <?= $doctorName ?>">
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
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
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
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

