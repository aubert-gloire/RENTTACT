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

if (!$property_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid property']);
    exit;
}

// Check if already favorited
$sql = "SELECT id FROM favorites WHERE user_id = '$user_id' AND property_id = '$property_id'";
$result = $db->query($sql);

if ($result && $result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Already in favorites']);
    exit;
}

// Add to favorites
$sql = "INSERT INTO favorites (user_id, property_id) VALUES ('$user_id', '$property_id')";
if ($db->query($sql)) {
    echo json_encode(['success' => true]);
} else {
    $error = $db->getConnection()->error;
    error_log("Failed to add favorite: " . $error);
    echo json_encode(['success' => false, 'message' => 'Failed to add to favorites: ' . $error]);
}

$db->close();
