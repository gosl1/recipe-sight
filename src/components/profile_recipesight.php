<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../styles/profile_recipesight.css">
    <title>Profile</title>
</head>
<body class="profilePage">
    <button onclick="showLogin()">Login</button>
    <button onclick="showSignup()">Sign Up</button>

    <div id="guestView">
        <div id="loginForm" style="display:none;">
            <label for="email">Email: </label><br>
            <input type="email" id="loginEmail" name="loginEmail"><br>
            <label for="password">Password: </label><br>
            <input type="password" id="loginPassword" name="loginPassword"><br>
            <button onclick="login()">Submit</button>
        </div>

        <div id="signupForm" style="display:none;">
            <label for="name">Name: </label><br>
            <input type="text" id="signupName" name="signupName"><br>
            <label for="email">Email: </label><br>
            <input type="email" id="signupEmail" name="signupEmail"><br>
            <label for="password">Password: </label><br>
            <input type="password" id="signupPassword" name="signupPassword"><br>
            <button onclick="signup()">Submit</button>
        </div>
    </div>

    <!-- Shown when logged in -->
    <div id="userView" style="display:none;">
        <div>
            <p>Username: <span id="displayUsername"></span></p>
            <p>Email: <span id="displayEmail"></span></p>
            <button onclick="logout()">Logout</button>
        </div>
        <div>
            <button onclick="showAddRecipe()">+ Add Recipe</button>

            <div id="addRecipeForm" style="display:none;">
                <label>Title:<br>
                    <input type="text" id="newTitle"><br>
                </label>
                <label>Description:<br>
                    <textarea id="newDesc"></textarea><br>
                </label>
                <label>Instructions:<br>
                    <textarea id="newInstr"></textarea><br>
                </label>
                <label>Category:<br>
                    <select id="newCategory">
                        <option value="1">Main Dish</option>
                        <option value="2">Breakfast</option>
                        <option value="3">Dessert</option>
                        <option value="4">Baking</option>
                        <option value="5">Soup</option>
                        <option value="6">Salad</option>
                        <option value="7">Snack</option>
                        <option value="8">Beverage</option>
                        <option value="9">Sauce</option>
                        <option value="10">Healthy</option>
                    </select><br>
                </label>

                <!-- Ingredient rows -->
                <label>Ingredients:</label><br>
                <div id="ingredientRows">
                    <!-- Rows injected by JS -->
                </div>
                <button type="button" onclick="addIngredientRow()">+ Add Ingredient</button><br><br>

                <button onclick="submitAddRecipe()">Submit</button>
                <button onclick="hideAddRecipe()">Cancel</button>
            </div>
        </div>

        <div>
            <table style="width: 100%">
                <tr>
                    <th>Recipe ID</th>
                    <th>Recipe Name</th>
                    <th>Description</th>
                    <th>Instructions</th>
                    <th>Ingredients</th>
                    <th>Category</th>
                    <th>Methods</th>
                </tr>
            </table>
        </div>
    </div>

    <script src="../scripts/profile_recipesight.js"></script>
</body>
</html>