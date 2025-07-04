<?php
session_start();
require_once __DIR__ . '/database/db_config.php';
require 'inc/_global/config.php';
require 'inc/backend/config.php';

// Ensure is_admin is set
if (!isset($_SESSION['is_admin'])) $_SESSION['is_admin'] = 0;

// --- CUD Logic (before any output) ---
if (isset($_POST['add_category'])) {
    if ($_SESSION['is_admin'] != 1) die('Unauthorized: Only admins can add categories.');
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description = trim($_POST['description']);
    $is_active = isset($_POST['status']) ? 1 : 0;
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->execute([$name, $slug, $description, $is_active]);
    header('Location: categories.php?msg=added');
    exit;
}
if (isset($_POST['update_category'])) {
    if ($_SESSION['is_admin'] != 1) die('Unauthorized: Only admins can update categories.');
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description = trim($_POST['description']);
    $is_active = isset($_POST['status']) ? 1 : 0;
    global $pdo;
    $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=?, description=?, is_active=?, updated_at=NOW() WHERE id=?");
    $stmt->execute([$name, $slug, $description, $is_active, $id]);
    header('Location: categories.php?msg=updated');
    exit;
}
if (isset($_GET['delete'])) {
    if ($_SESSION['is_admin'] != 1) die('Unauthorized: Only admins can delete categories.');
    $id = (int)$_GET['delete'];
    global $pdo;
    $stmt = $pdo->prepare("UPDATE categories SET deleted_at=NOW() WHERE id=?");
    $stmt->execute([$id]);
    header('Location: categories.php?msg=deleted');
    exit;
}

// --- Layout and Data Fetch ---
require 'inc/_global/views/head_start.php';
require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';

// Fetch all non-deleted categories
global $pdo;
$stmt = $pdo->prepare("SELECT * FROM categories WHERE deleted_at IS NULL ORDER BY created_at DESC");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="content">
    <h2 class="content-heading">Category Management</h2>
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <?php if ($_SESSION['is_admin'] == 1): ?>
    <!-- Add/Edit Category Form -->
    <div class="block mb-4">
        <div class="block-header block-header-default">
            <h3 class="block-title" id="form-title">Add New Category</h3>
        </div>
        <div class="block-content">
            <form method="post" id="category-form">
                <input type="hidden" name="id" id="edit_id" value="">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="edit_slug" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label><br>
                        <input type="checkbox" name="status" id="edit_status" value="1" checked> Active
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-10">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control"></textarea>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" name="add_category" class="btn btn-primary" id="add_btn">Add Category</button>
                        <button type="submit" name="update_category" class="btn btn-success d-none" id="update_btn">Update</button>
                        <button type="button" class="btn btn-secondary d-none" id="cancel_btn">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Category List Table -->
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Categories List</h3>
        </div>
        <div class="block-content">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <?php if ($_SESSION['is_admin'] == 1): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $cat): ?>
                            <tr>
                                <td><?php echo $cat['id']; ?></td>
                                <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                                <td><?php echo htmlspecialchars($cat['description']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $cat['is_active'] ? 'success' : 'danger'; ?>">
                                        <?php echo $cat['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo $cat['created_at']; ?></td>
                                <?php if ($_SESSION['is_admin'] == 1): ?>
                                <td>
                                    <button class="btn btn-sm btn-info edit-btn"
                                        data-id="<?php echo $cat['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($cat['name']); ?>"
                                        data-slug="<?php echo htmlspecialchars($cat['slug']); ?>"
                                        data-description="<?php echo htmlspecialchars($cat['description']); ?>"
                                        data-status="<?php echo isset($cat['is_active']) ? $cat['is_active'] : 0; ?>"
                                    >Edit</button>
                                    <a href="categories.php?delete=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</a>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($_SESSION['is_admin'] == 1): ?>
<script>
const editBtns = document.querySelectorAll('.edit-btn');
const addBtn = document.getElementById('add_btn');
const updateBtn = document.getElementById('update_btn');
const cancelBtn = document.getElementById('cancel_btn');
const formTitle = document.getElementById('form-title');
const form = document.getElementById('category-form');

editBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('edit_name').value = this.dataset.name;
        document.getElementById('edit_slug').value = this.dataset.slug;
        document.getElementById('edit_description').value = this.dataset.description;
        document.getElementById('edit_status').checked = this.dataset.status == '1';
        addBtn.classList.add('d-none');
        updateBtn.classList.remove('d-none');
        cancelBtn.classList.remove('d-none');
        formTitle.textContent = "Edit Category";
    });
});
cancelBtn.addEventListener('click', function() {
    document.getElementById('edit_id').value = '';
    document.getElementById('edit_name').value = '';
    document.getElementById('edit_slug').value = '';
    document.getElementById('edit_description').value = '';
    document.getElementById('edit_status').checked = true;
    addBtn.classList.remove('d-none');
    updateBtn.classList.add('d-none');
    cancelBtn.classList.add('d-none');
    formTitle.textContent = "Add New Category";
});
</script>
<?php endif; ?>