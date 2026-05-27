const user = JSON.parse(sessionStorage.getItem('user'));
if (user) showUserView(user);

// Populated once when the add-recipe form first opens
let allIngredients = [];

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

async function showAddRecipe() {
    document.getElementById('addRecipeForm').style.display = 'block';

    // Load ingredient list only once
    if (allIngredients.length === 0) {
        const res = await fetch('../database/get_ingredients.php');
        allIngredients = await res.json();
    }

    // Start with one empty row
    const rows = document.getElementById('ingredientRows');
    if (rows.children.length === 0) addIngredientRow();
}

function hideAddRecipe() {
    document.getElementById('addRecipeForm').style.display = 'none';
    document.getElementById('newTitle').value = '';
    document.getElementById('newDesc').value = '';
    document.getElementById('newInstr').value = '';
    document.getElementById('newCategory').selectedIndex = 0;
    document.getElementById('ingredientRows').innerHTML = '';
}

function buildIngredientSelect() {
    let options = '<option value="">-- Select Ingredient --</option>';
    allIngredients.forEach(ing => {
        options += `<option value="${ing.ingredient_id}">${ing.ingredient_name}</option>`;
    });
    return options;
}

function addIngredientRow() {
    const container = document.getElementById('ingredientRows');
    const row = document.createElement('div');
    row.className = 'ingredient-row';
    row.innerHTML = `
        <select class="ing-select">${buildIngredientSelect()}</select>
        <input type="number" class="ing-qty" placeholder="Qty" min="0.01" step="0.01" style="width:70px;">
        <select class="ing-unit">
            <option value="1">g</option>
            <option value="2">kg</option>
            <option value="3">tsp</option>
            <option value="4">tbsp</option>
            <option value="5">cup</option>
            <option value="6">ml</option>
            <option value="7">liter</option>
            <option value="8">piece</option>
            <option value="9">clove</option>
            <option value="10">slice</option>
            <option value="11">pack</option>
            <option value="12">bottle</option>
        </select>
        <button type="button" onclick="removeIngredientRow(this)">✕</button>
    `;
    container.appendChild(row);
}

function removeIngredientRow(btn) {
    btn.closest('.ingredient-row').remove();
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
        // Store full data for edit pre-population
        row.dataset.recipe = JSON.stringify(recipe);
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

const CATEGORIES = [
    { id: 1, name: 'Main Dish' },
    { id: 2, name: 'Breakfast' },
    { id: 3, name: 'Dessert' },
    { id: 4, name: 'Baking' },
    { id: 5, name: 'Soup' },
    { id: 6, name: 'Salad' },
    { id: 7, name: 'Snack' },
    { id: 8, name: 'Beverage' },
    { id: 9, name: 'Sauce' },
    { id: 10, name: 'Healthy' },
];

function buildCategorySelect(selectedId, idAttr) {
    let opts = CATEGORIES.map(c =>
        `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.name}</option>`
    ).join('');
    return `<select id="${idAttr}">${opts}</select>`;
}

function buildEditIngredientRow(ing = null) {
    const ingOptions = allIngredients.map(i =>
        `<option value="${i.ingredient_id}" ${ing && i.ingredient_id == ing.ingredient_id ? 'selected' : ''}>${i.ingredient_name}</option>`
    ).join('');

    const unitOptions = [
        [1,'g'],[2,'kg'],[3,'tsp'],[4,'tbsp'],[5,'cup'],
        [6,'ml'],[7,'liter'],[8,'piece'],[9,'clove'],[10,'slice'],[11,'pack'],[12,'bottle']
    ].map(([id, name]) =>
        `<option value="${id}" ${ing && id == ing.unit_id ? 'selected' : ''}>${name}</option>`
    ).join('');

    return `<div class="ingredient-row">
        <select class="ing-select"><option value="">-- Select --</option>${ingOptions}</select>
        <input type="number" class="ing-qty" placeholder="Qty" min="0.01" step="0.01" style="width:70px;" value="${ing ? ing.quantity : ''}">
        <select class="ing-unit">${unitOptions}</select>
        <button type="button" onclick="removeIngredientRow(this)">✕</button>
    </div>`;
}

async function editRecipe(recipeId) {
    // Ensure ingredient list is loaded
    if (allIngredients.length === 0) {
        const res = await fetch('../database/get_ingredients.php');
        allIngredients = await res.json();
    }

    const row = document.getElementById('recipe-row-' + recipeId);
    const recipe = JSON.parse(row.dataset.recipe);
    const cells = row.cells;

    cells[1].innerHTML = `<input type="text" id="edit-title-${recipeId}" value="${recipe.title.replace(/"/g, '&quot;')}">`;
    cells[2].innerHTML = `<textarea id="edit-desc-${recipeId}">${recipe.description}</textarea>`;
    cells[3].innerHTML = `<textarea id="edit-instr-${recipeId}">${recipe.instructions}</textarea>`;

    // Ingredient rows pre-populated from stored data
    const ingRows = (recipe.ingredient_list || []).map(ing => buildEditIngredientRow(ing)).join('');
    cells[4].innerHTML = `
        <div id="edit-ing-rows-${recipeId}">${ingRows}</div>
        <button type="button" onclick="addEditIngredientRow(${recipeId})">+ Add</button>
    `;

    cells[5].innerHTML = buildCategorySelect(recipe.category_id, `edit-category-${recipeId}`);

    cells[6].innerHTML = `
        <button onclick="saveRecipe(${recipeId})">Save</button>
        <button onclick="cancelEdit()">Cancel</button>
    `;
}

function addEditIngredientRow(recipeId) {
    document.getElementById('edit-ing-rows-' + recipeId)
        .insertAdjacentHTML('beforeend', buildEditIngredientRow(null));
}

async function saveRecipe(recipeId) {
    const title = document.getElementById('edit-title-' + recipeId).value.trim();
    if (!title) { alert('Title is required.'); return; }

    // Collect ingredient rows from the edit cell
    const ingContainer = document.getElementById('edit-ing-rows-' + recipeId);
    const ingredients = [];
    for (const row of ingContainer.querySelectorAll('.ingredient-row')) {
        const ingId  = row.querySelector('.ing-select').value;
        const qty    = row.querySelector('.ing-qty').value.trim();
        const unitId = row.querySelector('.ing-unit').value;
        if (!ingId) continue;
        if (!qty || isNaN(qty) || parseFloat(qty) <= 0) {
            alert('Please enter a valid quantity for each ingredient.');
            return;
        }
        ingredients.push({ ingredient_id: ingId, quantity: qty, unit_id: unitId });
    }

    const formDetails = new FormData();
    formDetails.append('recipe_id', recipeId);
    formDetails.append('title', title);
    formDetails.append('description', document.getElementById('edit-desc-' + recipeId).value);
    formDetails.append('instructions', document.getElementById('edit-instr-' + recipeId).value);
    formDetails.append('category_id', document.getElementById('edit-category-' + recipeId).value);
    formDetails.append('ingredients', JSON.stringify(ingredients));

    const res = await fetch('../database/edit_recipe.php', {
        method: 'POST',
        body: formDetails
    });

    const data = await res.text();
    if (data.trim() === 'success') {
        const table = document.querySelector('#userView table');
        while (table.rows.length > 1) table.deleteRow(1);
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

async function submitAddRecipe() {
    const title = document.getElementById('newTitle').value.trim();
    const desc = document.getElementById('newDesc').value.trim();
    const instr = document.getElementById('newInstr').value.trim();
    const categoryId = document.getElementById('newCategory').value;

    if (!title) {
        alert('Title is required.');
        return;
    }

    // Collect ingredient rows
    const ingredientRows = document.querySelectorAll('.ingredient-row');
    const ingredients = [];
    for (const row of ingredientRows) {
        const ingId  = row.querySelector('.ing-select').value;
        const qty    = row.querySelector('.ing-qty').value.trim();
        const unitId = row.querySelector('.ing-unit').value;

        if (!ingId) continue; // skip blank rows
        if (!qty || isNaN(qty) || parseFloat(qty) <= 0) {
            alert('Please enter a valid quantity for each ingredient.');
            return;
        }
        ingredients.push({ ingredient_id: ingId, quantity: qty, unit_id: unitId });
    }

    const user = JSON.parse(sessionStorage.getItem('user'));

    const formDetails = new FormData();
    formDetails.append('user_id', user.id);
    formDetails.append('title', title);
    formDetails.append('description', desc);
    formDetails.append('instructions', instr);
    formDetails.append('category_id', categoryId);
    // Send ingredients as JSON string
    formDetails.append('ingredients', JSON.stringify(ingredients));

    const res = await fetch('../database/add_recipe.php', {
        method: 'POST',
        body: formDetails
    });

    const data = await res.text();
    if (data.trim() === 'fail') {
        alert('Failed to add recipe.');
    } else {
        hideAddRecipe();
        const table = document.querySelector('#userView table');
        while (table.rows.length > 1) table.deleteRow(1);
        loadRecipes(user.id);
    }
}