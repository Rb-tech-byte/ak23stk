<?php
session_start();
require_once __DIR__ . '/database/db_config.php';
require 'inc/_global/config.php';
require 'inc/backend/config.php';
require 'inc/_global/views/head_start.php';
require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$baseUrl = rtrim($baseUrl, '/') . '/';
$shareBaseUrl = rtrim($baseUrl, '/') . '/share.php?media=';

// Initialize variables
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin';
$error = '';
$success = '';
$medias = [];
$products = [];

// Database connection (PDO)
global $pdo;
try {
    // Fetch products for form dropdown
    $stmt = $pdo->prepare("SELECT id, name FROM products WHERE deleted_at IS NULL");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}

// Handle Create/Update/Delete/Generate Share Link
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_errors = [];
    $products_id = intval($_POST['products_id'] ?? 0);
    $type = trim($_POST['type'] ?? '');
    $value = trim($_POST['value'] ?? '');
    $share_links = trim($_POST['share_links'] ?? '') ?: null;
    $action = $_POST['action'] ?? '';

    // Validate inputs for create/update
    if ($action === 'create' || $action === 'update') {
        if ($products_id <= 0) {
            $form_errors[] = 'Product is required.';
        }
        if (!in_array($type, ['upload', 'url'])) {
            $form_errors[] = 'Type must be "upload" or "url".';
        }
        if (!in_array($type, ['image', 'zip', 'pdf', 'wav'])) {
            $form_errors[] = 'File type must be "image", "zip", "pdf", or "wav".';
        }
        if (empty($value)) {
            $form_errors[] = 'Value is required.';
        }
        if ($share_links) {
            $params = [$share_links];
            $sql = "SELECT COUNT(*) FROM medias WHERE share_links = ? AND deleted_at IS NULL";
            if ($action === 'update' && isset($_POST['id'])) {
                $sql .= " AND id != ?";
                $params[] = intval($_POST['id']);
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            if ($stmt->fetchColumn() > 0) {
                $form_errors[] = 'Share link must be unique.';
            }
        }
    }

    // Perform create
    if ($action === 'create' && empty($form_errors)) {
        if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $fileName = uniqid() . '_' . basename($_FILES['file_upload']['name']);
            $targetFile = $uploadDir . $fileName;

            // Basic security check for file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/zip'];
            if (!in_array($_FILES['file_upload']['type'], $allowedTypes)) {
                $form_errors[] = 'Invalid file type. Allowed types: JPG, PNG, GIF, PDF, ZIP.';
            } elseif ($_FILES['file_upload']['size'] > 5000000) { // 5MB limit
                $form_errors[] = 'File too large. Maximum size is 5MB.';
            } elseif (!move_uploaded_file($_FILES['file_upload']['tmp_name'], $targetFile)) {
                $form_errors[] = 'File upload failed.';
            } else {
                $value = 'uploads/' . $fileName; // Save relative path
            }
        }

        if (empty($form_errors)) {
            $stmt = $pdo->prepare("INSERT INTO medias (products_id, type, file_type, value, share_links, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            if ($stmt->execute([$products_id, $type, $type, $value, $share_links])) {
                $success = 'Media created successfully.';
            } else {
                $error = 'Failed to create media.';
            }
        } else {
            $error = implode('<br>', $form_errors);
        }
    }
    // Perform update
    elseif ($action === 'update' && isset($_POST['id']) && empty($form_errors)) {
        $id = intval($_POST['id']);
        if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $fileName = uniqid() . '_' . basename($_FILES['file_upload']['name']);
            $targetFile = $uploadDir . $fileName;

            // Basic security check for file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/zip'];
            if (!in_array($_FILES['file_upload']['type'], $allowedTypes)) {
                $form_errors[] = 'Invalid file type. Allowed types: JPG, PNG, GIF, PDF, ZIP.';
            } elseif ($_FILES['file_upload']['size'] > 5000000) { // 5MB limit
                $form_errors[] = 'File too large. Maximum size is 5MB.';
            } elseif (!move_uploaded_file($_FILES['file_upload']['tmp_name'], $targetFile)) {
                $form_errors[] = 'File upload failed.';
            } else {
                $value = 'uploads/' . $fileName; // Save relative path
            }
        }

        if (empty($form_errors)) {
            $stmt = $pdo->prepare("UPDATE medias SET products_id = ?, type = ?, file_type = ?, value = ?, share_links = ?, updated_at = NOW() WHERE id = ? AND deleted_at IS NULL");
            if ($stmt->execute([$products_id, $type, $type, $value, $share_links, $id])) {
                $success = 'Media updated successfully.';
            } else {
                $error = 'Failed to update media.';
            }
        } else {
            $error = implode('<br>', $form_errors);
        }
    }
    // Show errors if any
    elseif (!empty($form_errors)) {
        $error = implode('<br>', $form_errors);
    }
    // Perform delete
    if (isset($_POST['delete_id'])) {
        $id = intval($_POST['delete_id']);
        $stmt = $pdo->prepare("UPDATE medias SET deleted_at = NOW() WHERE id = ? AND deleted_at IS NULL");
        if ($stmt->execute([$id])) {
            $success = 'Media deleted successfully.';
        } else {
            $error = 'Failed to delete media.';
        }
    }
    // Generate share link
    if (isset($_POST['generate_share_id'])) {
        $id = intval($_POST['generate_share_id']);
        $new_share_link = generate_share_link($pdo, $id);
        if ($new_share_link) {
            $success = 'Share link generated successfully.';
        } else {
            $error = 'Failed to generate share link.';
        }
    }
}

// Fetch medias with pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;
$stmt = $pdo->prepare("
    SELECT m.id, m.products_id, m.type, m.file_type, m.value, m.share_links, m.created_at, p.name AS product_name
    FROM medias m
    LEFT JOIN products p ON m.products_id = p.id
    WHERE m.deleted_at IS NULL
    ORDER BY m.created_at DESC
    LIMIT :offset, :perpage
");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':perpage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$medias = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($medias as &$row) {
    $row['full_share_link'] = $row['share_links'] ? $shareBaseUrl . urlencode($row['share_links']) : ($row['type'] === 'url' ? $row['value'] : 'N/A');
}

// Total medias for pagination
$stmt = $pdo->prepare("SELECT COUNT(*) FROM medias WHERE deleted_at IS NULL");
$stmt->execute();
$totalMedias = $stmt->fetchColumn();
$totalPages = ceil($totalMedias / $perPage);

require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';
?>

<link rel="stylesheet" href="<?php echo $one->assets_folder; ?>/css/4download-style.css">
<script src="<?php echo $one->assets_folder; ?>/js/plugins/sweetalert2/sweetalert2.min.js"></script>

<div class="container dashboard-container">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>admin_dashboard.php">
                            <i class="bi bi-speedometer"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>categories.php">
                            <i class="bi bi-tag"></i> Category
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>products.php">
                            <i class="bi bi-cart"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>payments.php">
                            <i class="bi bi-credit-card"></i> Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>users.php">
                            <i class="bi bi-people"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'medias.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>medias.php">
                            <i class="bi bi-image"></i> Medias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'support.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>support.php">
                            <i class="bi bi-headset"></i> Ticket/Support
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="generalSettingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear"></i> General Setting
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="generalSettingDropdown">
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>payment_methods.php">Payment Method</a></li>
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>file_storage.php">Files Storage</a></li>
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>update_system_name.php">Update System Name</a></li>
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>update_logo.php">Update Logo</a></li>
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>mail_smtp.php">Mail SMTP</a></li>
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>sms_setting.php">SMS Setting</a></li>
                        </ul>
                    </li>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_panel.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>admin_panel.php">
                            <i class="bi bi-lock"></i> Admin Panel
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-lg-9">
            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div class="block block-rounded mb-4 animated fadeIn">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Manage Medias</h3>
                    <div class="block-options">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createMediaModal" data-bs-toggle="tooltip" title="Add New Media">
                            <i class="bi bi-plus"></i> Add Media
                        </button>
                        <button type="button" class="btn btn-sm btn-alt-secondary" id="darkModeToggle" data-bs-toggle="tooltip" title="Toggle Dark Mode">
                            <i class="bi bi-moon"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <?php if (empty($medias)): ?>
                        <p class="text-muted">No medias found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>File Type</th>
                                        <th>Value</th>
                                        <th>Share Link</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($medias as $media): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($media['id']); ?></td>
                                            <td><?php echo htmlspecialchars($media['product_name'] ?? 'Unknown'); ?></td>
                                            <td><?php echo htmlspecialchars($media['type']); ?></td>
                                            <td><?php echo htmlspecialchars($media['file_type']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($media['value'], 0, 30)); ?>...</td>
                                            <td>
                                                <?php if ($media['full_share_link'] !== 'N/A'): ?>
                                                    <span class="share-link"><?php echo htmlspecialchars(substr($media['full_share_link'], 0, 30)); ?>...</span>
                                                    <button class="btn btn-sm btn-info copy-share-link ms-1" data-link="<?php echo htmlspecialchars($media['full_share_link']); ?>" data-bs-toggle="tooltip" title="Copy Share Link">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <form action="<?php echo htmlspecialchars($baseUrl); ?>medias.php" method="POST" style="display:inline;">
                                                        <input type="hidden" name="generate_share_id" value="<?php echo $media['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Generate Share Link">
                                                            <i class="bi bi-link"></i> Generate
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($media['created_at'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary me-1" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#updateMediaModal"
                                                        data-id="<?php echo $media['id']; ?>"
                                                        data-products-id="<?php echo $media['products_id']; ?>"
                                                        data-type="<?php echo htmlspecialchars($media['type']); ?>"
                                                        data-file-type="<?php echo htmlspecialchars($media['file_type']); ?>"
                                                        data-value="<?php echo htmlspecialchars($media['value']); ?>"
                                                        data-share-links="<?php echo htmlspecialchars($media['share_links'] ?? ''); ?>"
                                                        data-bs-toggle="tooltip" title="Edit Media">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="<?php echo htmlspecialchars($baseUrl); ?>medias.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="delete_id" value="<?php echo $media['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger js-sweetalert2-confirm" 
                                                            data-sweetalert2-title="Are you sure?" 
                                                            data-sweetalert2-text="This media will be deleted!" 
                                                            data-sweetalert2-confirm-button-text="Yes, delete it!" 
                                                            data-bs-toggle="tooltip" title="Delete Media">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <nav class="mt-3">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo $baseUrl; ?>medias.php?page=<?php echo $page - 1; ?>">Prev</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo $baseUrl; ?>medias.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo $baseUrl; ?>medias.php?page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-text">
        <strong>AK23StudioKits</strong> &copy; <span data-toggle="year-copy"><?php echo date('Y'); ?></span>
    </div>
</div>

<?php require 'inc/_global/views/page_end.php'; ?>

<div class="modal fade" id="createMediaModal" tabindex="-1" aria-labelledby="createMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title" id="createMediaModalLabel">Add New Media</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="block-content">
                <form action="<?php echo htmlspecialchars($baseUrl); ?>medias.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createProductsId" class="form-label">Product</label>
                                <select class="form-select" id="createProductsId" name="products_id" required>
                                    <option value="">Select Product</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createType" class="form-label">Type</label>
                                <select class="form-select" id="createType" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="upload">Upload</option>
                                    <option value="url">URL</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3" id="createValueFieldUpload" style="display: none;">
                                <label for="createValue" class="form-label">Media Content</label>
                                <input type="file" class="form-control" id="createValue" name="file_upload" accept="image/*,application/pdf,application/zip">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3" id="createValueFieldUrl" style="display: none;">
                                <label for="createValueUrl" class="form-label">Value (Path/URL)</label>
                                <input type="text" class="form-control" id="createValueUrl" name="value" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createShareLinks" class="form-label">Share Link (optional)</label>
                                <input type="text" class="form-control" id="createShareLinks" name="share_links" placeholder="e.g., media_1234567890">
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Create Media</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateMediaModal" tabindex="-1" aria-labelledby="updateMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title" id="updateMediaModalLabel">Edit Media</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="block-content">
                <form action="<?php echo htmlspecialchars($baseUrl); ?>medias.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="updateId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateProductsId" class="form-label">Product</label>
                                <select class="form-select" id="updateProductsId" name="products_id" required>
                                    <option value="">Select Product</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateType" class="form-label">Type</label>
                                <select class="form-select" id="updateType" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="upload">Upload</option>
                                    <option value="url">URL</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3" id="updateValueFieldUpload" style="display: none;">
                                <label for="updateValue" class="form-label">Media Content (leave blank to keep current)</label>
                                <input type="file" class="form-control" id="updateValue" name="file_upload" accept="image/*,application/pdf,application/zip">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3" id="updateValueFieldUrl" style="display: none;">
                                <label for="updateValueUrl" class="form-label">Value (Path/URL)</label>
                                <input type="text" class="form-control" id="updateValueUrl" name="value" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateShareLinks" class="form-label">Share Link (optional)</label>
                                <input type="text" class="form-control" id="updateShareLinks" name="share_links" placeholder="e.g., media_1234567890">
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Media</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Dark mode toggle
    document.getElementById('darkModeToggle').addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        this.querySelector('i').classList.toggle('bi-moon');
        this.querySelector('i').classList.toggle('bi-sun');
    });

    // Populate update modal
    var updateModal = document.getElementById('updateMediaModal');
    updateModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var productsId = button.getAttribute('data-products-id');
        var type = button.getAttribute('data-type');
        var fileType = button.getAttribute('data-file-type');
        var value = button.getAttribute('data-value');
        var shareLinks = button.getAttribute('data-share-links');

        var modal = this;
        modal.querySelector('#updateId').value = id;
        modal.querySelector('#updateProductsId').value = productsId;
        modal.querySelector('#updateType').value = type;
        modal.querySelector('#updateValueFieldUpload').style.display = type === 'upload' ? 'block' : 'none';
        modal.querySelector('#updateValueFieldUrl').style.display = type === 'url' ? 'block' : 'none';
        modal.querySelector('#updateValueUrl').value = value;
        modal.querySelector('#updateShareLinks').value = shareLinks;
    });

    // SweetAlert2 for delete confirmation
    var deleteButtons = document.querySelectorAll('.js-sweetalert2-confirm');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            var form = this.closest('form');
            Swal.fire({
                title: this.getAttribute('data-sweetalert2-title'),
                text: this.getAttribute('data-sweetalert2-text'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: this.getAttribute('data-sweetalert2-confirm-button-text'),
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Copy share link to clipboard
    var copyButtons = document.querySelectorAll('.copy-share-link');
    copyButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var link = this.getAttribute('data-link');
            navigator.clipboard.writeText(link).then(function() {
                Swal.fire({
                    title: 'Copied!',
                    text: 'Share link copied to clipboard.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }).catch(function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to copy share link.',
                    icon: 'error'
                });
            });
        });
    });

    // Toggle create value field based on type
    document.getElementById('createType').addEventListener('change', function() {
        var type = this.value;
        document.getElementById('createValueFieldUpload').style.display = type === 'upload' ? 'block' : 'none';
        document.getElementById('createValueFieldUrl').style.display = type === 'url' ? 'block' : 'none';
    });

    // Toggle update value field based on type
    document.getElementById('updateType').addEventListener('change', function() {
        var type = this.value;
        document.getElementById('updateValueFieldUpload').style.display = type === 'upload' ? 'block' : 'none';
        document.getElementById('updateValueFieldUrl').style.display = type === 'url' ? 'block' : 'none';
    });
});
</script>

<?php
require 'inc/_global/views/page_end.php';
require 'inc/_global/views/footer_start.php';
require 'inc/_global/views/footer_end.php';
?>