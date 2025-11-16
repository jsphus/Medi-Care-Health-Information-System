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
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .doctor-card {
        background: white;
        border: 1px solid #f3f4f6;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .doctor-card:hover,
    .doctor-card:focus {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        transform: translateY(-2px);
    }
    
    .doctor-avatar-large {
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
        margin-bottom: 1rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
        margin-bottom: 1rem;
    }
    
    .doctor-fee {
        font-size: 1rem;
        font-weight: 600;
        color: #3b82f6;
        margin-top: auto;
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
    <div class="search-filter-bar-modern">
        <form method="GET" style="flex: 1; display: flex; align-items: center; gap: 0.75rem;">
            <div class="search-input-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" 
                       name="search" 
                       class="search-input-modern"
                       placeholder="Search doctors by name or specialization..." 
                       value="<?= htmlspecialchars($search_query) ?>"
                       aria-label="Search doctors">
            </div>
            <?php if ($search_query || $filter_specialization): ?>
            <a href="/patient/book" class="btn-action btn-secondary" style="padding: 0.75rem 1.5rem; text-decoration: none; white-space: nowrap;">
                <i class="fas fa-times"></i> Clear
            </a>
            <?php endif; ?>
        </form>
        <div class="category-tabs">
            <button type="button" class="category-tab <?= empty($filter_specialization) ? 'active' : '' ?>" data-specialization="all">All</button>
            <?php foreach ($specializations as $spec): ?>
                <button type="button" class="category-tab <?= ($filter_specialization == $spec['spec_id']) ? 'active' : '' ?>" data-specialization="<?= $spec['spec_id'] ?>">
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
        <div class="doctors-grid">
            <?php foreach ($doctors as $doctor): ?>
                <?php
                $initials = strtoupper(substr($doctor['doc_first_name'], 0, 1) . substr($doctor['doc_last_name'], 0, 1));
                $doctorName = 'Dr. ' . htmlspecialchars($doctor['doc_first_name'] . ' ' . $doctor['doc_last_name']);
                $specialization = htmlspecialchars($doctor['spec_name'] ?? 'General Practice');
                $fee = $doctor['doc_consultation_fee'] ?? 0;
                ?>
                <a href="/patient/doctor-detail?id=<?= $doctor['doc_id'] ?>" class="doctor-card" aria-label="View details for <?= $doctorName ?>">
                    <div class="doctor-avatar-large"><?= $initials ?></div>
                    <div class="doctor-name"><?= $doctorName ?></div>
                    <div class="doctor-specialization"><?= $specialization ?></div>
                    <div class="doctor-fee">₱<?= number_format($fee, 2) ?>/consultation</div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Category tab functionality for specializations
document.addEventListener('DOMContentLoaded', function() {
    const categoryTabs = document.querySelectorAll('.category-tab');
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            categoryTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const specialization = this.dataset.specialization;
            filterBySpecialization(specialization);
        });
    });
});

function filterBySpecialization(specialization) {
    const params = new URLSearchParams(window.location.search);
    
    if (specialization === 'all') {
        params.delete('specialization');
    } else {
        params.set('specialization', specialization);
    }
    
    // Preserve search query if it exists
    const search = document.querySelector('input[name="search"]')?.value;
    if (search) {
        params.set('search', search);
    } else {
        params.delete('search');
    }
    
    window.location.href = '/patient/book' + (params.toString() ? '?' + params.toString() : '');
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

