<?php
header('Content-Type: text/plain');
$conn = new mysqli('localhost', 'root', '', 'recipe_sight');

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare('SELECT user_id, username, email, password_hash FROM user WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result && password_verify($password, $result['password_hash'])) {
    echo $result['user_id'] . '|' . $result['username'] . '|' . $result['email'];
} else {
    echo 'fail';
}
?>