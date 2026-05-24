<?php
// DBConnector.php - Auto-setup version

$servername = "localhost";
$server_username = "root";
$server_password = "";
$dbname = "recipe_sight"; // Change this to your database name

// First connect without database
$conn = new mysqli($servername, $server_username, $server_password);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
else{
    $conn->select_db($dbname);
}
?>