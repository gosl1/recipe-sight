<?php
header('Content-Type: text/plain');
$conn = new mysqli('localhost', 'root', '', 'recipe_sight');

$username = $_POST['username'];
$email = $_POST['email'];
$password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);

$stmt = $conn->prepare('INSERT INTO user (username, email, password_hash) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $username, $email, $password_hash);

if ($stmt->execute()) {
    $id = $stmt->insert_id;
    echo $id . '|' . $username . '|' . $email;
} else {
    echo 'fail';
}
?>