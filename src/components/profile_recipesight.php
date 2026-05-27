<!-- This html file is for the profile page not final -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | RecipeSights</title>
    <link rel="stylesheet" href="../styles/profile_recipesight.css">
</head>

<body class="profilePage">

<main class="profileContainer">

    <!-- Header -->
    <section class="profileHeader">
        <h1>My Profile</h1>
        <p>Manage your recipes and saved ingredients.</p>
    </section>

    <!-- Guest View -->
    <section id="guestView">

        <div class="authButtons">
            <button onclick="showLogin()">Login</button>
            <button onclick="showSignup()">Sign Up</button>
        </div>

        <!-- Login Form -->
        <div id="loginForm" class="authForm">
            <h2>Login</h2>

            <label for="loginEmail">Email</label>
            <input type="email" id="loginEmail">

            <label for="loginPassword">Password</label>
            <input type="password" id="loginPassword">

            <button onclick="login()">Submit</button>
        </div>

        <!-- Signup Form -->
        <div id="signupForm" class="authForm">
            <h2>Create Account</h2>

            <label for="signupName">Name</label>
            <input type="text" id="signupName">

            <label for="signupEmail">Email</label>
            <input type="email" id="signupEmail">

            <label for="signupPassword">Password</label>
            <input type="password" id="signupPassword">

            <button onclick="signup()">Submit</button>
        </div>

    <!-- Shown when logged in -->
    <div id="userView" style="display:none;">
        <div>
            <p>Username: <span id="displayUsername"></span></p>
            <p>Email: <span id="displayEmail"></span></p>
            <button onclick="logout()">Logout</button>
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

</body>
</html>