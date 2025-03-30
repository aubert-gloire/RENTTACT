<?php
$pageTitle = 'View Property';  // This will be overridden with the actual property title
require_once 'includes/functions.php';

$db = new Database();
$property_id = intval($_GET['id'] ?? 0);

if (!$property_id) {
    redirect('index.php');
}

// Get property details with landlord info
$sql = "SELECT p.*, u.name as landlord_name 
        FROM properties p 
        JOIN users u ON p.landlord_id = u.id 
        WHERE p.id = '$property_id'";
$result = $db->query($sql);

if (!$result || $result->num_rows === 0) {
    redirect('index.php');
}

$property = $result->fetch_assoc();
$pageTitle = $property['title'];  // Set the page title to the property title
require_once 'includes/header.php';  // Include header after setting pageTitle

// Get property reviews
$sql = "SELECT r.*, u.name as reviewer_name 
        FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.property_id = '$property_id' 
        ORDER BY r.created_at DESC";
$reviews = $db->query($sql);

// Calculate average rating
$avg_sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
            FROM reviews 
            WHERE property_id = '$property_id'";
$avg_result = $db->query($avg_sql);
$rating_info = $avg_result->fetch_assoc();
$avg_rating = round($rating_info['avg_rating'], 1);
?>

<!-- Property Header -->
<section class="py-4" style="background-color: #D1E6D5;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="index.php" class="text-dark">Home</a></li>
                <li class="breadcrumb-item"><a href="search.php" class="text-dark">Properties</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($property['title']); ?></li>
            </ol>
        </nav>
        <h1 class="mb-0 text-dark"><?php echo htmlspecialchars($property['title']); ?></h1>
        <p class="text-dark mb-0">
            <i class="bi bi-geo-alt-fill me-1"></i>
            <?php echo htmlspecialchars($property['location']); ?>
        </p>
    </div>
</section>

<div class="container py-4">
    <div class="row">
        <!-- Property Details -->
        <div class="col-md-8">
            <?php if ($property['image_path']): ?>
                <div class="card shadow-sm mb-4">
                    <img src="<?php echo htmlspecialchars($property['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($property['title']); ?>" 
                         class="card-img-top">
                </div>
            <?php endif; ?>

            <!-- Price and Basic Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h4 mb-0">$<?php echo number_format($property['price'], 2); ?>/month</h2>
                        <?php if (isLoggedIn() && isTenant()): ?>
                            <button class="btn btn-outline-primary add-to-favorites" data-property-id="<?php echo $property['id']; ?>">
                                <i class="bi bi-heart me-1"></i>
                                Add to Favorites
                            </button>
                        <?php endif; ?>
                    </div>
                    <hr>
                    <div class="property-description">
                        <?php echo nl2br(htmlspecialchars($property['description'])); ?>
                    </div>
                </div>
            </div>

            <!-- Amenities -->
            <?php if (!empty($property['amenities'])): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h5 mb-3">Amenities</h3>
                    <div class="row">
                        <?php 
                        $amenities = explode(',', $property['amenities']);
                        foreach ($amenities as $amenity): 
                            $amenity = trim($amenity);
                            if (!empty($amenity)):
                        ?>
                            <div class="col-md-6 mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <?php echo htmlspecialchars($amenity); ?>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Reviews Section -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h5 mb-0">
                            Reviews 
                            <small class="text-muted">(<?php echo $rating_info['review_count']; ?>)</small>
                        </h3>
                        <?php if (isLoggedIn() && isTenant()): ?>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                <i class="bi bi-star me-1"></i>
                                Write a Review
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if ($reviews && $reviews->num_rows > 0): ?>
                        <div class="reviews-list">
                            <?php while ($review = $reviews->fetch_assoc()): ?>
                                <div class="review-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($review['reviewer_name']); ?></h6>
                                            <div class="star-rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi bi-star<?php echo $i <= $review['rating'] ? '-fill' : ''; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No reviews yet. Be the first to review this property!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Contact Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h5 mb-3">Contact Information</h3>
                    <p class="mb-2">
                        <i class="bi bi-person-fill me-2"></i>
                        Listed by: <?php echo htmlspecialchars($property['landlord_name']); ?>
                    </p>
                    <?php if ($property['contact_phone']): ?>
                        <p class="mb-2">
                            <i class="bi bi-telephone-fill me-2"></i>
                            <a href="tel:<?php echo htmlspecialchars($property['contact_phone']); ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($property['contact_phone']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                    <?php if ($property['contact_email']): ?>
                        <p class="mb-0">
                            <i class="bi bi-envelope-fill me-2"></i>
                            <a href="mailto:<?php echo htmlspecialchars($property['contact_email']); ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($property['contact_email']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Map Preview (placeholder) -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="h5 mb-3">Location</h3>
                    <div class="ratio ratio-4x3 bg-light rounded">
                        <div class="d-flex align-items-center justify-content-center text-muted">
                            <div class="text-center">
                                <i class="bi bi-geo-alt" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2"><?php echo htmlspecialchars($property['location']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<?php if (isLoggedIn() && isTenant()): ?>
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <div class="star-rating-input">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" required>
                                <label for="star<?php echo $i; ?>"><i class="bi bi-star"></i></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Your Review</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitReview">Submit Review</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.star-rating {
    color: #ffc107;
}

.star-rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.star-rating-input input {
    display: none;
}

.star-rating-input label {
    cursor: pointer;
    font-size: 1.5rem;
    color: #ddd;
    margin: 0 2px;
}

.star-rating-input label:hover,
.star-rating-input label:hover ~ label,
.star-rating-input input:checked ~ label {
    color: #ffc107;
}

.property-description {
    white-space: pre-line;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to favorites functionality
    const favBtn = document.querySelector('.add-to-favorites');
    if (favBtn) {
        favBtn.addEventListener('click', function() {
            const propertyId = this.dataset.propertyId;
            fetch('add_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'property_id=' + propertyId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.innerHTML = '<i class="bi bi-heart-fill me-1"></i> Added to Favorites';
                    this.disabled = true;
                } else {
                    alert(data.message || 'Failed to add to favorites');
                }
            });
        });
    }

    // Submit review functionality
    const submitReviewBtn = document.getElementById('submitReview');
    if (submitReviewBtn) {
        submitReviewBtn.addEventListener('click', function() {
            const form = document.getElementById('reviewForm');
            const formData = new FormData(form);
            
            fetch('add_review.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to submit review');
                }
            });
        });
    }
});
</script>

<?php 
$db->close();
require_once 'includes/footer.php';
?>
