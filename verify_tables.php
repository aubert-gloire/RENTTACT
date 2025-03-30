<?php
require_once 'includes/functions.php';

$db = new Database();

echo "<h2>Database Table Verification</h2>";
echo "<pre>";

// Check if favorites table exists
$sql = "SHOW TABLES LIKE 'favorites'";
$result = $db->query($sql);

if ($result && $result->num_rows > 0) {
    echo "✓ Favorites table exists\n";
    
    // Check table structure
    $sql = "DESCRIBE favorites";
    $result = $db->query($sql);
    
    echo "\nCurrent table structure:\n";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    
    echo "\nDropping and recreating favorites table...\n";
    
    // Drop the table
    $sql = "DROP TABLE IF EXISTS favorites";
    if ($db->query($sql)) {
        echo "✓ Old table dropped successfully\n";
    } else {
        echo "❌ Error dropping table: " . $db->getConnection()->error . "\n";
    }
} else {
    echo "❌ Favorites table does not exist\n";
}

// Create the table
$sql = "CREATE TABLE favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    property_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (property_id) REFERENCES properties(id),
    UNIQUE KEY unique_favorite (user_id, property_id)
)";

if ($db->query($sql)) {
    echo "✓ New favorites table created successfully\n";
    
    echo "\nVerifying new table structure:\n";
    $sql = "DESCRIBE favorites";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "❌ Error creating table: " . $db->getConnection()->error . "\n";
}

echo "</pre>";
$db->close();
?>
