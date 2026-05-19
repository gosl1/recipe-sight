<?php
header('Content-Type: text/plain');
$database_servername = "localhost";
$database_username = "root";
$database_password = "";

$conn = new mysqli($database_servername, $database_username, $database_password, "recipe_sight");

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare('SELECT user_id, username, email, password_hash FROM user WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

    # temporary password hash fix since we don't have a hash system yet lol
if ($result && $password == $result['password_hash']) {
    echo $result['user_id'] . '|' . $result['username'] . '|' . $result['email'];
} else {
    echo 'fail';
}
?>