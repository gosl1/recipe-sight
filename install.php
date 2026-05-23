<?php
// install.php - Run this after each git pull to sync database
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_sight";  // Use underscore, not hyphen

echo "<pre>";

// Connect to MySQL without selecting a database
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
echo "✓ Connected to MySQL<br>";

// Drop database if exists
if ($conn->query("DROP DATABASE IF EXISTS $dbname")) {
    echo "✓ Dropped old database (if existed)<br>";
} else {
    echo "⚠️ Could not drop database: " . $conn->error . "<br>";
}

// Create fresh database
if ($conn->query("CREATE DATABASE $dbname")) {
    echo "✓ Created fresh database '$dbname'<br>";
} else {
    die("❌ Error creating database: " . $conn->error . "<br>");
}

// Select the database
$conn->select_db($dbname);
echo "✓ Selected database '$dbname'<br>";

// Import the SQL file - adjust path if needed
$sqlFile = __DIR__ . '/src/database/recipesight_builder.sql';

if (!file_exists($sqlFile)) {
    die("❌ Error: SQL file not found at: " . $sqlFile);
}
echo "✓ Found SQL file at: " . $sqlFile . "<br>";

// Read the SQL file
$sql = file_get_contents($sqlFile);

if ($sql === false) {
    die("❌ Error reading SQL file");
}
echo "✓ Read SQL file (" . strlen($sql) . " bytes)<br>";

// Execute the SQL queries
if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "✅ Database imported successfully!<br>";
} else {
    echo "❌ Error importing SQL: " . $conn->error . "<br>";
}

echo "</pre>";
echo "<a href='home.php'>Go to RecipeSight</a>";
?>