<?php
$role = 'guest';
if (isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] === true) {
    $role = 'superadmin';
} elseif (isset($_SESSION['staff_id']) && $_SESSION['staff_id'] !== null) {
    $role = 'staff';
} elseif (isset($_SESSION['doc_id']) && $_SESSION['doc_id'] !== null) {
    $role = 'doctor';
} elseif (isset($_SESSION['pat_id']) && $_SESSION['pat_id'] !== null) {
    $role = 'patient';
}

$menus = [
    'superadmin' => [
        ['icon' => 'dashboard', 'label' => 'Dashboard', 'url' => '/superadmin/dashboard'],
        ['icon' => 'people', 'label' => 'Users', 'url' => '/superadmin/users'],
        ['icon' => 'local_hospital', 'label' => 'Patients', 'url' => '/superadmin/patients'],
        ['icon' => 'medical_services', 'label' => 'Doctors', 'url' => '/superadmin/doctors'],
        ['icon' => 'badge', 'label' => 'Staff', 'url' => '/superadmin/staff'],
        ['icon' => 'school', 'label' => 'Specializations', 'url' => '/superadmin/specializations'],
        ['icon' => 'event', 'label' => 'Schedules', 'url' => '/superadmin/schedules'],
        ['icon' => 'flag', 'label' => 'Statuses', 'url' => '/superadmin/statuses'],
        ['icon' => 'science', 'label' => 'Services', 'url' => '/superadmin/services'],
        ['icon' => 'calendar_today', 'label' => 'Appointments', 'url' => '/superadmin/appointments'],
        ['icon' => 'folder', 'label' => 'Medical Records', 'url' => '/superadmin/medical-records'],
        ['icon' => 'credit_card', 'label' => 'Payment Methods', 'url' => '/superadmin/payment-methods'],
        ['icon' => 'account_balance', 'label' => 'Payment Statuses', 'url' => '/superadmin/payment-statuses'],
        ['icon' => 'payments', 'label' => 'Payments', 'url' => '/superadmin/payments'],
    ],
    
    'staff' => [
        ['icon' => 'dashboard', 'label' => 'Dashboard', 'url' => '/staff/dashboard'],
        ['icon' => 'badge', 'label' => 'Staff', 'url' => '/staff/staff'],
        ['icon' => 'school', 'label' => 'Specializations', 'url' => '/staff/specializations'],
        ['icon' => 'flag', 'label' => 'Statuses', 'url' => '/staff/statuses'],
        ['icon' => 'science', 'label' => 'Services', 'url' => '/staff/services'],
        ['icon' => 'credit_card', 'label' => 'Payment Methods', 'url' => '/staff/payment-methods'],
        ['icon' => 'account_balance', 'label' => 'Payment Statuses', 'url' => '/staff/payment-statuses'],
        ['icon' => 'payments', 'label' => 'Payments', 'url' => '/staff/payments'],
        ['icon' => 'folder', 'label' => 'Medical Records (View)', 'url' => '/staff/medical-records'],
    ],
    
    'doctor' => [
        ['icon' => 'dashboard', 'label' => 'Dashboard', 'url' => '/doctor/dashboard'],
        [
            'icon' => 'calendar_today', 
            'label' => 'Appointments', 
            'submenu' => [
                ['icon' => 'today', 'label' => 'Today\'s Appointments', 'url' => '/doctor/appointments/today'],
                ['icon' => 'history', 'label' => 'Previous Appointments', 'url' => '/doctor/appointments/previous'],
                ['icon' => 'event_available', 'label' => 'Future Appointments', 'url' => '/doctor/appointments/future'],
            ]
        ],
        [
            'icon' => 'schedule', 
            'label' => 'Schedules', 
            'submenu' => [
                ['icon' => 'person', 'label' => 'My Schedules', 'url' => '/doctor/schedules'],
                ['icon' => 'event', 'label' => 'All Schedules', 'url' => '/doctor/schedules/manage'],
            ]
        ],
        ['icon' => 'medical_services', 'label' => 'Doctors', 'url' => '/doctor/doctors'],
        ['icon' => 'folder', 'label' => 'Medical Records', 'url' => '/doctor/medical-records'],
        ['icon' => 'account_circle', 'label' => 'My Profile', 'url' => '/doctor/profile'],
    ],
    
    'patient' => [
        ['icon' => 'calendar_today', 'label' => 'My Appointments', 'url' => '/patient/appointments'],
        ['icon' => 'add_circle', 'label' => 'Book Appointment', 'url' => '/patient/appointments/create'],
        ['icon' => 'account_circle', 'label' => 'My Profile', 'url' => '/patient/profile'],
    ],
];

$currentMenu = $menus[$role] ?? [];
$currentPath = $_SERVER['REQUEST_URI'];
?>

<style>
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 260px;
        height: 100vh;
        background: #ffffff;
        border-right: 1px solid #e5e7eb;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 2px 0 8px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
    }
    
    .sidebar-header {
        padding: 24px 20px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-bottom: 1px solid rgba(255,255,255,0.1);
        color: white;
    }
    
    .sidebar-header h2 {
        margin: 0 0 8px 0;
        font-size: 22px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .sidebar-header p {
        margin: 0;
        font-size: 13px;
        opacity: 0.9;
        font-weight: 500;
    }
    
    .sidebar-menu {
        padding: 16px 0;
        flex: 1;
    }
    
    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: #6b7280;
        text-decoration: none;
        transition: all 0.2s;
        border-left: 3px solid transparent;
        font-size: 14px;
        font-weight: 500;
    }
    
    .sidebar-menu a:hover {
        background: #f3f4f6;
        color: #3b82f6;
        border-left-color: #3b82f6;
    }
    
    .sidebar-menu a.active {
        background: #eff6ff;
        color: #3b82f6;
        border-left-color: #3b82f6;
    }
    
    .sidebar-menu a .material-icons {
        font-size: 20px;
        margin-right: 12px;
        width: 24px;
    }
    
    .sidebar-menu-item {
        position: relative;
    }
    
    .sidebar-menu-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        color: #6b7280;
        text-decoration: none;
        transition: all 0.2s;
        border-left: 3px solid transparent;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
    }
    
    .sidebar-menu-toggle:hover {
        background: #f3f4f6;
        color: #3b82f6;
        border-left-color: #3b82f6;
    }
    
    .sidebar-menu-toggle .material-icons.toggle-icon {
        font-size: 18px;
        transition: transform 0.3s;
    }
    
    .sidebar-menu-toggle.active {
        background: #eff6ff;
        color: #3b82f6;
        border-left-color: #3b82f6;
    }
    
    .sidebar-menu-toggle.active .material-icons.toggle-icon {
        transform: rotate(90deg);
    }
    
    .sidebar-submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background: #f9fafb;
    }
    
    .sidebar-submenu.open {
        max-height: 500px;
    }
    
    .sidebar-submenu a {
        padding: 10px 20px 10px 56px;
        font-size: 13px;
    }
    
    .sidebar-footer {
        width: 100%;
        padding: 20px;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
        margin-top: auto;
    }
    
    .logout-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 12px;
        background: #ef4444;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        transition: background 0.2s;
        font-weight: 600;
        font-size: 14px;
        gap: 8px;
    }
    
    .logout-btn:hover {
        background: #dc2626;
    }
    
    .main-content {
        margin-left: 260px;
        min-height: 100vh;
        background: #f9fafb;
    }
    
    .top-bar {
        background: white;
        padding: 20px 30px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 16px;
    }
    
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s;
        }
        
        .sidebar.mobile-open {
            transform: translateX(0);
        }
        
        .main-content {
            margin-left: 0;
        }
    }
</style>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>
            <span class="material-icons">local_hospital</span>
            MediCare
        </h2>
        <p><?= ucfirst($role) ?> Portal</p>
    </div>
    
    <div class="sidebar-menu">
        <?php foreach ($currentMenu as $item): ?>
            <?php if (isset($item['submenu'])): ?>
                <div class="sidebar-menu-item">
                    <div class="sidebar-menu-toggle" onclick="toggleSubmenu(this)">
                        <div style="display: flex; align-items: center;">
                            <span class="material-icons"><?= $item['icon'] ?></span>
                            <span><?= $item['label'] ?></span>
                        </div>
                        <span class="material-icons toggle-icon">chevron_right</span>
                    </div>
                    <div class="sidebar-submenu">
                        <?php foreach ($item['submenu'] as $subitem): ?>
                            <?php 
                            $isActive = strpos($currentPath, $subitem['url']) !== false ? 'active' : '';
                            ?>
                            <a href="<?= $subitem['url'] ?>" class="<?= $isActive ?>">
                                <span class="material-icons"><?= $subitem['icon'] ?></span>
                                <span><?= $subitem['label'] ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <?php 
                $isActive = strpos($currentPath, $item['url']) !== false ? 'active' : '';
                ?>
                <a href="<?= $item['url'] ?>" class="<?= $isActive ?>">
                    <span class="material-icons"><?= $item['icon'] ?></span>
                    <span><?= $item['label'] ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    
    <script>
    function toggleSubmenu(element) {
        const submenu = element.nextElementSibling;
        const isOpen = submenu.classList.contains('open');
        
        document.querySelectorAll('.sidebar-submenu').forEach(sm => {
            sm.classList.remove('open');
        });
        document.querySelectorAll('.sidebar-menu-toggle').forEach(toggle => {
            toggle.classList.remove('active');
        });
        
        if (!isOpen) {
            submenu.classList.add('open');
            element.classList.add('active');
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const activeLink = document.querySelector('.sidebar-submenu a.active');
        if (activeLink) {
            const submenu = activeLink.closest('.sidebar-submenu');
            const toggle = submenu.previousElementSibling;
            submenu.classList.add('open');
            toggle.classList.add('active');
        }
    });
    </script>
    
    <div class="sidebar-footer">
        <a href="/logout" class="logout-btn">
            <span class="material-icons">logout</span>
            Logout
        </a>
    </div>
</div>

<script src="/public/js/dashboard.js"></script>