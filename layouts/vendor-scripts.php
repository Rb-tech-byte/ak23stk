<?php
/**
 * Vendor Scripts
 * 
 * This file contains all the vendor JavaScript includes for the application.
 * It should be included just before the closing </body> tag.
 */
?>

<!-- JAVASCRIPT -->
<script src="<?php echo BASE_URL; ?>assets/libs/jquery/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Plugins js -->
<script src="<?php echo BASE_URL; ?>assets/libs/simplebar/simplebar.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/libs/node-waves/waves.min.js"></script>

<!-- DataTables -->
<script src="<?php echo BASE_URL; ?>assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

<!-- Select2 -->
<script src="<?php echo BASE_URL; ?>assets/libs/select2/js/select2.min.js"></script>

<!-- Sweet Alert 2 -->
<script src="<?php echo BASE_URL; ?>assets/libs/sweetalert2/sweetalert2.min.js"></script>

<!-- Toastr -->
<script src="<?php echo BASE_URL; ?>assets/libs/toastr/build/toastr.min.js"></script>

<!-- App js -->
<script src="<?php echo BASE_URL; ?>assets/js/app.js"></script>

<!-- Custom scripts -->
<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Initialize popovers
var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
});

// Global AJAX setup
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': '<?php echo $_SESSION['csrf_token'] ?? ''; ?>'
    },
    error: function(xhr, status, error) {
        console.error('AJAX Error:', status, error);
        
        let errorMessage = 'An error occurred while processing your request.';
        
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        } else if (xhr.status === 0) {
            errorMessage = 'Unable to connect to the server. Please check your internet connection.';
        } else if (xhr.status === 419) {
            errorMessage = 'Your session has expired. Please refresh the page and try again.';
            // Optionally redirect to login
            // window.location.href = '<?php echo BASE_URL; ?>login.php';
        } else if (xhr.status === 500) {
            errorMessage = 'A server error occurred. Please try again later.';
        }
        
        // Show error message using SweetAlert2
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
            confirmButtonText: 'OK',
            confirmButtonColor: '#7266ee'
        });
    }
});

// Initialize DataTables with common settings
$(document).ready(function() {
    // Initialize all DataTables with common settings
    $('.datatable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });

    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Select an option',
        allowClear: true
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// Show toast notifications
function showToast(type, message, title = '') {
    const toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    toast.fire({
        icon: type,
        title: title,
        text: message
    });
}

// Confirm before action
function confirmAction(options) {
    return Swal.fire({
        title: options.title || 'Are you sure?',
        text: options.text || 'This action cannot be undone!',
        icon: options.icon || 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7266ee',
        cancelButtonColor: '#6c757d',
        confirmButtonText: options.confirmText || 'Yes, proceed!',
        cancelButtonText: options.cancelText || 'Cancel',
        reverseButtons: true
    });
}
</script>

<!-- Page-specific scripts -->
<?php if (isset($page_specific_scripts)): ?>
    <?php foreach ($page_specific_scripts as $script): ?>
        <script src="<?php echo rtrim(BASE_URL, '/') . '/' . ltrim($script, '/'); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Inline scripts -->
<?php if (isset($inline_scripts)): ?>
    <script>
        $(document).ready(function() {
            <?php echo $inline_scripts; ?>
        });
    </script>
<?php endif; ?>
