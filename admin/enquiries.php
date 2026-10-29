<?php
// admin/enquiries.php - Manage Warehouse Enquiries

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit;
}

// Include required files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Get database connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $table = $_POST['table'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';

    // Validate table name (whitelist)
    $allowed_tables = ['enquiries'];
    if (!in_array($table, $allowed_tables)) {
        $message = "Invalid table name.";
        $message_type = 'error';
    } else {
        // Validate status
        $allowed_statuses = ['New', 'Contacted', 'In Progress', 'Converted', 'Closed'];
        if (!in_array($status, $allowed_statuses)) {
            $message = "Invalid status value.";
            $message_type = 'error';
        } else {
            try {
                $stmt = $conn->prepare("UPDATE $table SET status = :status WHERE id = :id");
                $stmt->execute([':status' => $status, ':id' => $id]);
                $message = "Status updated successfully!";
                $message_type = 'success';
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}

// Get all enquiries
$enquiries = [];
try {
    $stmt = $conn->query("SELECT * FROM enquiries ORDER BY created_at DESC");
    $enquiries = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching enquiries: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Enquiries – Warehouzing Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f9;
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 30px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        @media (max-width: 992px) { .main-content { margin-left: 70px; } }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px 15px; } }

        .page-header {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }
        .page-header h1 { font-size: 24px; color: #1a2a4a; font-weight: 700; }
        .page-header .subtitle { color: #6c757d; font-size: 14px; margin-top: 4px; }

        .alert {
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .alert-error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }

        .table-container {
            background: #ffffff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            overflow-x: auto;
        }
        .table-container table { width: 100%; border-collapse: collapse; }
        .table-container th, .table-container td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }
        .table-container th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table-container tr:hover { background: #f8f9fa; }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        .status-badge.New { background: #e3f2fd; color: #0d47a1; }
        .status-badge.Contacted { background: #fff3e0; color: #e65100; }
        .status-badge.In-Progress { background: #e8f5e9; color: #1b5e20; }
        .status-badge.Converted { background: #c8e6c9; color: #1b5e20; }
        .status-badge.Closed { background: #f5f5f5; color: #616161; }

        .status-form {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
        }
        .status-form select {
            padding: 4px 8px;
            border: 1px solid #dde1e6;
            border-radius: 4px;
            font-size: 12px;
        }
        .status-form button {
            padding: 4px 10px;
            background: #2e8b57;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
        }
        .status-form button:hover { background: #3ca86b; }

        .empty-state { text-align: center; padding: 40px 20px; color: #6c757d; }
        .empty-state i { font-size: 48px; opacity: 0.3; margin-bottom: 15px; }

        @media (max-width: 768px) {
            .table-container { padding: 15px; }
            .table-container th, .table-container td { padding: 8px 10px; font-size: 13px; }
            .status-form { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>

<!-- Include Sidebar -->
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<main class="main-content">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-warehouse" style="color:#2e8b57;"></i> Warehouse Enquiries</h1>
            <p class="subtitle">All enquiries submitted via the "Find a Warehouse" page</p>
        </div>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <?php if (!empty($enquiries)): ?>
        <table>
            <thead>
                <tr>
                    <th>Enquiry ID</th>
                    <th>User</th>
                    <th>Company</th>
                    <th>Email / Mobile</th>
                    <th>Location</th>
                    <th>Requirement</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enquiries as $enquiry): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($enquiry['enquiry_id']) ?></strong></td>
                    <td><?= htmlspecialchars($enquiry['user_name']) ?></td>
                    <td><?= htmlspecialchars($enquiry['company_name'] ?? 'N/A') ?></td>
                    <td>
                        <?= htmlspecialchars($enquiry['email']) ?><br>
                        <small style="color:#6c757d;"><?= htmlspecialchars($enquiry['mobile']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($enquiry['location'] ?? 'N/A') ?></td>
                    <td>
                        <?php
                        $req = htmlspecialchars($enquiry['requirement'] ?? '');
                        // Truncate long text
                        if (strlen($req) > 60) {
                            $req = substr($req, 0, 60) . '...';
                        }
                        echo $req;
                        ?>
                    </td>
                    <td>
                        <form method="POST" class="status-form">
                            <input type="hidden" name="table" value="enquiries">
                            <input type="hidden" name="id" value="<?= $enquiry['id'] ?>">
                            <select name="status">
                                <option value="New" <?= $enquiry['status'] === 'New' ? 'selected' : '' ?>>New</option>
                                <option value="Contacted" <?= $enquiry['status'] === 'Contacted' ? 'selected' : '' ?>>Contacted</option>
                                <option value="In Progress" <?= $enquiry['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="Converted" <?= $enquiry['status'] === 'Converted' ? 'selected' : '' ?>>Converted</option>
                                <option value="Closed" <?= $enquiry['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
                            </select>
                            <button type="submit" name="update_status">Update</button>
                        </form>
                        <span class="status-badge <?= str_replace(' ', '-', $enquiry['status']) ?>">
                            <?= $enquiry['status'] ?>
                        </span>
                    </td>
                    <td><?= date('d M Y H:i', strtotime($enquiry['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No enquiries found.</p>
        </div>
        <?php endif; ?>
    </div>
</main>

</body>
</html>