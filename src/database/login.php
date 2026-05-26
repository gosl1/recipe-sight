<?php
// If logout is requested, destroy session and redirect
if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header('Location: ../src/components/home_recipesight.php');
    exit;
}

session_start();
header('Content-Type: text/plain');
include 'DBConnector.php';

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare('SELECT user_id, username, email, password_hash FROM user WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result && password_verify($password, $result['password_hash'])) {
    $_SESSION['user_id'] = $result['user_id'];
    $_SESSION['username'] = $result['username'];
    $_SESSION['email'] = $result['email'];
    echo $result['user_id'] . '|' . $result['username'] . '|' . $result['email'];
} else {
    echo 'fail';
}
$conn->close();
?>