<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../styles/home_recipesight.css">
    <title>Home - RecipeSight</title>
    <script src="../scripts/home_recipesight.js" defer></script>
    <!-- Additional inline style for autocomplete (small) -->
    <style>
        .autocomplete-items {
            position: absolute;
            border: 1px solid #ccc;
            background: #fff;  /* white background */
            max-height: 150px;
            overflow-y: auto;
            z-index: 99;
        }
        .autocomplete-items div {
            padding: 8px;
            cursor: pointer;
            color: #000;        /* black text */
            background-color: #fff;
        }
        .autocomplete-items div:hover {
            background-color: #ff8c00;  /* orange highlight */
            color: #fff;                /* white text on hover */
        }

        .search-fields {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 20px 50px;
        }
        .search-fields div {
            flex: 1;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-size: 18px;
        }
        input, select, button {
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            background: #ff8c00;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 22px;
        }
        button:hover {
            background: #e07b00;
        }
        .recipe-card {
            background: #313647;
            color: #fff8d4;
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
        }
        .recipe-card a {
            color: #ff8c00;
            text-decoration: none;
        }
        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }
        .tag-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        .ingredient-tag {
            background: #ff8c00;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .ingredient-tag button {
            background: none;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
            padding: 0;
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body class="homePage">
    <div class="homeRecipeSightBox">
        <h1 class="appName">RecipeSight</h1>

        <!-- Navigation -->
        <div class="nav" style="text-align:center; margin-bottom:30px;">
        </div>

        <!-- Search Form -->
        <form onsubmit="return false;">
            <div class="search-fields">
                <div>
                    <label>Recipe Name:</label>
                    <input type="text" id="recipeNameInput" placeholder="e.g., Chicken Adobo">
                </div>
                <div>
                    <label>Category:</label>
                    <select id="recipeCategorySelect">
                        <option value="">All Categories</option>
                        <option value="Main Dish">Main Dish</option>
                        <option value="Breakfast">Breakfast</option>
                        <option value="Dessert">Dessert</option>
                        <option value="Baking">Baking</option>
                        <option value="Soup">Soup</option>
                        <option value="Salad">Salad</option>
                        <option value="Snack">Snack</option>
                        <option value="Beverage">Beverage</option>
                        <option value="Sauce">Sauce</option>
                        <option value="Healthy">Healthy</option>
                    </select>
                </div>
                <div style="position:relative;">
                    <label>Ingredients:</label>
                    <input type="text" id="ingredientInput" placeholder="Type ingredient name and press Enter or click suggestion...">
                    <div id="ingredientTags" class="tag-container"></div>
                    <div id="autocomplete-list" class="autocomplete-items"></div>
                </div>
                <div>
                    <button type="button" onclick="searchRecipes()">Search</button>
                </div>
            </div>
        </form>

        <!-- Search Results -->
        <div id="searchResults"></div>
    </div>
</body>
</html>