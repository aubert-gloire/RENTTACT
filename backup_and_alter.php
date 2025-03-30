<?php
require_once 'includes/functions.php';

$db = new Database();

// First, backup existing data
$backup_sql = "CREATE TABLE properties_backup LIKE properties;
               INSERT INTO properties_backup SELECT * FROM properties";

echo "<h2>Backup and Alter Process:</h2>";

// Step 1: Create backup
if ($db->query("CREATE TABLE IF NOT EXISTS properties_backup LIKE properties")) {
    echo "✓ Created backup table structure<br>";
    
    if ($db->query("INSERT INTO properties_backup SELECT * FROM properties")) {
        echo "✓ Copied existing data to backup<br>";
        
        // Step 2: Add new columns
        $alter_sql = "ALTER TABLE properties
                     ADD COLUMN contact_phone VARCHAR(20) AFTER image_path,
                     ADD COLUMN contact_email VARCHAR(255) AFTER contact_phone";
        
        if ($db->query($alter_sql)) {
            echo "✓ Successfully added new columns<br>";
            
            // Step 3: Verify the changes
            $verify_sql = "DESCRIBE properties";
            $result = $db->query($verify_sql);
            
            echo "<h3>Updated Properties Table Structure:</h3>";
            echo "<pre>";
            while ($row = $result->fetch_assoc()) {
                print_r($row);
            }
            echo "</pre>";
            
            echo "<p>✓ All operations completed successfully!</p>";
            echo "<p>A backup of your data is stored in the 'properties_backup' table.</p>";
        } else {
            echo "❌ Error adding columns: " . $db->getConnection()->error;
        }
    } else {
        echo "❌ Error copying data: " . $db->getConnection()->error;
    }
} else {
    echo "❌ Error creating backup: " . $db->getConnection()->error;
}

$db->close();
?>
