<?php
header('Content-Type: text/plain');
include 'DBConnector.php';

$recipe_id = $_POST['recipe_id'];

$stmt = $conn->prepare('DELETE FROM recipe WHERE recipe_id = ?');
$stmt->bind_param('i', $recipe_id);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'fail';
}
?>