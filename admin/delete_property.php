<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isLandlord()) {
    redirect('../login.php');
}

$db = new Database();
$user_id = $_SESSION['user_id'];
$property_id = $_GET['id'] ?? 0;

// Verify property belongs to current landlord and get image path
$sql = "SELECT image_path FROM properties WHERE id = '$property_id' AND landlord_id = '$user_id'";
$result = $db->query($sql);

if ($result && $result->num_rows > 0) {
    $property = $result->fetch_assoc();
    
    // Delete the property image if it exists
    if ($property['image_path'] && file_exists('../' . $property['image_path'])) {
        unlink('../' . $property['image_path']);
    }
    
    // Delete the property from database
    $sql = "DELETE FROM properties WHERE id = '$property_id' AND landlord_id = '$user_id'";
    $db->query($sql);
}

$db->close();
redirect('dashboard.php');
?>
