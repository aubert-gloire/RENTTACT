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

$sql = "DELETE FROM favorites WHERE user_id = '$user_id' AND property_id = '$property_id'";
if ($db->query($sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove from favorites']);
}

$db->close();
