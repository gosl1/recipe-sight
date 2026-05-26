<?php
session_start(); 

if (!isset($_SESSION['user_id'])) {
    header('Location: ../components/profile_recipesight.php'); //redirect to the login/signup page
    exit;
}

require 'DBConnector.php';
$user_id = $_SESSION['user_id']; 


// Fetch inventory
$result = $conn->query("SELECT
        ui.inventory_id,
        ui.ingredient_id,
        ui.unit_id,
        i.ingredient_name,
        ui.quantity,
        u.unit_name
    FROM user_inventory ui
    JOIN ingredient i ON ui.ingredient_id = i.ingredient_id
    JOIN unit u       ON ui.unit_id       = u.unit_id
    WHERE ui.user_id = $user_id
    ORDER BY ui.inventory_id ASC
");

$inventory_list = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $inventory_list[] = $row;
    }
}

// Fetch ingredients for dropdown
$ing_result = $conn->query("
    SELECT ingredient_id, ingredient_name 
    FROM ingredient 
    ORDER BY ingredient_name ASC
");
$ingredient_list = [];
if ($ing_result) {
    while ($row = $ing_result->fetch_assoc()) {
        $ingredient_list[] = $row;
    }
}

// Fetch units for dropdown
$unit_result = $conn->query("
    SELECT unit_id, unit_name 
    FROM unit 
    ORDER BY unit_id ASC
");
$unit_list = [];
if ($unit_result) {
    while ($row = $unit_result->fetch_assoc()) {
        $unit_list[] = $row;
    }
}

// Load the HTML view
require '../components/inventory_recipesight.php';
?>