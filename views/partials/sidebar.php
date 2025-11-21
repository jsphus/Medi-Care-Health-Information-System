<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Determine user role
require_once __DIR__ . '/../../classes/Auth.php';
$auth = new Auth();
$role = $auth->getRole() ?? 'guest';

// Get user information
$userName = 'User';
$userInitial = 'U';
$userTitle = 'User';
$profilePictureUrl = null;

// Try to get full name from session first
if (isset($_SESSION['pat_first_name']) && isset($_SESSION['pat_last_name'])) {
    $userName = ($_SESSION['pat_first_name'] ?? '') . ' ' . ($_SESSION['pat_last_name'] ?? '');
    $userInitial = strtoupper(substr($_SESSION['pat_first_name'] ?? 'P', 0, 1));
    $userTitle = 'Patient';
} elseif (isset($_SESSION['doc_first_name']) && isset($_SESSION['doc_last_name'])) {
    $userName = ($_SESSION['doc_first_name'] ?? '') . ' ' . ($_SESSION['doc_last_name'] ?? '');
    $userInitial = strtoupper(substr($_SESSION['doc_first_name'] ?? 'D', 0, 1));
    $userTitle = 'Doctor';
} elseif (isset($_SESSION['staff_first_name']) && isset($_SESSION['staff_last_name'])) {
    $userName = ($_SESSION['staff_first_name'] ?? '') . ' ' . ($_SESSION['staff_last_name'] ?? '');
    $userInitial = strtoupper(substr($_SESSION['staff_first_name'] ?? 'S', 0, 1));
    $userTitle = 'Staff';
}

// If name not found in session, fetch from database
if ($userName === 'User' && isset($_SESSION['user_id'])) {
    try {
        $db = Database::getInstance();
        
        // Fetch profile picture
        $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && !empty($user['profile_picture_url'])) {
            $profilePictureUrl = $user['profile_picture_url'];
        }
        
        // Fetch user name based on role
        if (isset($_SESSION['pat_id']) && $_SESSION['pat_id'] !== null) {
            $stmt = $db->prepare("SELECT pat_first_name, pat_last_name FROM patients WHERE pat_id = :pat_id");
            $stmt->execute(['pat_id' => $_SESSION['pat_id']]);
            $patient = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($patient) {
                $userName = trim(($patient['pat_first_name'] ?? '') . ' ' . ($patient['pat_last_name'] ?? ''));
                if (!empty($userName)) {
                    $userInitial = strtoupper(substr($patient['pat_first_name'] ?? 'P', 0, 1));
                    $userTitle = 'Patient';
                }
            }
        } elseif (isset($_SESSION['doc_id']) && $_SESSION['doc_id'] !== null) {
            $stmt = $db->prepare("SELECT doc_first_name, doc_last_name FROM doctors WHERE doc_id = :doc_id");
            $stmt->execute(['doc_id' => $_SESSION['doc_id']]);
            $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($doctor) {
                $userName = trim(($doctor['doc_first_name'] ?? '') . ' ' . ($doctor['doc_last_name'] ?? ''));
                if (!empty($userName)) {
                    $userInitial = strtoupper(substr($doctor['doc_first_name'] ?? 'D', 0, 1));
                    $userTitle = 'Doctor';
                }
            }
        } elseif (isset($_SESSION['staff_id']) && $_SESSION['staff_id'] !== null) {
            $stmt = $db->prepare("SELECT staff_first_name, staff_last_name FROM staff WHERE staff_id = :staff_id");
            $stmt->execute(['staff_id' => $_SESSION['staff_id']]);
            $staff = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($staff) {
                $userName = trim(($staff['staff_first_name'] ?? '') . ' ' . ($staff['staff_last_name'] ?? ''));
                if (!empty($userName)) {
                    $userInitial = strtoupper(substr($staff['staff_first_name'] ?? 'S', 0, 1));
                    $userTitle = 'Staff';
                }
            }
        } elseif (isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] === true) {
            $userTitle = 'Super Admin';
            // For superadmin, use email if name not available
            if (isset($_SESSION['user_email'])) {
                $userName = $_SESSION['user_email'];
                $userInitial = strtoupper(substr($_SESSION['user_email'], 0, 1));
            }
        }
        
        // Fallback to email if name is still 'User'
        if ($userName === 'User' && isset($_SESSION['user_email'])) {
            $userName = $_SESSION['user_email'];
            $userInitial = strtoupper(substr($_SESSION['user_email'], 0, 1));
        }
    } catch (PDOException $e) {
        // Keep defaults if database query fails
        if (isset($_SESSION['user_email'])) {
            $userName = $_SESSION['user_email'];
            $userInitial = strtoupper(substr($_SESSION['user_email'], 0, 1));
        }
    }
} elseif (isset($_SESSION['user_id'])) {
    // Fetch profile picture only if name was already set from session
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && !empty($user['profile_picture_url'])) {
            $profilePictureUrl = $user['profile_picture_url'];
        }
    } catch (PDOException $e) {
        // Keep profilePictureUrl as null
    }
}

// Icon mapping function
function getIcon($emoji) {
    $iconMap = [
        '📊' => 'fas fa-chart-line',
        '👥' => 'fas fa-users',
        '🏥' => 'fas fa-hospital',
        '👨‍⚕️' => 'fas fa-user-md',
        '👔' => 'fas fa-user-tie',
        '🎓' => 'fas fa-graduation-cap',
        '🗓️' => 'fas fa-calendar-alt',
        '📋' => 'fas fa-clipboard-list',
        '🔬' => 'fas fa-flask',
        '📅' => 'fas fa-calendar-check',
        '📄' => 'fas fa-file-medical',
        '💳' => 'fas fa-credit-card',
        '💰' => 'fas fa-coins',
        '💵' => 'fas fa-money-bill-wave',
        '⏰' => 'fas fa-clock',
        '👤' => 'fas fa-user',
        '📜' => 'fas fa-scroll',
        '➕' => 'fas fa-plus-circle',
        '🏠' => 'fas fa-home',
        '📖' => 'fas fa-book',
        '🔔' => 'fas fa-bell',
    ];
    return $iconMap[$emoji] ?? 'fas fa-circle';
}

// Define menu items for each role
$menus = [
    'superadmin' => [
        ['icon' => '📊', 'label' => 'Dashboard', 'url' => '/superadmin/dashboard'],
        ['icon' => '👥', 'label' => 'Users', 'url' => '/superadmin/users'],
        ['icon' => '🏥', 'label' => 'Patients', 'url' => '/superadmin/patients'],
        ['icon' => '👨‍⚕️', 'label' => 'Doctors', 'url' => '/superadmin/doctors'],
        ['icon' => '👔', 'label' => 'Staff', 'url' => '/superadmin/staff'],
        ['icon' => '🎓', 'label' => 'Specializations', 'url' => '/superadmin/specializations'],
        ['icon' => '🗓️', 'label' => 'Schedules', 'url' => '/superadmin/schedules'],
        ['icon' => '📋', 'label' => 'Statuses', 'url' => '/superadmin/statuses'],
        ['icon' => '🔬', 'label' => 'Services', 'url' => '/superadmin/services'],
        ['icon' => '📅', 'label' => 'Appointments', 'url' => '/superadmin/appointments'],
        ['icon' => '📄', 'label' => 'Medical Records', 'url' => '/superadmin/medical-records'],
        ['icon' => '💳', 'label' => 'Payment Methods', 'url' => '/superadmin/payment-methods'],
        ['icon' => '💰', 'label' => 'Payment Statuses', 'url' => '/superadmin/payment-statuses'],
        ['icon' => '💵', 'label' => 'Payments', 'url' => '/superadmin/payments'],
    ],
    'staff' => [
        ['icon' => '📊', 'label' => 'Dashboard', 'url' => '/staff/dashboard'],
        ['icon' => '🔬', 'label' => 'Services', 'url' => '/staff/services'],
        ['icon' => '👔', 'label' => 'Staff', 'url' => '/staff/staff'],
        ['icon' => '🎓', 'label' => 'Specializations', 'url' => '/staff/specializations'],
        ['icon' => '📋', 'label' => 'Statuses', 'url' => '/staff/statuses'],
        ['icon' => '💳', 'label' => 'Payment Methods', 'url' => '/staff/payment-methods'],
        ['icon' => '💰', 'label' => 'Payment Statuses', 'url' => '/staff/payment-statuses'],
        ['icon' => '💵', 'label' => 'Payments', 'url' => '/staff/payments'],
        ['icon' => '📄', 'label' => 'Medical Records', 'url' => '/staff/medical-records'],
    ],
    'doctor' => [
        ['icon' => '📊', 'label' => 'Dashboard', 'url' => '/doctor/dashboard'],
        ['icon' => '📅', 'label' => 'Appointments', 'url' => '/doctor/appointments'],
        ['icon' => '⏰', 'label' => 'Schedules', 'url' => '/doctor/schedules'],
        ['icon' => '👨‍⚕️', 'label' => 'Doctors', 'url' => '/doctor/doctors'],
        ['icon' => '📄', 'label' => 'Medical Records', 'url' => '/doctor/medical-records'],
    ],
    'patient' => [
        ['icon' => '🏠', 'label' => 'Dashboard', 'url' => '/patient/dashboard'],
        ['icon' => '📅', 'label' => 'My Appointments', 'url' => '/patient/appointments'],
        ['icon' => '📖', 'label' => 'Book', 'url' => '/patient/book'],
        ['icon' => '📄', 'label' => 'Medical Records', 'url' => '/patient/medical-records'],
        ['icon' => '💳', 'label' => 'Payments', 'url' => '/patient/payments'],
        ['icon' => '🔔', 'label' => 'Notifications', 'url' => '/patient/notifications'],
    ],
];

$currentMenu = $menus[$role] ?? [];
$currentPath = $_SERVER['REQUEST_URI'];
?>

<div class="sidebar-modern" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="logo-icon-sidebar">
                <i class="fas fa-heartbeat"></i>
            </div>
            <span class="logo-text">Medi-Care</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <!-- Search Bar -->
    <div class="sidebar-search">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search..." class="search-input-sidebar" id="sidebarSearch">
    </div>
    
    <!-- Menu Items -->
    <div class="sidebar-menu">
        <?php foreach ($currentMenu as $item): ?>
            <?php 
            $isActive = strpos($currentPath, $item['url']) !== false;
            ?>
            <a href="<?= $item['url'] ?>" class="menu-item-modern <?= $isActive ? 'active' : '' ?>" 
               data-tooltip="<?= htmlspecialchars($item['label']) ?>">
                <i class="<?= getIcon($item['icon']) ?>"></i>
                <span class="menu-label"><?= htmlspecialchars($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    
    <!-- View As Section (Super Admin Only) -->
    <?php
    $isOriginalSuperAdmin = false;
    $isViewingAs = false;
    $viewingAsRole = null;
    
    if ($auth->isViewingAs()) {
        $isOriginalSuperAdmin = $_SESSION['original_is_superadmin'] ?? false;
        $isViewingAs = true;
        $viewingAsRole = $_SESSION['view_as_role'] ?? null;
    } else {
        $isOriginalSuperAdmin = $auth->isSuperAdmin();
    }
    
    if ($isOriginalSuperAdmin):
    ?>
    <div class="view-as-section" style="padding: 1rem; border-top: 1px solid #e5e7eb; margin-top: auto;">
        <?php if ($isViewingAs): ?>
            <div style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: #92400e;">
                    <i class="fas fa-eye" style="font-size: 0.875rem;"></i>
                    <span style="font-weight: 600;">Viewing as <?= ucfirst($viewingAsRole) ?></span>
                </div>
            </div>
            <a href="/view-as?action=exit" class="view-as-btn" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: #ef4444; color: white; border: none; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 500; width: 100%; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                <i class="fas fa-times"></i>
                <span>Exit View As</span>
            </a>
        <?php else: ?>
            <div style="margin-bottom: 0.5rem; font-size: 0.75rem; color: #6b7280; font-weight: 500;">
                <i class="fas fa-user-secret"></i> View As
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <a href="/view-as?action=doctor" class="view-as-btn" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: #3b82f6; color: white; border: none; border-radius: 0.375rem; text-decoration: none; font-size: 0.8125rem; transition: background 0.2s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                    <i class="fas fa-user-md"></i>
                    <span>View as Doctor</span>
                </a>
                <a href="/view-as?action=patient" class="view-as-btn" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: #10b981; color: white; border: none; border-radius: 0.375rem; text-decoration: none; font-size: 0.8125rem; transition: background 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                    <i class="fas fa-user"></i>
                    <span>View as Patient</span>
                </a>
                <a href="/view-as?action=staff" class="view-as-btn" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: #8b5cf6; color: white; border: none; border-radius: 0.375rem; text-decoration: none; font-size: 0.8125rem; transition: background 0.2s;" onmouseover="this.style.background='#7c3aed'" onmouseout="this.style.background='#8b5cf6'">
                    <i class="fas fa-user-tie"></i>
                    <span>View as Staff</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- User Profile Section -->
    <div class="sidebar-profile-modern">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div class="profile-info" onclick="toggleProfileMenu()">
                <div class="profile-avatar-modern" style="overflow: hidden; position: relative;">
                    <?php if (!empty($profilePictureUrl)): ?>
                        <img src="<?= htmlspecialchars($profilePictureUrl) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <?= $userInitial ?>
                    <?php endif; ?>
                </div>
                <div class="profile-details">
                    <div class="profile-name-modern"><?= htmlspecialchars($userName) ?></div>
                    <div class="profile-title"><?= htmlspecialchars($userTitle) ?></div>
                </div>
            </div>
            <button class="profile-logout" onclick="toggleProfileMenu()" aria-label="Profile menu">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
        
        <!-- Profile Dropdown -->
        <div class="profile-dropdown" id="profileDropdown">
            <a href="/<?= $role ?>/account" class="profile-dropdown-item">
                <i class="fas fa-user"></i>
                <span>Account</span>
            </a>
            <a href="/<?= $role ?>/settings" class="profile-dropdown-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="/<?= $role ?>/privacy" class="profile-dropdown-item">
                <i class="fas fa-shield-alt"></i>
                <span>Privacy</span>
            </a>
            <div class="profile-dropdown-divider"></div>
            <a href="/logout" class="profile-dropdown-item logout-item" id="logoutLink">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</div>

<script>
// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    
    // Check localStorage for sidebar state
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
        if (mainContent) mainContent.classList.add('sidebar-collapsed');
    }
    
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        if (mainContent) mainContent.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
    
    // Tooltip functionality for collapsed sidebar
    const menuItems = document.querySelectorAll('.menu-item-modern');
    menuItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            if (sidebar.classList.contains('collapsed')) {
                const tooltip = document.createElement('div');
                tooltip.className = 'menu-tooltip';
                tooltip.textContent = this.dataset.tooltip;
                document.body.appendChild(tooltip);
                
                const rect = this.getBoundingClientRect();
                tooltip.style.left = rect.right + 10 + 'px';
                tooltip.style.top = rect.top + (rect.height / 2) - (tooltip.offsetHeight / 2) + 'px';
                
                this._tooltip = tooltip;
            }
        });
        
        item.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                this._tooltip.remove();
                this._tooltip = null;
            }
        });
    });
    
    // Search functionality
    document.getElementById('sidebarSearch')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const menuItems = document.querySelectorAll('.menu-item-modern');
        
        menuItems.forEach(item => {
            const label = item.querySelector('.menu-label')?.textContent.toLowerCase() || '';
            if (label.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = searchTerm ? 'none' : 'flex';
            }
        });
    });
    
    // Logout confirmation
    const logoutLink = document.getElementById('logoutLink');
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Use the same confirmation modal as delete user
            if (typeof showConfirm === 'function') {
                showConfirm(
                    'Are you sure you want to logout?',
                    'Confirm Logout',
                    'Yes, Logout',
                    'Cancel',
                    'warning'
                ).then(confirmed => {
                    if (confirmed) {
                        window.location.href = '/logout';
                    }
                });
            } else {
                // Fallback to browser confirm if modal not loaded
                if (confirm('Are you sure you want to logout?')) {
                    window.location.href = '/logout';
                }
            }
        });
    }
});

// Profile menu toggle
function toggleProfileMenu() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.classList.toggle('active');
}

// Close profile dropdown when clicking outside
document.addEventListener('click', function(event) {
    const profileSection = document.querySelector('.sidebar-profile-modern');
    const dropdown = document.getElementById('profileDropdown');
    
    if (profileSection && dropdown && !profileSection.contains(event.target)) {
        dropdown.classList.remove('active');
    }
});
</script>
