<?php
require_once 'database/db_config.php';


if (isset($_POST['upload_logo'])) {
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array(strtolower($ext), $allowed)) {
            $msg = 'Invalid file type.';
        } else {
            $file_name = 'logo_' . time() . '.' . $ext;
            $upload_dir = __DIR__ . '/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $file_name);
            $stmt = $pdo->prepare("UPDATE settings SET value=? WHERE name='logo'");
            $stmt->execute(['uploads/' . $file_name]);
            $msg = 'Logo updated.';
        }
    } else {
        $msg = 'No file uploaded.';
    }
}
$stmt = $pdo->prepare("SELECT value FROM settings WHERE name='logo'");
$stmt->execute();
$current = $stmt->fetchColumn();

require_once __DIR__ . '/inc/_global/config.php';
require_once __DIR__ . '/inc/backend/config.php';
include __DIR__ . '/inc/_global/views/head_start.php';
include __DIR__ . '/inc/_global/views/head_end.php';
include __DIR__ . '/inc/_global/views/page_start.php';
?>
<div class="content">
    <h2 class="content-heading">Update Logo</h2>
    <?php if(isset($msg)): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <div class="block">
        <div class="block-header block-header-default"><h3 class="block-title">Logo</h3></div>
        <div class="block-content">
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="logo" accept="image/*" required>
                <button type="submit" name="upload_logo" class="btn btn-primary">Upload</button>
            </form>
            <?php if($current): ?>
                <div class="mt-3"><img src="<?php echo $current; ?>" height="80"></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
include __DIR__ . '/inc/_global/views/page_end.php';
?>
