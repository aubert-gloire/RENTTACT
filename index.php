<?php
$pageTitle = 'Find Your Perfect Home';
require_once 'includes/header.php';

$db = new Database();

// Get featured properties
$sql = "SELECT p.*, u.name as landlord_name, 
        (SELECT AVG(rating) FROM reviews WHERE property_id = p.id) as avg_rating,
        (SELECT COUNT(*) FROM reviews WHERE property_id = p.id) as review_count
        FROM properties p 
        JOIN users u ON p.landlord_id = u.id 
        ORDER BY p.created_at DESC 
        LIMIT 6";

$featured_properties = $db->query($sql);

// Get favorite properties for logged-in tenants
$favorite_properties = null;
if (isLoggedIn() && isTenant()) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT p.*, u.name as landlord_name,
            (SELECT AVG(rating) FROM reviews WHERE property_id = p.id) as avg_rating,
            (SELECT COUNT(*) FROM reviews WHERE property_id = p.id) as review_count
            FROM properties p 
            JOIN users u ON p.landlord_id = u.id
            JOIN favorites f ON p.id = f.property_id
            WHERE f.user_id = '$user_id'
            ORDER BY f.created_at DESC";
    
    $favorite_properties = $db->query($sql);
}
?>

<!-- Hero Section -->
<section class="hero py-5" style="background-color: #D1E6D5;">
    <div class="container">
        <h1 class="display-4 mb-4 text-dark text-center">Find Your Perfect Home</h1>
        <p class="lead mb-4 text-dark text-center">Browse through our curated selection of rental properties</p>
        <div class="text-center">
            <a href="search.php" class="btn btn-dark btn-lg">
                <i class="bi bi-search me-2"></i>
                Start Searching
            </a>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works py-5">
    <div class="container">
        <h2 class="text-center mb-5">How RENTTACT Works</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="display-4 text-primary mb-3">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <h3 class="h4 mb-3">1. Create an Account</h3>
                        <p class="text-muted">Sign up as a tenant to save favorites or as a landlord to list properties.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="display-4 text-primary mb-3">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3 class="h4 mb-3">2. Browse & Connect</h3>
                        <p class="text-muted">Search properties, save favorites, and connect with landlords directly.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="display-4 text-primary mb-3">
                            <i class="bi bi-key"></i>
                        </div>
                        <h3 class="h4 mb-3">3. Find Your Home</h3>
                        <p class="text-muted">Schedule viewings and find the perfect property that matches your needs.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="mb-4">Need Help?</h2>
                <p class="lead mb-4">Our team is here to assist you with any questions about finding or listing properties.</p>
                <div class="contact-info">
                    <p class="mb-3">
                        <i class="bi bi-envelope-fill me-2 text-primary"></i>
                        <a href="mailto:aubertgloire@gmail.com" class="text-decoration-none">aubertgloire@gmail.com</a>
                    </p>
                    <p class="mb-3">
                        <i class="bi bi-telephone-fill me-2 text-primary"></i>
                        <a href="tel:+250788268061" class="text-decoration-none">+250 788 268 061</a>
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-clock-fill me-2 text-primary"></i>
                        Available Monday - Saturday, 8:00 AM - 6:00 PM
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <img src="/renttact/images/contact-illustration.svg" alt="Contact Support" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<?php if (isLoggedIn() && isTenant() && $favorite_properties && $favorite_properties->num_rows > 0): ?>
<!-- Favorite Properties -->
<section class="favorite-properties py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Your Favorite Properties</h2>
        <div class="row g-4">
            <?php while ($property = $favorite_properties->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <?php if ($property['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($property['image_path']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($property['title']); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($property['title']); ?></h5>
                            <p class="card-text text-primary mb-2">$<?php echo number_format($property['price'], 2); ?>/month</p>
                            <p class="card-text">
                                <i class="bi bi-geo-alt-fill text-secondary"></i>
                                <?php echo htmlspecialchars($property['location']); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="rating">
                                    <?php
                                    $rating = round($property['avg_rating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo '<i class="bi bi-star' . ($i <= $rating ? '-fill' : '') . ' text-warning"></i>';
                                    }
                                    ?>
                                    <small class="text-muted ms-1">
                                        (<?php echo $property['review_count']; ?> reviews)
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="property.php?id=<?php echo $property['id']; ?>" class="btn btn-primary w-100">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Properties -->
<section class="featured-properties py-5">
    <div class="container">
        <h2 class="text-center mb-4">Featured Properties</h2>
        <?php if ($featured_properties && $featured_properties->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($property = $featured_properties->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <?php if ($property['image_path']): ?>
                                <img src="<?php echo htmlspecialchars($property['image_path']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($property['title']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($property['title']); ?></h5>
                                <p class="card-text text-primary mb-2">$<?php echo number_format($property['price'], 2); ?>/month</p>
                                <p class="card-text">
                                    <i class="bi bi-geo-alt-fill text-secondary"></i>
                                    <?php echo htmlspecialchars($property['location']); ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="rating">
                                        <?php
                                        $rating = round($property['avg_rating']);
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo '<i class="bi bi-star' . ($i <= $rating ? '-fill' : '') . ' text-warning"></i>';
                                        }
                                        ?>
                                        <small class="text-muted ms-1">
                                            (<?php echo $property['review_count']; ?> reviews)
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <a href="property.php?id=<?php echo $property['id']; ?>" class="btn btn-primary w-100">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">No properties available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

<?php 
$db->close();
require_once 'includes/footer.php';
?>
