<?php
header('Content-Type: text/plain');
require_once 'DBConnector.php';
session_start();
$user_id = $_SESSION['user_id'] ?? intval($_POST['user_id'] ?? 0);
$category_id = intval($_POST['category_id']);
$title       = trim($_POST['title']);
$description = trim($_POST['description'] ?? '');
$instructions = trim($_POST['instructions'] ?? '');
$ingredients_json = $_POST['ingredients'] ?? '[]';

if (!$title || !$user_id) {
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
    // 1. Insert the recipe
    $stmt = $conn->prepare(
        'INSERT INTO recipe (user_id, category_id, title, description, instructions)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('iisss', $user_id, $category_id, $title, $description, $instructions);
    $stmt->execute();
    $recipe_id = $conn->insert_id;
    $stmt->close();

    // 2. Insert each ingredient row
    if (!empty($ingredients)) {
        $stmt = $conn->prepare(
            'INSERT INTO recipe_ingredient (recipe_id, ingredient_id, quantity, unit_id)
             VALUES (?, ?, ?, ?)'
        );
        foreach ($ingredients as $ing) {
            $ing_id  = intval($ing['ingredient_id']);
            $qty     = floatval($ing['quantity']);
            $unit_id = intval($ing['unit_id']);
            $stmt->bind_param('iidd', $recipe_id, $ing_id, $qty, $unit_id);
            $stmt->execute();
        }
        $stmt->close();
    }

    $conn->commit();
    echo $recipe_id; // return new id so JS can reload

} catch (Exception $e) {
    $conn->rollback();
    echo 'fail';
}

$conn->close();
?>