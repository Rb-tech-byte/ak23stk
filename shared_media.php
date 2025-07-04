<?php
session_start();
require_once __DIR__ . '/database/db_config.php';
require 'inc/_global/config.php';
require 'inc/backend/config.php';
require 'inc/_global/views/head_start.php';

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$baseUrl = rtrim($baseUrl, '/') . '/';

$error = '';
$media = null;

if (isset($_GET['id']) && isset($_GET['token'])) {
    $link_id = intval($_GET['id']);
    $token = trim($_GET['token']);
    
    $mysqli = get_db_connection();
    if ($mysqli->connect_errno) {
        $error = 'Database connection failed!';
    } else {
        $stmt = $mysqli->prepare("\n            SELECT msl.media_id, msl.token, msl.expires_at, m.value, m.file_type, p.name as product_name\n            FROM media_share_links msl\n            JOIN medias m ON msl.media_id = m.id\n            JOIN products p ON m.products_id = p.id\n            WHERE msl.id = ? AND m.deleted_at IS NULL\n        ");
        $stmt->bind_param('i', $link_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row['token'] === $token && strtotime($row['expires_at']) > time()) {
                $media = $row;
            } else {
                $error = 'Invalid or expired link.';
            }
        } else {
            $error = 'Link not found.';
        }
        $stmt->close();
        $mysqli->close();
    }
} else {
    $error = 'Invalid request.';
}

require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';
?>

<div class="container dashboard-container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="block block-rounded mb-4 animated fadeIn">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Shared Media</h3>
                </div>
                <div class="block-content text-center">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php elseif ($media): ?>
                        <h4><?php echo htmlspecialchars($media['product_name']); ?></h4>
                        <p>File Type: <?php echo htmlspecialchars($media['file_type']); ?></p>
                        <a href="<?php echo htmlspecialchars($media['value']); ?>" target="_blank" class="btn btn-primary">Access Media</a>
                        <p class="mt-3 text-muted">This link will expire on <?php echo date('M d, Y', strtotime($media['expires_at'])); ?>.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require 'inc/_global/views/page_end.php';
require 'inc/_global/views/footer_start.php';
require 'inc/_global/views/footer_end.php';
?>
