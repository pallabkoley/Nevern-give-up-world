<?php
// admin/insights.php - Complete Insights Management

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
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/warehouzing/assets/images/insights/');
define('UPLOAD_URL', BASE_URL . 'assets/images/insights/');

// Create directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Helper function to generate slug
function generateSlug($title) {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

// Helper function to upload image
function uploadInsightImage($file, $existing_image = null) {
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
    $filename = 'insight_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $filepath = UPLOAD_DIR . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to upload file.');
    }
    
    if ($existing_image && file_exists(UPLOAD_DIR . $existing_image)) {
        unlink(UPLOAD_DIR . $existing_image);
    }
    
    return $filename;
}

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $title = sanitize($_POST['title'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $author = sanitize($_POST['author'] ?? '');
    $content = $_POST['content'] ?? '';
    $short_description = sanitize($_POST['short_description'] ?? '');
    $status = sanitize($_POST['status'] ?? 'Draft');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $published_date = $_POST['published_date'] ?? date('Y-m-d H:i:s');
    $existing_image = $_POST['existing_image'] ?? null;
    
    try {
        $image_filename = $existing_image;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $image_filename = uploadInsightImage($_FILES['image'], $existing_image);
        }
        
        if ($action === 'add') {
            $slug = generateSlug($title);
            // Check if slug exists
            $stmt = $conn->prepare("SELECT id FROM insights WHERE slug = :slug");
            $stmt->execute([':slug' => $slug]);
            if ($stmt->fetch()) {
                $slug = $slug . '-' . time();
            }
            
            $stmt = $conn->prepare("
                INSERT INTO insights (title, slug, category, author, content, short_description, image, is_featured, status, published_date)
                VALUES (:title, :slug, :category, :author, :content, :short_description, :image, :is_featured, :status, :published_date)
            ");
            $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':category' => $category,
                ':author' => $author,
                ':content' => $content,
                ':short_description' => $short_description,
                ':image' => $image_filename,
                ':is_featured' => $is_featured,
                ':status' => $status,
                ':published_date' => $published_date
            ]);
            $message = 'Article added successfully!';
            $message_type = 'success';
        } elseif ($action === 'edit' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            $slug = generateSlug($title);
            
            $stmt = $conn->prepare("
                UPDATE insights 
                SET title = :title, slug = :slug, category = :category, author = :author,
                    content = :content, short_description = :short_description,
                    image = :image, is_featured = :is_featured, status = :status,
                    published_date = :published_date
                WHERE id = :id
            ");
            $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':category' => $category,
                ':author' => $author,
                ':content' => $content,
                ':short_description' => $short_description,
                ':image' => $image_filename,
                ':is_featured' => $is_featured,
                ':status' => $status,
                ':published_date' => $published_date,
                ':id' => $id
            ]);
            $message = 'Article updated successfully!';
            $message_type = 'success';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
        error_log("Insight error: " . $e->getMessage());
    } catch (PDOException $e) {
        $message = 'Database error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Delete Insight
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $conn->prepare("SELECT image FROM insights WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $insight = $stmt->fetch();
        
        if ($insight && $insight['image'] && file_exists(UPLOAD_DIR . $insight['image'])) {
            unlink(UPLOAD_DIR . $insight['image']);
        }
        
        $stmt = $conn->prepare("DELETE FROM insights WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $message = 'Article deleted successfully!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get all insights
$insights = [];
try {
    $stmt = $conn->query("SELECT * FROM insights ORDER BY published_date DESC, created_at DESC");
    $insights = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = 'Error fetching insights: ' . $e->getMessage();
    $message_type = 'error';
}

// Get insight for editing
$edit_insight = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    try {
        $stmt = $conn->prepare("SELECT * FROM insights WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $edit_insight = $stmt->fetch();
    } catch (PDOException $e) {
        $message = 'Error fetching insight: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Categories list
$categories = [
    'Warehousing', 'Logistics', 'Transportation', 'Supply Chain',
    'E-commerce', 'Technology', 'AI & Automation', 'Infrastructure',
    'Market Updates', 'Industry News', 'Government Policies',
    'Industry Reports', 'Expert Opinions', 'Case Studies'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Insights – Warehouzing Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
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
        .status-badge.Draft { background: #fff3cd; color: #856404; }
        .table-container .featured-badge {
            color: #f59e0b;
            font-size: 14px;
        }
        .table-container .insight-image {
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
            max-width: 800px;
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
        .form-group textarea { resize: vertical; min-height: 100px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        
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
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #2e8b57;
            cursor: pointer;
        }
        .checkbox-group label {
            font-weight: 400;
            cursor: pointer;
        }
        
        .CodeMirror {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            min-height: 200px;
            font-family: 'Inter', monospace;
        }
        
        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
            .form-row-3 { grid-template-columns: 1fr; }
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
            <h1><i class="fas fa-newspaper" style="color:#2e8b57;"></i> Industry Insights</h1>
            <p class="subtitle">Manage articles, news, and industry reports</p>
        </div>
        <div class="actions">
            <button class="btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add Article
            </button>
            <a href="<?php echo BASE_URL; ?>pages/insights.php" target="_blank" class="btn-secondary">
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
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search by title, category, author..." onkeyup="filterTable()" />
            <select id="categoryFilter" onchange="filterTable()">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                <?php endforeach; ?>
            </select>
            <select id="statusFilter" onchange="filterTable()">
                <option value="">All Status</option>
                <option value="Published">Published</option>
                <option value="Draft">Draft</option>
            </select>
        </div>

        <?php if (!empty($insights)): ?>
        <table id="insightTable">
            <thead>
                <tr>
                    <th style="width:50px;">Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Featured</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($insights as $insight): ?>
                <tr data-title="<?php echo strtolower($insight['title']); ?>"
                    data-category="<?php echo strtolower($insight['category']); ?>"
                    data-author="<?php echo strtolower($insight['author']); ?>"
                    data-status="<?php echo $insight['status']; ?>">
                    <td>
                        <img src="<?php echo UPLOAD_URL . ($insight['image'] ?? 'insight-placeholder.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($insight['title']); ?>"
                             class="insight-image"
                             onerror="this.src='<?php echo BASE_URL; ?>assets/images/insights/insight-placeholder.jpg'">
                    </td>
                    <td><strong><?php echo htmlspecialchars($insight['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($insight['category']); ?></td>
                    <td><?php echo htmlspecialchars($insight['author'] ?? '-'); ?></td>
                    <td>
                        <span class="status-badge <?php echo $insight['status']; ?>">
                            <?php echo $insight['status']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($insight['is_featured']): ?>
                            <span class="featured-badge"><i class="fas fa-star"></i></span>
                        <?php else: ?>
                            <span style="color:#dde1e6;"><i class="fas fa-star"></i></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('d M Y', strtotime($insight['published_date'] ?? $insight['created_at'])); ?></td>
                    <td>
                        <div class="actions-cell">
                            <a href="?edit=<?php echo $insight['id']; ?>" class="btn-sm btn-secondary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?php echo $insight['id']; ?>" class="btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this article permanently?')">
                                <i class="fas fa-trash"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>pages/insight-detail.php?id=<?php echo $insight['id']; ?>" target="_blank" class="btn-sm btn-secondary" title="View">
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
            <i class="fas fa-newspaper"></i>
            <h4>No Articles Found</h4>
            <p>Start by adding your first insight article.</p>
            <button class="btn-primary" onclick="openAddModal()" style="margin-top:15px;">
                <i class="fas fa-plus"></i> Add Article
            </button>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- ===== ADD/EDIT MODAL ===== -->
<div id="insightModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add Article</h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="insightId" value="">
            <input type="hidden" name="existing_image" id="existingImage" value="">
            
            <div class="form-group">
                <label for="title">Article Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" placeholder="e.g. The Future of Warehousing in India" required>
            </div>
            
            <div class="form-row">
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
                    <label for="author">Author</label>
                    <input type="text" id="author" name="author" placeholder="Author name">
                </div>
            </div>
            
            <div class="form-group">
                <label for="short_description">Short Description</label>
                <textarea id="short_description" name="short_description" rows="2" placeholder="Brief summary of the article (displayed in listings)"></textarea>
            </div>
            
            <div class="form-group">
                <label for="content">Content <span class="required">*</span></label>
                <textarea id="content" name="content" rows="8" placeholder="Full article content (HTML allowed)"></textarea>
            </div>
            
            <!-- Image Upload -->
            <div class="form-group">
                <label>Featured Image</label>
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
            
            <div class="form-row-3">
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="Draft">Draft</option>
                        <option value="Published">Published</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="published_date">Published Date</label>
                    <input type="datetime-local" id="published_date" name="published_date">
                </div>
                <div class="form-group checkbox-group" style="margin-top:28px;">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1">
                    <label for="is_featured">Feature this article</label>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">Save Article</button>
        </form>
    </div>
</div>

<!-- ===== JAVASCRIPT ===== -->
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
    document.getElementById('modalTitle').textContent = 'Add Article';
    document.getElementById('formAction').value = 'add';
    document.getElementById('insightId').value = '';
    document.getElementById('existingImage').value = '';
    document.getElementById('title').value = '';
    document.getElementById('category').value = '';
    document.getElementById('author').value = '';
    document.getElementById('short_description').value = '';
    document.getElementById('content').value = '';
    document.getElementById('status').value = 'Draft';
    document.getElementById('is_featured').checked = false;
    document.getElementById('published_date').value = '';
    document.getElementById('imageUpload').value = '';
    document.getElementById('fileNameDisplay').textContent = '';
    document.getElementById('currentImageContainer').innerHTML = '';
    document.getElementById('insightModal').classList.add('active');
}

// Edit Modal (populated with existing data)
<?php if ($edit_insight): ?>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalTitle').textContent = 'Edit Article';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('insightId').value = '<?php echo $edit_insight['id']; ?>';
    document.getElementById('existingImage').value = '<?php echo $edit_insight['image']; ?>';
    document.getElementById('title').value = '<?php echo addslashes($edit_insight['title']); ?>';
    document.getElementById('category').value = '<?php echo addslashes($edit_insight['category']); ?>';
    document.getElementById('author').value = '<?php echo addslashes($edit_insight['author'] ?? ''); ?>';
    document.getElementById('short_description').value = '<?php echo addslashes($edit_insight['short_description'] ?? ''); ?>';
    document.getElementById('content').value = '<?php echo addslashes($edit_insight['content']); ?>';
    document.getElementById('status').value = '<?php echo $edit_insight['status']; ?>';
    document.getElementById('is_featured').checked = <?php echo $edit_insight['is_featured'] ? 'true' : 'false'; ?>;
    
    if (<?php echo $edit_insight['published_date'] ? 'true' : 'false'; ?>) {
        const date = new Date('<?php echo $edit_insight['published_date']; ?>');
        document.getElementById('published_date').value = date.toISOString().slice(0, 16);
    }
    
    <?php if ($edit_insight['image']): ?>
    document.getElementById('currentImageContainer').innerHTML = `
        <div class="current-image">
            <img src="<?php echo UPLOAD_URL . $edit_insight['image']; ?>" alt="Current image">
            <div class="image-info">
                <strong>Current Image:</strong> <?php echo $edit_insight['image']; ?>
                <br>
                <span class="remove-link" onclick="removeImage()">Remove image</span>
            </div>
        </div>
    `;
    <?php endif; ?>
    
    document.getElementById('insightModal').classList.add('active');
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
    document.getElementById('insightModal').classList.remove('active');
}

// Close modal on overlay click
document.getElementById('insightModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Filter Table
function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('#insightTable tbody tr');
    
    rows.forEach(row => {
        const title = row.dataset.title || '';
        const category = row.dataset.category || '';
        const author = row.dataset.author || '';
        const status = row.dataset.status || '';
        
        const matchesSearch = title.includes(filter) || category.includes(filter) || author.includes(filter);
        const matchesCategory = categoryFilter === '' || category === categoryFilter;
        const matchesStatus = statusFilter === '' || status === statusFilter;
        
        row.style.display = (matchesSearch && matchesCategory && matchesStatus) ? '' : 'none';
    });
}
</script>

</body>
</html>