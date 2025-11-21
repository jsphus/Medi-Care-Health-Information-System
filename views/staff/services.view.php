<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">All Services</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span><?= htmlspecialchars($success) ?></span>
    </div>
<?php endif; ?>

<!-- Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #8b5cf6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Services</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Service Appointments</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total_appointments'] ?? 0 ?></div>
    </div>
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></div>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Revenue</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);">₱<?= number_format($stats['total_revenue'] ?? 0, 0) ?></div>
    </div>
</div>

<!-- Table Container -->
<div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Table Header with Add Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">All Services</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleServiceFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddServiceModal()" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i>
            <span>Add Service</span>
        </button>
    </div>

    <!-- Filter Bar (Hidden by default) -->
    <div id="servicesFilterBar" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Services
            </h3>
            <button type="button" class="btn btn-sm" onclick="resetServiceFilters()" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
                <i class="fas fa-redo"></i>
                <span>Reset Filters</span>
            </button>
        </div>
        <div class="filter-controls-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-tag" style="margin-right: 0.25rem;"></i>Service Name
                </label>
                <input type="text" id="filterServiceName" class="filter-input" placeholder="Search service name..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-align-left" style="margin-right: 0.25rem;"></i>Description
                </label>
                <input type="text" id="filterDescription" class="filter-input" placeholder="Search description..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-dollar-sign" style="margin-right: 0.25rem;"></i>Price Range
                </label>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="number" id="filterPriceMin" class="filter-input" placeholder="Min" step="0.01" min="0" style="flex: 1; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
                    <span style="color: var(--text-secondary);">-</span>
                    <input type="number" id="filterPriceMax" class="filter-input" placeholder="Max" step="0.01" min="0" style="flex: 1; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
                </div>
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>Duration Range (min)
                </label>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="number" id="filterDurationMin" class="filter-input" placeholder="Min" min="1" style="flex: 1; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
                    <span style="color: var(--text-secondary);">-</span>
                    <input type="number" id="filterDurationMax" class="filter-input" placeholder="Max" min="1" style="flex: 1; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
                </div>
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-folder" style="margin-right: 0.25rem;"></i>Category
                </label>
                <select id="filterCategory" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem; background: white; cursor: pointer;">
                    <option value="">All Categories</option>
                    <?php if (!empty($filter_categories)): ?>
                        <?php foreach ($filter_categories as $category): ?>
                            <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    </div>

    <?php if (empty($services)): ?>
        <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
            <i class="fas fa-flask" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p style="margin: 0;">No services found.</p>
        </div>
    <?php else: ?>
        <div id="servicesGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; padding: 1.5rem;">
            <?php foreach ($services as $service): ?>
                <div class="service-card" 
                     data-service-name="<?= htmlspecialchars(strtolower($service['service_name'])) ?>"
                     data-description="<?= htmlspecialchars(strtolower($service['service_description'] ?? '')) ?>"
                     data-price="<?= floatval($service['service_price'] ?? 0) ?>"
                     data-duration="<?= intval($service['service_duration_minutes'] ?? 30) ?>"
                     data-category="<?= htmlspecialchars($service['service_category'] ?? '') ?>"
                     style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid #f3f4f6; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; display: flex; flex-direction: column;">
                    <!-- Card Header -->
                    <div style="padding: 1.5rem; border-bottom: 2px solid #f3f4f6;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></div>
                            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">
                                <?= htmlspecialchars($service['service_name']) ?>
                            </h3>
                        </div>
                        <?php if (!empty($service['service_category'])): ?>
                            <span style="display: inline-block; padding: 0.375rem 0.75rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: 8px; font-size: 0.75rem; color: var(--text-secondary); font-weight: 500;">
                                <?= htmlspecialchars($service['service_category']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Card Body -->
                    <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                        <!-- Service Details -->
                        <div style="flex: 1;">
                            <?php if (!empty($service['service_description'])): ?>
                                <p style="margin: 0 0 1rem 0; color: var(--text-secondary); font-size: 0.875rem; line-height: 1.5;">
                                    <?= htmlspecialchars($service['service_description']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Price</div>
                                    <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">
                                        ₱<?= number_format($service['service_price'] ?? 0, 2) ?>
                                    </div>
                                </div>
                                <div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Duration</div>
                                    <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">
                                        <?= htmlspecialchars($service['service_duration_minutes'] ?? 30) ?> min
                                    </div>
                                </div>
                            </div>
                            
                            <div style="padding: 0.75rem; background: var(--bg-light); border-radius: 8px; margin-bottom: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-calendar-check" style="color: var(--primary-blue);"></i>
                                    <span style="font-size: 0.875rem; color: var(--text-secondary);">
                                        <strong style="color: var(--text-primary);"><?= $service['appointment_count'] ?? 0 ?></strong> appointment(s)
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Card Actions -->
                        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: auto;">
                            <button type="button" 
                                    class="btn btn-primary show-appointments-btn" 
                                    data-service-id="<?= $service['service_id'] ?>"
                                    data-service-name="<?= htmlspecialchars($service['service_name']) ?>"
                                    style="width: 100%; padding: 0.75rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 8px; font-weight: 500;">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Show Appointments</span>
                            </button>
                            
                            <div style="display: flex; gap: 0.5rem;">
                                <button class="btn btn-sm edit-service-btn" 
                                        data-service="<?= base64_encode(json_encode($service)) ?>" 
                                        title="Edit"
                                        style="flex: 1; padding: 0.625rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: 8px; color: var(--primary-blue); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                    <i class="fas fa-edit"></i>
                                    <span style="font-size: 0.875rem;">Edit</span>
                                </button>
                                <form method="POST" style="flex: 1; display: inline;" onsubmit="return handleDelete(event, 'Are you sure you want to delete this service?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $service['service_id'] ?>">
                                    <button type="submit" class="btn btn-sm" title="Delete"
                                            style="width: 100%; padding: 0.625rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: 8px; color: var(--status-error); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                        <i class="fas fa-trash"></i>
                                        <span style="font-size: 0.875rem;">Delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- No Results Message (hidden by default) -->
        <div id="noResultsMessage" style="display: none; padding: 3rem; text-align: center; color: var(--text-secondary);">
            <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p style="margin: 0;">No services match the current filters.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Add Service Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add New Service</h2>
            <button type="button" class="modal-close" onclick="closeAddServiceModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-grid">
                <div class="form-group">
                    <label>Service Name: <span style="color: var(--status-error);">*</span></label>
                    <input type="text" name="service_name" required placeholder="e.g., Consultation, Laboratory Test" class="form-control">
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <input type="text" name="service_category" placeholder="e.g., Medical, Diagnostic" class="form-control">
                </div>
                <div class="form-group">
                    <label>Price (₱):</label>
                    <input type="number" name="service_price" step="0.01" min="0" value="0" class="form-control">
                </div>
                <div class="form-group">
                    <label>Duration (Minutes):</label>
                    <input type="number" name="service_duration" min="1" value="30" class="form-control">
                </div>
            </div>
            <div class="form-group form-grid-full">
                <label>Description:</label>
                <textarea name="service_description" rows="3" class="form-control"></textarea>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    <span>Add Service</span>
                </button>
                <button type="button" onclick="closeAddServiceModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Service Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Service</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-grid">
                <div class="form-group">
                    <label>Service Name: <span style="color: var(--status-error);">*</span></label>
                    <input type="text" name="service_name" id="edit_service_name" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <input type="text" name="service_category" id="edit_service_category" class="form-control">
                </div>
                <div class="form-group">
                    <label>Price (₱):</label>
                    <input type="number" name="service_price" id="edit_service_price" step="0.01" min="0" class="form-control">
                </div>
                <div class="form-group">
                    <label>Duration (Minutes):</label>
                    <input type="number" name="service_duration" id="edit_service_duration" min="1" class="form-control">
                </div>
            </div>
            <div class="form-group form-grid-full">
                <label>Description:</label>
                <textarea name="service_description" id="edit_service_description" rows="3" class="form-control"></textarea>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    <span>Update Service</span>
                </button>
                <button type="button" onclick="closeEditModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Appointments Modal -->
<div id="appointmentsModal" class="modal">
    <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h2 class="modal-title" id="appointmentsModalTitle">Service Appointments</h2>
            <button type="button" class="modal-close" onclick="closeAppointmentsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="appointmentsModalBody" style="padding: 1.5rem;">
            <div style="text-align: center; padding: 2rem;">
                <div class="spinner" style="border: 3px solid var(--border-light); border-top: 3px solid var(--primary-blue); border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                <p style="color: var(--text-secondary);">Loading appointments...</p>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.service-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.12) !important;
}

.appointment-item {
    padding: 1rem;
    border: 1px solid var(--border-light);
    border-radius: 8px;
    margin-bottom: 0.75rem;
    background: var(--bg-light);
    transition: background 0.2s;
}

.appointment-item:hover {
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
</style>

<script>
// Dynamic Filtering System for Services Cards
function filterServices() {
    const serviceNameFilter = document.getElementById('filterServiceName').value.toLowerCase().trim();
    const descriptionFilter = document.getElementById('filterDescription').value.toLowerCase().trim();
    const priceMin = parseFloat(document.getElementById('filterPriceMin').value) || 0;
    const priceMax = parseFloat(document.getElementById('filterPriceMax').value) || Infinity;
    const durationMin = parseInt(document.getElementById('filterDurationMin').value) || 0;
    const durationMax = parseInt(document.getElementById('filterDurationMax').value) || Infinity;
    const categoryFilter = document.getElementById('filterCategory').value;
    
    const cards = document.querySelectorAll('.service-card');
    const grid = document.getElementById('servicesGrid');
    const noResultsMsg = document.getElementById('noResultsMessage');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const serviceName = card.getAttribute('data-service-name') || '';
        const description = card.getAttribute('data-description') || '';
        const price = parseFloat(card.getAttribute('data-price')) || 0;
        const duration = parseInt(card.getAttribute('data-duration')) || 0;
        const category = card.getAttribute('data-category') || '';
        
        // Apply filters
        const matchesServiceName = !serviceNameFilter || serviceName.includes(serviceNameFilter);
        const matchesDescription = !descriptionFilter || description.includes(descriptionFilter);
        const matchesPrice = price >= priceMin && price <= priceMax;
        const matchesDuration = duration >= durationMin && duration <= durationMax;
        const matchesCategory = !categoryFilter || category === categoryFilter;
        
        // Show card if all filters match
        if (matchesServiceName && matchesDescription && matchesPrice && matchesDuration && matchesCategory) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide "no results" message (only if filters are active)
    const hasActiveFilters = serviceNameFilter || descriptionFilter || 
                             document.getElementById('filterPriceMin').value || 
                             document.getElementById('filterPriceMax').value ||
                             document.getElementById('filterDurationMin').value ||
                             document.getElementById('filterDurationMax').value ||
                             categoryFilter;
    
    if (visibleCount === 0 && cards.length > 0 && hasActiveFilters) {
        if (grid) grid.style.display = 'none';
        if (noResultsMsg) noResultsMsg.style.display = 'block';
    } else {
        if (grid) grid.style.display = 'grid';
        if (noResultsMsg) noResultsMsg.style.display = 'none';
    }
}

function resetServiceFilters() {
    document.getElementById('filterServiceName').value = '';
    document.getElementById('filterDescription').value = '';
    document.getElementById('filterPriceMin').value = '';
    document.getElementById('filterPriceMax').value = '';
    document.getElementById('filterDurationMin').value = '';
    document.getElementById('filterDurationMax').value = '';
    document.getElementById('filterCategory').value = '';
    filterServices();
}

function toggleServiceFilters() {
    const filterBar = document.getElementById('servicesFilterBar');
    const toggleBtn = document.getElementById('toggleFilterBtn');
    
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

// Add event listeners for real-time filtering
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = [
        'filterServiceName',
        'filterDescription',
        'filterPriceMin',
        'filterPriceMax',
        'filterDurationMin',
        'filterDurationMax',
        'filterCategory'
    ];
    
    filterInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', filterServices);
            input.addEventListener('change', filterServices);
        }
    });
    
    // Initial filter check (in case page loads with filters)
    filterServices();
});

function openAddServiceModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddServiceModal() {
    document.getElementById('addModal').classList.remove('active');
    document.querySelector('#addModal form').reset();
}

function editService(service) {
    document.getElementById('edit_id').value = service.service_id;
    document.getElementById('edit_service_name').value = service.service_name;
    document.getElementById('edit_service_category').value = service.service_category || '';
    document.getElementById('edit_service_price').value = service.service_price || 0;
    document.getElementById('edit_service_duration').value = service.service_duration_minutes || 30;
    document.getElementById('edit_service_description').value = service.service_description || '';
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

// Appointments Modal Functions
function openAppointmentsModal(serviceId, serviceName) {
    const modal = document.getElementById('appointmentsModal');
    const modalTitle = document.getElementById('appointmentsModalTitle');
    const modalBody = document.getElementById('appointmentsModalBody');
    
    modalTitle.textContent = serviceName + ' - Appointments';
    modalBody.innerHTML = `
        <div style="text-align: center; padding: 2rem;">
            <div class="spinner" style="border: 3px solid var(--border-light); border-top: 3px solid var(--primary-blue); border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
            <p style="color: var(--text-secondary);">Loading appointments...</p>
        </div>
    `;
    modal.classList.add('active');
    
    // Fetch appointments
    fetch(`/staff/services?action=get_appointments&service_id=${serviceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayAppointments(data.appointments, serviceName);
            } else {
                modalBody.innerHTML = `
                    <div style="text-align: center; padding: 2rem; color: var(--status-error);">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <p>${data.message || 'Failed to load appointments'}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = `
                <div style="text-align: center; padding: 2rem; color: var(--status-error);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <p>An error occurred while loading appointments.</p>
                </div>
            `;
        });
}

function displayAppointments(appointments, serviceName) {
    const modalBody = document.getElementById('appointmentsModalBody');
    
    if (!appointments || appointments.length === 0) {
        modalBody.innerHTML = `
            <div style="text-align: center; padding: 3rem;">
                <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3; color: var(--text-secondary);"></i>
                <p style="color: var(--text-secondary); margin: 0;">No appointments found for this service.</p>
            </div>
        `;
        return;
    }
    
    let html = `
        <div style="margin-bottom: 1rem; padding: 1rem; background: var(--bg-light); border-radius: 8px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <strong style="color: var(--text-primary);">Total Appointments:</strong>
                    <span style="color: var(--text-secondary); margin-left: 0.5rem;">${appointments.length}</span>
                </div>
            </div>
        </div>
        <div style="max-height: 60vh; overflow-y: auto;">
    `;
    
    appointments.forEach(apt => {
        let appointmentDate = 'N/A';
        let appointmentTime = 'N/A';
        
        if (apt.appointment_date) {
            try {
                const date = new Date(apt.appointment_date);
                appointmentDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            } catch (e) {
                appointmentDate = apt.appointment_date;
            }
        }
        
        if (apt.appointment_time) {
            try {
                // Handle time format (HH:MM:SS or HH:MM)
                const timeStr = apt.appointment_time.length === 5 ? apt.appointment_time + ':00' : apt.appointment_time;
                const time = new Date('1970-01-01T' + timeStr);
                appointmentTime = time.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            } catch (e) {
                appointmentTime = apt.appointment_time;
            }
        }
        const patientName = (apt.pat_first_name || '') + ' ' + (apt.pat_last_name || '');
        const doctorName = 'Dr. ' + (apt.doc_first_name || '') + ' ' + (apt.doc_last_name || '');
        const statusColor = apt.status_color || '#3B82F6';
        const statusName = apt.status_name || 'N/A';
        
        html += `
            <div class="appointment-item">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0.75rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Appointment ID</div>
                        <div style="font-weight: 600; color: var(--text-primary);">#${apt.appointment_id || 'N/A'}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Status</div>
                        <span class="badge" style="background: ${statusColor}; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; color: white;">
                            ${statusName}
                        </span>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0.75rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Date & Time</div>
                        <div style="color: var(--text-primary);">
                            <i class="fas fa-calendar" style="margin-right: 0.5rem; color: var(--primary-blue);"></i>${appointmentDate}
                            <br>
                            <i class="fas fa-clock" style="margin-right: 0.5rem; color: var(--primary-blue);"></i>${appointmentTime}
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Duration</div>
                        <div style="color: var(--text-primary);">
                            <i class="fas fa-hourglass-half" style="margin-right: 0.5rem; color: var(--primary-blue);"></i>${apt.appointment_duration || 30} min
                        </div>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Patient</div>
                        <div style="color: var(--text-primary);">
                            <i class="fas fa-user" style="margin-right: 0.5rem; color: var(--primary-blue);"></i>${patientName.trim() || 'N/A'}
                        </div>
                        ${apt.pat_phone ? `<div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">
                            <i class="fas fa-phone" style="margin-right: 0.5rem;"></i><a href="tel:${apt.pat_phone}" style="color: var(--primary-blue); text-decoration: none;">${apt.pat_phone}</a>
                        </div>` : ''}
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Doctor</div>
                        <div style="color: var(--text-primary);">
                            <i class="fas fa-user-md" style="margin-right: 0.5rem; color: var(--primary-blue);"></i>${doctorName.trim() || 'N/A'}
                        </div>
                    </div>
                </div>
                ${apt.appointment_notes ? `
                    <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--border-light);">
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Notes</div>
                        <div style="color: var(--text-primary); font-size: 0.875rem; font-style: italic;">
                            ${apt.appointment_notes}
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
    });
    
    html += `</div>`;
    modalBody.innerHTML = html;
}

function closeAppointmentsModal() {
    document.getElementById('appointmentsModal').classList.remove('active');
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
    
    // Add event listeners for edit buttons
    document.querySelectorAll('.edit-service-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-service');
                const decodedJson = atob(encodedData);
                const serviceData = JSON.parse(decodedJson);
                editService(serviceData);
            } catch (e) {
                console.error('Error parsing service data:', e);
                alert('Error loading service data. Please check the console for details.');
            }
        });
    });
    
    // Add event listeners for show appointments buttons
    document.querySelectorAll('.show-appointments-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const serviceId = this.getAttribute('data-service-id');
            const serviceName = this.getAttribute('data-service-name');
            openAppointmentsModal(serviceId, serviceName);
        });
    });
});

function filterByCategory(category) {
    if (category === 'all') {
        window.location.href = '/staff/services';
    }
}

function applyServiceFilters() {
    const filters = {
        category: document.querySelector('input[name="filter_category"]:checked')?.value || ''
    };
    const params = new URLSearchParams();
    if (filters.category) params.append('category', filters.category);
    const url = '/staff/services' + (params.toString() ? '?' + params.toString() : '');
    window.location.href = url;
}

function clearAllFilters() {
    document.querySelectorAll('.filter-sidebar input[type="radio"]').forEach(radio => {
        radio.checked = false;
    });
    const categorySearch = document.getElementById('categorySearch');
    if (categorySearch) categorySearch.value = '';
}

// Close modals on outside click and Escape key
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                modal.classList.remove('active');
            });
        }
    });
});
</script>

<!-- Filter Sidebar -->
<div class="filter-sidebar" id="filterSidebar">
    <div class="filter-sidebar-header">
        <h3>Filters</h3>
        <button type="button" class="filter-sidebar-close" onclick="toggleFilterSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <!-- Category Filter -->
    <?php if (!empty($filter_categories)): ?>
    <div class="filter-section">
        <div class="filter-section-header" onclick="toggleFilterSection('category')">
            <h4 class="filter-section-title">Category</h4>
            <button type="button" class="filter-section-toggle" id="categoryToggle">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
        <div class="filter-section-content" id="categoryContent">
            <input type="text" class="filter-search-input" placeholder="Search Category" id="categorySearch">
            <div class="filter-radio-group" id="categoryList">
                <?php foreach ($filter_categories as $category): ?>
                    <div class="filter-radio-item">
                        <input type="radio" name="filter_category" id="category_<?= htmlspecialchars(strtolower(str_replace(' ', '_', $category))) ?>" value="<?= htmlspecialchars($category) ?>">
                        <label for="category_<?= htmlspecialchars(strtolower(str_replace(' ', '_', $category))) ?>"><?= htmlspecialchars($category) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Filter Actions -->
    <div class="filter-sidebar-actions">
        <button type="button" class="filter-clear-btn" onclick="clearAllFilters()">Clear all</button>
        <button type="button" class="filter-apply-btn" onclick="applyServiceFilters()">Apply all filter</button>
    </div>
</div>

<script>
function toggleFilterSidebar() {
    const sidebar = document.getElementById('filterSidebar');
    const mainContent = document.querySelector('.main-content');
    const filterBtn = document.querySelector('.filter-toggle-btn');
    
    sidebar.classList.toggle('active');
    if (mainContent) {
        mainContent.classList.toggle('filter-active');
    }
    if (filterBtn) {
        filterBtn.classList.toggle('active');
    }
}

function toggleFilterSection(sectionId) {
    const content = document.getElementById(sectionId + 'Content');
    const toggle = document.getElementById(sectionId + 'Toggle');
    
    if (content && toggle) {
        content.classList.toggle('collapsed');
        const icon = toggle.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-chevron-up');
            icon.classList.toggle('fa-chevron-down');
        }
    }
}

// Search functionality for category filter
document.addEventListener('DOMContentLoaded', function() {
    const categorySearch = document.getElementById('categorySearch');
    if (categorySearch) {
        categorySearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const categoryItems = document.querySelectorAll('#categoryList .filter-radio-item');
            categoryItems.forEach(item => {
                const label = item.querySelector('label');
                if (label) {
                    const text = label.textContent.toLowerCase();
                    item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
                }
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
