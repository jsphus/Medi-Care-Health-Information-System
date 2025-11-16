<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
    .medical-records-page {
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .page-header {
        margin-bottom: 2rem;
    }
    
    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .page-subtitle {
        color: #6b7280;
        font-size: 1rem;
    }
    
    .search-bar {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        background: white;
        padding: 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .search-input-wrapper {
        flex: 1;
        position: relative;
    }
    
    .search-input-wrapper i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
    }
    
    .search-input-wrapper input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }
    
    .search-input-wrapper input:focus {
        outline: none;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .records-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .record-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }
    
    .record-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        border-color: var(--primary-blue);
    }
    
    .record-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .record-date-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .record-doctor-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .doctor-avatar-small {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .doctor-details h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.25rem 0;
    }
    
    .doctor-details p {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }
    
    .record-content {
        margin-bottom: 1rem;
    }
    
    .record-section {
        margin-bottom: 1rem;
    }
    
    .record-section-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .record-section-content {
        font-size: 0.9375rem;
        color: #4b5563;
        line-height: 1.6;
        background: #f9fafb;
        padding: 1rem;
        border-radius: 0.5rem;
    }
    
    .record-actions {
        display: flex;
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-primary {
        background: var(--primary-blue);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--primary-blue-dark);
    }
    
    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }
    
    .btn-secondary:hover {
        background: #e5e7eb;
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .empty-state-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    .empty-state-text {
        font-size: 1.125rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    
    .empty-state-subtext {
        font-size: 0.875rem;
        color: #9ca3af;
    }
</style>

<div class="medical-records-page">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title" style="margin: 0;">My Medical Records</h1>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>
    
    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: #8b5cf6;"></div>
                <span style="font-size: 0.875rem; color: var(--text-secondary);">Total Records</span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['total'] ?? 0 ?></div>
        </div>
        <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></div>
                <span style="font-size: 0.875rem; color: var(--text-secondary);">Records This Month</span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['this_month'] ?? 0 ?></div>
        </div>
        <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></div>
                <span style="font-size: 0.875rem; color: var(--text-secondary);">Pending Follow-up</span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);"><?= $stats['pending_followup'] ?? 0 ?></div>
        </div>
    </div>
    
    <?php if (empty($records)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-file-medical"></i></div>
            <div class="empty-state-text">No medical records found</div>
            <div class="empty-state-subtext">
                <?php if ($search_query): ?>
                    Try adjusting your search terms
                <?php else: ?>
                    Your medical records will appear here after your appointments
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="records-list">
            <?php foreach ($records as $record): ?>
                <?php
                $docInitial = strtoupper(substr($record['doc_first_name'] ?? 'D', 0, 1));
                $docName = 'Dr. ' . htmlspecialchars(($record['doc_first_name'] ?? '') . ' ' . ($record['doc_last_name'] ?? ''));
                $specName = htmlspecialchars($record['spec_name'] ?? 'General Practice');
                ?>
                <div class="record-card">
                    <div class="record-header">
                        <div>
                            <div class="record-date-info">
                                <i class="fas fa-calendar"></i>
                                <span><?= date('F j, Y', strtotime($record['record_date'])) ?></span>
                                <?php if ($record['appointment_date']): ?>
                                    <span>•</span>
                                    <span>Appointment: <?= date('M j, Y', strtotime($record['appointment_date'])) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="record-doctor-info">
                            <div class="doctor-avatar-small" style="overflow: hidden;">
                                <?php if (!empty($record['doctor_profile_picture'])): ?>
                                    <img src="<?= htmlspecialchars($record['doctor_profile_picture']) ?>" alt="Doctor" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <?= $docInitial ?>
                                <?php endif; ?>
                            </div>
                            <div class="doctor-details">
                                <h3><?= $docName ?></h3>
                                <p><?= $specName ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="record-content">
                        <?php if ($record['diagnosis']): ?>
                        <div class="record-section">
                            <div class="record-section-title">Diagnosis</div>
                            <div class="record-section-content"><?= nl2br(htmlspecialchars($record['diagnosis'])) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($record['treatment']): ?>
                        <div class="record-section">
                            <div class="record-section-title">Treatment</div>
                            <div class="record-section-content"><?= nl2br(htmlspecialchars($record['treatment'])) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($record['prescription']): ?>
                        <div class="record-section">
                            <div class="record-section-title">Prescription</div>
                            <div class="record-section-content"><?= nl2br(htmlspecialchars($record['prescription'])) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($record['notes']): ?>
                        <div class="record-section">
                            <div class="record-section-title">Notes</div>
                            <div class="record-section-content"><?= nl2br(htmlspecialchars($record['notes'])) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($record['follow_up_date']): ?>
                        <div class="record-section">
                            <div class="record-section-title">Follow-up Date</div>
                            <div class="record-section-content">
                                <i class="fas fa-calendar-check"></i> <?= date('F j, Y', strtotime($record['follow_up_date'])) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="record-actions">
                        <button class="btn-action btn-secondary" onclick="printRecord(<?= $record['record_id'] ?>)">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button class="btn-action btn-secondary" onclick="downloadRecord(<?= $record['record_id'] ?>)">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function printRecord(recordId) {
    // TODO: Implement print functionality
    window.print();
}

function downloadRecord(recordId) {
    // TODO: Implement download functionality
    alert('Download functionality will be implemented here for record ID: ' + recordId);
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

