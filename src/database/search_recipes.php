<?php
ini_set('display_errors', 0); // Do not output HTML errors
header('Content-Type: application/json');
require_once __DIR__ . '/DBConnector.php'; // Use absolute path

// Get search parameters
$user_id = $_GET['user_id'] ?? $_POST['user_id'] ?? 1;
$recipe_name = trim($_GET['recipe_name'] ?? $_POST['recipe_name'] ?? '');
$category = trim($_GET['category'] ?? $_POST['category'] ?? '');
$ingredient = trim($_GET['ingredient'] ?? $_POST['ingredient'] ?? '');

// Build query with OR conditions
$sql = "
    SELECT DISTINCT r.recipe_id, r.title, r.description, r.instructions, c.category_name,
           n.calories, n.protein, n.carbs, n.fats
    FROM recipe r
    JOIN category c ON r.category_id = c.category_id
    LEFT JOIN nutrition_info n ON r.recipe_id = n.recipe_id
    WHERE r.user_id = ?
";
$params = [$user_id];
$types = 'i';
$conditions = [];

if (!empty($recipe_name)) {
    $conditions[] = "r.title LIKE ?";
    $params[] = "%$recipe_name%";
    $types .= 's';
}
if (!empty($category)) {
    $conditions[] = "c.category_name = ?";
    $params[] = $category;
    $types .= 's';
}
if (!empty($ingredient)) {
    $conditions[] = "r.recipe_id IN (
        SELECT ri.recipe_id
        FROM recipe_ingredient ri
        JOIN ingredient i ON ri.ingredient_id = i.ingredient_id
        WHERE i.ingredient_name = ?
    )";
    $params[] = $ingredient;
    $types .= 's';
}

if (count($conditions) > 0) {
    $sql .= " AND (" . implode(" OR ", $conditions) . ")";
}

$sql .= " ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$recipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get ingredients for each recipe
foreach ($recipes as &$recipe) {
    $istmt = $conn->prepare('
        SELECT i.ingredient_name, ri.quantity, u.unit_name
        FROM recipe_ingredient ri
        JOIN ingredient i ON ri.ingredient_id = i.ingredient_id
        LEFT JOIN unit u ON ri.unit_id = u.unit_id
        WHERE ri.recipe_id = ?
    ');
    $istmt->bind_param('i', $recipe['recipe_id']);
    $istmt->execute();
    $ingredients = $istmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $ingredient_strings = array_map(function($ing) {
        $qty = $ing['quantity'] ? $ing['quantity'] . ' ' : '';
        $unit = $ing['unit_name'] ? $ing['unit_name'] . ' ' : '';
        return $qty . $unit . $ing['ingredient_name'];
    }, $ingredients);
    $recipe['ingredients'] = implode('<br>', $ingredient_strings);
}

echo json_encode($recipes);
?>