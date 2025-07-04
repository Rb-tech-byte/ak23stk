<?php
session_start();
require_once __DIR__ . '/database/db_config.php';
require 'inc/_global/config.php';
require 'inc/_global/views/head_start.php';

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Error handling and base URL
$error = '';
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$baseUrl = rtrim($baseUrl, '/') . '/';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $error = 'Invalid CSRF token.';
    } else {
        $email = filter_var(trim($_POST['login-email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['login-password'] ?? '';
        if ($email && $password) {
            try {
                $stmt = $pdo->prepare('SELECT id, name, password, is_admin FROM users WHERE email = ? AND deleted_at IS NULL');
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $email;
                    $_SESSION['is_admin'] = $user['is_admin'];
                    $_SESSION['last_login'] = date('Y-m-d H:i:s');
                    header('Location: ' . $baseUrl . 'users_dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        } else {
            $error = 'Please fill in all fields.';
        }
    }
}

// Category groups for mega menu
$categoryGroups = [
    'Audio' => ['Audio Plugins', 'Digital Audio Workstations', 'Kontakt Libraries', 'Audio Samples', 'Synth Presets', 'Audio Libraries'],
    'Video/Graphics' => ['Photo Editing Software', 'Video Editing Software', 'Graphic Design Tools', 'Screen Capture & Recorder'],
    'Utilities' => ['Converters', 'Security Tools', 'System Utilities', 'Download Managers', 'Office Tools'],
    'Others' => ['Activators', 'Operating Systems', 'Plugins Tools & Utilities']
];

// Fetch categories for mega menu
$categories = [];
try {
    $stmt = $pdo->prepare("SELECT id, name, slug FROM categories WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categories[$row['id']] = $row;
    }
} catch (PDOException $e) {
    // Optionally log or display error
}

// Build query string for preserving parameters
$queryParams = [];
if (isset($_GET['category'])) {
    $queryParams['category'] = intval($_GET['category']);
}
if (isset($_GET['page']) && intval($_GET['page']) > 1) {
    $queryParams['page'] = intval($_GET['page']);
}
$queryString = $queryParams ? '?' . http_build_query($queryParams) : '';

require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';
?>

<!-- Additional Libraries -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?php echo $one->assets_folder; ?>/css/4download-style.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $one->assets_folder; ?>/js/plugins/sweetalert2/sweetalert2.min.js"></script>


<!-- Header -->
<?php
require_once 'includes/navbar.php';
?>
<!-- END Header -->


<!-- Page Content -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white text-center py-4">
                    <h2 class="card-title mb-0">Sign In</h2>
                    <p class="card-subtitle text-white-50">Welcome back to AK23StudioKits</p>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger text-center mb-4" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="mb-4">
                            <label for="login-email" class="form-label">Email Address</label>
                            <input type="email" class="form-control form-control-lg h-12" id="login-email" name="login-email" placeholder="Enter your email" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <div class="mb-4">
                            <label for="login-password" class="form-label">Password</label>
                            <input type="password" class="form-control form-control-lg h-12" id="login-password" name="login-password" placeholder="Enter your password" required>
                            <div class="invalid-feedback">Please enter your password.</div>
                        </div>
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="login-remember" name="login-remember">
                            <label class="form-check-label" for="login-remember">Remember Me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Sign In <i class="fas fa-sign-in-alt ms-2"></i></button>
                    </form>
                    <div class="mt-4 text-center">
                        <a href="<?php echo $baseUrl; ?>op_auth_reminder.php" class="text-primary">Forgot Password?</a>
                        <span class="mx-2">|</span>
                        <a href="<?php echo $baseUrl; ?>signup.php" class="text-primary">Create New Account</a>
                    </div>
                </div>
                <div class="card-footer text-center py-3 bg-light">
                    <small>&copy; AK23StudioKits <?php echo date('Y'); ?> - All rights reserved. Last updated: <?php echo date('h:i A T, F d, Y'); ?></small>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END Page Content -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Bootstrap validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Show error via SweetAlert2 if present
    <?php if ($error): ?>
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: '<?php echo addslashes($error); ?>',
            confirmButtonText: 'Try Again',
            confirmButtonColor: '#6B46C1'
        });
    <?php endif; ?>

    // Logo hover animation
    const logo = document.querySelector('.logo-large');
    logo.addEventListener('mouseover', () => logo.style.transform = 'scale(1.1)');
    logo.addEventListener('mouseout', () => logo.style.transform = 'scale(1)');
});
</script>

<?php
require 'inc/_global/views/page_end.php';
require 'inc/_global/views/footer_start.php';
require 'inc/_global/views/footer_end.php';
?>