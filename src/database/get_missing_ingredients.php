<?php
header('Content-Type: application/json');
require_once 'DBConnector.php';

// Auth is not fully implemented yet — hardcoded to user_id 1 (recipe_admin).
// Once login is wired up, replace this with: $_SESSION['user_id'] ?? 1
$user_id   = 1;
$recipe_id = intval($_GET['recipe_id'] ?? $_POST['recipe_id'] ?? 0);

if (!$recipe_id) {
    echo json_encode(['error' => 'No recipe ID provided']);
    exit;
}

// All ingredients required by the recipe
$stmt = $conn->prepare('
    SELECT i.ingredient_id, i.ingredient_name, ri.quantity, u.unit_name
    FROM recipe_ingredient ri
    JOIN ingredient i ON ri.ingredient_id = i.ingredient_id
    LEFT JOIN unit u   ON ri.unit_id       = u.unit_id
    WHERE ri.recipe_id = ?
');
$stmt->bind_param('i', $recipe_id);
$stmt->execute();
$recipe_ingredients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Ingredients the user already has
$stmt2 = $conn->prepare('SELECT ingredient_id FROM user_inventory WHERE user_id = ?');
$stmt2->bind_param('i', $user_id);
$stmt2->execute();
$user_ids = array_column($stmt2->get_result()->fetch_all(MYSQLI_ASSOC), 'ingredient_id');

// Return only what's missing
$missing = [];
foreach ($recipe_ingredients as $ri) {
    if (!in_array($ri['ingredient_id'], $user_ids)) {
        $missing[] = [
            'name'     => $ri['ingredient_name'],
            'quantity' => $ri['quantity'],
            'unit'     => $ri['unit_name'] ?? '',
        ];
    }
}

echo json_encode($missing);