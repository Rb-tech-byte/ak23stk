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
$paymentMethods = [];

// Database connection


// Handle Create/Update/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $name = trim($_POST['name'] ?? '');
        $api_key = trim($_POST['api_key'] ?? '');
        $api_secret = trim($_POST['api_secret'] ?? '');
        $merchant_id = trim($_POST['merchant_id'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Validate inputs
        $form_errors = [];
        if (empty($name)) {
            $form_errors[] = 'Name is required.';
        }
        if (empty($api_key) && $name !== 'manual') {
            $form_errors[] = 'API Key is required.';
        }
        if (empty($api_secret) && $name !== 'manual') {
            $form_errors[] = 'API Secret is required.';
        }
        if (empty($merchant_id) && $name !== 'manual') {
            $form_errors[] = 'Merchant ID is required.';
        }

        if (empty($form_errors)) {
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO payment_methods (name, api_key, api_secret, merchant_id, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
                if ($stmt->execute([$name, $api_key, $api_secret, $merchant_id, $is_active])) {
                    $success = 'Payment method created successfully.';
                } else {
                    $error = 'Failed to create payment method.';
                }
            } elseif ($action === 'update' && isset($_POST['id'])) {
                $id = intval($_POST['id']);
                $stmt = $pdo->prepare("UPDATE payment_methods SET name=?, api_key=?, api_secret=?, merchant_id=?, is_active=?, updated_at=NOW() WHERE id=?");
                if ($stmt->execute([$name, $api_key, $api_secret, $merchant_id, $is_active, $id])) {
                    $success = 'Payment method updated successfully.';
                } else {
                    $error = 'Failed to update payment method.';
                }
            }
        } else {
            $error = implode(' ', $form_errors);
        }
    } elseif (isset($_POST['delete_id'])) {
        $id = intval($_POST['delete_id']);
        $stmt = $pdo->prepare("UPDATE payment_methods SET deleted_at = NOW() WHERE id = ?");
        if ($stmt->execute([$id])) {
            $success = 'Payment method deleted successfully.';
        } else {
            $error = 'Failed to delete payment method.';
        }
    }
}

// Fetch payment methods
try {
    $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE deleted_at IS NULL ORDER BY created_at DESC");
    $stmt->execute();
    $paymentMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}

require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';
?>

<link rel="stylesheet" href="<?php echo $one->assets_folder; ?>/css/4download-style.css">
<!-- SweetAlert2 for delete confirmation -->
<script src="<?php echo $one->assets_folder; ?>/js/plugins/sweetalert2/sweetalert2.min.js"></script>

<!-- Page Content -->
<div class="container dashboard-container">
    <div class="row g-4">
        <!-- Sidebar -->
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

        <!-- Main Content -->
        <div class="col-lg-9">
            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <!-- Payment Methods Table -->
            <div class="block block-rounded mb-4 animated fadeIn">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Manage Payment Methods</h3>
                    <div class="block-options">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createPaymentMethodModal" data-bs-toggle="tooltip" title="Add New Payment Method">
                            <i class="bi bi-plus"></i> Add Payment Method
                        </button>
                        <button type="button" class="btn btn-sm btn-alt-secondary" id="darkModeToggle" data-bs-toggle="tooltip" title="Toggle Dark Mode">
                            <i class="bi bi-moon"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <?php if (empty($paymentMethods)): ?>
                        <p class="text-muted">No payment methods found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>API Key</th>
                                        <th>Merchant ID</th>
                                        <th>Active</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($paymentMethods as $method): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($method['id']); ?></td>
                                            <td><?php echo htmlspecialchars($method['name']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($method['api_key'], 0, 5) . '****'); ?></td>
                                            <td><?php echo htmlspecialchars($method['merchant_id']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $method['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo $method['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($method['created_at'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary me-1" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#updatePaymentMethodModal"
                                                        data-id="<?php echo $method['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($method['name']); ?>"
                                                        data-api-key="<?php echo htmlspecialchars($method['api_key']); ?>"
                                                        data-api-secret="<?php echo htmlspecialchars($method['api_secret']); ?>"
                                                        data-merchant-id="<?php echo htmlspecialchars($method['merchant_id']); ?>"
                                                        data-is-active="<?php echo $method['is_active']; ?>"
                                                        data-bs-toggle="tooltip" title="Edit Payment Method">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="<?php echo htmlspecialchars($baseUrl); ?>payment_methods.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="delete_id" value="<?php echo $method['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger js-sweetalert2-confirm" 
                                                            data-sweetalert2-title="Are you sure?" 
                                                            data-sweetalert2-text="This payment method will be deleted!" 
                                                            data-sweetalert2-confirm-button-text="Yes, delete it!" 
                                                            data-bs-toggle="tooltip" title="Delete Payment Method">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-text">
        <strong>AK23StudioKits</strong> &copy; <span data-toggle="year-copy"><?php echo date('Y'); ?></span>
    </div>
</div>

<!-- Create Payment Method Modal -->
<div class="modal fade" id="createPaymentMethodModal" tabindex="-1" aria-labelledby="createPaymentMethodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title" id="createPaymentMethodModalLabel">Add New Payment Method</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="block-content">
                <form action="<?php echo htmlspecialchars($baseUrl); ?>payment_methods.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createName" class="form-label">Name</label>
                                <select class="form-select" id="createName" name="name" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="pesapal">Pesapal</option>
                                    <option value="evmak">EvMak</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="stripe">Stripe</option>
                                    <option value="manual">Manual/Bank Transfer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createApiKey" class="form-label">API Key</label>
                                <input type="text" class="form-control" id="createApiKey" name="api_key">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createApiSecret" class="form-label">API Secret</label>
                                <input type="text" class="form-control" id="createApiSecret" name="api_secret">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="createMerchantId" class="form-label">Merchant ID</label>
                                <input type="text" class="form-control" id="createMerchantId" name="merchant_id">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Active</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="createIsActive" name="is_active" checked>
                                    <label class="form-check-label" for="createIsActive">Is Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Create Payment Method</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Update Payment Method Modal -->
<div class="modal fade" id="updatePaymentMethodModal" tabindex="-1" aria-labelledby="updatePaymentMethodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title" id="updatePaymentMethodModalLabel">Edit Payment Method</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="block-content">
                <form action="<?php echo htmlspecialchars($baseUrl); ?>payment_methods.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="updateId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateName" class="form-label">Name</label>
                                <select class="form-select" id="updateName" name="name" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="pesapal">Pesapal</option>
                                    <option value="evmak">EvMak</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="stripe">Stripe</option>
                                    <option value="manual">Manual/Bank Transfer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateApiKey" class="form-label">API Key</label>
                                <input type="text" class="form-control" id="updateApiKey" name="api_key">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateApiSecret" class="form-label">API Secret</label>
                                <input type="text" class="form-control" id="updateApiSecret" name="api_secret">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updateMerchantId" class="form-label">Merchant ID</label>
                                <input type="text" class="form-control" id="updateMerchantId" name="merchant_id">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Active</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="updateIsActive" name="is_active">
                                    <label class="form-check-label" for="updateIsActive">Is Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Payment Method</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Modals, Tooltips, and Dark Mode -->
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
    var updateModal = document.getElementById('updatePaymentMethodModal');
    updateModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var name = button.getAttribute('data-name');
        var apiKey = button.getAttribute('data-api-key');
        var apiSecret = button.getAttribute('data-api-secret');
        var merchantId = button.getAttribute('data-merchant-id');
        var isActive = button.getAttribute('data-is-active') === '1';
        
        updateModal.querySelector('#updateId').value = id;
        updateModal.querySelector('#updateName').value = name;
        updateModal.querySelector('#updateApiKey').value = apiKey;
        updateModal.querySelector('#updateApiSecret').value = apiSecret;
        updateModal.querySelector('#updateMerchantId').value = merchantId;
        updateModal.querySelector('#updateIsActive').checked = isActive;
    });

    // SweetAlert2 for delete confirmation
    document.querySelectorAll('.js-sweetalert2-confirm').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.form;
            const title = this.getAttribute('data-sweetalert2-title') || 'Are you sure?';
            const text = this.getAttribute('data-sweetalert2-text') || 'This action cannot be undone!';
            const confirmButtonText = this.getAttribute('data-sweetalert2-confirm-button-text') || 'Yes, proceed!';

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: confirmButtonText
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Toggle API fields based on payment method selection
    function toggleApiFields(selectElement, apiKeyInput, apiSecretInput, merchantIdInput) {
        if (selectElement.value === 'manual') {
            apiKeyInput.disabled = true;
            apiKeyInput.value = '';
            apiSecretInput.disabled = true;
            apiSecretInput.value = '';
            merchantIdInput.disabled = true;
            merchantIdInput.value = '';
        } else {
            apiKeyInput.disabled = false;
            apiSecretInput.disabled = false;
            merchantIdInput.disabled = false;
        }
    }

    document.getElementById('createName').addEventListener('change', function() {
        toggleApiFields(this, document.getElementById('createApiKey'), document.getElementById('createApiSecret'), document.getElementById('createMerchantId'));
    });

    document.getElementById('updateName').addEventListener('change', function() {
        toggleApiFields(this, document.getElementById('updateApiKey'), document.getElementById('updateApiSecret'), document.getElementById('updateMerchantId'));
    });
});
</script>

<?php require 'inc/_global/views/page_end.php'; ?>
