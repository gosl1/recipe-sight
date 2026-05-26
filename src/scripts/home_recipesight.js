// home_recipesight.js
let allIngredients = [];

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

const ingInput = document.getElementById('recipeIngredientsInput');
const autoList = document.getElementById('autocomplete-list');

if (ingInput) {
    ingInput.addEventListener('input', function() {
        const val = this.value.toLowerCase();
        autoList.innerHTML = '';
        if (!val) return;
        const matches = allIngredients.filter(i => i.toLowerCase().includes(val)).slice(0, 8);
        matches.forEach(m => {
            const div = document.createElement('div');
            div.textContent = m;
            div.onclick = () => {
                ingInput.value = m;
                autoList.innerHTML = '';
            };
            autoList.appendChild(div);
        });
    });
}

document.addEventListener('click', (e) => {
    if (e.target !== ingInput) autoList.innerHTML = '';
});

async function searchRecipes() {
    const recipeName = document.getElementById('recipeNameInput').value.trim();
    const category = document.getElementById('recipeCategorySelect').value;
    const ingredient = document.getElementById('recipeIngredientsInput').value.trim();

    const params = new URLSearchParams();
    params.append('user_id', '1');
    if (recipeName) params.append('recipe_name', recipeName);
    if (category) params.append('category', category);
    if (ingredient) params.append('ingredient', ingredient);

    const url = '../database/search_recipes.php?' + params.toString();
    const resultsDiv = document.getElementById('searchResults');
    resultsDiv.innerHTML = '<div style="text-align:center; padding:20px;">Searching...</div>';

    try {
        const response = await fetch(url);
        if (!response.ok) {
            const text = await response.text();
            console.error('API error response:', text.substring(0, 200));
            throw new Error('Server error');
        }
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