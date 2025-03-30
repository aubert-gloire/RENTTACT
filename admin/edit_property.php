<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isLandlord()) {
    redirect('../login.php');
}

$db = new Database();
$user_id = $_SESSION['user_id'];
$property_id = $_GET['id'] ?? 0;

// Verify property belongs to current landlord
$sql = "SELECT * FROM properties WHERE id = '$property_id' AND landlord_id = '$user_id'";
$result = $db->query($sql);

if (!$result || $result->num_rows === 0) {
    redirect('dashboard.php');
}

$property = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $db->escape($_POST['title'] ?? '');
    $description = $db->escape($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $location = $db->escape($_POST['location'] ?? '');
    $amenities = $db->escape($_POST['amenities'] ?? '');
    $contact_phone = $db->escape($_POST['contact_phone'] ?? '');
    $contact_email = $db->escape($_POST['contact_email'] ?? '');
    
    // Handle image upload
    $image_path = $property['image_path'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $new_image_path = uploadImage($_FILES['image']);
        if ($new_image_path) {
            // Delete old image if exists
            if ($image_path && file_exists('../' . $image_path)) {
                unlink('../' . $image_path);
            }
            $image_path = $new_image_path;
        }
    }
    
    $sql = "UPDATE properties 
            SET title = '$title', description = '$description', price = '$price', 
                location = '$location', amenities = '$amenities', 
                contact_phone = '$contact_phone', contact_email = '$contact_email', 
                image_path = '$image_path' 
            WHERE id = '$property_id' AND landlord_id = '$user_id'";
    
    if ($db->query($sql)) {
        $success = 'Property updated successfully!';
        // Refresh property data
        $result = $db->query("SELECT * FROM properties WHERE id = '$property_id'");
        $property = $result->fetch_assoc();
    } else {
        $error = 'Failed to update property. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property - RENTTACT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">RENTTACT</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="card shadow">
            <div class="card-body">
                <h2 class="card-title mb-4">Edit Property</h2>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Property Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo $property['title']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Monthly Rent (USD)</label>
                            <input type="number" class="form-control" id="price" name="price" value="<?php echo $property['price']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo $property['location']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $property['description']; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="amenities" class="form-label">Amenities</label>
                        <textarea class="form-control" id="amenities" name="amenities" rows="2"><?php echo $property['amenities']; ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact_phone" class="form-label">Contact Phone</label>
                            <input type="tel" class="form-control" id="contact_phone" name="contact_phone" value="<?php echo $property['contact_phone']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact_email" class="form-label">Contact Email</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo $property['contact_email']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Property Image</label>
                        <?php if ($property['image_path']): ?>
                            <div class="mb-2">
                                <img src="<?php echo '../' . $property['image_path']; ?>" alt="Current property image" style="max-width: 200px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image</small>
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">Update Property</button>
                        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $db->close(); ?>
