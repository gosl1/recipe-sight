<?php
// DBConnector.php - Auto-setup version

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_sight"; // Change this to your database name

// First connect without database
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
else{
    $conn->select_db($dbname);
}
?>