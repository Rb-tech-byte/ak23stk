<?php
session_start();
require_once __DIR__ . '/database/db_config.php';
require 'inc/_global/config.php';
require 'inc/backend/config.php';
require 'inc/_global/views/head_start.php';
require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';


// Block/Suspend user
if (isset($_GET['block'])) {
    $id = (int)$_GET['block'];
    $stmt = $pdo->prepare("UPDATE users SET status=0 WHERE id=?");
    $stmt->execute([$id]);
    header('Location: users.php?msg=blocked');
    exit;
}
// Unblock user
if (isset($_GET['unblock'])) {
    $id = (int)$_GET['unblock'];
    $stmt = $pdo->prepare("UPDATE users SET status=1 WHERE id=?");
    $stmt->execute([$id]);
    header('Location: users.php?msg=unblocked');
    exit;
}
// List users
$stmt = $pdo->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC");
$users = $stmt->fetchAll();


?>

<div class="content">
    <h2 class="content-heading">Users Management</h2>
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Users List</h3>
        </div>
        <div class="block-content">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                        <td>
    <?php
    if (!isset($user['status'])) {
        echo '<span class="badge bg-secondary">Unknown</span>';
    } else {
        echo $user['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Blocked</span>';
    }
    ?>
</td>
<td><?php echo $user['created_at']; ?></td>
<td>
    <?php if(isset($user['status']) && $user['status']): ?>
        <a href="users.php?block=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Block</a>
    <?php elseif(isset($user['status'])): ?>
        <a href="users.php?unblock=<?php echo $user['id']; ?>" class="btn btn-sm btn-success">Unblock</a>
    <?php else: ?>
        <span class="text-muted">N/A</span>
    <?php endif; ?>
</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
require 'inc/_global/views/page_end.php';
?>
