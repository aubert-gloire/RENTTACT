<?php
require_once 'includes/functions.php';

$db = new Database();

// Check table structure
$sql = "DESCRIBE properties";
$result = $db->query($sql);

echo "<h2>Properties Table Structure:</h2>";
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
echo "</pre>";

$db->close();
?>
