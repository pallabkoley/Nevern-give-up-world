<?php
// admin/includes/sidebar.php - COMPLETE ADMIN SIDEBAR

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <a href="<?php echo BASE_URL; ?>admin/dashboard.php">
            <div class="brand-icon">
                <i class="fas fa-warehouse"></i>
            </div>
            <div class="brand-text">
                <span class="brand-name">Warehouzing</span>
                <span class="brand-tagline">Admin Panel</span>
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-list">
            <!-- Dashboard -->
            <li class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"> 
    <a href="<?php echo BASE_URL; ?>admin/index.php" class="nav-link"> 
        <i class="fas fa-th-large"></i> 
        <span>Dashboard</span> 
    </a> 
</li>

            <!-- Warehouses -->
            <li class="nav-item <?php echo in_array($current_page, ['warehouses.php', 'add-warehouse.php', 'edit-warehouse.php']) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/warehouses.php" class="nav-link">
                    <i class="fas fa-warehouse"></i>
                    <span>Warehouses</span>
                </a>
            </li>

            <!-- Equipment -->
            <li class="nav-item <?php echo in_array($current_page, ['equipment.php', 'add-equipment.php', 'edit-equipment.php']) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/equipment.php" class="nav-link">
                    <i class="fas fa-boxes"></i>
                    <span>Equipment</span>
                </a>
            </li>

            <!-- Equipment Enquiries (NEW + badge) -->
            <li class="nav-item <?php echo ($current_page == 'equipment-enquiries.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/equipment-enquiries.php" class="nav-link">
                    <i class="fas fa-dolly"></i>
                    <span>Equipment Enquiries</span>
                    <?php
                    // Get count of new equipment enquiries
                    try {
                        $db_eq = Database::getInstance();
                        $conn_eq = $db_eq->getConnection();
                        $stmt_eq = $conn_eq->query("SELECT COUNT(*) as count FROM equipment_enquiries WHERE status = 'New'");
                        $row_eq = $stmt_eq->fetch(PDO::FETCH_ASSOC);
                        $new_eq_count = $row_eq['count'] ?? 0;
                        if ($new_eq_count > 0) {
                            echo '<span class="badge">' . $new_eq_count . '</span>';
                        }
                    } catch (PDOException $e) {
                        // Silent fail – do not break the sidebar
                    }
                    ?>
                </a>
            </li>

            <!-- Manpower -->
            <li class="nav-item <?php echo ($current_page == 'manpower.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/manpower.php" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Manpower</span>
                </a>
            </li>

            <!-- Insights -->
            <li class="nav-item <?php echo in_array($current_page, ['insights.php', 'add-insight.php', 'edit-insight.php']) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/insights.php" class="nav-link">
                    <i class="fas fa-newspaper"></i>
                    <span>Insights</span>
                </a>
            </li>

            <!-- ===== ENQUIRIES (with badge) ===== -->
            <li class="nav-item <?php echo ($current_page == 'enquiries.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/enquiries.php" class="nav-link">
                    <i class="fas fa-envelope"></i>
                    <span>Enquiries</span>
                    <?php
                    // Get count of new enquiries (only if database is available)
                    try {
                        $db = Database::getInstance();
                        $conn = $db->getConnection();
                        $stmt = $conn->query("SELECT COUNT(*) as count FROM enquiries WHERE status = 'New'");
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $new_count = $row['count'] ?? 0;
                        if ($new_count > 0) {
                            echo '<span class="badge">' . $new_count . '</span>';
                        }
                    } catch (PDOException $e) {
                        // Silent fail – do not break the sidebar
                    }
                    ?>
                </a>
            </li>

            <!-- Users -->
            <li class="nav-item <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/users.php" class="nav-link">
                    <i class="fas fa-users-cog"></i>
                    <span>Users</span>
                </a>
            </li>

            <!-- Settings -->
            <li class="nav-item <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/settings.php" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>

        <!-- Logout Section -->
        <div class="nav-footer">
            <div class="admin-info">
                <div class="admin-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="admin-details">
                    <span class="admin-name"><?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></span>
                    <span class="admin-role"><?php echo $_SESSION['admin_role'] ?? 'Administrator'; ?></span>
                </div>
            </div>
            <a href="<?php echo BASE_URL; ?>admin/logout.php" class="logout-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</aside>

<style>
/* ===== ADMIN SIDEBAR STYLES ===== */
.admin-sidebar {
    width: 260px;
    height: 100vh;
    background: #0f1a2f;
    color: #ffffff;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
    overflow-y: auto;
    overflow-x: hidden;
}

/* Scrollbar styling */
.admin-sidebar::-webkit-scrollbar {
    width: 4px;
}
.admin-sidebar::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.05);
}
.admin-sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.15);
    border-radius: 10px;
}
.admin-sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.25);
}

/* Sidebar Brand */
.sidebar-brand {
    padding: 25px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    flex-shrink: 0;
}
.sidebar-brand a {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: #ffffff;
}
.brand-icon {
    width: 42px;
    height: 42px;
    background: #2e8b57;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
    transition: all 0.3s ease;
}
.sidebar-brand a:hover .brand-icon {
    transform: scale(1.05);
    background: #3ca86b;
}
.brand-text {
    line-height: 1.2;
}
.brand-name {
    display: block;
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -0.5px;
}
.brand-tagline {
    display: block;
    font-size: 11px;
    color: rgba(255,255,255,0.5);
    font-weight: 400;
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* Navigation */
.sidebar-nav {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 20px 0;
}
.nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
    flex: 1;
}
.nav-item {
    margin: 2px 12px;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.nav-item:hover {
    background: rgba(255,255,255,0.06);
}
.nav-item.active {
    background: rgba(46, 139, 87, 0.15);
    box-shadow: inset 3px 0 0 #2e8b57;
}
.nav-link {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 16px;
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    border-radius: 6px;
    position: relative;
}
.nav-link:hover {
    color: #ffffff;
    background: rgba(255,255,255,0.05);
}
.nav-item.active .nav-link {
    color: #ffffff;
}
.nav-link i {
    width: 20px;
    font-size: 16px;
    text-align: center;
    flex-shrink: 0;
}
.nav-link span {
    flex: 1;
}
.nav-link .badge {
    background: #dc3545;
    color: #ffffff;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    min-width: 20px;
    text-align: center;
    animation: pulse-badge 2s infinite;
}
@keyframes pulse-badge {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Nav Footer - Admin Info & Logout */
.nav-footer {
    border-top: 1px solid rgba(255,255,255,0.08);
    padding: 16px 20px;
    flex-shrink: 0;
    margin-top: auto;
}
.admin-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 0 12px;
}
.admin-avatar {
    width: 38px;
    height: 38px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: rgba(255,255,255,0.7);
    flex-shrink: 0;
}
.admin-details {
    flex: 1;
    min-width: 0;
}
.admin-name {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.admin-role {
    display: block;
    font-size: 11px;
    color: rgba(255,255,255,0.4);
}
.logout-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    color: rgba(255,255,255,0.5);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.3s ease;
    margin-top: 4px;
}
.logout-link:hover {
    color: #dc3545;
    background: rgba(220, 53, 69, 0.1);
}
.logout-link i {
    width: 20px;
    text-align: center;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 992px) {
    .admin-sidebar { width: 70px; }
    .admin-sidebar:hover { width: 260px; }
    .brand-text, .nav-link span, .nav-link .badge, .admin-details, .logout-link span {
        display: none;
    }
    .admin-sidebar:hover .brand-text,
    .admin-sidebar:hover .nav-link span,
    .admin-sidebar:hover .nav-link .badge,
    .admin-sidebar:hover .admin-details,
    .admin-sidebar:hover .logout-link span {
        display: block;
    }
    .admin-sidebar:hover .nav-link .badge { display: inline-block; }
    .admin-sidebar:hover .nav-link { justify-content: flex-start; }
    .admin-sidebar:hover .admin-info { justify-content: flex-start; }
    .nav-item { margin: 2px 8px; }
    .nav-link { justify-content: center; padding: 12px; }
    .admin-sidebar:hover .nav-link { padding: 12px 16px; justify-content: flex-start; }
    .admin-info { justify-content: center; padding: 8px 0; }
    .admin-sidebar:hover .admin-info { justify-content: flex-start; padding: 8px 0 12px; }
    .logout-link { justify-content: center; padding: 10px; }
    .admin-sidebar:hover .logout-link { justify-content: flex-start; padding: 10px 12px; }
    .sidebar-brand a { justify-content: center; }
    .admin-sidebar:hover .sidebar-brand a { justify-content: flex-start; }
    .brand-icon { width: 36px; height: 36px; font-size: 16px; }
}

@media (max-width: 768px) {
    .admin-sidebar {
        width: 60px;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    .admin-sidebar.open {
        transform: translateX(0);
        width: 260px;
    }
    .admin-sidebar.open .brand-text,
    .admin-sidebar.open .nav-link span,
    .admin-sidebar.open .nav-link .badge,
    .admin-sidebar.open .admin-details,
    .admin-sidebar.open .logout-link span {
        display: block;
    }
    .admin-sidebar.open .nav-link { justify-content: flex-start; padding: 12px 16px; }
    .admin-sidebar.open .admin-info { justify-content: flex-start; padding: 8px 0 12px; }
    .admin-sidebar.open .logout-link { justify-content: flex-start; padding: 10px 12px; }
    .admin-sidebar.open .sidebar-brand a { justify-content: flex-start; }
    .brand-text, .nav-link span, .nav-link .badge, .admin-details, .logout-link span {
        display: none;
    }
    .admin-sidebar.open .brand-text,
    .admin-sidebar.open .nav-link span,
    .admin-sidebar.open .nav-link .badge,
    .admin-sidebar.open .admin-details,
    .admin-sidebar.open .logout-link span {
        display: block;
    }
    .admin-sidebar.open .nav-link .badge { display: inline-block; }
    /* Mobile overlay */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 999;
    }
    .sidebar-overlay.active { display: block; }
}

/* ===== TOGGLE BUTTON FOR MOBILE ===== */
.sidebar-toggle {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 1001;
    background: #0f1a2f;
    color: #ffffff;
    border: none;
    padding: 10px 12px;
    border-radius: 6px;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}
.sidebar-toggle:hover { background: #1a2a4a; }
@media (max-width: 768px) { .sidebar-toggle { display: block; } }
</style>

<!-- ===== SIDEBAR TOGGLE JAVASCRIPT ===== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create toggle button for mobile
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'sidebar-toggle';
    toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
    toggleBtn.setAttribute('aria-label', 'Toggle Sidebar');
    document.body.prepend(toggleBtn);

    // Create overlay for mobile
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.prepend(overlay);

    // Toggle sidebar
    toggleBtn.addEventListener('click', function() {
        const sidebar = document.querySelector('.admin-sidebar');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
        if (sidebar.classList.contains('open')) {
            toggleBtn.innerHTML = '<i class="fas fa-times"></i>';
        } else {
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
        }
    });

    // Close sidebar when clicking overlay
    overlay.addEventListener('click', function() {
        const sidebar = document.querySelector('.admin-sidebar');
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
    });

    // Close sidebar on window resize (if open)
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            const sidebar = document.querySelector('.admin-sidebar');
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
        }
    });
});
</script>