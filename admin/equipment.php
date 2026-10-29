<?php
// admin/equipment.php - Equipment Management

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
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/warehouzing/assets/images/equipment/');
define('UPLOAD_URL', BASE_URL . 'assets/images/equipment/');

// Create directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Handle form submissions
$message = '';
$message_type = '';

// Upload function
function uploadEquipmentImage($file, $existing_image = null) {
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
    $filename = 'equipment_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $filepath = UPLOAD_DIR . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to upload file.');
    }
    
    if ($existing_image && file_exists(UPLOAD_DIR . $existing_image)) {
        unlink(UPLOAD_DIR . $existing_image);
    }
    
    return $filename;
}

// Add/Edit Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $name = sanitize($_POST['name'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $specifications = sanitize($_POST['specifications'] ?? '');
    $status = sanitize($_POST['status'] ?? 'Published');
    $existing_image = $_POST['existing_image'] ?? null;
    
    try {
        $image_filename = $existing_image;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $image_filename = uploadEquipmentImage($_FILES['image'], $existing_image);
        }
        
        if ($action === 'add') {
            $stmt = $conn->prepare("
                INSERT INTO equipment (name, category, description, specifications, image, status)
                VALUES (:name, :category, :description, :specifications, :image, :status)
            ");
            $stmt->execute([
                ':name' => $name,
                ':category' => $category,
                ':description' => $description,
                ':specifications' => $specifications,
                ':image' => $image_filename,
                ':status' => $status
            ]);
            $message = 'Product added successfully!';
            $message_type = 'success';
        } elseif ($action === 'edit' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            $stmt = $conn->prepare("
                UPDATE equipment 
                SET name = :name, category = :category, description = :description,
                    specifications = :specifications, image = :image, status = :status
                WHERE id = :id
            ");
            $stmt->execute([
                ':name' => $name,
                ':category' => $category,
                ':description' => $description,
                ':specifications' => $specifications,
                ':image' => $image_filename,
                ':status' => $status,
                ':id' => $id
            ]);
            $message = 'Product updated successfully!';
            $message_type = 'success';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    } catch (PDOException $e) {
        $message = 'Database error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Delete Product
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $conn->prepare("SELECT image FROM equipment WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();
        
        if ($product && $product['image'] && file_exists(UPLOAD_DIR . $product['image'])) {
            unlink(UPLOAD_DIR . $product['image']);
        }
        
        $stmt = $conn->prepare("DELETE FROM equipment WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $message = 'Product deleted successfully!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Toggle Status
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    try {
        $stmt = $conn->prepare("SELECT status FROM equipment WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $current = $stmt->fetch();
        
        $new_status = ($current['status'] === 'Published') ? 'Unpublished' : 'Published';
        
        $stmt = $conn->prepare("UPDATE equipment SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $new_status, ':id' => $id]);
        
        $message = 'Status updated to ' . $new_status . '!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get all products
$products = [];
try {
    $stmt = $conn->query("SELECT * FROM equipment ORDER BY category, name");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = 'Error fetching products: ' . $e->getMessage();
    $message_type = 'error';
}

// Get categories for dropdown
$categories = [];
try {
    $stmt = $conn->query("SELECT DISTINCT category FROM equipment ORDER BY category");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    // Use default categories
    $categories = ['Storage & Racking', 'Material Handling', 'Packaging Supplies', 'Conveyor Systems', 'Barcode Systems', 'RFID Systems', 'Safety Equipment', 'Dock Equipment', 'Cleaning Equipment', 'Warehouse Technology', 'Other Supplies'];
}

// Get product for editing
$edit_product = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    try {
        $stmt = $conn->prepare("SELECT * FROM equipment WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $edit_product = $stmt->fetch();
    } catch (PDOException $e) {
        $message = 'Error fetching product: ' . $e->getMessage();
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Equipment – Warehouzing Admin</title>
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
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        .status-badge.Published { background: #d4edda; color: #155724; }
        .status-badge.Unpublished { background: #f8d7da; color: #721c24; }
        .table-container .product-image {
            width: 50px;
            height: 40px;
            object-fit: cover;
            border-radius: 6px;
        }
        .table-container .actions-cell { display: flex; gap: 6px; flex-wrap: wrap; }
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
        .file-upload-wrapper:hover { border-color: #2e8b57; background: #f0f8f4; }
        .file-upload-wrapper input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .file-upload-wrapper .upload-icon { font-size: 36px; color: #2e8b57; margin-bottom: 8px; }
        .file-upload-wrapper .upload-text { color: #6c757d; font-size: 14px; }
        .file-upload-wrapper .upload-text strong { color: #2e8b57; }
        .file-upload-wrapper .file-name { margin-top: 8px; font-size: 13px; color: #2e8b57; font-weight: 500; }

        .current-image {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 8px;
        }
        .current-image img { width: 60px; height: 45px; object-fit: cover; border-radius: 6px; }
        .current-image .image-info { font-size: 13px; color: #495057; }
        .current-image .image-info .remove-link { color: #dc3545; cursor: pointer; text-decoration: underline; font-weight: 500; }

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
            <h1><i class="fas fa-boxes" style="color:#2e8b57;"></i> Equipment & Supplies</h1>
            <p class="subtitle">Manage warehouse equipment and supply products</p>
        </div>
        <div class="actions">
            <button class="btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add Product
            </button>
            <a href="<?php echo BASE_URL; ?>pages/equipment.php" target="_blank" class="btn-secondary">
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
        <?php if (!empty($products)): ?>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <img src="<?php echo UPLOAD_URL . ($product['image'] ?? 'equipment-placeholder.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             class="product-image"
                             onerror="this.src='<?php echo BASE_URL; ?>assets/images/equipment/equipment-placeholder.jpg'">
                    </td>
                    <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td>
                        <span class="status-badge <?php echo $product['status']; ?>">
                            <?php echo $product['status']; ?>
                        </span>
                    </td>
                    <td>
                        <div class="actions-cell">
                            <a href="?edit=<?php echo $product['id']; ?>" class="btn-sm btn-secondary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?toggle=<?php echo $product['id']; ?>" class="btn-sm btn-warning" title="Toggle Status" onclick="return confirm('Toggle status?')">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                            <a href="?delete=<?php echo $product['id']; ?>" class="btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this product permanently?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-box"></i>
            <h4>No Products Found</h4>
            <p>Start by adding your first equipment product.</p>
            <button class="btn-primary" onclick="openAddModal()" style="margin-top:15px;">
                <i class="fas fa-plus"></i> Add Product
            </button>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- ===== ADD/EDIT MODAL ===== -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add Product</h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="productId" value="">
            <input type="hidden" name="existing_image" id="existingImage" value="">
            
            <div class="form-group">
                <label for="name">Product Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" placeholder="e.g. Heavy Duty Pallet Racking" required>
            </div>
            
            <div class="form-group">
                <label for="category">Category <span class="required">*</span></label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" placeholder="Product description"></textarea>
            </div>
            
            <div class="form-group">
                <label for="specifications">Specifications</label>
                <textarea id="specifications" name="specifications" rows="2" placeholder="Technical specifications"></textarea>
            </div>
            
            <!-- Image Upload -->
            <div class="form-group">
                <label>Product Image</label>
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
            
            <button type="submit" class="btn-submit">Save Product</button>
        </form>
    </div>
</div>

<script>
// File upload handler
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

// Open Add Modal
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Product';
    document.getElementById('formAction').value = 'add';
    document.getElementById('productId').value = '';
    document.getElementById('existingImage').value = '';
    document.getElementById('name').value = '';
    document.getElementById('category').value = '';
    document.getElementById('description').value = '';
    document.getElementById('specifications').value = '';
    document.getElementById('status').value = 'Published';
    document.getElementById('imageUpload').value = '';
    document.getElementById('fileNameDisplay').textContent = '';
    document.getElementById('currentImageContainer').innerHTML = '';
    document.getElementById('productModal').classList.add('active');
}

// Edit Modal
<?php if ($edit_product): ?>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalTitle').textContent = 'Edit Product';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('productId').value = '<?php echo $edit_product['id']; ?>';
    document.getElementById('existingImage').value = '<?php echo $edit_product['image']; ?>';
    document.getElementById('name').value = '<?php echo addslashes($edit_product['name']); ?>';
    document.getElementById('category').value = '<?php echo addslashes($edit_product['category']); ?>';
    document.getElementById('description').value = '<?php echo addslashes($edit_product['description'] ?? ''); ?>';
    document.getElementById('specifications').value = '<?php echo addslashes($edit_product['specifications'] ?? ''); ?>';
    document.getElementById('status').value = '<?php echo $edit_product['status']; ?>';
    
    <?php if ($edit_product['image']): ?>
    document.getElementById('currentImageContainer').innerHTML = `
        <div class="current-image">
            <img src="<?php echo UPLOAD_URL . $edit_product['image']; ?>" alt="Current image">
            <div class="image-info">
                <strong>Current Image:</strong> <?php echo $edit_product['image']; ?>
                <br>
                <span class="remove-link" onclick="removeImage()">Remove image</span>
            </div>
        </div>
    `;
    <?php endif; ?>
    
    document.getElementById('productModal').classList.add('active');
});
<?php endif; ?>

// Remove image
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

// Close Modal
function closeModal() {
    document.getElementById('productModal').classList.remove('active');
}

document.getElementById('productModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

</body>
</html>