<?php
// admin/warehouses.php - Warehouse Management with Image Upload + DataTables

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

// Define upload directory
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/warehouzing/assets/images/warehouses/');
define('UPLOAD_URL', BASE_URL . 'assets/images/warehouses/');

// Create directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Handle actions
$message = '';
$message_type = '';

// Handle Image Upload
function uploadImage($file, $existing_image = null) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return $existing_image;
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.');
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('File size too large. Maximum 5MB allowed.');
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'warehouse_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $filepath = UPLOAD_DIR . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to upload file. Check directory permissions.');
    }
    
    if ($existing_image && file_exists(UPLOAD_DIR . $existing_image)) {
        unlink(UPLOAD_DIR . $existing_image);
    }
    
    return $filename;
}

// Add/Edit Warehouse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $name = sanitize($_POST['name'] ?? '');
    $location = sanitize($_POST['location'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $state = sanitize($_POST['state'] ?? '');
    $available_area = sanitize($_POST['available_area'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $status = sanitize($_POST['status'] ?? 'Published');
    $existing_image = $_POST['existing_image'] ?? null;

    try {
        $image_filename = $existing_image;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $image_filename = uploadImage($_FILES['image'], $existing_image);
        }

        if ($action === 'add') {
            $stmt = $conn->prepare("
                INSERT INTO warehouses (name, location, city, state, available_area, description, image, status) 
                VALUES (:name, :location, :city, :state, :available_area, :description, :image, :status)
            ");
            $stmt->execute([
                ':name' => $name,
                ':location' => $location,
                ':city' => $city,
                ':state' => $state,
                ':available_area' => $available_area,
                ':description' => $description,
                ':image' => $image_filename,
                ':status' => $status
            ]);
            $message = 'Warehouse added successfully!';
            $message_type = 'success';
        } elseif ($action === 'edit' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            $stmt = $conn->prepare("
                UPDATE warehouses 
                SET name = :name, location = :location, city = :city, state = :state, 
                    available_area = :available_area, description = :description, 
                    image = :image, status = :status 
                WHERE id = :id
            ");
            $stmt->execute([
                ':name' => $name,
                ':location' => $location,
                ':city' => $city,
                ':state' => $state,
                ':available_area' => $available_area,
                ':description' => $description,
                ':image' => $image_filename,
                ':status' => $status,
                ':id' => $id
            ]);
            $message = 'Warehouse updated successfully!';
            $message_type = 'success';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
        error_log("Warehouse error: " . $e->getMessage());
    } catch (PDOException $e) {
        $message = 'Database error: ' . $e->getMessage();
        $message_type = 'error';
        error_log("Warehouse DB error: " . $e->getMessage());
    }
}

// Delete Warehouse
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $conn->prepare("SELECT image FROM warehouses WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $warehouse = $stmt->fetch();
        
        if ($warehouse && $warehouse['image'] && file_exists(UPLOAD_DIR . $warehouse['image'])) {
            unlink(UPLOAD_DIR . $warehouse['image']);
        }
        
        $stmt = $conn->prepare("DELETE FROM warehouses WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $message = 'Warehouse deleted successfully!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Cannot delete: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Toggle Status
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    try {
        $stmt = $conn->prepare("SELECT status FROM warehouses WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $current = $stmt->fetch();
        
        $new_status = ($current['status'] === 'Published') ? 'Unpublished' : 'Published';
        
        $stmt = $conn->prepare("UPDATE warehouses SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $new_status, ':id' => $id]);
        
        $message = 'Status updated to ' . $new_status . '!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get all warehouses
$warehouses = [];
try {
    $stmt = $conn->query("SELECT * FROM warehouses ORDER BY created_at DESC");
    $warehouses = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = 'Error fetching warehouses: ' . $e->getMessage();
    $message_type = 'error';
}

// Get warehouse for editing
$edit_warehouse = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    try {
        $stmt = $conn->prepare("SELECT * FROM warehouses WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $edit_warehouse = $stmt->fetch();
    } catch (PDOException $e) {
        $message = 'Error fetching warehouse: ' . $e->getMessage();
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Warehouses – Warehouzing Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- ===== DATATABLES CSS (CDN) ===== -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.3/css/dataTables.dataTables.min.css" />
    <style>
        /* ===== YOUR EXISTING STYLES (unchanged) ===== */
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
        
        @media (max-width: 992px) {
            .main-content { margin-left: 70px; }
        }
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 20px 15px; }
        }

        .page-header {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .page-header h1 { font-size: 24px; color: #1a2a4a; font-weight: 700; }
        .page-header .subtitle { color: #6c757d; font-size: 14px; margin-top: 4px; }
        .page-header .actions { display: flex; gap: 10px; flex-wrap: wrap; }
        
        .btn-primary, .btn-secondary, .btn-danger, .btn-sm {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-primary { background: #2e8b57; color: #fff; }
        .btn-primary:hover { background: #3ca86b; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(46,139,87,0.3); }
        .btn-secondary { background: #e9ecef; color: #495057; }
        .btn-secondary:hover { background: #dee2e6; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-danger:hover { background: #c82333; transform: translateY(-2px); }
        .btn-sm { padding: 6px 14px; font-size: 13px; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-success:hover { background: #218838; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; }
        
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
        .table-container .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        .status-badge.Published { background: #d4edda; color: #155724; }
        .status-badge.Unpublished { background: #f8d7da; color: #721c24; }
        
        .table-container .actions-cell { display: flex; gap: 6px; flex-wrap: wrap; }
        .table-container .warehouse-image {
            width: 60px;
            height: 45px;
            object-fit: cover;
            border-radius: 6px;
        }
        
        .empty-state { text-align: center; padding: 40px 20px; color: #6c757d; }
        .empty-state i { font-size: 48px; opacity: 0.3; margin-bottom: 15px; }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: #fff;
            max-width: 700px;
            width: 100%;
            padding: 35px;
            border-radius: 16px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .modal-header h2 { font-size: 22px; color: #1a2a4a; }
        .modal-close {
            font-size: 28px;
            cursor: pointer;
            color: #6c757d;
            background: none;
            border: none;
            transition: 0.3s;
        }
        .modal-close:hover { color: #dc3545; transform: rotate(90deg); }
        
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: #495057;
            margin-bottom: 4px;
        }
        .form-group .required { color: #dc3545; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f9fbfd;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #2e8b57;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(46,139,87,0.08);
        }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        
        /* File Upload */
        .file-upload-wrapper {
            position: relative;
            border: 2px dashed #dde1e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f9fbfd;
        }
        .file-upload-wrapper:hover {
            border-color: #2e8b57;
            background: #f0f8f4;
        }
        .file-upload-wrapper input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .file-upload-wrapper .upload-icon {
            font-size: 36px;
            color: #2e8b57;
            margin-bottom: 8px;
        }
        .file-upload-wrapper .upload-text {
            color: #6c757d;
            font-size: 14px;
        }
        .file-upload-wrapper .upload-text strong { color: #2e8b57; }
        .file-upload-wrapper .file-name {
            margin-top: 8px;
            font-size: 13px;
            color: #2e8b57;
            font-weight: 500;
        }
        .current-image {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 8px;
        }
        .current-image img {
            width: 60px;
            height: 45px;
            object-fit: cover;
            border-radius: 6px;
        }
        .current-image .image-info {
            font-size: 13px;
            color: #495057;
        }
        .current-image .image-info .remove-link {
            color: #dc3545;
            cursor: pointer;
            text-decoration: underline;
            font-weight: 500;
        }
        
        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #2e8b57;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-submit:hover { background: #3ca86b; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(46,139,87,0.3); }
        
        /* ===== SEARCH BOX ===== */
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .search-box input {
            flex: 1;
            padding: 10px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            min-width: 200px;
        }
        .search-box input:focus { outline: none; border-color: #2e8b57; }
        .search-box select {
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            background: #fff;
        }
        
        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
            .modal-content { padding: 25px; margin: 10px; }
            .page-header h1 { font-size: 20px; }
            .table-container { padding: 15px; }
            .table-container th, .table-container td { padding: 8px 10px; font-size: 13px; }
        }
        @media (max-width: 480px) {
            .page-header { flex-direction: column; align-items: stretch; }
            .page-header .actions { justify-content: stretch; }
            .page-header .actions .btn-primary { width: 100%; justify-content: center; }
            .table-container { padding: 10px; }
            .table-container .warehouse-image { width: 40px; height: 30px; }
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
            <h1><i class="fas fa-warehouse" style="color:#2e8b57;"></i> Warehouses</h1>
            <p class="subtitle">Manage all warehouse listings on your platform</p>
        </div>
        <div class="actions">
            <button class="btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add Warehouse
            </button>
            <a href="<?php echo BASE_URL; ?>pages/find-warehouse.php" target="_blank" class="btn-secondary">
                <i class="fas fa-external-link-alt"></i> View Site
            </a>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <!-- ===== SEARCH BOX ===== -->
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search by name, city, state..." />
            <select id="statusFilter">
                <option value="">All Status</option>
                <option value="Published">Published</option>
                <option value="Unpublished">Unpublished</option>
            </select>
        </div>

        <?php if (!empty($warehouses)): ?>
        <table id="warehouseTable">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Area (Sq. Ft.)</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($warehouses as $warehouse): ?>
                <tr>
                    <td>
                        <img src="<?php echo UPLOAD_URL . ($warehouse['image'] ?? 'warehouse-placeholder.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($warehouse['name']); ?>"
                             class="warehouse-image"
                             onerror="this.src='<?php echo BASE_URL; ?>assets/images/warehouse-placeholder.jpg'">
                    </td>
                    <td><strong><?php echo htmlspecialchars($warehouse['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($warehouse['city'] . ', ' . $warehouse['state']); ?></td>
                    <td><?php echo number_format($warehouse['available_area']); ?></td>
                    <td>
                        <span class="status-badge <?php echo $warehouse['status']; ?>">
                            <?php echo $warehouse['status']; ?>
                        </span>
                    </td>
                    <td><?php echo date('d M Y', strtotime($warehouse['created_at'])); ?></td>
                    <td>
                        <div class="actions-cell">
                            <a href="?edit=<?php echo $warehouse['id']; ?>" class="btn-sm btn-secondary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?toggle=<?php echo $warehouse['id']; ?>" class="btn-sm btn-warning" title="Toggle Status" onclick="return confirm('Toggle status?')">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                            <a href="?delete=<?php echo $warehouse['id']; ?>" class="btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this warehouse permanently?')">
                                <i class="fas fa-trash"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>pages/warehouse-detail.php?id=<?php echo $warehouse['id']; ?>" target="_blank" class="btn-sm btn-secondary" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-warehouse"></i>
            <h4>No Warehouses Found</h4>
            <p>Start by adding your first warehouse listing.</p>
            <button class="btn-primary" onclick="openAddModal()" style="margin-top:15px;">
                <i class="fas fa-plus"></i> Add Warehouse
            </button>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- ===== ADD/EDIT MODAL ===== -->
<div id="warehouseModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add Warehouse</h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="warehouseId" value="">
            <input type="hidden" name="existing_image" id="existingImage" value="">
            
            <div class="form-group">
                <label for="name">Warehouse Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" placeholder="e.g. Delhi Logistics Hub" required>
            </div>
            
            <div class="form-group">
                <label for="location">Location / Address <span class="required">*</span></label>
                <input type="text" id="location" name="location" placeholder="e.g. Kirti Nagar Industrial Area" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="city">City <span class="required">*</span></label>
                    <input type="text" id="city" name="city" placeholder="e.g. Delhi" required>
                </div>
                <div class="form-group">
                    <label for="state">State <span class="required">*</span></label>
                    <input type="text" id="state" name="state" placeholder="e.g. Delhi" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="available_area">Available Area (Sq. Ft.) <span class="required">*</span></label>
                <input type="number" id="available_area" name="available_area" placeholder="e.g. 25000" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" placeholder="Describe the warehouse features, amenities, etc."></textarea>
            </div>
            
            <!-- Image Upload -->
            <div class="form-group">
                <label>Warehouse Image</label>
                <div class="file-upload-wrapper">
                    <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <div class="upload-text">
                        <strong>Click to upload</strong> or drag and drop<br>
                        <small>JPG, PNG, GIF, WEBP (Max 5MB)</small>
                    </div>
                    <input type="file" name="image" id="imageUpload" accept="image/*" onchange="handleFileSelect(this)">
                    <div class="file-name" id="fileNameDisplay"></div>
                </div>
                <div id="currentImageContainer"></div>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="Published">Published</option>
                    <option value="Unpublished">Unpublished</option>
                </select>
            </div>
            
            <button type="submit" class="btn-submit">Save Warehouse</button>
        </form>
    </div>
</div>

<!-- ===== JAVASCRIPT ===== -->
<!-- ===== DATATABLES JS (standalone) ===== -->
<script src="https://cdn.datatables.net/2.3.3/js/dataTables.min.js"></script>

<script>
// ===== YOUR EXISTING JS FUNCTIONS =====
function handleFileSelect(input) {
    const file = input.files[0];
    const display = document.getElementById('fileNameDisplay');
    if (file) {
        display.textContent = '📎 ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        display.style.color = '#2e8b57';
    } else {
        display.textContent = '';
    }
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Warehouse';
    document.getElementById('formAction').value = 'add';
    document.getElementById('warehouseId').value = '';
    document.getElementById('existingImage').value = '';
    document.getElementById('name').value = '';
    document.getElementById('location').value = '';
    document.getElementById('city').value = '';
    document.getElementById('state').value = '';
    document.getElementById('available_area').value = '';
    document.getElementById('description').value = '';
    document.getElementById('status').value = 'Published';
    document.getElementById('imageUpload').value = '';
    document.getElementById('fileNameDisplay').textContent = '';
    document.getElementById('currentImageContainer').innerHTML = '';
    document.getElementById('warehouseModal').classList.add('active');
}

<?php if ($edit_warehouse): ?>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalTitle').textContent = 'Edit Warehouse';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('warehouseId').value = '<?php echo $edit_warehouse['id']; ?>';
    document.getElementById('existingImage').value = '<?php echo $edit_warehouse['image']; ?>';
    document.getElementById('name').value = '<?php echo addslashes($edit_warehouse['name']); ?>';
    document.getElementById('location').value = '<?php echo addslashes($edit_warehouse['location']); ?>';
    document.getElementById('city').value = '<?php echo addslashes($edit_warehouse['city']); ?>';
    document.getElementById('state').value = '<?php echo addslashes($edit_warehouse['state']); ?>';
    document.getElementById('available_area').value = '<?php echo $edit_warehouse['available_area']; ?>';
    document.getElementById('description').value = '<?php echo addslashes($edit_warehouse['description']); ?>';
    document.getElementById('status').value = '<?php echo $edit_warehouse['status']; ?>';
    
    <?php if ($edit_warehouse['image']): ?>
    document.getElementById('currentImageContainer').innerHTML = `
        <div class="current-image">
            <img src="<?php echo UPLOAD_URL . $edit_warehouse['image']; ?>" alt="Current image">
            <div class="image-info">
                <strong>Current Image:</strong> <?php echo $edit_warehouse['image']; ?>
                <br>
                <span class="remove-link" onclick="removeImage()">Remove image</span>
            </div>
        </div>
    `;
    <?php endif; ?>
    
    document.getElementById('warehouseModal').classList.add('active');
});
<?php endif; ?>

function removeImage() {
    if (confirm('Remove this image?')) {
        document.getElementById('existingImage').value = '';
        document.getElementById('currentImageContainer').innerHTML = `
            <div class="current-image" style="background:#f8d7da; color:#721c24;">
                <i class="fas fa-trash"></i>
                <div class="image-info">
                    Image will be removed upon saving.
                </div>
            </div>
        `;
    }
}

function closeModal() {
    document.getElementById('warehouseModal').classList.remove('active');
}

document.getElementById('warehouseModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// ===== DATATABLES INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function () {
    // Safety check
    if (typeof DataTable === 'undefined') {
        console.error('DataTables library not loaded. Please check CDN.');
        return;
    }

    const tableEl = document.querySelector('#warehouseTable');
    if (!tableEl) return;

    // Initialize DataTable
    const table = new DataTable('#warehouseTable', {
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[5, 'desc']], // column index 5 = Created
        columnDefs: [
            { orderable: false, targets: [0, 6] } // Image & Actions
        ],
        language: {
            search: "Search:",
            searchPlaceholder: "Search..."
        },
        // Ensure pagination is enabled (default is true)
        paging: true,
        info: true  // shows "Showing 1 to 10 of X entries"
    });

    // 2. Custom search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            table.search(this.value).draw();
        });
    }

    // 3. Status filter (jQuery-free)
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        let statusFilterFn = null;

        function applyStatusFilter() {
            // Remove previous filter if exists
            if (statusFilterFn !== null) {
                const idx = DataTable.ext.search.indexOf(statusFilterFn);
                if (idx !== -1) {
                    DataTable.ext.search.splice(idx, 1);
                }
                statusFilterFn = null;
            }

            const selectedStatus = statusFilter.value;
            if (selectedStatus === '') {
                table.draw();
                return;
            }

            statusFilterFn = function(settings, data, dataIndex) {
                const status = data[4] || '';
                return status.trim() === selectedStatus;
            };
            DataTable.ext.search.push(statusFilterFn);
            table.draw();
        }

        // Initial apply
        applyStatusFilter();

        // On change
        statusFilter.addEventListener('change', function () {
            applyStatusFilter();
        });
    }

    // (Optional) Hide the default DataTables search box to avoid duplication
    // Uncomment the next line if you want to hide it:
    // document.querySelector('.dt-search')?.style.display = 'none';
});
</script>

</body>
</html>