<?php
$pageTitle = 'My Profile';
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = new Database();
$user_id = $_SESSION['user_id'];

// Handle profile update
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $address = $_POST['address'] ?? '';
    
    // Handle file upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowed_types)) {
            $error_message = 'Invalid file type. Please upload a JPEG, PNG, or GIF image.';
        } elseif ($file['size'] > $max_size) {
            $error_message = 'File is too large. Maximum size is 5MB.';
        } else {
            // Create uploads directory if it doesn't exist
            $upload_dir = 'uploads/profiles';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('profile_') . '.' . $extension;
            $target_path = $upload_dir . '/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                // Delete old profile image if exists
                $old_image_query = "SELECT profile_image FROM users WHERE id = '$user_id'";
                $old_image_result = $db->query($old_image_query);
                if ($old_image_result && $old_image_result->num_rows > 0) {
                    $old_image = $old_image_result->fetch_assoc()['profile_image'];
                    if ($old_image && file_exists($old_image)) {
                        unlink($old_image);
                    }
                }
                
                // Update database with new image path
                $image_path = $target_path;
                $sql = "UPDATE users SET 
                        name = '" . $db->escape($name) . "',
                        phone = '" . $db->escape($phone) . "',
                        bio = '" . $db->escape($bio) . "',
                        address = '" . $db->escape($address) . "',
                        profile_image = '" . $db->escape($image_path) . "'
                        WHERE id = '$user_id'";
            } else {
                $error_message = 'Failed to upload image. Please try again.';
            }
        }
    } else {
        // Update without changing profile image
        $sql = "UPDATE users SET 
                name = '" . $db->escape($name) . "',
                phone = '" . $db->escape($phone) . "',
                bio = '" . $db->escape($bio) . "',
                address = '" . $db->escape($address) . "'
                WHERE id = '$user_id'";
    }
    
    if (empty($error_message)) {
        if ($db->query($sql)) {
            $_SESSION['user_name'] = $name; // Update session name
            $success_message = 'Profile updated successfully!';
        } else {
            $error_message = 'Failed to update profile. Please try again.';
        }
    }
}

// Get user data
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = $db->query($sql);
$user = $result->fetch_assoc();

// Get statistics based on user role
$stats = [];
if (isTenant()) {
    // Get favorite properties count
    $sql = "SELECT COUNT(*) as count FROM favorites WHERE user_id = '$user_id'";
    $result = $db->query($sql);
    $stats['favorites'] = $result->fetch_assoc()['count'];
    
    // Get reviews count
    $sql = "SELECT COUNT(*) as count FROM reviews WHERE user_id = '$user_id'";
    $result = $db->query($sql);
    $stats['reviews'] = $result->fetch_assoc()['count'];
} else {
    // Get properties count for landlord
    $sql = "SELECT COUNT(*) as count FROM properties WHERE landlord_id = '$user_id'";
    $result = $db->query($sql);
    $stats['properties'] = $result->fetch_assoc()['count'];
    
    // Get total reviews on their properties
    $sql = "SELECT COUNT(*) as count FROM reviews r 
            JOIN properties p ON r.property_id = p.id 
            WHERE p.landlord_id = '$user_id'";
    $result = $db->query($sql);
    $stats['reviews'] = $result->fetch_assoc()['count'];
}
?>

<!-- Profile Header -->
<section class="py-4" style="background-color: #D1E6D5;">
    <div class="container">
        <h1 class="mb-0 text-dark">My Profile</h1>
        <p class="text-dark mb-0">
            <i class="bi bi-person-badge me-2"></i>
            <?php echo isTenant() ? 'Tenant Account' : 'Landlord Account'; ?>
        </p>
    </div>
</section>

<div class="container py-4">
    <div class="row">
        <!-- Profile Information -->
        <div class="col-md-8">
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row mb-4">
                            <div class="col-auto">
                                <div class="position-relative" style="width: 100px; height: 100px;">
                                    <?php if ($user['profile_image'] && file_exists($user['profile_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" 
                                             alt="Profile Picture" 
                                             class="rounded-circle img-thumbnail"
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                             style="width: 100px; height: 100px;">
                                            <i class="bi bi-person text-secondary" style="font-size: 3rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                    <label for="profile_image" class="position-absolute bottom-0 end-0 bg-primary rounded-circle p-2 cursor-pointer">
                                        <i class="bi bi-camera text-white"></i>
                                        <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*">
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <h3 class="h5 mb-3">Profile Information</h3>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                    <small class="text-muted">Email cannot be changed</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="h5 mb-4">Account Statistics</h3>
                    
                    <?php if (isTenant()): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-heart text-primary"></i>
                            </div>
                            <div>
                                <h4 class="h6 mb-1">Favorite Properties</h4>
                                <p class="mb-0"><?php echo $stats['favorites']; ?> properties</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-star text-primary"></i>
                            </div>
                            <div>
                                <h4 class="h6 mb-1">Reviews Written</h4>
                                <p class="mb-0"><?php echo $stats['reviews']; ?> reviews</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-house text-primary"></i>
                            </div>
                            <div>
                                <h4 class="h6 mb-1">Listed Properties</h4>
                                <p class="mb-0"><?php echo $stats['properties']; ?> properties</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-star text-primary"></i>
                            </div>
                            <div>
                                <h4 class="h6 mb-1">Property Reviews</h4>
                                <p class="mb-0"><?php echo $stats['reviews']; ?> reviews</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('profile_image').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (this.files[0].size > maxSize) {
            alert('File is too large. Maximum size is 5MB.');
            this.value = '';
            return;
        }
        
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(this.files[0].type)) {
            alert('Invalid file type. Please upload a JPEG, PNG, or GIF image.');
            this.value = '';
            return;
        }
    }
});
</script>

<?php 
$db->close();
require_once 'includes/footer.php';
?>
