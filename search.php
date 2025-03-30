<?php
$pageTitle = 'Search Properties';
require_once 'includes/header.php';

$db = new Database();

// Get search parameters
$location = $db->escape($_GET['location'] ?? '');
$min_price = floatval($_GET['min_price'] ?? 0);
$max_price = floatval($_GET['max_price'] ?? 0);
$amenities = $_GET['amenities'] ?? [];

// Build search query
$sql = "SELECT p.*, u.name as landlord_name, 
        (SELECT AVG(rating) FROM reviews WHERE property_id = p.id) as avg_rating,
        (SELECT COUNT(*) FROM reviews WHERE property_id = p.id) as review_count
        FROM properties p 
        JOIN users u ON p.landlord_id = u.id 
        WHERE 1=1";

if ($location) {
    $sql .= " AND p.location LIKE '%$location%'";
}
if ($min_price > 0) {
    $sql .= " AND p.price >= $min_price";
}
if ($max_price > 0) {
    $sql .= " AND p.price <= $max_price";
}
if (!empty($amenities)) {
    foreach ($amenities as $amenity) {
        $amenity = $db->escape($amenity);
        $sql .= " AND p.amenities LIKE '%$amenity%'";
    }
}

$sql .= " ORDER BY p.created_at DESC";
$result = $db->query($sql);
?>

<div class="container py-5">
    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">
                <i class="bi bi-search me-2"></i>
                Search Properties
            </h5>
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="bi bi-geo-alt me-1"></i>
                        Location
                    </label>
                    <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($location); ?>" placeholder="Enter location...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="bi bi-currency-dollar me-1"></i>
                        Price Range
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="min_price" value="<?php echo $min_price ?: ''; ?>" placeholder="Min">
                        <span class="input-group-text">to</span>
                        <input type="number" class="form-control" name="max_price" value="<?php echo $max_price ?: ''; ?>" placeholder="Max">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="bi bi-check-circle me-1"></i>
                        Amenities
                    </label>
                    <select class="form-select" name="amenities[]" multiple>
                        <option value="wifi" <?php echo in_array('wifi', $amenities) ? 'selected' : ''; ?>>
                            <i class="bi bi-wifi"></i> WiFi
                        </option>
                        <option value="parking" <?php echo in_array('parking', $amenities) ? 'selected' : ''; ?>>
                            <i class="bi bi-p-square"></i> Parking
                        </option>
                        <option value="security" <?php echo in_array('security', $amenities) ? 'selected' : ''; ?>>
                            <i class="bi bi-shield"></i> Security
                        </option>
                        <!-- Add more amenities as needed -->
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>
                        Search
                    </button>
                    <a href="search.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="mb-3">
        <h5>
            <i class="bi bi-list-ul me-2"></i>
            Search Results
            <?php if ($result): ?>
                <small class="text-muted">(<?php echo $result->num_rows; ?> properties found)</small>
            <?php endif; ?>
        </h5>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($property = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <?php if ($property['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($property['image_path']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($property['title']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($property['title']); ?>
                            </h5>
                            <p class="card-text">
                                <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                                <?php echo htmlspecialchars($property['location']); ?><br>
                                <i class="bi bi-currency-dollar text-primary me-1"></i>
                                <?php echo number_format($property['price'], 2); ?>/month
                            </p>
                            
                            <!-- Rating -->
                            <div class="mb-2">
                                <div class="star-rating" style="color: #ffc107;">
                                    <?php
                                    $rating = round($property['avg_rating'], 1);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<i class="bi bi-star-fill"></i>';
                                        } elseif ($i - 0.5 <= $rating) {
                                            echo '<i class="bi bi-star-half"></i>';
                                        } else {
                                            echo '<i class="bi bi-star"></i>';
                                        }
                                    }
                                    ?>
                                    <small class="text-muted ms-1">
                                        (<?php echo $property['review_count']; ?> reviews)
                                    </small>
                                </div>
                            </div>
                            
                            <!-- Amenities preview -->
                            <?php if ($property['amenities']): ?>
                                <div class="small text-muted mb-2">
                                    <?php
                                    $amenities = array_slice(explode(',', $property['amenities']), 0, 3);
                                    foreach ($amenities as $amenity) {
                                        echo '<i class="bi bi-check-circle-fill text-success me-1"></i>';
                                        echo htmlspecialchars(trim($amenity)) . ' ';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="property.php?id=<?php echo $property['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-info-circle me-1"></i>
                                    View Details
                                </a>
                                <?php if (isLoggedIn() && isTenant()): ?>
                                    <button class="btn btn-outline-primary btn-sm add-to-favorites" data-property-id="<?php echo $property['id']; ?>">
                                        <i class="bi bi-heart me-1"></i>
                                        Add to Favorites
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-house-slash text-muted" style="font-size: 3rem;"></i>
            <p class="lead mt-3">No properties found matching your criteria.</p>
            <a href="search.php" class="btn btn-primary">
                <i class="bi bi-arrow-repeat me-1"></i>
                Reset Search
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.add-to-favorites').forEach(button => {
    button.addEventListener('click', function() {
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
                this.innerHTML = '<i class="bi bi-heart-fill me-1"></i> Added';
                this.disabled = true;
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-success');
            } else {
                alert(data.message || 'Failed to add to favorites');
            }
        });
    });
});
</script>

<?php $db->close(); ?>
