<?php
$pageTitle = 'My Favorites';
require_once 'includes/header.php';

if (!isLoggedIn() || !isTenant()) {
    redirect('login.php');
}

$db = new Database();
$user_id = $_SESSION['user_id'];

$sql = "SELECT p.*, f.created_at as favorited_at 
        FROM favorites f 
        JOIN properties p ON f.property_id = p.id 
        WHERE f.user_id = '$user_id' 
        ORDER BY f.created_at DESC";
$result = $db->query($sql);
?>

<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-heart-fill text-danger me-2" style="font-size: 1.5rem;"></i>
        <h1 class="mb-0">My Favorites</h1>
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
                            <h5 class="card-title"><?php echo htmlspecialchars($property['title']); ?></h5>
                            <p class="card-text">
                                <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                                <?php echo htmlspecialchars($property['location']); ?><br>
                                <i class="bi bi-currency-dollar text-primary me-1"></i>
                                <?php echo number_format($property['price'], 2); ?>/month
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="property.php?id=<?php echo $property['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-info-circle me-1"></i>
                                    View Details
                                </a>
                                <button class="btn btn-outline-danger btn-sm remove-favorite" data-property-id="<?php echo $property['id']; ?>">
                                    <i class="bi bi-heart-break me-1"></i>
                                    Remove
                                </button>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="bi bi-clock me-1"></i>
                                Added <?php echo date('M j, Y', strtotime($property['favorited_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-heart text-muted" style="font-size: 3rem;"></i>
            <p class="lead mt-3">You haven't added any properties to your favorites yet.</p>
            <a href="search.php" class="btn btn-primary">
                <i class="bi bi-search me-1"></i>
                Browse Properties
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.remove-favorite').forEach(button => {
    button.addEventListener('click', function() {
        if (confirm('Remove this property from favorites?')) {
            const propertyId = this.dataset.propertyId;
            fetch('remove_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'property_id=' + propertyId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closest('.col-md-6').remove();
                    if (document.querySelectorAll('.col-md-6').length === 0) {
                        location.reload();
                    }
                } else {
                    alert(data.message || 'Failed to remove from favorites');
                }
            });
        }
    });
});
</script>

<?php $db->close(); ?>
