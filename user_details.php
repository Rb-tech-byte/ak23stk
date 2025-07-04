<?php
session_start();
require_once __DIR__ . '/database/db_config.php';
require 'inc/_global/config.php';
require 'inc/backend/config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$user = null;

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = ? AND u.deleted_at IS NULL");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error fetching user details: " . $e->getMessage();
    }
}

if (!$user) {
    $_SESSION['error_message'] = "User not found or has been deleted.";
    header('Location: admin_dashboard.php');
    exit;
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
    
// --- Layout and Data Fetch ---
require 'inc/_global/views/head_start.php';
require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';

    include 'inc/_global/views/head_end.php'; 
    ?>
    <title>User Details - Admin Dashboard</title>
</head>
<body>
    <?php include 'sidebar_start.php'; ?>
    
    <div class="content">
        <h1>User Details</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">User: <?php echo htmlspecialchars($user['name']); ?></h6>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>Personal Information</h5>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name'] ?? 'N/A'); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></p>
                        <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role_name'] ?? 'N/A'); ?></p>
                        <p><strong>Is Admin:</strong> <?php echo $user['is_admin'] ? 'Yes' : 'No'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Address Information</h5>
                        <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($user['address'] ?? 'N/A')); ?></p>
                        <p><strong>City:</strong> <?php echo htmlspecialchars($user['city'] ?? 'N/A'); ?></p>
                        <p><strong>State:</strong> <?php echo htmlspecialchars($user['state'] ?? 'N/A'); ?></p>
                        <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($user['postal_code'] ?? 'N/A'); ?></p>
                        <p><strong>Country:</strong> <?php echo htmlspecialchars($user['country'] ?? 'N/A'); ?></p>
                    </div>
                </div>
                
                <hr>
                
                <h5>Account Information</h5>
                <p><strong>Created At:</strong> <?php echo htmlspecialchars($user['created_at'] ?? 'N/A'); ?></p>
                <p><strong>Updated At:</strong> <?php echo htmlspecialchars($user['updated_at'] ?? 'N/A'); ?></p>
                <p><strong>Email Verified At:</strong> <?php echo htmlspecialchars($user['email_verified_at'] ?? 'Not verified'); ?></p>
                
                <div class="mt-3">
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning" data-toggle="tooltip" title="Edit User"><i class="bi bi-pencil"></i> Edit</a>
                    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'sidebar_end.php'; ?>
</body>
</html>
