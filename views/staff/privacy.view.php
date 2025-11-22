<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <div class="page-header-top">
        <h1 class="page-title">Privacy Settings</h1>
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

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Privacy & Data</h2>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                    <input type="checkbox" name="data_sharing" value="1" style="width: auto;">
                    <span>Allow data sharing for research purposes</span>
                </label>
            </div>
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                    <input type="checkbox" name="profile_visibility" value="1" checked style="width: auto;">
                    <span>Make profile visible to administrators</span>
                </label>
            </div>
            <div class="action-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    <span>Save Privacy Settings</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

