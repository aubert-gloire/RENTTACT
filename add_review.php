<?php
require_once 'includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isTenant()) {
    echo json_encode(['success' => false, 'message' => 'Please log in as a tenant']);
    exit;
}

$db = new Database();
$user_id = $_SESSION['user_id'];
$property_id = intval($_POST['property_id'] ?? 0);
$rating = intval($_POST['rating'] ?? 0);
$comment = $db->escape($_POST['comment'] ?? '');

if (!$property_id || $rating < 1 || $rating > 5 || empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Invalid review data']);
    exit;
}

// Check if user already reviewed this property
$sql = "SELECT id FROM reviews WHERE property_id = '$property_id' AND user_id = '$user_id'";
$result = $db->query($sql);

if ($result && $result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already reviewed this property']);
    exit;
}

// Add review
$sql = "INSERT INTO reviews (property_id, user_id, rating, comment) VALUES ('$property_id', '$user_id', '$rating', '$comment')";
if ($db->query($sql)) {
    // Get updated average rating
    $avg_sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM reviews WHERE property_id = '$property_id'";
    $avg_result = $db->query($avg_sql);
    $rating_info = $avg_result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'message' => 'Review added successfully',
        'avgRating' => round($rating_info['avg_rating'], 1),
        'reviewCount' => $rating_info['review_count']
    ]);
} else {
    $error = $db->getConnection()->error;
    error_log("Failed to add review: " . $error);
    echo json_encode(['success' => false, 'message' => 'Failed to add review: ' . $error]);
}

$db->close();
