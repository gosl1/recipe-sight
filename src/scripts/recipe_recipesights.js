/**
 * RecipeSight - Production Stable Free API Integration
 */
document.addEventListener('DOMContentLoaded', () => {
    const recipeGrid = document.querySelector('.recipeGrid');
    const searchInput = document.querySelector('.searchInput');
    const searchForm = document.querySelector('.searchBarForm');

    // EXPLICIT FIXED ENDPOINT
    const API_URL = 'https://www.themealdb.com/api/json/v1/1/search.php';

    // NETWORK LAYER: FETCH DATA FROM SERVER
    async function fetchRecipes(searchQuery = '') {
        try {
            // Provide visual feedback inside the grid layout container
            if (recipeGrid) {
                recipeGrid.innerHTML = `<p class="loadingText" style="grid-column: span 3; text-align: center; font-weight: bold; font-size: 20px; color: #313647; padding: 40px 0;">Loading fresh recipes...</p>`;
            }

            const cleanQuery = searchQuery.trim() === '' ? 'a' : searchQuery.trim();
            const requestUrl = `${API_URL}?s=${encodeURIComponent(cleanQuery)}`;

            console.log("Fetching endpoint details from:", requestUrl);

            const response = await fetch(requestUrl);
            
            // Check HTTP payload status
            if (!response.ok) {
                throw new Error(`HTTP network error! Status code context: ${response.status}`);
            }

            const data = await response.json();
            
            // TheMealDB returns data wrapped inside an array named .meals
            renderRecipes(data.meals);

        } catch (error) {
            console.error('Critical Error loading database content:', error);
            if (recipeGrid) {
                recipeGrid.innerHTML = `<p style="grid-column: span 3; text-align: center; color: #D32F2F; font-weight: bold; padding: 40px 0;">⚠️ Network Connection Error. Please verify your internet access or browser CORS parameters.</p>`;
            }
        }
    }

    // PRESENTATION LAYER: RENDER CONTENT CARDS
    function renderRecipes(meals) {
        if (!recipeGrid) return;

        // Clear loading texts or previous old records completely
        recipeGrid.innerHTML = '';

        // If API returns no matching query objects, display a clean empty-state message
        if (!meals || meals.length === 0) {
            recipeGrid.innerHTML = `<p style="grid-column: span 3; text-align: center; font-weight: bold; font-size: 18px; color: #555; padding: 40px 0;">No recipes found matching that specific keyword.</p>`;
            return;
        }

        // Loop through array rows smoothly
        meals.forEach(meal => {
            // Count total active ingredients dynamically
            let totalIngredients = 0;
            for (let i = 1; i <= 20; i++) {
                if (meal[`strIngredient${i}`] && meal[`strIngredient${i}`].trim() !== "") {
                    totalIngredients++;
                }
            }

            // Create pseudo random data models to dynamically balance metadata tags across grid rows
            const mockTime = 20 + (parseInt(meal.idMeal) % 35);
            const mockRating = (4 + (parseInt(meal.idMeal) % 10) / 10).toFixed(1);

            // Construct exact layout string matching your exact CSS variables
            const cardHTML = `
                <div class="recipeCard" data-id="${meal.idMeal}">
                    <div class="cardImageWrapper">
                        <img src="${meal.strMealThumb}" alt="${meal.strMeal}" class="recipeImg" loading="lazy">
                        <button class="favoriteBtn" aria-label="Save Recipe">
                            <i class="far fa-heart"></i>
                        </button>
                        <span class="inventoryMatchTag">${totalIngredients} Ingredients</span>
                    </div>
                    <div class="cardContent">
                        <div class="cardTags">
                            <span class="tag">${meal.strCategory || 'Uncategorized'}</span>
                        </div>
                        <h2 class="recipeName" style="font-size: 20px; font-weight: bold; line-height: 1.3; height: 52px; overflow: hidden; margin-bottom: 15px; color: #313647;">${meal.strMeal}</h2>
                        <div class="recipeMeta">
                            <span class="metaItem"><i class="far fa-clock"></i> ${mockTime} mins</span>
                            <span class="metaItem" style="color: #FFC107;"><i class="fas fa-star"></i> <span style="color: #555555;">${mockRating}</span></span>
                        </div>
                    </div>
                </div>
            `;
            
            recipeGrid.insertAdjacentHTML('beforeend', cardHTML);
        });

        // Initialize event logic handlers on freshly rendered cards
        attachFavoriteListeners();
    }

    // INTERACTION LAYER: SEARCH EVENTS & DOM SUBMISSIONS
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault(); // Lock form from refreshing page on submission
            if (searchInput) {
                fetchRecipes(searchInput.value);
            }
        });
    }

    // Interactive Favorite/Save heart button handler logic
    function attachFavoriteListeners() {
        document.querySelectorAll('.favoriteBtn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation(); // Stop parent clicks
                const heartIcon = button.querySelector('i');
                if (heartIcon) {
                    heartIcon.classList.toggle('fas');
                    heartIcon.classList.toggle('far');
                    button.style.color = heartIcon.classList.contains('fas') ? '#E2725B' : '#313647';
                }
            });
        });
    }

    // INITIAL TRIGGER: Fire the initial rendering thread directly on document mounting
    fetchRecipes();
});