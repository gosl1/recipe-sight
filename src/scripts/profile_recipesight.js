const user = JSON.parse(sessionStorage.getItem('user'));
if (user) showUserView(user);

function showUserView(user) {
    document.getElementById('guestView').style.display = 'none';
    document.getElementById('userView').style.display = 'block';
    document.getElementById('displayUsername').textContent = user.username;
    document.getElementById('displayUsername').dataset.id = user.id;
    document.getElementById('displayEmail').textContent = user.email;
    loadRecipes(user.id);
}

function showLogin() {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('signupForm').style.display = 'none';
}

function showSignup() {
    document.getElementById('signupForm').style.display = 'block';
    document.getElementById('loginForm').style.display = 'none';
}

async function login() {
    const formDetails = new FormData();
    formDetails.append('email', document.getElementById('loginEmail').value);
    formDetails.append('password', document.getElementById('loginPassword').value);

    const res = await fetch('../database/login.php', {
        method: 'POST',
        body: formDetails
    });

    const data = await res.text();
    if (data === 'fail') {
        alert('Invalid email or password');
    } else {
        const [id, username, email] = data.split('|');
        const user = { id, username, email };
        sessionStorage.setItem('user', JSON.stringify(user));
        showUserView(user);
    }
    
}

async function signup() {
    const formDetails = new FormData();
    formDetails.append('username', document.getElementById('signupName').value);
    formDetails.append('email', document.getElementById('signupEmail').value);
    formDetails.append('password', document.getElementById('signupPassword').value);

    const res = await fetch('../database/signup.php', {
        method: 'POST',
        body: formDetails
    });

    const data = await res.text();
    if (data === 'fail') {
        alert('Signup failed. Email may already be in use.');
    } else {
        const [id, username, email] = data.split('|');
        const user = { id, username, email };
        sessionStorage.setItem('user', JSON.stringify(user));
        showUserView(user);
    }
    console.log('signup response:', JSON.stringify(data));
}

async function logout() {
    await fetch('../database/login.php?logout=1');
    sessionStorage.removeItem('user');
    location.reload();
}

async function loadRecipes(userId) {
    const formDetails = new FormData();
    formDetails.append('user_id', userId);

    const res = await fetch('../database/get_recipes.php', {
        method: 'POST',
        body: formDetails
    });

    const recipes = await res.json();
    const table = document.querySelector('#userView table');

    recipes.forEach(recipe => {
        const row = document.createElement('tr');
        row.id = 'recipe-row-' + recipe.recipe_id;
        row.innerHTML = `
            <td>${recipe.recipe_id}</td>
            <td>${recipe.title}</td>
            <td>${recipe.description}</td>
            <td>${recipe.instructions}</td>
            <td>${recipe.ingredients}</td>
            <td>${recipe.category_name}</td>
            <td>
                <button onclick="editRecipe(${recipe.recipe_id})">Edit</button>
                <button onclick="deleteRecipe(${recipe.recipe_id})">Delete</button>
            </td>
        `;
        table.appendChild(row);
    });
}

function editRecipe(recipeId) {
    const row = document.getElementById('recipe-row-' + recipeId);
    const cells = row.cells;

    const currentTitle = cells[1].innerText;
    const currentDesc = cells[2].innerText;
    const currentInstr = cells[3].innerText;

    cells[1].innerHTML = `<input type="text" id="edit-title-${recipeId}" value="${currentTitle}">`;
    cells[2].innerHTML = `<textarea id="edit-desc-${recipeId}">${currentDesc}</textarea>`;
    cells[3].innerHTML = `<textarea id="edit-instr-${recipeId}">${currentInstr}</textarea>`;

    cells[6].innerHTML = `
        <button onclick="saveRecipe(${recipeId})">Save</button>
        <button onclick="cancelEdit()">Cancel</button>
    `;
}

async function saveRecipe(recipeId) {
    const formDetails = new FormData();
    formDetails.append('recipe_id', recipeId);
    formDetails.append('title', document.getElementById('edit-title-' + recipeId).value);
    formDetails.append('description', document.getElementById('edit-desc-' + recipeId).value);
    formDetails.append('instructions', document.getElementById('edit-instr-' + recipeId).value);

    const res = await fetch('../database/edit_recipe.php', {
        method: 'POST',
        body: formDetails
    });

    const data = await res.text();
    if (data.trim() === 'success') {
        // Reload the table
        const table = document.querySelector('#userView table');
        while (table.rows.length > 1) table.deleteRow(1); // clear rows except header
        const user = JSON.parse(sessionStorage.getItem('user'));
        loadRecipes(user.id);
    } else {
        alert('Failed to save changes.');
    }
}

async function deleteRecipe(recipeId) {
    if (!confirm('Are you sure you want to delete this recipe?')) return;

    const formDetails = new FormData();
    formDetails.append('recipe_id', recipeId);

    const res = await fetch('../database/delete_recipe.php', {
        method: 'POST',
        body: formDetails
    });

    const data = await res.text();
    if (data.trim() === 'success') {
        document.getElementById('recipe-row-' + recipeId).remove();
    } else {
        alert('Failed to delete recipe.');
    }
}

function cancelEdit() {
    const table = document.querySelector('#userView table');
    while (table.rows.length > 1) table.deleteRow(1);
    const user = JSON.parse(sessionStorage.getItem('user'));
    loadRecipes(user.id);
}