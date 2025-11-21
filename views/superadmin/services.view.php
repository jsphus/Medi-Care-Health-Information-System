<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">All Services</h1>
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
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-sm" onclick="applyServiceFilters()" style="padding: 0.5rem 1rem; background: var(--primary-blue); border: 1px solid var(--primary-blue); border-radius: var(--radius-md); color: white; cursor: pointer; font-size: 0.875rem;">
                    <i class="fas fa-check"></i>
                    <span>Apply Filters</span>
                </button>
                <button type="button" class="btn btn-sm" onclick="resetServiceFilters()" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
                    <i class="fas fa-redo"></i>
                    <span>Reset Filters</span>
                </button>
            </div>
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
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>Date Created
                </label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                    <select id="filterDateMonth" class="filter-input" style="padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem; background: white; cursor: pointer;">
                        <option value="">All Months</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                    <select id="filterDateDay" class="filter-input" style="padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem; background: white; cursor: pointer;">
                        <option value="">All Days</option>
                        <?php for ($i = 1; $i <= 31; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                    <select id="filterDateYear" class="filter-input" style="padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem; background: white; cursor: pointer;">
                        <option value="">All Years</option>
                        <?php 
                        $current_year = (int)date('Y');
                        for ($year = $current_year; $year >= 2020; $year--): 
                        ?>
                            <option value="<?= $year ?>"><?= $year ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($services)): ?>
        <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
            <i class="fas fa-flask" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p style="margin: 0;">No services found.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid var(--border-light);">
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Service Name
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Description
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Price
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Duration
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Category
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Date Created
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">
                            Date Updated
                        </th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Action</th>
                    </tr>
                </thead>
                <tbody id="servicesTableBody">
                    <?php foreach ($services as $service): ?>
                        <tr class="service-row" 
                            data-service-name="<?= htmlspecialchars(strtolower($service['service_name'])) ?>"
                            data-description="<?= htmlspecialchars(strtolower($service['service_description'] ?? '')) ?>"
                            data-date="<?= !empty($service['created_at']) ? date('Y-m-d', strtotime($service['created_at'])) : '' ?>"
                            data-price="<?= floatval($service['service_price'] ?? 0) ?>"
                            data-duration="<?= intval($service['service_duration_minutes'] ?? 30) ?>"
                            data-category="<?= htmlspecialchars($service['service_category'] ?? '') ?>"
                            style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" 
                            onmouseover="this.style.background='#f9fafb'" 
                            onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <strong style="color: var(--text-primary);"><?= htmlspecialchars($service['service_name']) ?></strong>
                            </td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($service['service_description'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary); font-weight: 600;">₱<?= number_format($service['service_price'] ?? 0, 2) ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($service['service_duration_minutes'] ?? 30) ?> min</td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($service['service_category'] ?? 'N/A') ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $service['created_at'] ? date('d M Y', strtotime($service['created_at'])) : 'N/A' ?></td>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?= $service['updated_at'] ? date('d M Y', strtotime($service['updated_at'])) : 'N/A' ?></td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button class="btn btn-sm edit-service-btn" 
                                            data-service="<?= base64_encode(json_encode($service)) ?>" 
                                            title="Edit"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm view-service-btn" 
                                            data-service="<?= base64_encode(json_encode($service)) ?>" 
                                            title="View"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return handleDelete(event, 'Are you sure you want to delete this service?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $service['service_id'] ?>">
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
        <div id="paginationContainer" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-top: 1px solid var(--border-light);">
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
        <!-- Filter Active Message -->
        <div id="filterActiveMessage" style="display: none; padding: 1rem 1.5rem; border-top: 1px solid var(--border-light); background: var(--primary-blue-bg);">
            <div style="display: flex; align-items: center; gap: 0.75rem; color: var(--primary-blue-dark); font-size: 0.875rem;">
                <i class="fas fa-info-circle"></i>
                <span>Filters are applied to the current page. Clear filters to see all results across all pages.</span>
            </div>
        </div>
        <?php endif; ?>
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
                    <input type="text" name="service_name" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <input type="text" name="category" class="form-control">
                </div>
                <div class="form-group">
                    <label>Price:</label>
                    <input type="number" name="price" step="0.01" min="0" value="0" class="form-control">
                </div>
                <div class="form-group">
                    <label>Duration (Minutes):</label>
                    <input type="number" name="duration" min="1" value="30" class="form-control">
                </div>
            </div>
            <div class="form-group form-grid-full">
                <label>Description:</label>
                <textarea name="description" rows="3" class="form-control"></textarea>
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
                    <input type="text" name="category" id="edit_category" class="form-control">
                </div>
                <div class="form-group">
                    <label>Price:</label>
                    <input type="number" name="price" id="edit_price" step="0.01" min="0" class="form-control">
                </div>
                <div class="form-group">
                    <label>Duration (Minutes):</label>
                    <input type="number" name="duration" id="edit_duration" min="1" class="form-control">
                </div>
            </div>
            <div class="form-group form-grid-full">
                <label>Description:</label>
                <textarea name="description" id="edit_description" rows="3" class="form-control"></textarea>
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

<script>
// Dynamic Filtering System for Services Table
function applyServiceFilters() {
    // Ensure we're in all_results mode for filtering to work properly
    const url = new URL(window.location.href);
    const isAllResultsMode = url.searchParams.get('all_results') === '1';
    
    if (!isAllResultsMode) {
        // Store filter values before reloading
        const filterValues = {
            filterServiceName: document.getElementById('filterServiceName')?.value || '',
            filterDescription: document.getElementById('filterDescription')?.value || '',
            filterPriceMin: document.getElementById('filterPriceMin')?.value || '',
            filterPriceMax: document.getElementById('filterPriceMax')?.value || '',
            filterDurationMin: document.getElementById('filterDurationMin')?.value || '',
            filterDurationMax: document.getElementById('filterDurationMax')?.value || '',
            filterCategory: document.getElementById('filterCategory')?.value || '',
            filterDateMonth: document.getElementById('filterDateMonth')?.value || '',
            filterDateDay: document.getElementById('filterDateDay')?.value || '',
            filterDateYear: document.getElementById('filterDateYear')?.value || ''
        };
        sessionStorage.setItem('pendingFilters', JSON.stringify(filterValues));
        // Load all results first, then apply filters after page reloads
        loadAllResults();
        return;
    }
    
    // Apply filters if already in all_results mode
    filterServices();
}

function filterServices() {
    const serviceNameFilter = document.getElementById('filterServiceName').value.toLowerCase().trim();
    const descriptionFilter = document.getElementById('filterDescription').value.toLowerCase().trim();
    const priceMin = parseFloat(document.getElementById('filterPriceMin').value) || 0;
    const priceMax = parseFloat(document.getElementById('filterPriceMax').value) || Infinity;
    const durationMin = parseInt(document.getElementById('filterDurationMin').value) || 0;
    const durationMax = parseInt(document.getElementById('filterDurationMax').value) || Infinity;
    const categoryFilter = document.getElementById('filterCategory').value;
    const dateMonthFilter = document.getElementById('filterDateMonth')?.value || '';
    const dateDayFilter = document.getElementById('filterDateDay')?.value || '';
    const dateYearFilter = document.getElementById('filterDateYear')?.value || '';
    
    const rows = document.querySelectorAll('.service-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const serviceName = row.getAttribute('data-service-name') || '';
        const description = row.getAttribute('data-description') || '';
        const price = parseFloat(row.getAttribute('data-price')) || 0;
        const duration = parseInt(row.getAttribute('data-duration')) || 0;
        const category = row.getAttribute('data-category') || '';
        const dateStr = row.getAttribute('data-date') || '';
        
        // Apply filters
        const matchesServiceName = !serviceNameFilter || serviceName.includes(serviceNameFilter);
        const matchesDescription = !descriptionFilter || description.includes(descriptionFilter);
        const matchesPrice = price >= priceMin && price <= priceMax;
        const matchesDuration = duration >= durationMin && duration <= durationMax;
        const matchesCategory = !categoryFilter || category === categoryFilter;
        
        // Date filtering - extract month, day, year from date string (format: YYYY-MM-DD)
        let matchesDate = true;
        if (dateMonthFilter || dateDayFilter || dateYearFilter) {
            if (dateStr) {
                const dateParts = dateStr.split('-');
                if (dateParts.length === 3) {
                    const year = dateParts[0];
                    const month = dateParts[1];
                    const day = dateParts[2];
                    
                    const matchesMonth = !dateMonthFilter || month === String(dateMonthFilter).padStart(2, '0');
                    const matchesDay = !dateDayFilter || day === String(dateDayFilter).padStart(2, '0');
                    const matchesYear = !dateYearFilter || year === dateYearFilter;
                    
                    matchesDate = matchesMonth && matchesDay && matchesYear;
                } else {
                    matchesDate = false;
                }
            } else {
                matchesDate = false;
            }
        }
        
        // Show row if all filters match
        if (matchesServiceName && matchesDescription && matchesPrice && matchesDuration && matchesCategory && matchesDate) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide "no results" message (only if filters are active)
    const hasActiveFilters = serviceNameFilter || descriptionFilter || 
                             document.getElementById('filterPriceMin').value || 
                             document.getElementById('filterPriceMax').value ||
                             document.getElementById('filterDurationMin').value ||
                             document.getElementById('filterDurationMax').value ||
                             categoryFilter || dateMonthFilter || dateDayFilter || dateYearFilter;
    
    const tableBody = document.getElementById('servicesTableBody');
    const noResultsMsg = document.getElementById('noResultsMessage');
    const paginationContainer = document.getElementById('paginationContainer');
    const filterActiveMessage = document.getElementById('filterActiveMessage');
    
    // Show/hide pagination based on filter state
    if (paginationContainer) {
        if (hasActiveFilters) {
            paginationContainer.style.display = 'none';
            if (filterActiveMessage) {
                filterActiveMessage.style.display = 'block';
            }
        } else {
            paginationContainer.style.display = 'flex';
            if (filterActiveMessage) {
                filterActiveMessage.style.display = 'none';
            }
        }
    }
    
    if (visibleCount === 0 && rows.length > 0 && hasActiveFilters) {
        if (!noResultsMsg) {
            const msg = document.createElement('tr');
            msg.id = 'noResultsMessage';
            msg.innerHTML = '<td colspan="6" style="padding: 3rem; text-align: center; color: var(--text-secondary);"><i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i><p style="margin: 0;">No services match the current filters on this page.</p><p style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--text-light);">Try clearing filters or navigate to another page.</p></td>';
            tableBody.appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
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
    document.getElementById('filterDateMonth').value = '';
    document.getElementById('filterDateDay').value = '';
    document.getElementById('filterDateYear').value = '';
    filterServices(); // This will restore pagination visibility
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
        // Filters only apply when "Apply Filters" button is clicked
    });
    
    // Check if we're in all_results mode and restore filters
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('all_results') === '1') {
        // Restore filter values from sessionStorage and apply them
        const pendingFilters = sessionStorage.getItem('pendingFilters');
        if (pendingFilters) {
            try {
                const filterValues = JSON.parse(pendingFilters);
                if (filterValues.filterServiceName && document.getElementById('filterServiceName')) {
                    document.getElementById('filterServiceName').value = filterValues.filterServiceName;
                }
                if (filterValues.filterDescription && document.getElementById('filterDescription')) {
                    document.getElementById('filterDescription').value = filterValues.filterDescription;
                }
                if (filterValues.filterPriceMin && document.getElementById('filterPriceMin')) {
                    document.getElementById('filterPriceMin').value = filterValues.filterPriceMin;
                }
                if (filterValues.filterPriceMax && document.getElementById('filterPriceMax')) {
                    document.getElementById('filterPriceMax').value = filterValues.filterPriceMax;
                }
                if (filterValues.filterDurationMin && document.getElementById('filterDurationMin')) {
                    document.getElementById('filterDurationMin').value = filterValues.filterDurationMin;
                }
                if (filterValues.filterDurationMax && document.getElementById('filterDurationMax')) {
                    document.getElementById('filterDurationMax').value = filterValues.filterDurationMax;
                }
                if (filterValues.filterCategory && document.getElementById('filterCategory')) {
                    document.getElementById('filterCategory').value = filterValues.filterCategory;
                }
                if (filterValues.filterDateMonth && document.getElementById('filterDateMonth')) {
                    document.getElementById('filterDateMonth').value = filterValues.filterDateMonth;
                }
                if (filterValues.filterDateDay && document.getElementById('filterDateDay')) {
                    document.getElementById('filterDateDay').value = filterValues.filterDateDay;
                }
                if (filterValues.filterDateYear && document.getElementById('filterDateYear')) {
                    document.getElementById('filterDateYear').value = filterValues.filterDateYear;
                }
                // Apply the filters
                filterServices();
                // Clear the stored filters
                sessionStorage.removeItem('pendingFilters');
            } catch (e) {
                console.error('Error restoring filters:', e);
                sessionStorage.removeItem('pendingFilters');
            }
        }
    }
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
    document.getElementById('edit_description').value = service.service_description || '';
    document.getElementById('edit_price').value = service.service_price || 0;
    document.getElementById('edit_duration').value = service.service_duration_minutes || 30;
    document.getElementById('edit_category').value = service.service_category || '';
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
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
});

function filterByCategory(category) {
    if (category === 'all') {
        window.location.href = '/superadmin/services';
    }
}

function applyServiceFilters() {
    const filters = {
        category: document.querySelector('input[name="filter_category"]:checked')?.value || ''
    };
    const params = new URLSearchParams();
    if (filters.category) params.append('category', filters.category);
    const url = '/superadmin/services' + (params.toString() ? '?' + params.toString() : '');
    window.location.href = url;
}

function clearAllFilters() {
    document.querySelectorAll('.filter-sidebar input[type="radio"]').forEach(radio => {
        radio.checked = false;
    });
    const categorySearch = document.getElementById('categorySearch');
    if (categorySearch) categorySearch.value = '';
}
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
