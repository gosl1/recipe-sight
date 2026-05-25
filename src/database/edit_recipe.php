<?php
header('Content-Type: text/plain');
include 'DBConnector.php';

$recipe_id   = $_POST['recipe_id'];
$title       = $_POST['title'];
$description = $_POST['description'];
$instructions = $_POST['instructions'];

$stmt = $conn->prepare('UPDATE recipe SET title = ?, description = ?, instructions = ? WHERE recipe_id = ?');
$stmt->bind_param('sssi', $title, $description, $instructions, $recipe_id);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'fail';
}
?>