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
$payments = [];
$users = [];
$orders = [];

try {
    // Fetch users for form dropdown
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE deleted_at IS NULL");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch orders for form dropdown
    $stmt = $pdo->prepare("SELECT id, user_id FROM orders WHERE deleted_at IS NULL");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle Create/Update/Delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            $user_id = intval($_POST['user_id'] ?? 0);
            $order_id = intval($_POST['order_id'] ?? 0);
            $amount = floatval($_POST['amount'] ?? 0);
            $payment_method = trim($_POST['payment_method'] ?? '');
            $status = trim($_POST['status'] ?? '');
            $transaction_id = trim($_POST['transaction_id'] ?? '') ?: null;
            $payment_details = trim($_POST['payment_details'] ?? '') ?: null;

            // Validate inputs
            $form_errors = [];
            if ($user_id <= 0) {
                $form_errors[] = 'User is required.';
            }
            if ($order_id <= 0) {
                $form_errors[] = 'Order is required.';
            }
            if ($amount <= 0) {
                $form_errors[] = 'Amount must be greater than 0.';
            }
            if (!in_array($payment_method, ['pesapal', 'TigoPesa', 'AirtelMoney', 'Mpesa', 'Cash', 'Visa'])) {
                $form_errors[] = 'Invalid payment method.';
            }
            if (!in_array($status, ['pending', 'completed', 'failed'])) {
                $form_errors[] = 'Invalid status.';
            }
            if ($transaction_id) {
                $query = "SELECT COUNT(*) FROM payments WHERE transaction_id = :transaction_id AND deleted_at IS NULL";
                $params = [':transaction_id' => $transaction_id];
                if (isset($_POST['id'])) {
                    $query .= " AND id != :id";
                    $params[':id'] = $_POST['id'];
                }
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                if ($stmt->fetchColumn() > 0) {
                    $form_errors[] = 'Transaction ID must be unique.';
                }
            }
            if ($payment_details) {
                if (!json_decode($payment_details)) {
                    $form_errors[] = 'Payment details must be valid JSON.';
                }
            }

            if (empty($form_errors)) {
                if ($action === 'create') {
                    $stmt = $pdo->prepare("INSERT INTO payments (user_id, order_id, amount, payment_method, status, transaction_id, payment_details, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    if (!$stmt) {
                        $error = 'Failed to prepare create query: ' . implode(' ', $pdo->errorInfo());
                    } else {
                        if ($stmt->execute([$user_id, $order_id, $amount, $payment_method, $status, $transaction_id, $payment_details])) {
                            $success = 'Payment created successfully.';
                        } else {
                            $error = 'Failed to create payment: ' . implode(' ', $stmt->errorInfo());
                        }
                    }
                } elseif ($action === 'update' && isset($_POST['id'])) {
                    $id = intval($_POST['id']);
                    $stmt = $pdo->prepare("UPDATE payments SET user_id = ?, order_id = ?, amount = ?, payment_method = ?, status = ?, transaction_id = ?, payment_details = ?, updated_at = NOW() WHERE id = ? AND deleted_at IS NULL");
                    if (!$stmt) {
                        $error = 'Failed to prepare update query: ' . implode(' ', $pdo->errorInfo());
                    } else {
                        if ($stmt->execute([$user_id, $order_id, $amount, $payment_method, $status, $transaction_id, $payment_details, $id])) {
                            $success = 'Payment updated successfully.';
                        } else {
                            $error = 'Failed to update payment: ' . implode(' ', $stmt->errorInfo());
                        }
                    }
                }
            } else {
                $error = implode(' ', $form_errors);
            }
        } elseif (isset($_POST['delete_id'])) {
            $id = intval($_POST['delete_id']);
            $stmt = $pdo->prepare("UPDATE payments SET deleted_at = NOW() WHERE id = ? AND deleted_at IS NULL");
            if (!$stmt) {
                $error = 'Failed to prepare delete query: ' . implode(' ', $pdo->errorInfo());
            } else {
                if ($stmt->execute([$id])) {
                    $success = 'Payment deleted successfully.';
                } else {
                    $error = 'Failed to delete payment: ' . implode(' ', $stmt->errorInfo());
                }
            }
        }
    }

    // Fetch payments with pagination
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = 10;
    $offset = ($page - 1) * $perPage;
    $stmt = $pdo->prepare("
        SELECT p.id, p.user_id, p.order_id, p.amount, p.payment_method, p.status, p.transaction_id, p.payment_details, p.created_at, u.name AS user_name
        FROM payments p
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.deleted_at IS NULL
        ORDER BY p.created_at DESC
        LIMIT :offset, :perPage
    ");
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Total payments for pagination
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE deleted_at IS NULL");
    $stmt->execute();
    $totalPayments = $stmt->fetchColumn();
    $totalPages = ceil($totalPayments / $perPage);

} catch (Exception $e) {
    $error = 'Database error: ' . $e->getMessage();
}

require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';
?>

<link rel="stylesheet" href="<?php echo $one->assets_folder; ?>/css/4download-style.css">
<script src="<?php echo $one->assets_folder; ?>/js/plugins/sweetalert2/sweetalert2.min.js"></script>

<div class="container dashboard-container">
    <div class="row g-4">
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

        <div class="col-lg-9">
            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div class="block block-rounded mb-4 animated fadeIn">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Manage Payments</h3>
                    <div class="block-options">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createPaymentModal" data-bs-toggle="tooltip" title="Add New Payment">
                            <i class="bi bi-plus"></i> Add Payment
                        </button>
                        <button type="button" class="btn btn-sm btn-alt-secondary" id="darkModeToggle" data-bs-toggle="tooltip" title="Toggle Dark Mode">
                            <i class="bi bi-moon"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <?php if (empty($payments)): ?>
                        <p class="text-muted">No payments found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Order ID</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Transaction ID</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($payment['id']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['user_name'] ?? 'Unknown'); ?></td>
                                            <td><?php echo htmlspecialchars($payment['order_id']); ?></td>
                                            <td><?php echo number_format($payment['amount'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['status'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($payment['transaction_id'] ?? 'N/A'); ?></td>
                                            <td><?php echo !empty($payment['created_at']) ? date('M d, Y', strtotime($payment['created_at'])) : 'N/A'; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary me-1" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#updatePaymentModal"
                                                        data-id="<?php echo $payment['id']; ?>"
                                                        data-user-id="<?php echo $payment['user_id']; ?>"
                                                        data-order-id="<?php echo $payment['order_id']; ?>"
                                                        data-amount="<?php echo $payment['amount']; ?>"
                                                        data-payment-method="<?php echo htmlspecialchars($payment['payment_method']); ?>"
                                                        data-status="<?php echo htmlspecialchars($payment['status'] ?? ''); ?>"
                                                        data-transaction-id="<?php echo htmlspecialchars($payment['transaction_id'] ?? ''); ?>"
                                                        data-payment-details="<?php echo htmlspecialchars($payment['payment_details'] ?? ''); ?>"
                                                        data-bs-toggle="tooltip" title="Edit Payment">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="<?php echo htmlspecialchars($baseUrl); ?>payments.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="delete_id" value="<?php echo $payment['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger js-sweetalert2-confirm" 
                                                            data-sweetalert2-title="Are you sure?" 
                                                            data-sweetalert2-text="This payment will be deleted!" 
                                                            data-sweetalert2-confirm-button-text="Yes, delete it!" 
                                                            data-bs-toggle="tooltip" title="Delete Payment">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <nav class="mt-3">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo $baseUrl; ?>payments.php?page=<?php echo $page - 1; ?>">Prev</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo $baseUrl; ?>payments.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo $baseUrl; ?>payments.php?page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-text">
        <strong>AK23StudioKits</strong> © <span data-toggle="year-copy"><?php echo date('Y'); ?></span>
    </div>
</div>

<div class="modal fade" id="createPaymentModal" tabindex="-1" aria-labelledby="createPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title" id="createPaymentModalLabel">Add New Payment</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="block-content">
                <form action="<?php echo htmlspecialchars($baseUrl); ?>payments.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createUserId" class="form-label">User</label>
                                <select class="form-select" id="createUserId" name="user_id" required>
                                    <option value="">Select User</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createOrderId" class="form-label">Order</label>
                                <select class="form-select" id="createOrderId" name="order_id" required>
                                    <option value="">Select Order</option>
                                    <?php foreach ($orders as $order): ?>
                                        <option value="<?php echo $order['id']; ?>">Order #<?php echo $order['id']; ?> (User ID: <?php echo $order['user_id']; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createAmount" class="form-label">Amount</label>
                                <input type="number" step="0.01" class="form-control" id="createAmount" name="amount" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createPaymentMethod" class="form-label">Payment Method</label>
                                <select class="form-select" id="createPaymentMethod" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="pesapal">Pesapal</option>
                                    <option value="TigoPesa">TigoPesa</option>
                                    <option value="AirtelMoney">AirtelMoney</option>
                                    <option value="Mpesa">Mpesa</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Visa">Visa</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createStatus" class="form-label">Status</label>
                                <select class="form-select" id="createStatus" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createTransactionId" class="form-label">Transaction ID (optional)</label>
                                <input type="text" class="form-control" id="createTransactionId" name="transaction_id" placeholder="e.g., TXN001">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createPaymentDetails" class="form-label">Payment Details (JSON, optional)</label>
                                <textarea class="form-control" id="createPaymentDetails" name="payment_details" placeholder='e.g., {"card_type": "Visa", "last_four": "1234"}'></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Create Payment</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updatePaymentModal" tabindex="-1" aria-labelledby="updatePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title" id="updatePaymentModalLabel">Edit Payment</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="block-content">
                <form action="<?php echo htmlspecialchars($baseUrl); ?>payments.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="updateId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateUserId" class="form-label">User</label>
                                <select class="form-select" id="updateUserId" name="user_id" required>
                                    <option value="">Select User</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateOrderId" class="form-label">Order</label>
                                <select class="form-select" id="updateOrderId" name="order_id" required>
                                    <option value="">Select Order</option>
                                    <?php foreach ($orders as $order): ?>
                                        <option value="<?php echo $order['id']; ?>">Order #<?php echo $order['id']; ?> (User ID: <?php echo $order['user_id']; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateAmount" class="form-label">Amount</label>
                                <input type="number" step="0.01" class="form-control" id="updateAmount" name="amount" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updatePaymentMethod" class="form-label">Payment Method</label>
                                <select class="form-select" id="updatePaymentMethod" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="pesapal">Pesapal</option>
                                    <option value="TigoPesa">TigoPesa</option>
                                    <option value="AirtelMoney">AirtelMoney</option>
                                    <option value="Mpesa">Mpesa</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Visa">Visa</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateStatus" class="form-label">Status</label>
                                <select class="form-select" id="updateStatus" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateTransactionId" class="form-label">Transaction ID (optional)</label>
                                <input type="text" class="form-control" id="updateTransactionId" name="transaction_id" placeholder="e.g., TXN001">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updatePaymentDetails" class="form-label">Payment Details (JSON, optional)</label>
                                <textarea class="form-control" id="updatePaymentDetails" name="payment_details" placeholder='e.g., {"card_type": "Visa", "last_four": "1234"}'></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Payment</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
    var updateModal = document.getElementById('updatePaymentModal');
    updateModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var userId = button.getAttribute('data-user-id');
        var orderId = button.getAttribute('data-order-id');
        var amount = button.getAttribute('data-amount');
        var paymentMethod = button.getAttribute('data-payment-method');
        var status = button.getAttribute('data-status');
        var transactionId = button.getAttribute('data-transaction-id');
        var paymentDetails = button.getAttribute('data-payment-details');

        var modal = this;
        modal.querySelector('#updateId').value = id;
        modal.querySelector('#updateUserId').value = userId;
        modal.querySelector('#updateOrderId').value = orderId;
        modal.querySelector('#updateAmount').value = amount;
        modal.querySelector('#updatePaymentMethod').value = paymentMethod;
        modal.querySelector('#updateStatus').value = status;
        modal.querySelector('#updateTransactionId').value = transactionId;
        modal.querySelector('#updatePaymentDetails').value = paymentDetails;
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