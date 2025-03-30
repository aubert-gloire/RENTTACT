<?php
require_once 'includes/functions.php';

$db = new Database();

echo "<h2>Reviews Table Verification</h2>";
echo "<pre>";

// Check if reviews table exists
$sql = "SHOW TABLES LIKE 'reviews'";
$result = $db->query($sql);

if ($result && $result->num_rows > 0) {
    echo "✓ Reviews table exists\n";
    
    // Check table structure
    $sql = "DESCRIBE reviews";
    $result = $db->query($sql);
    
    echo "\nCurrent table structure:\n";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    
    echo "\nDropping and recreating reviews table...\n";
    
    // Drop the table
    $sql = "DROP TABLE IF EXISTS reviews";
    if ($db->query($sql)) {
        echo "✓ Old table dropped successfully\n";
    } else {
        echo "❌ Error dropping table: " . $db->getConnection()->error . "\n";
    }
} else {
    echo "❌ Reviews table does not exist\n";
}

// Create the table
$sql = "CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_review (property_id, user_id)
)";

if ($db->query($sql)) {
    echo "✓ New reviews table created successfully\n";
    
    echo "\nVerifying new table structure:\n";
    $sql = "DESCRIBE reviews";
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
