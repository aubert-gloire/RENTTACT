<?php
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Image Upload - RENTTACT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2>Test Image Upload</h2>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
            $result = uploadImage($_FILES['test_image']);
            if ($result) {
                echo '<div class="alert alert-success">
                    Image uploaded successfully!<br>
                    Path: ' . htmlspecialchars($result) . '<br>
                    <img src="' . htmlspecialchars($result) . '" class="mt-3" style="max-width: 300px;">
                </div>';
            } else {
                echo '<div class="alert alert-danger">
                    Upload failed. Make sure the image is JPG/PNG and less than 5MB.
                </div>';
            }
        }
        ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="test_image" class="form-label">Select Image to Upload</label>
                        <input type="file" class="form-control" id="test_image" name="test_image" accept="image/*" required>
                        <small class="text-muted">Allowed: JPG, PNG (max 5MB)</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Image</button>
                </form>
            </div>
        </div>

        <div class="mt-4">
            <h4>Debug Information:</h4>
            <pre><?php
            echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
            echo "Upload Path Constant: " . UPLOAD_PATH . "\n";
            echo "Target Directory: " . $_SERVER['DOCUMENT_ROOT'] . '/renttact/' . UPLOAD_PATH . "\n";
            echo "Directory Exists: " . (is_dir($_SERVER['DOCUMENT_ROOT'] . '/renttact/' . UPLOAD_PATH) ? 'Yes' : 'No') . "\n";
            echo "Directory Writable: " . (is_writable($_SERVER['DOCUMENT_ROOT'] . '/renttact/' . UPLOAD_PATH) ? 'Yes' : 'No') . "\n";
            ?></pre>
        </div>
    </div>
</body>
</html>
