<?php
header('Content-Type: application/json');
require_once 'DBConnector.php';

$result = $conn->query('SELECT ingredient_id, ingredient_name FROM ingredient ORDER BY ingredient_name');
$ingredients = $result->fetch_all(MYSQLI_ASSOC);
echo json_encode($ingredients);
?>