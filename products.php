<?php
session_start();
require_once __DIR__ . '/database/db_config.php';
require 'inc/_global/config.php';
require 'inc/backend/config.php';
require 'inc/_global/views/head_start.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$baseUrl = rtrim($baseUrl, '/') . '/';

// Initialize variables
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin';
$error = '';
$success = '';
$products = [];
$categories = [];

try {
    // Fetch categories for form dropdown
    $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE deleted_at IS NULL");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Validate JSON structure
    function validate_json_field($json, $field_name, $expected_structure) {
        if (empty($json)) return true; // Allow empty JSON
        $decoded = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }
        if (!is_array($decoded)) {
            return false;
        }
        foreach ($decoded as $item) {
            foreach ($expected_structure as $key => $type) {
                if (!isset($item[$key]) || gettype($item[$key]) !== $type) {
                    return false;
                }
            }
        }
        return true;
    }

    // Handle Create/Update/Delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            $category_id = intval($_POST['category_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $type = trim($_POST['type'] ?? '') ?: null;
            $files = trim($_POST['files'] ?? '') ?: null;
            $images = trim($_POST['images'] ?? '') ?: null;
            $license = trim($_POST['license'] ?? 'Commercial');
            $file_size = trim($_POST['file_size'] ?? '120 MB');
            $publisher = trim($_POST['publisher'] ?? 'AK23STUDIOKITS');
            $password_hint = trim($_POST['password_hint'] ?? 'ak23studiokits.com');
            $rating = floatval($_POST['rating'] ?? 4.9);
            $votes = intval($_POST['votes'] ?? 128);
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $features = trim($_POST['features'] ?? '') ?: null;

            // Validate inputs
            $form_errors = [];
            if ($category_id <= 0) {
                $form_errors[] = 'Category is required.';
            }
            if (empty($name)) {
                $form_errors[] = 'Name is required.';
            }
            if (empty($description)) {
                $form_errors[] = 'Description is required.';
            }
            if (!empty($slug)) {
                $query = "SELECT COUNT(*) FROM products WHERE slug = :slug AND deleted_at IS NULL";
                $params = [':slug' => $slug];
                if (isset($_POST['id'])) {
                    $query .= " AND id != :id";
                    $params[':id'] = $_POST['id'];
                }
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                if ($stmt->fetchColumn() > 0) {
                    $form_errors[] = 'Slug must be unique.';
                }
            }
            if ($price <= 0) {
                $form_errors[] = 'Price must be greater than 0.';
            }
            if ($type && !in_array($type, ['upload', 'url'])) {
                $form_errors[] = 'Type must be "upload" or "url".';
            }
            if ($files && !validate_json_field($files, 'files', ['type' => 'string', 'file_type' => 'string', 'value' => 'string'])) {
                $form_errors[] = 'Files must be valid JSON array of {type, file_type, value}.';
            }
            if ($images && !validate_json_field($images, 'images', ['type' => 'string', 'value' => 'string'])) {
                $form_errors[] = 'Images must be valid JSON array of {type, value}.';
            }
            if ($features && !validate_json_field($features, 'features', ['title' => 'string', 'description' => 'string'])) {
                $form_errors[] = 'Features must be valid JSON array of {title, description}.';
            }

            if (empty($form_errors)) {
                if ($action === 'create') {
                    $stmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, price, description, type, files, images, license, file_size, publisher, password_hint, rating, votes, is_featured, is_active, features, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    if ($stmt->execute([$category_id, $name, $slug, $price, $description, $type, $files, $images, $license, $file_size, $publisher, $password_hint, $rating, $votes, $is_featured, $is_active, $features])) {
                        $success = 'Product created successfully.';
                    } else {
                        $error = 'Failed to create product.';
                    }
                } elseif ($action === 'update' && isset($_POST['id'])) {
                    $id = intval($_POST['id']);
                    $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, slug = ?, price = ?, description = ?, type = ?, files = ?, images = ?, license = ?, file_size = ?, publisher = ?, password_hint = ?, rating = ?, votes = ?, is_featured = ?, is_active = ?, features = ?, updated_at = NOW() WHERE id = ? AND deleted_at IS NULL");
                    if ($stmt->execute([$category_id, $name, $slug, $price, $description, $type, $files, $images, $license, $file_size, $publisher, $password_hint, $rating, $votes, $is_featured, $is_active, $features, $id])) {
                        $success = 'Product updated successfully.';
                    } else {
                        $error = 'Failed to update product.';
                    }
                }
            } else {
                $error = implode(' ', $form_errors);
            }
        } elseif (isset($_POST['delete_id'])) {
            $id = intval($_POST['delete_id']);
            $stmt = $pdo->prepare("UPDATE products SET deleted_at = NOW() WHERE id = ?");
            if ($stmt->execute([$id])) {
                $success = 'Product deleted successfully.';
            } else {
                $error = 'Failed to delete product.';
            }
        }
    }

    // Fetch products with pagination
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = 10;
    $offset = ($page - 1) * $perPage;
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.slug, p.price, p.description, p.type, p.files, p.images, p.license, p.file_size, p.publisher, p.password_hint, p.rating, p.votes, p.is_featured, p.is_active, p.features, p.created_at, p.category_id, c.name AS category_name   
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.deleted_at IS NULL
        ORDER BY p.created_at DESC
        LIMIT :offset, :perPage
    ");
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Total products for pagination
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE deleted_at IS NULL");
    $stmt->execute();
    $totalProducts = $stmt->fetchColumn();
    $totalPages = ceil($totalProducts / $perPage);

} catch (Exception $e) {
    $error = 'Database error: ' . $e->getMessage();
}

require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';
?>

<link rel="stylesheet" href="<?php echo $one->assets_folder; ?>/css/4download-style.css">
<!-- SweetAlert2 for delete confirmation -->
<script src="<?php echo $one->assets_folder; ?>/js/plugins/sweetalert2/sweetalert2.min.js"></script>

<!-- Page Content -->
<div class="container dashboard-container">
    <div class="row g-4">
        <!-- Sidebar -->
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

        <!-- Main Content -->
        <div class="col-lg-9">
            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <!-- Products Table -->
            <div class="block block-rounded mb-4 animated fadeIn">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Manage Products</h3>
                    <div class="block-options">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal" data-bs-toggle="tooltip" title="Add New Product">
                            <i class="bi bi-plus"></i> Add Product
                        </button>
                        <button type="button" class="btn btn-sm btn-alt-secondary" id="darkModeToggle" data-bs-toggle="tooltip" title="Toggle Dark Mode">
                            <i class="bi bi-moon"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <?php if (empty($products)): ?>
                        <p class="text-muted">No products found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Type</th>
                                        <th>License</th>
                                        <th>Rating</th>
                                        <th>Featured</th>
                                        <th>Active</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td><?php echo htmlspecialchars($product['category_name'] ?? 'Unknown'); ?></td>
                                            <td>TZS <?php echo number_format($product['price'], 0); ?></td>
                                            <td><?php echo htmlspecialchars($product['type'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($product['license']); ?></td>
                                            <td><?php echo number_format($product['rating'], 1); ?> (<?php echo $product['votes']; ?> votes)</td>
                                            <td>
                                                <span class="badge <?php echo $product['is_featured'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo $product['is_featured'] ? 'Yes' : 'No'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $product['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary me-1" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#updateProductModal"
                                                        data-id="<?php echo $product['id']; ?>"
                                                        data-category-id="<?php echo $product['category_id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                                        data-slug="<?php echo htmlspecialchars($product['slug'] ?? ''); ?>"
                                                        data-price="<?php echo $product['price']; ?>"
                                                        data-description="<?php echo htmlspecialchars($product['description']); ?>"
                                                        data-type="<?php echo htmlspecialchars($product['type'] ?? ''); ?>"
                                                        data-files="<?php echo htmlspecialchars($product['files'] ?? ''); ?>"
                                                        data-images="<?php echo htmlspecialchars($product['images'] ?? ''); ?>"
                                                        data-license="<?php echo htmlspecialchars($product['license']); ?>"
                                                        data-file-size="<?php echo htmlspecialchars($product['file_size']); ?>"
                                                        data-publisher="<?php echo htmlspecialchars($product['publisher']); ?>"
                                                        data-password-hint="<?php echo htmlspecialchars($product['password_hint']); ?>"
                                                        data-rating="<?php echo $product['rating']; ?>"
                                                        data-votes="<?php echo $product['votes']; ?>"
                                                        data-is-featured="<?php echo $product['is_featured']; ?>"
                                                        data-is-active="<?php echo $product['is_active']; ?>"
                                                        data-features="<?php echo htmlspecialchars($product['features'] ?? ''); ?>"
                                                        data-bs-toggle="tooltip" title="Edit Product">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="<?php echo htmlspecialchars($baseUrl); ?>products.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="delete_id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger js-sweetalert2-confirm" 
                                                            data-sweetalert2-title="Are you sure?" 
                                                            data-sweetalert2-text="This product will be deleted!" 
                                                            data-sweetalert2-confirm-button-text="Yes, delete it!" 
                                                            data-bs-toggle="tooltip" title="Delete Product">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <nav class="mt-3">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo $baseUrl; ?>products.php?page=<?php echo $page - 1; ?>">Prev</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo $baseUrl; ?>products.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo $baseUrl; ?>products.php?page=<?php echo $page + 1; ?>">Next</a>
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

<!-- Create Product Modal -->
<div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title" id="createProductModalLabel">Add New Product</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="block-content">
                <form action="<?php echo htmlspecialchars($baseUrl); ?>products.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createCategoryId" class="form-label">Category</label>
                                <select class="form-select" id="createCategoryId" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="createName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createSlug" class="form-label">Slug (optional)</label>
                                <input type="text" class="form-control" id="createSlug" name="slug">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createPrice" class="form-label">Price (TZS)</label>
                                <input type="number" step="0.01" class="form-control" id="createPrice" name="price" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createType" class="form-label">Type</label>
                                <select class="form-select" id="createType" name="type">
                                    <option value="">None</option>
                                    <option value="upload">Upload</option>
                                    <option value="url">URL</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="createDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="createDescription" name="description" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createFiles" class="form-label">Files (JSON, e.g., [{"type":"url","file_type":"zip","value":"http://example.com/file.zip"}])</label>
                                <textarea class="form-control" id="createFiles" name="files" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createImages" class="form-label">Images (JSON, e.g., [{"type":"url","value":"http://example.com/image.jpg"}])</label>
                                <textarea class="form-control" id="createImages" name="images" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createLicense" class="form-label">License</label>
                                <input type="text" class="form-control" id="createLicense" name="license" value="Commercial">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createFileSize" class="form-label">File Size</label>
                                <input type="text" class="form-control" id="createFileSize" name="file_size" value="120 MB">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createPublisher" class="form-label">Publisher</label>
                                <input type="text" class="form-control" id="createPublisher" name="publisher" value="AK23STUDIOKITS">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createPasswordHint" class="form-label">Password Hint</label>
                                <input type="text" class="form-control" id="createPasswordHint" name="password_hint" value="ak23studiokits.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createRating" class="form-label">Rating</label>
                                <input type="number" step="0.1" class="form-control" id="createRating" name="rating" value="4.9">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createVotes" class="form-label">Votes</label>
                                <input type="number" class="form-control" id="createVotes" name="votes" value="128">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Featured</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="createIsFeatured" name="is_featured">
                                    <label class="form-check-label" for="createIsFeatured">Is Featured</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Active</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="createIsActive" name="is_active" checked>
                                    <label class="form-check-label" for="createIsActive">Is Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="createFeatures" class="form-label">Features (JSON, e.g., [{"title":"High Quality","description":"High resolution audio"}])</label>
                                <textarea class="form-control" id="createFeatures" name="features" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Create Product</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Update Product Modal -->
<div class="modal fade" id="updateProductModal" tabindex="-1" aria-labelledby="updateProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title" id="updateProductModalLabel">Edit Product</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="block-content">
                <form action="<?php echo htmlspecialchars($baseUrl); ?>products.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="updateId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateCategoryId" class="form-label">Category</label>
                                <select class="form-select" id="updateCategoryId" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="updateName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateSlug" class="form-label">Slug (optional)</label>
                                <input type="text" class="form-control" id="updateSlug" name="slug">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updatePrice" class="form-label">Price (TZS)</label>
                                <input type="number" step="0.01" class="form-control" id="updatePrice" name="price" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateType" class="form-label">Type</label>
                                <select class="form-select" id="updateType" name="type">
                                    <option value="">None</option>
                                    <option value="upload">Upload</option>
                                    <option value="url">URL</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="updateDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="updateDescription" name="description" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateFiles" class="form-label">Files (JSON, e.g., [{"type":"url","file_type":"zip","value":"http://example.com/file.zip"}])</label>
                                <textarea class="form-control" id="updateFiles" name="files" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateImages" class="form-label">Images (JSON, e.g., [{"type":"url","value":"http://example.com/image.jpg"}])</label>
                                <textarea class="form-control" id="updateImages" name="images" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateLicense" class="form-label">License</label>
                                <input type="text" class="form-control" id="updateLicense" name="license">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateFileSize" class="form-label">File Size</label>
                                <input type="text" class="form-control" id="updateFileSize" name="file_size">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updatePublisher" class="form-label">Publisher</label>
                                <input type="text" class="form-control" id="updatePublisher" name="publisher">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updatePasswordHint" class="form-label">Password Hint</label>
                                <input type="text" class="form-control" id="updatePasswordHint" name="password_hint">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateRating" class="form-label">Rating</label>
                                <input type="number" step="0.1" class="form-control" id="updateRating" name="rating">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateVotes" class="form-label">Votes</label>
                                <input type="number" class="form-control" id="updateVotes" name="votes">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Featured</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="updateIsFeatured" name="is_featured">
                                    <label class="form-check-label" for="updateIsFeatured">Is Featured</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Active</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="updateIsActive" name="is_active">
                                    <label class="form-check-label" for="updateIsActive">Is Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="updateFeatures" class="form-label">Features (JSON, e.g., [{"title":"High Quality","description":"High resolution audio"}])</label>
                                <textarea class="form-control" id="updateFeatures" name="features" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Product</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Modals, Tooltips, and Dark Mode -->
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
    var updateModal = document.getElementById('updateProductModal');
    updateModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var categoryId = button.getAttribute('data-category-id');
        var name = button.getAttribute('data-name');
        var slug = button.getAttribute('data-slug');
        var price = button.getAttribute('data-price');
        var description = button.getAttribute('data-description');
        var type = button.getAttribute('data-type');
        var files = button.getAttribute('data-files');
        var images = button.getAttribute('data-images');
        var license = button.getAttribute('data-license');
        var fileSize = button.getAttribute('data-file-size');
        var publisher = button.getAttribute('data-publisher');
        var passwordHint = button.getAttribute('data-password-hint');
        var rating = button.getAttribute('data-rating');
        var votes = button.getAttribute('data-votes');
        var isFeatured = button.getAttribute('data-is-featured') === '1';
        var isActive = button.getAttribute('data-is-active') === '1';
        var features = button.getAttribute('data-features');

        var modal = this;
        modal.querySelector('#updateId').value = id;
        modal.querySelector('#updateCategoryId').value = categoryId;
        modal.querySelector('#updateName').value = name;
        modal.querySelector('#updateSlug').value = slug;
        modal.querySelector('#updatePrice').value = price;
        modal.querySelector('#updateDescription').value = description;
        modal.querySelector('#updateType').value = type;
        modal.querySelector('#updateFiles').value = files;
        modal.querySelector('#updateImages').value = images;
        modal.querySelector('#updateLicense').value = license;
        modal.querySelector('#updateFileSize').value = fileSize;
        modal.querySelector('#updatePublisher').value = publisher;
        modal.querySelector('#updatePasswordHint').value = passwordHint;
        modal.querySelector('#updateRating').value = rating;
        modal.querySelector('#updateVotes').value = votes;
        modal.querySelector('#updateIsFeatured').checked = isFeatured;
        modal.querySelector('#updateIsActive').checked = isActive;
        modal.querySelector('#updateFeatures').value = features;
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
});
</script>

<?php
require 'inc/_global/views/page_end.php';
require 'inc/_global/views/footer_start.php';
require 'inc/_global/views/footer_end.php';
?>