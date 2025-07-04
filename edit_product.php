<?php
session_start();
require_once __DIR__ . '/database/db_config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$product = null;
$categories = [];

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->query("SELECT id, name FROM categories WHERE deleted_at IS NULL");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error fetching data: " . $e->getMessage();
    }
}

if (!$product) {
    $_SESSION['error_message'] = "Product not found or has been deleted.";
    header('Location: admin_dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $slug = filter_input(INPUT_POST, 'slug', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $stock_quantity = filter_input(INPUT_POST, 'stock_quantity', FILTER_VALIDATE_INT);
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    
    // Handle file upload
    $target_file = $product['image_url'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/products/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error_message'] = "File is not an image.";
        } elseif ($_FILES["image"]["size"] > 5000000) { // 5MB max
            $_SESSION['error_message'] = "Sorry, your file is too large.";
        } elseif (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            $_SESSION['error_message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // File uploaded successfully
        } else {
            $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
        }
    }
    
    if ($name && $slug && $price && $stock_quantity !== false && $category_id && empty($_SESSION['error_message'])) {
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, slug = ?, description = ?, price = ?, stock_quantity = ?, category_id = ?, image_url = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $price, $stock_quantity, $category_id, $target_file, $id]);
            $_SESSION['success_message'] = "Product updated successfully.";
            header('Location: product_details.php?slug=' . $slug);
            exit;
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error updating product: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = $_SESSION['error_message'] ?? "Please fill in all required fields with valid data.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
    if (!isset($one)) {
        $one = new stdClass();
        $one->assets_folder = 'assets';
        $one->theme = 'default';
    }
    include 'inc/_global/views/head_end.php'; 
    ?>
    <title>Edit Product - Admin Dashboard</title>
</head>
<body>
    <?php include 'sidebar_start.php'; ?>
    
    <div class="content">
        <h1>Edit Product</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Product: <?php echo htmlspecialchars($product['name']); ?></h6>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="edit_product.php?id=<?php echo $product['id']; ?>" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="slug">Slug (URL-friendly name)</label>
                        <input type="text" name="slug" id="slug" class="form-control" value="<?php echo htmlspecialchars($product['slug']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price ($)</label>
                        <input type="number" name="price" id="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Product Image (leave blank to keep current)</label>
                        <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
                        <?php if ($product['image_url']): ?>
                            <div class="mt-2">
                                <small>Current image: <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current product image" style="max-height: 100px;"></small>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Update Product</button>
                    <a href="product_details.php?slug=<?php echo $product['slug']; ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'sidebar_end.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');
            
            nameInput.addEventListener('input', function() {
                if (!slugInput.value || slugInput.value === slugify(nameInput.value)) {
                    slugInput.value = slugify(nameInput.value);
                }
            });
            
            function slugify(text) {
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-')           // Replace spaces with -
                    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                    .replace(/^-+/, '')              // Trim - from start of text
                    .replace(/-+$/, '');             // Trim - from end of text
            }
        });
    </script>
</body>
</html>
