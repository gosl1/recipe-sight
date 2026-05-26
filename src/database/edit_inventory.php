<?php
require 'DBConnector.php';

$inventory_id  = (int)   $_POST['inventory_id'];
$ingredient_id = (int)   $_POST['ingredient_id'];
$quantity      = (float) $_POST['quantity'];
$unit_id       = (int)   $_POST['unit_id'];

$stmt = $conn->prepare("UPDATE user_inventory
    SET ingredient_id = ?, quantity = ?, unit_id = ?
    WHERE inventory_id = ?
");
$stmt->bind_param('idii', $ingredient_id, $quantity, $unit_id, $inventory_id);
$stmt->execute();
$stmt->close();

header('Location: inventory.php');
exit;
?>