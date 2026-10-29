<?php
// admin/dashboard.php
require_once __DIR__ . '/auth.php';
require_login(); // Force login

require_once __DIR__ . '/../includes/db.php';

$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// ড্যাশবোর্ড স্ট্যাটস
$stats = ['stories' => 0, 'enrollments' => 0, 'donations_amount' => 0, 'messages' => 0];
$recent_messages = [];

try {
    $stats['stories'] = (int)$pdo->query("SELECT COUNT(*) FROM stories WHERE status = 'published'")->fetchColumn();
    
    $e1 = (int)$pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
    $e2 = (int)$pdo->query("SELECT COUNT(*) FROM capacity_enrollments")->fetchColumn();
    $e3 = (int)$pdo->query("SELECT COUNT(*) FROM happiness_enrollments")->fetchColumn();
    $stats['enrollments'] = $e1 + $e2 + $e3;
    
    $stats['donations_amount'] = (float)$pdo->query("SELECT COALESCE(SUM(amount), 0) FROM donations")->fetchColumn();
    $stats['messages'] = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
    
    $recent_messages = $pdo->query("SELECT id, full_name, email, subject, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
} catch (Throwable $e) {
    error_log("Dashboard Query Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Never Give Up World Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        }
        /* Sidebar */
        .sidebar {
            background-color: var(--sidebar-bg);
            color: #fff;
            height: 100vh; height: 100dvh;
            position: fixed; top: 0; left: 0;
            width: 250px; z-index: 100;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }
        .sidebar .logo-container {
            padding: 20px; border-bottom: 1px solid #334155; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
            color: #fff; text-decoration: none; font-weight: 800; font-size: 20px;
        }
        .logo-icon {
            width: 32px; height: 32px; background: linear-gradient(135deg, var(--primary-blue), #60a5fa);
            border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 900;
        }
        .sidebar .nav-item { margin: 5px 10px; }
        .sidebar .nav-link {
            color: #cbd5e1; padding: 12px 15px; border-radius: 8px;
            display: flex; align-items: center; transition: all 0.3s; white-space: nowrap;
        }
        .sidebar .nav-link:hover { background-color: var(--sidebar-hover); color: #fff; }
        .sidebar .nav-link.active { background-color: var(--sidebar-hover); color: #fff; border-left: 4px solid var(--sidebar-active); }
        .sidebar .nav-link i { margin-right: 12px; width: 20px; text-align: center; font-size: 1.1rem; }
        
        /* Main Content */
        .main-content { margin-left: 250px; padding: 20px; transition: margin-left 0.3s ease; }
        
        /* Mobile */
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
        }

        /* Cards */
        .stat-card {
            background: #fff; border-radius: 12px; padding: 24px; height: 100%;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: none;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-icon {
            width: 48px; height: 48px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: #fff; margin-bottom: 15px;
        }
        .stat-value { font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 5px; }
        .stat-label { font-size: 14px; color: #64748b; font-weight: 600; text-transform: uppercase; }
        
        /* Table */
        .table-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .table th { background: #f8fafc; color: #475569; font-weight: 600; font-size: 14px; border-bottom: 2px solid #e2e8f0; }
        .table td { vertical-align: middle; color: #334155; font-size: 14px; }
    </style>
</head>
<body>
    <!-- Mobile Toggle -->
    <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="bi bi-list"></i></button>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <a href="dashboard.php" class="logo-container">
            <div class="logo-icon">N</div>
            <div>Never Give Up <span style="color:#60a5fa">World</span></div>
        </a>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-journal-text"></i> Stories</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-people"></i> Enrollments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-heart"></i> Donations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-envelope"></i> Messages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-person-badge"></i> Volunteers</a>
            </li>
        </ul>
        <div class="mt-auto p-3 border-top border-secondary">
            <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h2 class="fw-bold mb-1">Dashboard</h2>
                <p class="text-muted mb-0">Welcome back, <?= htmlspecialchars($admin_name) ?>. Here is your website overview.</p>
            </div>
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:45px; height:45px; font-weight:bold;">
                    <?= strtoupper(substr($admin_name, 0, 1)) ?>
                </div>
                <div class="d-none d-md-block">
                    <p class="mb-0 fw-bold"><?= htmlspecialchars($admin_name) ?></p>
                    <p class="mb-0 text-muted small">Administrator</p>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary"><i class="bi bi-journal-richtext"></i></div>
                    <div class="stat-value"><?= number_format($stats['stories']) ?></div>
                    <div class="stat-label">Published Stories</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success"><i class="bi bi-person-plus"></i></div>
                    <div class="stat-value"><?= number_format($stats['enrollments']) ?></div>
                    <div class="stat-label">Total Enrollments</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning"><i class="bi bi-currency-rupee"></i></div>
                    <div class="stat-value">₹<?= number_format($stats['donations_amount'], 2) ?></div>
                    <div class="stat-label">Total Donations</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info"><i class="bi bi-envelope-paper"></i></div>
                    <div class="stat-value"><?= number_format($stats['messages']) ?></div>
                    <div class="stat-label">Contact Messages</div>
                </div>
            </div>
        </div>

        <!-- Recent Messages Table -->
        <div class="row">
            <div class="col-12">
                <div class="table-card">
                    <h5 class="fw-bold mb-3">Recent Contact Messages</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_messages)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No recent messages found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_messages as $msg): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($msg['full_name']) ?></td>
                                            <td><?= htmlspecialchars($msg['email']) ?></td>
                                            <td><?= htmlspecialchars($msg['subject']) ?></td>
                                            <td><?= date('d M Y, h:i A', strtotime($msg['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const btn = document.getElementById('mobileMenuBtn');

        function toggleMenu() {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        }

        btn.addEventListener('click', toggleMenu);
        backdrop.addEventListener('click', toggleMenu);
    </script>
</body>
</html>