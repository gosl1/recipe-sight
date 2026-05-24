<?php
header('Content-Type: application/json');
include 'DBConnector.php';

$user_id = $_POST['user_id'];

// Get all recipes for this user
$stmt = $conn->prepare('
    SELECT r.recipe_id, r.title, r.description, r.instructions, c.category_name
    FROM recipe r
    JOIN category c ON r.category_id = c.category_id
    WHERE r.user_id = ?
');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$recipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// For each recipe, get its ingredients as a single grouped string
foreach ($recipes as &$recipe) {
    $rid = $recipe['recipe_id'];
    $istmt = $conn->prepare('
        SELECT i.ingredient_name, ri.quantity, u.unit_name
        FROM recipe_ingredient ri
        JOIN ingredient i ON ri.ingredient_id = i.ingredient_id
        JOIN unit u ON ri.unit_id = u.unit_id
        WHERE ri.recipe_id = ?
    ');
    $istmt->bind_param('i', $rid);
    $istmt->execute();
    $ingredients = $istmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Combine all ingredients into one string
    $recipe['ingredients'] = implode('<br>', array_map(function($ing) {
        return $ing['quantity'] . ' ' . $ing['unit_name'] . ' ' . $ing['ingredient_name'];
    }, $ingredients));
}

echo json_encode($recipes);
?>