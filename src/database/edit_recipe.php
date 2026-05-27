<?php
header('Content-Type: text/plain');
include 'DBConnector.php';

$recipe_id    = intval($_POST['recipe_id']);
$title        = trim($_POST['title']);
$description  = trim($_POST['description'] ?? '');
$instructions = trim($_POST['instructions'] ?? '');
$category_id  = intval($_POST['category_id']);
$ingredients_json = $_POST['ingredients'] ?? '[]';

if (!$recipe_id || !$title) {
    echo 'fail';
    exit;
}

$ingredients = json_decode($ingredients_json, true);
if ($ingredients === null) {
    echo 'fail';
    exit;
}

$conn->begin_transaction();

try {
    // 1. Update the recipe row (now includes category_id)
    $stmt = $conn->prepare(
        'UPDATE recipe SET title = ?, description = ?, instructions = ?, category_id = ?
         WHERE recipe_id = ?'
    );
    $stmt->bind_param('sssii', $title, $description, $instructions, $category_id, $recipe_id);
    $stmt->execute();
    $stmt->close();

    // 2. Replace all ingredient rows
    $del = $conn->prepare('DELETE FROM recipe_ingredient WHERE recipe_id = ?');
    $del->bind_param('i', $recipe_id);
    $del->execute();
    $del->close();

    if (!empty($ingredients)) {
        $ins = $conn->prepare(
            'INSERT INTO recipe_ingredient (recipe_id, ingredient_id, quantity, unit_id)
             VALUES (?, ?, ?, ?)'
        );
        foreach ($ingredients as $ing) {
            $ing_id  = intval($ing['ingredient_id']);
            $qty     = floatval($ing['quantity']);
            $unit_id = intval($ing['unit_id']);
            $ins->bind_param('iidd', $recipe_id, $ing_id, $qty, $unit_id);
            $ins->execute();
        }
        $ins->close();
    }

    $conn->commit();
    echo 'success';

} catch (Exception $e) {
    $conn->rollback();
    echo 'fail';
}

$conn->close();
?>