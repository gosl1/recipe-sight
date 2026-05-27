<?php
require 'DBConnector.php';

$user_id       = (int)   $_POST['user_id'];
$ingredient_id = (int)   $_POST['ingredient_id'];
$quantity      = (float) $_POST['quantity'];
$unit_id       = (int)   $_POST['unit_id'];

$stmt = $conn->prepare("INSERT INTO user_inventory (user_id, ingredient_id, quantity, unit_id)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param('iidi', $user_id, $ingredient_id, $quantity, $unit_id);
$stmt->execute();
$stmt->close();

header('Location: inventory.php');
exit;
?>