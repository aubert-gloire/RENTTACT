<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isLandlord()) {
    redirect('../login.php');
}

$db = new Database();
$user_id = $_SESSION['user_id'];

// Get landlord's information
$sql = "SELECT name, phone_number, email FROM users WHERE id = '$user_id'";
$user_result = $db->query($sql);
$user_info = $user_result->fetch_assoc();

// Handle property form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $db->escape($_POST['title'] ?? '');
    $description = $db->escape($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $location = $db->escape($_POST['location'] ?? '');
    $amenities = $db->escape($_POST['amenities'] ?? '');
    $contact_phone = $db->escape($_POST['contact_phone'] ?? $user_info['phone_number']);
    $contact_email = $db->escape($_POST['contact_email'] ?? $user_info['email']);
    
    $upload_error = '';
    $image_path = '';
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_path = uploadImage($_FILES['image']);
        if ($image_path === false) {
            $upload_error = 'Failed to upload image. Please ensure it is a JPG/PNG file under 5MB.';
        }
    }
    
    if (empty($upload_error)) {
        $sql = "INSERT INTO properties (landlord_id, title, description, price, location, amenities, image_path, contact_phone, contact_email) 
                VALUES ('$user_id', '$title', '$description', '$price', '$location', '$amenities', '$image_path', '$contact_phone', '$contact_email')";
        
        if ($db->query($sql)) {
            $success = 'Property listed successfully!';
        } else {
            $error = 'Failed to list property: ' . $db->getConnection()->error;
        }
    } else {
        $error = $upload_error;
    }
}

// Get landlord's properties
$sql = "SELECT * FROM properties WHERE landlord_id = '$user_id' ORDER BY created_at DESC";
$properties = $db->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Dashboard - RENTTACT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">RENTTACT</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../search.php">Search</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h2 class="mb-4">Landlord Dashboard</h2>
        
        <!-- Debug Information -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <h4 class="alert-heading">Error Details:</h4>
                <p><?php echo $error; ?></p>
                <?php if (isset($_FILES['image'])): ?>
                    <hr>
                    <h5>Upload Information:</h5>
                    <pre><?php print_r($_FILES['image']); ?></pre>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Add Property Form -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <h3 class="card-title">Add New Property</h3>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Property Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Monthly Rent (USD)</label>
                            <input type="number" class="form-control" id="price" name="price" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="amenities" class="form-label">Amenities</label>
                        <textarea class="form-control" id="amenities" name="amenities" rows="2" placeholder="e.g., WiFi, Parking, Security"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact_phone" class="form-label">Contact Phone</label>
                            <input type="tel" class="form-control" id="contact_phone" name="contact_phone" value="<?php echo $user_info['phone_number']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact_email" class="form-label">Contact Email</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo $user_info['email']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Property Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Accepted formats: JPG, PNG (max 5MB)</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Property</button>
                </form>
            </div>
        </div>
        
        <!-- Property Listings -->
        <h3 class="mb-3">Your Properties</h3>
        <div class="row">
            <?php if ($properties && $properties->num_rows > 0): ?>
                <?php while ($property = $properties->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card property-card">
                            <?php if ($property['image_path']): ?>
                                <img src="<?php echo '../' . $property['image_path']; ?>" class="card-img-top" alt="<?php echo $property['title']; ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $property['title']; ?></h5>
                                <p class="card-text">
                                    <strong>$<?php echo number_format($property['price'], 2); ?>/month</strong><br>
                                    <?php echo $property['location']; ?><br>
                                    <small class="text-muted">
                                        Contact: <?php echo $property['contact_phone']; ?><br>
                                        Email: <?php echo $property['contact_email']; ?>
                                    </small>
                                </p>
                                <div class="btn-group">
                                    <a href="edit_property.php?id=<?php echo $property['id']; ?>" class="btn btn-primary">Edit</a>
                                    <a href="delete_property.php?id=<?php echo $property['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this property?')">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col">
                    <p>No properties listed yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $db->close(); ?>
