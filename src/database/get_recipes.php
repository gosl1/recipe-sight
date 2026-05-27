<?php
header('Content-Type: application/json');
require_once 'DBConnector.php';

$user_id = $_POST['user_id'] ?? $_GET['user_id'] ?? 1;

$stmt = $conn->prepare('
    SELECT r.recipe_id, r.title, r.description, r.instructions,
           r.category_id, c.category_name,
           n.calories, n.protein, n.carbs, n.fats
    FROM recipe r
    JOIN category c ON r.category_id = c.category_id
    LEFT JOIN nutrition_info n ON r.recipe_id = n.recipe_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$recipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($recipes as &$recipe) {
    $istmt = $conn->prepare('
        SELECT i.ingredient_id, i.ingredient_name, ri.quantity, ri.unit_id, u.unit_name
        FROM recipe_ingredient ri
        JOIN ingredient i ON i.ingredient_id = ri.ingredient_id
        LEFT JOIN unit u ON ri.unit_id = u.unit_id
        WHERE ri.recipe_id = ?
    ');
    $istmt->bind_param('i', $recipe['recipe_id']);
    $istmt->execute();
    $rows = $istmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Raw array for edit pre-population
    $recipe['ingredient_list'] = $rows;

    // Display string for the table cell
    $recipe['ingredients'] = implode('<br>', array_map(function($ing) {
        return $ing['quantity'] . ' ' . $ing['unit_name'] . ' ' . $ing['ingredient_name'];
    }, $rows));
}

echo json_encode($recipes);
?>