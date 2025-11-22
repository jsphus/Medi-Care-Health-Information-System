<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-top">
        <h1 class="page-title">Manage All Doctor Schedules</h1>
    </div>
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

<?php
// Helper function to format time to 12-hour AM/PM format
function formatTimeToAMPM($time) {
    if (empty($time)) return 'N/A';
    try {
        // Handle both HH:MM:SS and HH:MM formats
        $timeParts = explode(':', $time);
        if (count($timeParts) >= 2) {
            $hour = (int)$timeParts[0];
            $minute = $timeParts[1];
            $amPm = $hour >= 12 ? 'PM' : 'AM';
            if ($hour == 0) {
                $hour12 = 12; // 00:00 = 12:00 AM
            } else if ($hour == 12) {
                $hour12 = 12; // 12:00 = 12:00 PM
            } else if ($hour > 12) {
                $hour12 = $hour - 12; // 13:00 = 1:00 PM, etc.
            } else {
                $hour12 = $hour; // 1-11 stay as is
            }
            return sprintf('%d:%s %s', $hour12, $minute, $amPm);
        }
        return $time;
    } catch (Exception $e) {
        return $time;
    }
}
?>

<style>
/* Scrollable schedules container styling */
.schedules-scroll-container {
    overflow-x: hidden;
    overflow-y: auto;
    flex: 1;
    max-height: 500px;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
}

.schedules-scroll-container::-webkit-scrollbar {
    width: 8px;
}

.schedules-scroll-container::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 4px;
}

.schedules-scroll-container::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 4px;
}

.schedules-scroll-container::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}

@media (max-width: 1024px) {
    .schedules-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<!-- Today's and Tomorrow's Schedules -->
<?php if (!empty($today_schedules) || !empty($tomorrow_schedules)): ?>
<div class="schedules-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Today's Schedules -->
    <?php if (!empty($today_schedules)): ?>
        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 1.5rem; display: flex; flex-direction: column; height: 100%;">
            <div style="margin-bottom: 1.5rem; flex-shrink: 0;">
                <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">Today's Schedules</h2>
                <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem; color: var(--text-secondary);"><?= date('F d, Y') ?></p>
            </div>
            <div class="schedules-scroll-container">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($today_schedules as $sched): ?>
                        <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f9fafb; border-radius: 8px; border: 1px solid var(--border-light); transition: all 0.2s; flex-shrink: 0;" 
                             onmouseover="this.style.background='#f3f4f6'; this.style.borderColor='var(--primary-blue)';" 
                             onmouseout="this.style.background='#f9fafb'; this.style.borderColor='var(--border-light)';">
                            <div style="position: relative; flex-shrink: 0;">
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.125rem; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <?php if (!empty($sched['profile_picture_url'])): ?>
                                        <img src="<?= htmlspecialchars($sched['profile_picture_url']) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <?= strtoupper(substr($sched['doc_first_name'] ?? 'D', 0, 1) . substr($sched['doc_last_name'] ?? 'D', 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; color: var(--text-primary); font-size: 0.9375rem; margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    Dr. <?= htmlspecialchars(($sched['doc_first_name'] ?? '') . ' ' . ($sched['doc_last_name'] ?? '')) ?>
                                </div>
                                <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= htmlspecialchars($sched['spec_name'] ?? 'N/A') ?>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: var(--primary-blue); color: white; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-clock"></i>
                                        <span><?= formatTimeToAMPM($sched['start_time'] ?? '') ?> - <?= formatTimeToAMPM($sched['end_time'] ?? '') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tomorrow's Schedules -->
    <?php if (!empty($tomorrow_schedules)): ?>
        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 1.5rem; display: flex; flex-direction: column; height: 100%;">
            <div style="margin-bottom: 1.5rem; flex-shrink: 0;">
                <h2 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">Tomorrow's Schedules</h2>
                <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem; color: var(--text-secondary);"><?= date('F d, Y', strtotime('+1 day')) ?></p>
            </div>
            <div class="schedules-scroll-container">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($tomorrow_schedules as $sched): ?>
                        <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f9fafb; border-radius: 8px; border: 1px solid var(--border-light); transition: all 0.2s; flex-shrink: 0;" 
                             onmouseover="this.style.background='#f3f4f6'; this.style.borderColor='var(--primary-blue)';" 
                             onmouseout="this.style.background='#f9fafb'; this.style.borderColor='var(--border-light)';">
                            <div style="position: relative; flex-shrink: 0;">
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.125rem; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <?php if (!empty($sched['profile_picture_url'])): ?>
                                        <img src="<?= htmlspecialchars($sched['profile_picture_url']) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <?= strtoupper(substr($sched['doc_first_name'] ?? 'D', 0, 1) . substr($sched['doc_last_name'] ?? 'D', 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; color: var(--text-primary); font-size: 0.9375rem; margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    Dr. <?= htmlspecialchars(($sched['doc_first_name'] ?? '') . ' ' . ($sched['doc_last_name'] ?? '')) ?>
                                </div>
                                <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= htmlspecialchars($sched['spec_name'] ?? 'N/A') ?>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: var(--primary-blue); color: white; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-clock"></i>
                                        <span><?= formatTimeToAMPM($sched['start_time'] ?? '') ?> - <?= formatTimeToAMPM($sched['end_time'] ?? '') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- All Schedules -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <h2 class="card-title" style="margin: 0;">All Doctor Schedules</h2>
            <button type="button" id="toggleFilterBtn" class="btn btn-sm" onclick="toggleTableFilters()" style="padding: 0.5rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem;">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <button type="button" class="btn btn-primary" onclick="openAddScheduleModal()" style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-plus"></i>
                <span>Add Schedule</span>
            </button>
            <button type="button" class="btn btn-success" onclick="openBatchCreateModal()" style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calendar-plus"></i>
                <span>Batch Create</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar (Hidden by default) -->
    <div id="tableFilterBar" class="services-filter-bar" style="display: none; padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-primary);">
                <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Schedules
            </h3>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-sm" onclick="applyTableFilters()" style="padding: 0.5rem 1rem; background: var(--primary-blue); border: 1px solid var(--primary-blue); border-radius: var(--radius-md); color: white; cursor: pointer; font-size: 0.875rem;">
                    <i class="fas fa-check"></i>
                    <span>Apply Filters</span>
                </button>
                <button type="button" class="btn btn-sm" onclick="resetTableFilters()" style="padding: 0.5rem 1rem; background: var(--bg-light); border: 1px solid var(--border-light); border-radius: var(--radius-md); color: var(--text-secondary); cursor: pointer; font-size: 0.875rem;">
                    <i class="fas fa-redo"></i>
                    <span>Reset Filters</span>
                </button>
            </div>
        </div>
        <div class="filter-controls-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-user-md" style="margin-right: 0.25rem;"></i>Doctor Name
                </label>
                <input type="text" id="filterDoctor" class="filter-input" placeholder="Search doctor..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-graduation-cap" style="margin-right: 0.25rem;"></i>Specialization
                </label>
                <input type="text" id="filterSpecialization" class="filter-input" placeholder="Search specialization..." style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
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
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>Start Time
                </label>
                <input type="time" id="filterStartTime" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
            <div class="filter-control">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>End Time
                </label>
                <input type="time" id="filterEndTime" class="filter-input" style="width: 100%; padding: 0.625rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); font-size: 0.875rem;">
            </div>
        </div>
    </div>
    <?php if (empty($schedules)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
            <div class="empty-state-text">No schedules found.</div>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <?php
                        $current_sort = $_GET['sort'] ?? 'schedule_date';
                        $current_order = $_GET['order'] ?? 'DESC';
                        ?>
                        <th class="sortable <?= $current_sort === 'schedule_date' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('schedule_date')">
                            Date
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th class="sortable <?= $current_sort === 'start_time' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('start_time')">
                            Start Time
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th class="sortable <?= $current_sort === 'end_time' ? 'sort-' . strtolower($current_order) : '' ?>" 
                            onclick="sortTable('end_time')">
                            End Time
                            <span class="sort-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <i class="fas fa-arrow-down"></i>
                            </span>
                        </th>
                        <th>Date Created</th>
                        <th>Date Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($schedules as $sched): ?>
                        <tr class="table-row" 
                            data-doctor="<?= htmlspecialchars(strtolower(($sched['doc_first_name'] ?? '') . ' ' . ($sched['doc_last_name'] ?? ''))) ?>"
                            data-specialization="<?= htmlspecialchars(strtolower($sched['spec_name'] ?? '')) ?>"
                            data-date="<?= !empty($sched['created_at']) ? date('Y-m-d', strtotime($sched['created_at'])) : '' ?>"
                            data-start-time="<?= htmlspecialchars($sched['start_time'] ?? '') ?>"
                            data-end-time="<?= htmlspecialchars($sched['end_time'] ?? '') ?>"
>
                            <td><strong><?= $sched['schedule_date'] ? date('d M Y', strtotime($sched['schedule_date'])) : 'N/A' ?></strong></td>
                            <td>Dr. <?= htmlspecialchars($sched['doc_first_name'] . ' ' . $sched['doc_last_name']) ?></td>
                            <td><?= htmlspecialchars($sched['spec_name'] ?? 'N/A') ?></td>
                            <td><?= formatTimeToAMPM($sched['start_time']) ?></td>
                            <td><?= formatTimeToAMPM($sched['end_time']) ?></td>
                            <td style="color: var(--text-secondary);"><?= $sched['created_at'] ? date('d M Y', strtotime($sched['created_at'])) : 'N/A' ?></td>
                            <td style="color: var(--text-secondary);"><?= $sched['updated_at'] ? date('d M Y', strtotime($sched['updated_at'])) : 'N/A' ?></td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <button class="btn btn-sm edit-schedule-btn" 
                                            data-schedule="<?= base64_encode(json_encode($sched)) ?>" 
                                            title="Edit"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--primary-blue); cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm view-schedule-btn" 
                                            data-schedule="<?= base64_encode(json_encode($sched)) ?>" 
                                            title="View"
                                            style="padding: 0.5rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return handleDelete(event, 'Are you sure you want to delete this schedule?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $sched['schedule_id'] ?>">
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
        <div id="paginationContainer" class="pagination">
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
    <?php endif; ?>
</div>

<!-- Edit Schedule Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Schedule</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-grid">
                <div class="form-group">
                    <label>Date: <span style="color: var(--status-error);">*</span></label>
                    <input type="date" name="schedule_date" id="edit_schedule_date" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Start Time: <span style="color: var(--status-error);">*</span></label>
                    <input type="time" name="start_time" id="edit_start_time" required class="form-control">
                </div>
                <div class="form-group">
                    <label>End Time: <span style="color: var(--status-error);">*</span></label>
                    <input type="time" name="end_time" id="edit_end_time" required class="form-control">
                </div>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    <span>Update Schedule</span>
                </button>
                <button type="button" onclick="closeEditModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Schedule Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add New Schedule</h2>
            <button type="button" class="modal-close" onclick="closeAddScheduleModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            <div class="form-grid">
                <div class="form-group">
                    <label>Doctor: <span style="color: var(--status-error);">*</span></label>
                    <select name="doc_id" required class="form-control">
                        <option value="">Select Doctor</option>
                        <?php foreach ($doctors as $doc): ?>
                            <option value="<?= $doc['doc_id'] ?>"><?= htmlspecialchars($doc['doctor_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Schedule Date: <span style="color: var(--status-error);">*</span></label>
                    <input type="date" name="schedule_date" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Start Time: <span style="color: var(--status-error);">*</span></label>
                    <input type="time" name="start_time" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>End Time: <span style="color: var(--status-error);">*</span></label>
                    <input type="time" name="end_time" required class="form-control">
                </div>
                
                <div class="form-group">
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; cursor: pointer; margin-top: 2rem;">
                    </label>
                </div>
            </div>
            
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    <span>Add Schedule</span>
                </button>
                <button type="button" onclick="closeAddScheduleModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Batch Create Schedule Modal -->
<div id="batchCreateModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2 class="modal-title">Batch Create Schedules</h2>
            <button type="button" class="modal-close" onclick="closeBatchCreateModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="batch_create">
            <div class="form-grid">
                <div class="form-group">
                    <label>Doctor: <span style="color: var(--status-error);">*</span></label>
                    <select name="doc_id" id="batch_doc_id" required class="form-control">
                        <option value="">Select Doctor</option>
                        <?php foreach ($doctors as $doc): ?>
                            <option value="<?= $doc['doc_id'] ?>"><?= htmlspecialchars($doc['doctor_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Start Date: <span style="color: var(--status-error);">*</span></label>
                    <input type="date" name="start_date" id="batch_start_date" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>End Date: <span style="color: var(--status-error);">*</span></label>
                    <input type="date" name="end_date" id="batch_end_date" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Start Time: <span style="color: var(--status-error);">*</span></label>
                    <input type="time" name="start_time" id="batch_start_time" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>End Time: <span style="color: var(--status-error);">*</span></label>
                    <input type="time" name="end_time" id="batch_end_time" required class="form-control">
                </div>
                
                <div class="form-group">
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Days of Week: <span style="color: var(--status-error);">*</span></label>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="monday" style="margin-right: 0.5rem; width: auto;">
                        <span>Monday</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="tuesday" style="margin-right: 0.5rem; width: auto;">
                        <span>Tuesday</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="wednesday" style="margin-right: 0.5rem; width: auto;">
                        <span>Wednesday</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="thursday" style="margin-right: 0.5rem; width: auto;">
                        <span>Thursday</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="friday" style="margin-right: 0.5rem; width: auto;">
                        <span>Friday</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="saturday" style="margin-right: 0.5rem; width: auto;">
                        <span>Saturday</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                        <input type="checkbox" name="days_of_week[]" value="sunday" style="margin-right: 0.5rem; width: auto;">
                        <span>Sunday</span>
                    </label>
                </div>
            </div>
            
            
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Create Schedules</span>
                </button>
                <button type="button" onclick="closeBatchCreateModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Schedule Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2 class="modal-title">Schedule Details</h2>
            <button type="button" class="modal-close" onclick="closeViewModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="viewContent"></div>
        <div class="action-buttons" style="margin-top: 1.5rem;">
            <button type="button" onclick="closeViewModal()" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                <span>Close</span>
            </button>
        </div>
    </div>
</div>

<script>
// Add Schedule Modal Functions
function openAddScheduleModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddScheduleModal() {
    const modal = document.getElementById('addModal');
    modal.classList.remove('active');
    modal.querySelector('form').reset();
}

// Batch Create Modal Functions
function openBatchCreateModal() {
    document.getElementById('batchCreateModal').classList.add('active');
}

function closeBatchCreateModal() {
    const modal = document.getElementById('batchCreateModal');
    modal.classList.remove('active');
    modal.querySelector('form').reset();
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
    url.searchParams.delete('page'); // Reset to page 1 when sorting
    
    window.location.href = url.toString();
}

// Filtering Functions
function applyTableFilters() {
    // Ensure we're in all_results mode for filtering to work properly
    const url = new URL(window.location.href);
    const isAllResultsMode = url.searchParams.get('all_results') === '1';
    
    if (!isAllResultsMode) {
        // Store filter values before reloading
        const filterValues = {
            filterDoctor: document.getElementById('filterDoctor')?.value || '',
            filterSpecialization: document.getElementById('filterSpecialization')?.value || '',
            filterDateMonth: document.getElementById('filterDateMonth')?.value || '',
            filterDateDay: document.getElementById('filterDateDay')?.value || '',
            filterDateYear: document.getElementById('filterDateYear')?.value || '',
            filterStartTime: document.getElementById('filterStartTime')?.value || '',
            filterEndTime: document.getElementById('filterEndTime')?.value || '',
        };
        sessionStorage.setItem('pendingFilters', JSON.stringify(filterValues));
        // Load all results first, then apply filters after page reloads
        loadAllResults();
        return;
    }
    
    // Apply filters if already in all_results mode
    filterTable();
}

function filterTable() {
    const tbody = document.getElementById('tableBody');
    if (!tbody) return;
    
    const rows = tbody.querySelectorAll('.table-row');
    const filterDoctor = document.getElementById('filterDoctor')?.value.toLowerCase().trim() || '';
    const filterSpecialization = document.getElementById('filterSpecialization')?.value.toLowerCase().trim() || '';
    const dateMonthFilter = document.getElementById('filterDateMonth')?.value || '';
    const dateDayFilter = document.getElementById('filterDateDay')?.value || '';
    const dateYearFilter = document.getElementById('filterDateYear')?.value || '';
    const filterStartTime = document.getElementById('filterStartTime')?.value || '';
    const filterEndTime = document.getElementById('filterEndTime')?.value || '';
    let visibleCount = 0;
    let hasActiveFilters = filterDoctor || filterSpecialization || dateMonthFilter || dateDayFilter || dateYearFilter || filterStartTime || filterEndTime;
    
    rows.forEach(row => {
        const doctor = row.getAttribute('data-doctor') || '';
        const specialization = row.getAttribute('data-specialization') || '';
        const dateStr = row.getAttribute('data-date') || '';
        const startTime = row.getAttribute('data-start-time') || '';
        const endTime = row.getAttribute('data-end-time') || '';
        const matchesDoctor = !filterDoctor || doctor.includes(filterDoctor);
        const matchesSpecialization = !filterSpecialization || specialization.includes(filterSpecialization);
        
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
        
        const matchesStartTime = !filterStartTime || startTime === filterStartTime;
        const matchesEndTime = !filterEndTime || endTime === filterEndTime;
        
        if (matchesDoctor && matchesSpecialization && matchesDate && matchesStartTime && matchesEndTime) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide pagination and filter message
    const paginationContainer = document.getElementById('paginationContainer');
    let filterActiveMessage = document.getElementById('filterActiveMessage');
    
    if (hasActiveFilters) {
        if (paginationContainer) paginationContainer.style.display = 'none';
        
        if (!filterActiveMessage) {
            filterActiveMessage = document.createElement('div');
            filterActiveMessage.id = 'filterActiveMessage';
            filterActiveMessage.style.cssText = 'padding: 1.5rem; text-align: center; color: var(--text-secondary); font-size: 0.875rem; border-top: 1px solid var(--border-light);';
            tbody.parentElement.parentElement.appendChild(filterActiveMessage);
        }
        
        if (visibleCount === 0) {
            filterActiveMessage.innerHTML = '<i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>No schedules match the applied filters.';
        } else {
            filterActiveMessage.innerHTML = `<i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Showing ${visibleCount} schedule(s) matching your filters. <a href="javascript:void(0)" onclick="resetTableFilters()" style="color: var(--primary-blue); text-decoration: underline; margin-left: 0.5rem;">Clear filters</a>`;
        }
        filterActiveMessage.style.display = 'block';
    } else {
        if (paginationContainer) paginationContainer.style.display = '';
        if (filterActiveMessage) filterActiveMessage.style.display = 'none';
    }
}

function resetTableFilters() {
    document.getElementById('filterDoctor').value = '';
    document.getElementById('filterSpecialization').value = '';
    document.getElementById('filterDateMonth').value = '';
    document.getElementById('filterDateDay').value = '';
    document.getElementById('filterDateYear').value = '';
    document.getElementById('filterStartTime').value = '';
    document.getElementById('filterEndTime').value = '';
    
    filterTable();
    resetToPaginatedView();
}

function toggleTableFilters() {
    const filterBar = document.getElementById('tableFilterBar');
    const toggleBtn = document.getElementById('toggleFilterBtn');
    
    if (filterBar.style.display === 'none' || !filterBar.style.display) {
        filterBar.style.display = 'block';
        toggleBtn.classList.add('active');
        toggleBtn.style.background = 'var(--primary-blue)';
        toggleBtn.style.color = 'white';
        loadAllResults();
    } else {
        filterBar.style.display = 'none';
        toggleBtn.classList.remove('active');
        toggleBtn.style.background = 'var(--bg-light)';
        toggleBtn.style.color = 'var(--text-secondary)';
        resetTableFilters();
        resetToPaginatedView();
    }
}

function loadAllResults() {
    const url = new URL(window.location.href);
    url.searchParams.set('all_results', '1');
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

function resetToPaginatedView() {
    const url = new URL(window.location.href);
    url.searchParams.delete('all_results');
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

// Edit and View Schedule Functions
function editSchedule(sched) {
    document.getElementById('edit_id').value = sched.schedule_id;
    document.getElementById('edit_schedule_date').value = sched.schedule_date;
    document.getElementById('edit_start_time').value = sched.start_time;
    document.getElementById('edit_end_time').value = sched.end_time;
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

// Helper function to format time to 12-hour AM/PM format
function formatTimeToAMPM(time) {
    if (!time || time === 'N/A') return 'N/A';
    try {
        const timeParts = time.split(':');
        if (timeParts.length >= 2) {
            let hour = parseInt(timeParts[0], 10);
            const minute = timeParts[1];
            const amPm = hour >= 12 ? 'PM' : 'AM';
            if (hour === 0) {
                hour = 12; // 00:00 = 12:00 AM
            } else if (hour === 12) {
                hour = 12; // 12:00 = 12:00 PM
            } else if (hour > 12) {
                hour = hour - 12; // 13:00 = 1:00 PM, etc.
            }
            return `${hour}:${minute} ${amPm}`;
        }
        return time;
    } catch (e) {
        return time;
    }
}

function viewSchedule(sched) {
    const doctorName = sched.doc_first_name && sched.doc_last_name 
        ? `Dr. ${sched.doc_first_name} ${sched.doc_last_name}` 
        : 'N/A';
    const specName = sched.spec_name || 'N/A';
    const scheduleDate = sched.schedule_date ? new Date(sched.schedule_date).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    }) : 'N/A';
    const startTime = formatTimeToAMPM(sched.start_time || '');
    const endTime = formatTimeToAMPM(sched.end_time || '');
    const created = sched.created_at ? new Date(sched.created_at).toLocaleString('en-US') : 'N/A';
    const updated = sched.updated_at ? new Date(sched.updated_at).toLocaleString('en-US') : 'N/A';
    
    const content = `
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3 class="card-title" style="color: var(--primary-blue);">Schedule Information</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Doctor:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--text-primary);">${doctorName}</p>
                    </div>
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Specialization:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--text-primary);">${specName}</p>
                    </div>
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Schedule Date:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--text-primary);">${scheduleDate}</p>
                    </div>
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Time:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--text-primary);">${startTime} - ${endTime}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="color: var(--primary-blue);">Timestamps</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Created At:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--text-primary);">${created}</p>
                    </div>
                    <div>
                        <strong style="color: var(--text-secondary); font-size: 0.875rem;">Updated At:</strong>
                        <p style="margin: 0.5rem 0 0 0; color: var(--text-primary);">${updated}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.getElementById('viewContent').innerHTML = content;
    document.getElementById('viewModal').classList.add('active');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.remove('active');
}

// Initialize filter event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Filters only apply when "Apply Filters" button is clicked
    
    // Add event listeners for edit buttons
    document.querySelectorAll('.edit-schedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-schedule');
                const decodedJson = atob(encodedData);
                const scheduleData = JSON.parse(decodedJson);
                editSchedule(scheduleData);
            } catch (e) {
                console.error('Error parsing schedule data:', e);
                alert('Error loading schedule data. Please check the console for details.');
            }
        });
    });
    
    // Add event listeners for view buttons
    document.querySelectorAll('.view-schedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const encodedData = this.getAttribute('data-schedule');
                const decodedJson = atob(encodedData);
                const scheduleData = JSON.parse(decodedJson);
                viewSchedule(scheduleData);
            } catch (e) {
                console.error('Error parsing schedule data:', e);
                alert('Error loading schedule data. Please check the console for details.');
            }
        });
    });
    
    // Close modals on outside click and Escape key
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                if (this.id === 'addModal') {
                    closeAddScheduleModal();
                } else if (this.id === 'batchCreateModal') {
                    closeBatchCreateModal();
                } else {
                    this.classList.remove('active');
                }
            }
        });
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                if (modal.id === 'addModal') {
                    closeAddScheduleModal();
                } else if (modal.id === 'batchCreateModal') {
                    closeBatchCreateModal();
                } else {
                    modal.classList.remove('active');
                }
            });
        }
    });
    
    // Check if filters are active on page load
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('all_results') === '1') {
        const filterBar = document.getElementById('tableFilterBar');
        const toggleBtn = document.getElementById('toggleFilterBtn');
        if (filterBar) {
            filterBar.style.display = 'block';
            toggleBtn.classList.add('active');
            toggleBtn.style.background = 'var(--primary-blue)';
            toggleBtn.style.color = 'white';
        }
        
        // Restore filter values from sessionStorage and apply them
        const pendingFilters = sessionStorage.getItem('pendingFilters');
        if (pendingFilters) {
            try {
                const filterValues = JSON.parse(pendingFilters);
                if (filterValues.filterDoctor && document.getElementById('filterDoctor')) {
                    document.getElementById('filterDoctor').value = filterValues.filterDoctor;
                }
                if (filterValues.filterSpecialization && document.getElementById('filterSpecialization')) {
                    document.getElementById('filterSpecialization').value = filterValues.filterSpecialization;
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
                if (filterValues.filterStartTime && document.getElementById('filterStartTime')) {
                    document.getElementById('filterStartTime').value = filterValues.filterStartTime;
                }
                if (filterValues.filterEndTime && document.getElementById('filterEndTime')) {
                    document.getElementById('filterEndTime').value = filterValues.filterEndTime;
                }
                // Apply the filters
                filterTable();
                // Clear the stored filters
                sessionStorage.removeItem('pendingFilters');
            } catch (e) {
                console.error('Error restoring filters:', e);
                sessionStorage.removeItem('pendingFilters');
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
