    </div> <!-- End content-container -->
<?php if (isset($_SESSION['user_id'])): ?>
</div> <!-- End main-content -->
<?php endif; ?>
</div> <!-- End app-container -->

<script>
// User Menu Dropdown - Moved to sidebar

// Ensure theme is always light
document.addEventListener('DOMContentLoaded', function() {
    const html = document.documentElement;
    html.setAttribute('data-theme', 'light');
    localStorage.setItem('theme', 'light');
});

// Header Button Functions
function openSearch() {
    alert('Search functionality coming soon!');
}

function openNotifications() {
    alert('Notifications panel coming soon!');
}
</script>

<script src="/public/js/confirm-modal.js"></script>

</body>
</html>