<?php
require_once 'includes/functions.php';

$db = new Database();

$sql = "CREATE TABLE IF NOT EXISTS favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    property_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (property_id) REFERENCES properties(id),
    UNIQUE KEY unique_favorite (user_id, property_id)
)";

if ($db->query($sql)) {
    echo "✓ Favorites table created successfully!";
} else {
    echo "❌ Error creating favorites table: " . $db->getConnection()->error;
}

$db->close();
?>
