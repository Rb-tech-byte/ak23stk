<?php
// Handle file upload for 'upload' type
if ($type === 'upload' && isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $fileName = uniqid() . '_' . basename($_FILES['file_upload']['name']);
    $targetFile = $uploadDir . $fileName;

    // Basic security check for file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/zip'];
    if (!in_array($_FILES['file_upload']['type'], $allowedTypes)) {
        $form_errors[] = 'Invalid file type. Allowed types: JPG, PNG, GIF, PDF, ZIP.';
    } elseif ($_FILES['file_upload']['size'] > 5000000) { // 5MB limit
        $form_errors[] = 'File too large. Maximum size is 5MB.';
    } elseif (!move_uploaded_file($_FILES['file_upload']['tmp_name'], $targetFile)) {
        $form_errors[] = 'File upload failed.';
    } else {
        $value = 'uploads/' . $fileName; // Save relative path
        $file_type = $_FILES['file_upload']['type'];
    }
}
?>

<!-- Form snippets for Create and Update with file upload support -->

<!-- Create Form -->
<form action="medias.php" method="POST" enctype="multipart/form-data" class="mb-5">
    <!-- Other form fields -->
    <div class="mb-3">
        <label for="createValue" class="form-label">Media Content</label>
        <input type="file" class="form-control" id="createValue" name="file_upload" accept="image/*,application/pdf,application/zip">
        <div id="urlInputFallback" class="mt-2 d-none">
            <input type="text" class="form-control" name="value" placeholder="Or enter URL manually if file upload not available">
        </div>
    </div>
    <!-- Other form fields and submit button -->
</form>

<!-- Update Form -->
<form action="medias.php" method="POST" enctype="multipart/form-data" class="mb-5">
    <!-- Other form fields -->
    <div class="mb-3">
        <label for="updateValue" class="form-label">Media Content (leave blank to keep current)</label>
        <input type="file" class="form-control" id="updateValue" name="file_upload" accept="image/*,application/pdf,application/zip">
        <div id="urlInputFallbackUpdate" class="mt-2 d-none">
            <input type="text" class="form-control" name="value" placeholder="Or enter URL manually">
        </div>
        <small class="form-text text-muted">Current: <span id="currentValue">[Existing content]</span></small>
    </div>
    <!-- Other form fields and submit button -->
</form>
