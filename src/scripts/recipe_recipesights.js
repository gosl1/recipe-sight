const searchInput = document.getElementById('search-bar');
const searchButton = document.getElementById('search-btn');
const cardOutputGrid = document.getElementById('recipe-cards-output');
let debounceTimer;

/**
 * Handles communication with the backend endpoint
 */
async function triggerSearch(queryTerm = '') {
    try {
        const queryURL = `/recipe-sight/src/database/get_recipes.php?q=${encodeURIComponent(queryTerm)}`;
        const networkResponse = await fetch(queryURL);
        
        if (!networkResponse.ok) {
            throw new Error(`Server returned status code: ${networkResponse.status}`);
        }
        
        const payloadData = await networkResponse.json();
        renderDetailedCards(payloadData);
        
    } catch (networkError) {
        console.error('Data pipeline exception occurred:', networkError);
        if (cardOutputGrid) {
            cardOutputGrid.innerHTML = `
                <div class="state-notice" style="color: #ef4444; font-weight: 600; text-align: center; padding: 20px;">
                    Unable to reach data stream. Please try again later.
                </div>`;
        }
    }
}

/**
 * Iterates over the raw data rows to render recipe layouts
 */
function renderDetailedCards(collectionList) {
    if (!cardOutputGrid) return;

    if (collectionList.length === 0) {
        cardOutputGrid.innerHTML = `
            <div class="state-notice" style="text-align: center; padding: 20px; color: #6b7280;">
                No matching recipes found. Try a different category or ingredient keyword!
            </div>`;
        return;
    }

    cardOutputGrid.innerHTML = collectionList.map(recipe => {
        const cardFallbackImage = recipe.image_url 
            ? recipe.image_url 
            : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80';
        
        const assignedCategory = recipe.category_name ? recipe.category_name : 'General Selection';
        const displayDescription = recipe.description ? recipe.description : 'No overview available for this item.';
        const preparationGuide = recipe.instructions ? recipe.instructions : 'Refer to directions.';
        
        const metricCalories = recipe.calories ? Math.round(recipe.calories) : '—';
        const metricProtein  = recipe.protein  ? Math.round(recipe.protein) + 'g' : '—';
        const metricCarbs    = recipe.carbs    ? Math.round(recipe.carbs) + 'g' : '—';
        const metricFats     = recipe.fats     ? Math.round(recipe.fats) + 'g' : '—';

        return `
            <article class="detailed-recipe-card" style="border: 1px solid #e5e7eb; border-radius: 8px; margin: 10px; overflow: hidden; background: #fff;">
                <img src="${cardFallbackImage}" alt="${recipe.title}" style="width: 100%; height: 200px; object-fit: cover;">
                <div style="padding: 15px;">
                    <span style="font-size: 0.75rem; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-weight: 600;">${assignedCategory}</span>
                    <h2 style="margin: 10px 0 5px 0; font-size: 1.25rem;">${recipe.title}</h2>
                    <p style="color: #4b5563; font-size: 0.875rem; margin-bottom: 15px;">${displayDescription}</p>
                    <div style="background: #f9fafb; padding: 10px; border-radius: 6px; font-size: 0.85rem; margin-bottom: 15px;">
                        <strong>Instructions:</strong> ${preparationGuide}
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 5px; text-align: center; font-size: 0.75rem; background: #f3f4f6; padding: 8px; border-radius: 6px;">
                        <div>Cal<br><strong>${metricCalories}</strong></div>
                        <div>Prot<br><strong>${metricProtein}</strong></div>
                        <div>Carb<br><strong>${metricCarbs}</strong></div>
                        <div>Fat<br><strong>${metricFats}</strong></div>
                    </div>
                </div>
            </article>
        `;
    }).join('');
}

// Active Search Event Listeners
if (searchInput) {
    // 1. Dynamic Typing Active Search (with 300ms Debounce Delay)
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            triggerSearch(searchInput.value.trim());
        }, 300);
    });

    // 2. Keyboard Enter Bypasses Delay
    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            clearTimeout(debounceTimer);
            triggerSearch(searchInput.value.trim());
        }
    });
}

// 3. Fallback Click Event Button
if (searchButton) {
    searchButton.addEventListener('click', () => {
        clearTimeout(debounceTimer);
        triggerSearch(searchInput.value.trim());
    });
}

// Initial default query catalog pull on load
window.addEventListener('DOMContentLoaded', () => {
    triggerSearch('');
});