<?php
session_start();
$recipe_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($recipe_id === 0) {
    header('Location: home_recipesight.php');
    exit;
}

// Include database connection (adjust path: from src/components to src/database)
require_once __DIR__ . '/../database/DBConnector.php';

// Fetch recipe details
$stmt = $conn->prepare('
    SELECT r.recipe_id, r.title, r.description, r.instructions, c.category_name,
           n.calories, n.protein, n.carbs, n.fats
    FROM recipe r
    JOIN category c ON r.category_id = c.category_id
    LEFT JOIN nutrition_info n ON r.recipe_id = n.recipe_id
    WHERE r.recipe_id = ?
');
$stmt->bind_param('i', $recipe_id);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();

if (!$recipe) {
    echo 'Recipe not found.';
    exit;
}

// Fetch ingredients
$stmt = $conn->prepare('
    SELECT i.ingredient_name, ri.quantity, u.unit_name
    FROM recipe_ingredient ri
    JOIN ingredient i ON ri.ingredient_id = i.ingredient_id
    LEFT JOIN unit u ON ri.unit_id = u.unit_id
    WHERE ri.recipe_id = ?
');
$stmt->bind_param('i', $recipe_id);
$stmt->execute();
$ingredients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../styles/recipe_recipesight.css">
    <title><?php echo htmlspecialchars($recipe['title']); ?> - RecipeSight</title>
    <style>
        body { background: #fff8d4; font-family: Arial, sans-serif; padding: 20px; }
        .recipe-container { background: #313647; color: #fff8d4; padding: 20px; border-radius: 12px; max-width: 800px; margin: auto; }
        h1 { color: #ff8c00; }
        .back-link { display: inline-block; margin-top: 20px; color: #ff8c00; text-decoration: none; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; }
    </style>
</head>
<body>
    <div class="recipe-container">
        <h1><?php echo htmlspecialchars($recipe['title']); ?></h1>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($recipe['category_name']); ?></p>
        <p><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>
        
        <h2>Ingredients</h2>
        <ul>
        <?php foreach ($ingredients as $ing): ?>
            <li><?php 
                echo $ing['quantity'] . ' ' . $ing['unit_name'] . ' ' . $ing['ingredient_name'];
            ?></li>
        <?php endforeach; ?>
        </ul>
        
        <h2>Instructions</h2>
        <p><?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?></p>
        
        <?php if ($recipe['calories']): ?>
        <h2>Nutrition (per recipe)</h2>
        <p>🔥 Calories: <?php echo $recipe['calories']; ?> | 
           🥩 Protein: <?php echo $recipe['protein']; ?>g | 
           🍚 Carbs: <?php echo $recipe['carbs']; ?>g | 
           🧈 Fats: <?php echo $recipe['fats']; ?>g</p>
        <?php endif; ?>
        
        <a href="home_recipesight.php" class="back-link">← Back to search</a>
    </div>
</body>
</html>