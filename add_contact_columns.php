<?php
require_once 'includes/functions.php';

$db = new Database();

$sql = "ALTER TABLE properties
        ADD COLUMN contact_phone VARCHAR(20) AFTER image_path,
        ADD COLUMN contact_email VARCHAR(255) AFTER contact_phone";

if ($db->query($sql)) {
    echo "Successfully added contact_phone and contact_email columns!";
    
    // Verify the changes
    $verify_sql = "DESCRIBE properties";
    $result = $db->query($verify_sql);
    
    echo "<h2>Updated Properties Table Structure:</h2>";
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "Error adding columns: " . $db->getConnection()->error;
}

$db->close();
?>
