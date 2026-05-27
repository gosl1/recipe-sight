<?php
require 'DBConnector.php';
 
$inventory_id = (int) $_GET['id'];
 
if ($inventory_id <= 0) {
    die('Invalid ID.');
}
 
$stmt = $conn->prepare("DELETE FROM user_inventory WHERE inventory_id = ?");
$stmt->bind_param('i', $inventory_id);
$stmt->execute();
$stmt->close();
 
header('Location: inventory.php');
exit;
?>
 