let allIngredients = [];
let selectedIngredients = [];

// Load ingredient list from database
async function loadIngredients() {
    try {
        const res = await fetch('../database/get_ingredients.php');
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();
        allIngredients = data.map(i => i.ingredient_name);
    } catch(e) {
        console.error('Failed to load ingredients:', e);
    }
}

const ingInput = document.getElementById('ingredientInput');
const autoList = document.getElementById('autocomplete-list');
const tagContainer = document.getElementById('ingredientTags');

// Render tags
function renderTags() {
    tagContainer.innerHTML = '';
    selectedIngredients.forEach(ing => {
        const tag = document.createElement('span');
        tag.className = 'ingredient-tag';
        tag.innerHTML = `${ing} <button type="button" data-ing="${ing}">✖</button>`;
        tag.querySelector('button').onclick = () => {
            selectedIngredients = selectedIngredients.filter(i => i !== ing);
            renderTags();
        };
        tagContainer.appendChild(tag);
    });
}

// Handle Enter key (add ingredient if valid)
ingInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        const val = ingInput.value.trim();
        if (val && !selectedIngredients.includes(val) && allIngredients.includes(val)) {
            selectedIngredients.push(val);
            renderTags();
            ingInput.value = '';
            autoList.innerHTML = '';
        } else if (val && !allIngredients.includes(val)) {
            alert('Please select an ingredient from the list');
        }
    }
});

// Autocomplete on input
ingInput.addEventListener('input', function() {
    const val = this.value.toLowerCase();
    autoList.innerHTML = '';
    if (!val) return;
    const matches = allIngredients.filter(i =>
        i.toLowerCase().includes(val) && !selectedIngredients.includes(i)
    ).slice(0, 8);
    matches.forEach(m => {
        const div = document.createElement('div');
        div.textContent = m;
        div.onclick = () => {
            if (!selectedIngredients.includes(m)) {
                selectedIngredients.push(m);
                renderTags();
                ingInput.value = '';
                autoList.innerHTML = '';
            }
        };
        autoList.appendChild(div);
    });
});

// Close autocomplete when clicking outside
document.addEventListener('click', (e) => {
    if (e.target !== ingInput) autoList.innerHTML = '';
});

// Search function
async function searchRecipes() {
    const recipeName = document.getElementById('recipeNameInput').value.trim();
    const category = document.getElementById('recipeCategorySelect').value;
    
    const params = new URLSearchParams();
    // Read user from sessionStorage (set by login) so search works for any user,
    // not just user 1. Falls back to 1 if not logged in (guest browsing).
    const sessionUser = JSON.parse(sessionStorage.getItem('user') || 'null');
    const currentUserId = (sessionUser && sessionUser.id) ? sessionUser.id : '1';
    params.append('user_id', currentUserId);
    if (recipeName) params.append('recipe_name', recipeName);
    if (category) params.append('category', category);
    if (selectedIngredients.length > 0) {
        params.append('ingredients', JSON.stringify(selectedIngredients));
    }
    
    params.append('scope', 'all'); // home page shows all users' recipes
    const url = '../database/search_recipes.php?' + params.toString();
    const resultsDiv = document.getElementById('searchResults');
    resultsDiv.innerHTML = '<div style="text-align:center; padding:20px;">Searching...</div>';
    
    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error('Server error');
        const recipes = await response.json();
        let html = '<h2>Search Results</h2>';
        if (recipes.length === 0) {
            html += '<p>No recipes found. Try different search terms.</p>';
        } else {
            recipes.forEach(r => {
                html += `
                    <div class="recipe-card">
                        <h3>${escapeHtml(r.title)}</h3>
                        <p><strong>Category:</strong> ${escapeHtml(r.category_name)}</p>
                        <p>${escapeHtml(r.description) || ''}</p>
                        <p><strong>Ingredients:</strong><br>${r.ingredients || 'N/A'}</p>
                        <a href="read_recipe.php?id=${r.recipe_id}">View Recipe →</a>
                    </div>
                `;
            });
        }
        resultsDiv.innerHTML = html;
    } catch (error) {
        console.error('Search error:', error);
        resultsDiv.innerHTML = '<p style="color:red;">Error searching recipes. Check console for details.</p>';
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;'}[m]));
}

loadIngredients();