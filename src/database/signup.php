<?php
header('Content-Type: text/plain');
require_once 'DBConnector.php';

$conn = new mysqli($database_servername, $database_username, $database_password, $dbname);

$username = $_POST['username'];
$email = $_POST['email'];
$password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);

$stmt = $conn->prepare('INSERT INTO user (username, email, password_hash) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $username, $email, $password_hash);

try {
    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        echo $id . '|' . $new_username . '|' . $new_email;
    } else {
        echo 'fail';
    }
} catch (Exception $e) {
    echo 'fail';
}
?>