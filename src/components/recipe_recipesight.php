<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecipeSight Catalog</title>
    <link rel="stylesheet" href="../styles/recipe_recipesight.css">

</head>
<body>

<div class="container">
    <header class="search-header">
        <h1>Sight Your Recipes</h1>
        
        <div class="search-action-row">
            <input type="text" id="search-bar" placeholder="Enter dish name, category, or ingredients..." autocomplete="off">
            <button id="search-btn">Search</button>
        </div>
    </header>

    <main class="recipe-container-grid" id="recipe-cards-output"></main>
</div>

<script src="../scripts/recipe_recipesight.js"></script>
</body>
</html>