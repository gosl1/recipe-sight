<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipes - RecipeSight</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/recipe_recipesight.css">
</head>
<body class="recipePage">

    <div class="recipeContainer">

        <div class="searchSection">
            <form action="/search" method="GET" class="searchBarForm">
                <div class="searchWrapper">
                    <i class="fas fa-search searchIcon"></i>
                    <input type="text" name="query" placeholder="Find a Recipe..." autocomplete="off" class="searchInput">
                    <button type="submit" class="searchSubmitBtn"> Search
                        <i class="fas fa-arrow-circle-right"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="filterSection">
            <label class="inventoryToggle">
                <input type="checkbox" id="matchInventory">
                <span class="slider"></span> Match My Inventory Ingredients
            </label>
        </div>

        <h1 class="recipeTitle">Suggested Recipes</h1>

        <div class="recipeGrid">

            <!-- <div class="recipeCard">
                <div class="cardImageWrapper">
                    <img src="../images/chicken-curry.jpg" alt="Chicken Curry" class="recipeImg">
                    <button class="favoriteBtn" aria-label="Save Recipe"><i class="far fa-heart"></i></button>
                    <span class="inventoryMatchTag">4/5 Ingredients Owned</span>
                </div>
                <div class="cardContent">
                    <div class="cardTags">
                        <span class="tag cuisine">Indian</span>
                        <span class="tag difficulty">Medium</span>
                    </div>
                    <h2 class="recipeName">Chicken Curry</h2>
                    <div class="recipeMeta">
                        <span class="metaItem"><i class="far fa-clock"></i> 45 mins</span>
                        <span class="metaItem"><i class="fas fa-star"></i> 4.8</span>
                    </div>
                </div>
            </div>

            <div class="recipeCard">
                <div class="cardImageWrapper">
                    <img src="../images/pork-sinigang.jpg" alt="Pork Sinigang" class="recipeImg">
                    <button class="favoriteBtn" aria-label="Save Recipe"><i class="far fa-heart"></i></button>
                    <span class="inventoryMatchTag ready">Ready to Cook</span>
                </div>
                <div class="cardContent">
                    <div class="cardTags">
                        <span class="tag cuisine">Filipino</span>
                        <span class="tag difficulty">Easy</span>
                    </div>
                    <h2 class="recipeName">Pork Sinigang</h2>
                    <div class="recipeMeta">
                        <span class="metaItem"><i class="far fa-clock"></i> 40 mins</span>
                        <span class="metaItem"><i class="fas fa-star"></i> 4.9</span>
                    </div>
                </div>
            </div> -->

            <div class="recipeCard">
                <div class="cardImageWrapper">
                    <img src="../images/placeholder.jpg" alt="Recipe Template" class="recipeImg">
                    <button class="favoriteBtn" aria-label="Save Recipe"><i class="far fa-heart"></i></button>
                </div>
                <div class="cardContent">
                    <div class="cardTags">
                        <span class="tag category">Category</span>
                        <span class="tag difficulty">Easy</span>
                    </div>
                    <h2 class="recipeName">Recipe Title Template</h2>
                    <div class="recipeMeta">
                        <span class="metaItem"><i class="far fa-clock"></i> -- mins</span>
                        <span class="metaItem"><i class="fas fa-star"></i> 0.0</span>
                    </div>
                </div>
            </div>

        </div> </div> <script src="../scripts/recipe_recipesights.js"></script>
</body>
</html>