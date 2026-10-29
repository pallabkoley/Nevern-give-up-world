<?php
// admin/equipment-enquiries.php - Equipment Enquiries Management

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$db = Database::getInstance();
$conn = $db->getConnection();

// Allowed statuses (whitelist)
$allowed_statuses = ['New', 'Contacted', 'In Progress', 'Converted', 'Closed'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id     = intval($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if (!in_array($status, $allowed_statuses, true)) {
        $message = "Invalid status value.";
        $message_type = 'error';
    } elseif ($id <= 0) {
        $message = "Invalid enquiry ID.";
        $message_type = 'error';
    } else {
        try {
            $stmt = $conn->prepare("UPDATE equipment_enquiries SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $status, ':id' => $id]);
            $message = "Status updated successfully!";
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = "Error updating status.";
            $message_type = 'error';
        }
    }
}

// Fetch equipment enquiries
$enquiries = [];
$error = null;
try {
    $stmt = $conn->query("SELECT * FROM equipment_enquiries ORDER BY created_at DESC");
    $enquiries = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching equipment enquiries.";
}

// CSS-safe class from status
function status_class($s) {
    return str_replace(' ', '-', $s);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Equipment Enquiries – Warehouzing Admin</title>
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
            vertical-align: top;
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
            margin-bottom: 6px;
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

        .interest-tag {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            background: #eef2ff;
            color: #3730a3;
        }

        .empty-state { text-align: center; padding: 40px 20px; color: #6c757d; }
        .empty-state i { font-size: 48px; opacity: 0.3; margin-bottom: 15px; }

        @media (max-width: 768px) {
            .table-container { padding: 15px; }
            .table-container th, .table-container td { padding: 8px 10px; font-size: 13px; }
        }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-dolly" style="color:#2e8b57;"></i> Equipment Enquiries</h1>
            <p class="subtitle">Manage equipment purchase & rental enquiries submitted from the website</p>
        </div>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <h3 style="margin-bottom:15px; color:#159447;">
            <i class="fas fa-clipboard-list"></i> All Equipment Enquiries
        </h3>

        <?php if (!empty($enquiries)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Interest</th>
                    <th>Qty</th>
                    <th>Company / Contact</th>
                    <th>Delivery Address</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enquiries as $enquiry): ?>
                <tr>
                    <td><strong>#<?= (int)$enquiry['id'] ?></strong></td>
                    <td>
                        <?= htmlspecialchars($enquiry['product_name']) ?>
                        <br><small style="color:#6c757d;">Product ID: <?= (int)$enquiry['product_id'] ?></small>
                    </td>
                    <td>
                        <span class="interest-tag"><?= htmlspecialchars($enquiry['interest_type']) ?></span>
                    </td>
                    <td><?= (int)$enquiry['quantity'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($enquiry['company_name']) ?></strong><br>
                        <small style="color:#6c757d;"><?= htmlspecialchars($enquiry['contact_person']) ?></small><br>
                        <small style="color:#6c757d;"><?= htmlspecialchars($enquiry['mobile']) ?></small><br>
                        <small style="color:#6c757d;"><?= htmlspecialchars($enquiry['email']) ?></small>
                    </td>
                    <td style="max-width:260px;">
                        <?= nl2br(htmlspecialchars($enquiry['delivery_address'])) ?>
                    </td>
                    <td>
                        <form method="POST" class="status-form">
                            <input type="hidden" name="id" value="<?= (int)$enquiry['id'] ?>">
                            <select name="status">
                                <?php foreach ($allowed_statuses as $s): ?>
                                    <option value="<?= htmlspecialchars($s) ?>" <?= $enquiry['status'] === $s ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_status">Update</button>
                        </form>
                        <span class="status-badge <?= htmlspecialchars(status_class($enquiry['status'])) ?>">
                            <?= htmlspecialchars($enquiry['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?= date('d M Y H:i', strtotime($enquiry['created_at'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No equipment enquiries found.</p>
        </div>
        <?php endif; ?>
    </div>
</main>

</body>
</html>