<?php
// src/database/display_recipe.php
include 'DBConnector.php'; // Includes your MySQLi $conn connection

header('Content-Type: application/json');
error_reporting(0); // Suppress raw error leaks from corrupting the JSON stream

// Validate that your MySQLi connection variable is active
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection variable missing or offline.']);
    exit;
}

// Capture a specific recipe ID parameter safely if requested by your frontend script
$recipeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    if ($recipeId > 0) {
        // 1. If an explicit ID is passed, fetch that single specific recipe
        $sql = "SELECT r.recipe_id, r.title, r.description, r.instructions, r.image_url,
                       c.category_name, 
                       n.calories, n.protein, n.carbs, n.fats
                FROM recipe r
                LEFT JOIN category c ON r.category_id = c.category_id
                LEFT JOIN nutrition_info n ON r.recipe_id = n.recipe_id
                WHERE r.recipe_id = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        
        $stmt->bind_param('i', $recipeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $recipe = $result->fetch_assoc();

        if ($recipe) {
            echo json_encode($recipe);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'No recipe matches the provided item ID.']);
        }
    } else {
        // 2. Fallback: If no ID is passed, fetch all recipes (useful for catalogs)
        $sql = "SELECT r.recipe_id, r.title, r.description, r.instructions, r.image_url,
                       c.category_name, 
                       n.calories, n.protein, n.carbs, n.fats
                FROM recipe r
                LEFT JOIN category c ON r.category_id = c.category_id
                LEFT JOIN nutrition_info n ON r.recipe_id = n.recipe_id
                ORDER BY r.title ASC";
        
        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception($conn->error);
        }

        $recipes = [];
        while ($row = $result->fetch_assoc()) {
            $recipes[] = $row;
        }
        echo json_encode($recipes);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Data retrieval statement failed: ' . $e->getMessage()]);
}
?>