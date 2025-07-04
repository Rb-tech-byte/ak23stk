<?php
session_start();
require 'inc/_global/config.php';
require 'inc/_global/views/head_start.php';
require_once __DIR__ . '/database/db_config.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'requests.php';
    header('Location: login.php');
    exit();
}

$pageTitle = 'Product Requests';
$userId = $_SESSION['user_id'];
$message = '';

// Handle new request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $categoryId = intval($_POST['category_id']);
    
    if (empty($title) || empty($description)) {
        $message = '<div class="alert alert-danger">Please fill in all required fields.</div>';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO product_requests (user_id, title, description, category_id, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->execute([$userId, $title, $description, $categoryId]);
            
            $message = '<div class="alert alert-success">Your request has been submitted successfully!</div>';
            
            // Clear form
            $_POST = [];
        } catch (PDOException $e) {
            error_log("Error submitting request: " . $e->getMessage());
            $message = '<div class="alert alert-danger">An error occurred while submitting your request. Please try again.</div>';
        }
    }
}

// Get user's requests
try {
    $stmt = $pdo->prepare("SELECT pr.*, c.name as category_name, 
                          CASE 
                              WHEN pr.status = 'completed' THEN 'success'
                              WHEN pr.status = 'rejected' THEN 'danger'
                              ELSE 'info'
                          END as status_class
                          FROM product_requests pr 
                          LEFT JOIN categories c ON pr.category_id = c.id 
                          WHERE pr.user_id = ? 
                          ORDER BY pr.created_at DESC");
    $stmt->execute([$userId]);
    $userRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get categories for dropdown
    $categoriesStmt = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC");
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $userRequests = [];
    $categories = [];
    $message = '<div class="alert alert-danger">Error loading requests. Please try again later.</div>';
}

require 'includes/header.php';
?>

<!-- Page Content -->
<div class="content">
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title">Request a Product</h3>
        </div>
        <div class="block-content">
            <?php echo $message; ?>
            
            <form action="requests.php" method="POST" onsubmit="return validateRequestForm()">
                <div class="row push">
                    <div class="col-lg-8">
                        <div class="mb-4">
                            <label class="form-label" for="title">Product Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label" for="category_id">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label" for="description">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="6" placeholder="Please provide as much detail as possible about the product you're looking for..." required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            <div class="form-text">
                                Be specific about what you're looking for. Include version numbers, features, or any other relevant details.
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <button type="submit" name="submit_request" class="btn btn-primary">
                                <i class="fa fa-paper-plane me-1"></i> Submit Request
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="alert alert-info">
                            <h4 class="alert-heading">Tips for better requests</h4>
                            <ul class="mb-0">
                                <li>Be specific about the product name and version</li>
                                <li>Mention any specific features you need</li>
                                <li>Include links to the product if possible</li>
                                <li>Check if the product already exists before requesting</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <?php if (!empty($userRequests)): ?>
        <div class="block block-rounded">
            <div class="block-header">
                <h3 class="block-title">My Requests</h3>
            </div>
            <div class="block-content">
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Date Requested</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userRequests as $request): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($request['title']); ?></strong>
                                        <?php if (!empty($request['admin_comment'])): ?>
                                            <div class="text-muted mt-1">
                                                <small><strong>Admin:</strong> <?php echo htmlspecialchars($request['admin_comment']); ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($request['category_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($request['created_at'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $request['status_class']; ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-toggle="modal" data-bs-target="#requestDetailsModal<?php echo $request['id']; ?>">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Request Details Modal -->
                                        <div class="modal" id="requestDetailsModal<?php echo $request['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="requestDetailsModalLabel<?php echo $request['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="block block-rounded block-transparent mb-0">
                                                        <div class="block-header bg-primary">
                                                            <h3 class="block-title text-white">Request Details</h3>
                                                            <div class="block-options">
                                                                <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                                                    <i class="fa fa-fw fa-times text-white"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="block-content">
                                                            <div class="mb-4">
                                                                <h4><?php echo htmlspecialchars($request['title']); ?></h4>
                                                                <p class="text-muted">
                                                                    Requested on <?php echo date('F j, Y \a\t g:i A', strtotime($request['created_at'])); ?>
                                                                    <span class="badge bg-<?php echo $request['status_class']; ?> ms-2">
                                                                        <?php echo ucfirst($request['status']); ?>
                                                                    </span>
                                                                </p>
                                                            </div>
                                                            
                                                            <div class="mb-4">
                                                                <h5>Description</h5>
                                                                <p><?php echo nl2br(htmlspecialchars($request['description'])); ?></p>
                                                            </div>
                                                            
                                                            <?php if (!empty($request['admin_comment'])): ?>
                                                                <div class="alert alert-info">
                                                                    <h5>Admin Response</h5>
                                                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($request['admin_comment'])); ?></p>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="block-content block-content-full block-content-sm bg-body-light text-end">
                                                            <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-dismiss="modal">Close</button>
                                                            <?php if ($request['status'] === 'pending'): ?>
                                                                <button type="button" class="btn btn-sm btn-danger" onclick="if(confirm('Are you sure you want to cancel this request?')) window.location.href='cancel_request.php?id=<?php echo $request['id']; ?>'">
                                                                    <i class="fa fa-times me-1"></i> Cancel Request
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- END Request Details Modal -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<!-- END Page Content -->

<script>
function validateRequestForm() {
    const title = document.getElementById('title').value.trim();
    const category = document.getElementById('category_id').value;
    const description = document.getElementById('description').value.trim();
    
    if (title.length < 5) {
        alert('Please enter a more descriptive title (at least 5 characters)');
        return false;
    }
    
    if (!category) {
        alert('Please select a category');
        return false;
    }
    
    if (description.length < 20) {
        alert('Please provide a more detailed description (at least 20 characters)');
        return false;
    }
    
    return true;
}
</script>

<?php 
require 'includes/footer.php';
require 'inc/_global/views/page_end.php';
?>
