// favorites.js - Handle favorites functionality
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize favorites for all recipe cards
    initializeFavorites();
    
    // Handle favorite heart clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.favorite-heart')) {
            e.preventDefault();
            e.stopPropagation();
            const heartElement = e.target.closest('.favorite-heart');
            const recipeId = heartElement.getAttribute('data-recipe-id');
            toggleFavorite(recipeId, heartElement);
        }
    });
});

function initializeFavorites() {
    const favoriteHearts = document.querySelectorAll('.favorite-heart');
    favoriteHearts.forEach(heart => {
        const recipeId = heart.getAttribute('data-recipe-id');
        checkFavoriteStatus(recipeId, heart);
    });
}

function checkFavoriteStatus(recipeId, heartElement) {
    fetch('favorites.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=check&recipe_id=${recipeId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateHeartDisplay(heartElement, data.is_favorite);
        }
    })
    .catch(error => {
        console.error('Error checking favorite status:', error);
    });
}

function toggleFavorite(recipeId, heartElement) {
    // Show loading state
    heartElement.style.opacity = '0.5';
    
    fetch('favorites.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=toggle&recipe_id=${recipeId}`
    })
    .then(response => response.json())
    .then(data => {
        heartElement.style.opacity = '1';
        
        if (data.success) {
            updateHeartDisplay(heartElement, data.is_favorite);
            showToast(data.message, 'success');
            
            // If we're on the favorites page and item was removed, remove the card
            if (window.location.pathname.includes('my_favorites.php') && !data.is_favorite) {
                const card = heartElement.closest('.card');
                if (card) {
                    card.style.opacity = '0.5';
                    setTimeout(() => {
                        card.remove();
                        // Check if no more cards and show message
                        checkForEmptyFavorites();
                    }, 300);
                }
            }
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        heartElement.style.opacity = '1';
        console.error('Error toggling favorite:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

function updateHeartDisplay(heartElement, isFavorite) {
    const icon = heartElement.querySelector('i');
    
    if (isFavorite) {
        heartElement.classList.add('favorite-active');
        icon.className = 'fas fa-heart';
        heartElement.title = 'Remove from favorites';
    } else {
        heartElement.classList.remove('favorite-active');
        icon.className = 'far fa-heart';
        heartElement.title = 'Add to favorites';
    }
}

function showToast(message, type = 'success') {
    // Remove any existing toast
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add styles
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#d4edda' : '#f8d7da'};
        color: ${type === 'success' ? '#155724' : '#721c24'};
        border: 1px solid ${type === 'success' ? '#c3e6cb' : '#f5c6cb'};
        border-radius: 8px;
        padding: 12px 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        font-family: 'Raleway', sans-serif;
        font-size: 14px;
        min-width: 250px;
        animation: slideIn 0.3s ease-out;
    `;
    
    // Add animation styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .toast-content {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    `;
    document.head.appendChild(style);
    
    // Add to document
    document.body.appendChild(toast);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease-out reverse';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 300);
    }, 3000);
}

function checkForEmptyFavorites() {
    if (window.location.pathname.includes('my_favorites.php')) {
        const recipeGrid = document.querySelector('.recipe-grid');
        if (recipeGrid && recipeGrid.children.length === 0) {
            const cardContainer = document.querySelector('.card-container');
            if (cardContainer) {
                cardContainer.innerHTML = `
                    <div class="no-favorites">
                        <i class="fas fa-heart-broken" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                        <p class="raleway" style="font-size: 1.2rem; color: #666;">You haven't added any recipes to your favorites yet.</p>
                        <a href="recipes.php" class="raleway" style="color: #e74c3c; text-decoration: none; font-weight: 500;">
                            <i class="fas fa-arrow-right"></i> Explore Recipes
                        </a>
                    </div>
                `;
            }
        }
    }
}
