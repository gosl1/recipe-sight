<?php
header('Content-Type: application/json');
include 'DBConnector.php';


error_reporting(0); // Prevents warnings from corrupting the JSON payload

if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection is offline.']);
    exit;
}

// Read the search term sent by your JS file
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$safeSearch = $conn->real_escape_string($searchTerm);

// Base query structured to match recipesight_builder.sql
$sql = "SELECT r.recipe_id, r.title, r.description, r.instructions, r.image_url,
               c.category_name, 
               n.calories, n.protein, n.carbs, n.fats
        FROM recipe r
        LEFT JOIN category c ON r.category_id = c.category_id
        LEFT JOIN nutrition_info n ON r.recipe_id = n.recipe_id";

// Apply filters if the user typed something into the input field
if ($safeSearch !== '') {
    $sql .= " WHERE r.title LIKE '%$safeSearch%' 
                 OR r.description LIKE '%$safeSearch%' 
                 OR c.category_name LIKE '%$safeSearch%'";
}

$sql .= " ORDER BY r.title ASC";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed: ' . $conn->error]);
    exit;
}

// Gather rows into an array
$recipes = [];
while ($row = $result->fetch_assoc()) {
    $recipes[] = $row;
}

// Output cleanly back to your JavaScript file
echo json_encode($recipes);
?>