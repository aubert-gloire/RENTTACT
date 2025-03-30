<?php
require_once 'includes/functions.php';

$db = new Database();

// Get current user info
session_start();
$user_id = $_SESSION['user_id'] ?? 'Not logged in';
$role = $_SESSION['role'] ?? 'No role';

echo "<h2>Debug Information:</h2>";
echo "<pre>";

echo "Current User:\n";
echo "User ID: $user_id\n";
echo "Role: $role\n\n";

// Check if user exists
if (isset($_SESSION['user_id'])) {
    $sql = "SELECT id, name, role FROM users WHERE id = '" . $_SESSION['user_id'] . "'";
    $result = $db->query($sql);
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "User found in database:\n";
        print_r($user);
    } else {
        echo "User not found in database!\n";
    }
}

// Check property from last attempt
if (isset($_GET['property_id'])) {
    $property_id = intval($_GET['property_id']);
    echo "\nChecking Property ID: $property_id\n";
    
    $sql = "SELECT id, landlord_id, title FROM properties WHERE id = '$property_id'";
    $result = $db->query($sql);
    if ($result && $result->num_rows > 0) {
        $property = $result->fetch_assoc();
        echo "Property found in database:\n";
        print_r($property);
    } else {
        echo "Property not found in database!\n";
    }
}

// Check favorites table structure
$sql = "DESCRIBE favorites";
$result = $db->query($sql);
if ($result) {
    echo "\nFavorites Table Structure:\n";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "\nError checking favorites table: " . $db->getConnection()->error;
}

// Try a test insert with error reporting
if (isset($_SESSION['user_id']) && isset($_GET['property_id'])) {
    $test_user_id = $_SESSION['user_id'];
    $test_property_id = intval($_GET['property_id']);
    
    echo "\nTrying test insert:\n";
    echo "User ID: $test_user_id\n";
    echo "Property ID: $test_property_id\n";
    
    $sql = "INSERT INTO favorites (user_id, property_id) VALUES ('$test_user_id', '$test_property_id')";
    if ($db->query($sql)) {
        echo "Test insert successful!\n";
        // Clean up test insert
        $db->query("DELETE FROM favorites WHERE user_id = '$test_user_id' AND property_id = '$test_property_id'");
    } else {
        echo "Test insert failed: " . $db->getConnection()->error . "\n";
    }
}

echo "</pre>";
$db->close();
?>
