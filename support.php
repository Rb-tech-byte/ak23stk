<?php
session_start();
require_once __DIR__ . '/database/db_config.php';
require 'inc/_global/config.php';
require 'inc/backend/config.php';
require 'inc/_global/views/head_start.php';
require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';

// Handle reply
if (isset($_POST['reply_ticket'])) {
    $ticket_id = (int)$_POST['ticket_id'];
    $reply = trim($_POST['reply']);
    $stmt = $pdo->prepare("INSERT INTO ticket_replies (ticket_id, reply, replied_by, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$ticket_id, $reply, $_SESSION['user_id'] ?? 0]);
    $pdo->prepare("UPDATE tickets SET status='Closed' WHERE id=?")->execute([$ticket_id]);
    header('Location: support.php?msg=replied');
    exit;
}
// Update ticket status
if (isset($_GET['close'])) {
    $id = (int)$_GET['close'];
    $pdo->prepare("UPDATE tickets SET status='Closed' WHERE id=?")->execute([$id]);
    header('Location: support.php?msg=closed');
    exit;
}
// List tickets
$stmt = $pdo->query("SELECT t.*, u.name as user_name FROM tickets t LEFT JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
$tickets = $stmt->fetchAll();


?>
<div class="content">
    <h2 class="content-heading">Support Tickets</h2>
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Tickets List</h3>
        </div>
        <div class="block-content">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($tickets as $ticket): ?>
                    <tr>
                        <td><?php echo $ticket['id']; ?></td>
                        <td><?php echo htmlspecialchars($ticket['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                        <td><?php echo $ticket['created_at']; ?></td>
                        <td>
                            <form method="post" style="display:inline-block">
                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                <input type="text" name="reply" placeholder="Reply..." class="form-control" required>
                                <button type="submit" name="reply_ticket" class="btn btn-sm btn-info">Reply & Close</button>
                            </form>
                            <?php if($ticket['status'] != 'Closed'): ?>
                                <a href="support.php?close=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-warning">Close</a>
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
include $one->inc_footer;
?>
