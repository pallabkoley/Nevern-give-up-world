<?php
// admin/index.php - Main Admin Dashboard
require_once __DIR__ . '/auth.php';
require_login(); // Forces unauthenticated users back to admin-login.php

require_once __DIR__ . '/../includes/db.php';

// Fetch Admin Name from session
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Initialize stats
$stats = [
    'stories' => 0,
    'donations' => 0,
    'enrollments' => 0,
    'messages' => 0
];
$recent_messages = [];

try {
    // 1. Total Published Stories
    $stats['stories'] = (int)$pdo->query("SELECT COUNT(*) FROM stories WHERE status = 'published'")->fetchColumn();
    
    // 2. Total Donations Amount (INR)
    $stats['donations'] = (float)$pdo->query("SELECT COALESCE(SUM(amount), 0) FROM donations")->fetchColumn();
    
    // 3. Total Enrollments (Combining all 3 tables)
    $e1 = (int)$pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
    $e2 = (int)$pdo->query("SELECT COUNT(*) FROM capacity_enrollments")->fetchColumn();
    $e3 = (int)$pdo->query("SELECT COUNT(*) FROM happiness_enrollments")->fetchColumn();
    $stats['enrollments'] = $e1 + $e2 + $e3;
    
    // 4. Total Contact Messages
    $stats['messages'] = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
    
    // 5. Recent Messages (Latest 5)
    $recent_messages = $pdo->query("SELECT id, full_name, email, subject, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
    
} catch (Throwable $e) {
    // Log error securely (won't break the page if a table is empty)
    error_log("Dashboard Query Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Never Give Up World Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --sidebar-active: #3b82f6;
            --primary-blue: #1d4ed8;
            --bg-light: #f5f7fa;
        }
        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            margin: 0;
        }
        
        /* Sidebar Styling */
        .sidebar {
            background-color: var(--sidebar-bg);
            color: #fff;
            height: 100vh; height: 100dvh;
            position: fixed; top: 0; left: 0;
            width: 250px; z-index: 100;
            transition: transform 0.3s ease;
            display: flex; flex-direction: column;
        }
        .sidebar .logo-container {
            padding: 20px; border-bottom: 1px solid #334155;
            display: flex; align-items: center; gap: 10px;
            color: #fff; text-decoration: none; font-weight: 800; font-size: 20px;
        }
        .logo-icon {
            width: 32px; height: 32px; background: linear-gradient(135deg, var(--primary-blue), #60a5fa);
            border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 900;
        }
        .sidebar .nav { flex: 1; padding: 20px 10px; overflow-y: auto; }
        .sidebar .nav-item { margin-bottom: 5px; }
        .sidebar .nav-link {
            color: #cbd5e1; padding: 12px 15px; border-radius: 8px;
            display: flex; align-items: center; transition: all 0.2s; white-space: nowrap;
        }
        .sidebar .nav-link:hover { background-color: var(--sidebar-hover); color: #fff; }
        .sidebar .nav-link.active { background-color: var(--sidebar-hover); color: #fff; border-left: 4px solid var(--sidebar-active); }
        .sidebar .nav-link i { margin-right: 12px; width: 20px; text-align: center; font-size: 1.1rem; }
        .sidebar .logout-wrap { padding: 20px; border-top: 1px solid #334155; }
        
        /* Main Content */
        .main-content { margin-left: 250px; padding: 25px; transition: margin-left 0.3s ease; min-height: 100vh; }
        
        /* Top Bar / Header */
        .top-header {
            background: #fff; border-radius: 12px; padding: 20px 25px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03); margin-bottom: 25px;
        }
        
        /* Dashboard Cards */
        .stat-card {
            background: #fff; border-radius: 12px; padding: 25px;
            border-left: 4px solid var(--primary-blue); height: 100%;
            box-shadow: 0 4px 6px rgba(0,0,0,0.03); transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 15px rgba(0,0,0,0.08); }
        .stat-card.green { border-left-color: #10b981; }
        .stat-card.orange { border-left-color: #f59e0b; }
        .stat-card.purple { border-left-color: #8b5cf6; }
        
        .stat-icon {
            width: 48px; height: 48px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; margin-bottom: 15px;
        }
        .bg-light-blue { background: #e0e7ff; color: #1d4ed8; }
        .bg-light-green { background: #d1fae5; color: #10b981; }
        .bg-light-orange { background: #fef3c7; color: #f59e0b; }
        .bg-light-purple { background: #ede9fe; color: #8b5cf6; }
        
        .stat-value { font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 5px; }
        .stat-label { font-size: 14px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Table Card */
        .table-card {
            background: #fff; border-radius: 12px; padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.03); margin-top: 25px;
        }
        .table th { background: #f8fafc; color: #475569; font-weight: 600; font-size: 13px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; }
        .table td { vertical-align: middle; color: #334155; font-size: 14px; padding: 15px; }
        
        /* Mobile Responsive */
        .mobile-menu-btn {
            display: none; position: fixed; top: 15px; left: 15px; z-index: 101;
            background-color: var(--sidebar-bg); color: white; border: none; border-radius: 5px;
            width: 44px; height: 44px; font-size: 22px; align-items: center; justify-content: center;
        }
        .sidebar-backdrop {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            opacity: 0; visibility: hidden; transition: all 0.3s; z-index: 99;
        }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .sidebar-backdrop.show { opacity: 1; visibility: visible; }
            .mobile-menu-btn { display: flex; }
            .main-content { margin-left: 0; padding-top: 74px; }
            .top-header { flex-direction: column; align-items: flex-start; gap: 15px; }
        }
    </style>
</head>
<body>

    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="bi bi-list"></i>
    </button>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <a href="index.php" class="logo-container">
            <div class="logo-icon">N</div>
            <div>Never Give Up <span style="color:#60a5fa">World</span></div>
        </a>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="index.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-journal-richtext"></i> Manage Stories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-people"></i> Enrollments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-currency-rupee"></i> Donations
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-envelope"></i> Contact Messages
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-person-badge"></i> Volunteers
                </a>
            </li>
        </ul>
        
        <div class="logout-wrap mt-auto">
            <a href="logout.php" class="nav-link text-danger" style="padding-left: 15px;">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <div class="top-header">
            <div>
                <h3 class="fw-bold mb-1">Dashboard Overview</h3>
                <p class="text-muted mb-0 small"><i class="bi bi-calendar3"></i> <?= date('l, F d, Y') ?></p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="../index.php" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-box-arrow-up-right"></i> View Live Site
                </a>
                <div class="d-flex align-items-center border-start ps-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px; font-weight:bold;">
                        <?= strtoupper(substr($admin_name, 0, 1)) ?>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold small"><?= htmlspecialchars($admin_name) ?></p>
                        <p class="mb-0 text-muted" style="font-size: 12px;">Admin</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="row g-4">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-light-blue"><i class="bi bi-journal-richtext"></i></div>
                    <div class="stat-value"><?= number_format($stats['stories']) ?></div>
                    <div class="stat-label">Published Stories</div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-xl-3">
                <div class="stat-card green">
                    <div class="stat-icon bg-light-green"><i class="bi bi-currency-rupee"></i></div>
                    <div class="stat-value">₹<?= number_format($stats['donations'], 2) ?></div>
                    <div class="stat-label">Total Donations</div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-xl-3">
                <div class="stat-card orange">
                    <div class="stat-icon bg-light-orange"><i class="bi bi-people"></i></div>
                    <div class="stat-value"><?= number_format($stats['enrollments']) ?></div>
                    <div class="stat-label">Total Enrollments</div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-xl-3">
                <div class="stat-card purple">
                    <div class="stat-icon bg-light-purple"><i class="bi bi-envelope"></i></div>
                    <div class="stat-value"><?= number_format($stats['messages']) ?></div>
                    <div class="stat-label">Contact Messages</div>
                </div>
            </div>
        </div>

        <!-- Recent Messages Table -->
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Recent Contact Messages</h5>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_messages)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i> No recent messages found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_messages as $msg): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($msg['full_name']) ?></td>
                                    <td><a href="mailto:<?= htmlspecialchars($msg['email']) ?>" class="text-decoration-none"><?= htmlspecialchars($msg['email']) ?></a></td>
                                    <td><?= htmlspecialchars($msg['subject']) ?></td>
                                    <td><?= date('d M Y, h:i A', strtotime($msg['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Sidebar Mobile Toggle JS -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const btn = document.getElementById('mobileMenuBtn');

        function toggleMenu() {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        }

        if (btn) btn.addEventListener('click', toggleMenu);
        if (backdrop) backdrop.addEventListener('click', toggleMenu);
    </script>
</body>
</html>