<?php
session_start();
require_once __DIR__ . '/database/db_config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id) {
    try {
        $stmt = $pdo->prepare("UPDATE payments SET deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success_message'] = "Payment deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error deleting payment: " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "Invalid payment ID.";
}

// Note: If this file needs to render HTML, ensure it includes 'inc/_global/views/head_end.php' for layout consistency.

header('Location: admin_dashboard.php');
exit;
?>
