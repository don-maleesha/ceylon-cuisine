document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('recipeModal');
    const urlParams = new URLSearchParams(window.location.search);
    const recipeId = urlParams.get('id');

    if (recipeId && modal) {
        modal.style.display = 'block';
    }

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
});

function closeModal() {
    const modal = document.getElementById('recipeModal');
    if (modal) {
        modal.style.display = 'none';
        history.replaceState({}, document.title, window.location.pathname);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        const star = e.target.closest('.interactive-stars .fa-star');
        if (!star) {
            return;
        }

        const container = star.closest('.interactive-stars');
        const recipeId = container ? container.dataset.recipeId : null;
        const rating = parseInt(star.dataset.rating);

        if (!recipeId) {
            console.error('Recipe ID not found');
            return;
        }

        const stars = container.querySelectorAll('.fa-star');
        stars.forEach((s, idx) => {
            s.classList.toggle('rated', (idx + 1) <= rating);
            s.classList.toggle('fas', (idx + 1) <= rating);
            s.classList.toggle('far', (idx + 1) > rating);
        });

        const formData = new FormData();
        formData.append('recipe_id', recipeId);
        formData.append('rating', rating);

        fetch('../php/rateRecipes.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const avgRatingElement = document.querySelector('.average-rating-section .stars');
                if (avgRatingElement) {
                    const average = parseFloat(data.average_rating);
                    avgRatingElement.innerHTML = generateStarsHTML(average);
                    avgRatingElement.querySelector('.rating-value').textContent = `(${data.average_rating})`;
                }
            } else {
                console.error('Rating failed:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    function generateStarsHTML(rating) {
        let html = '';
        const fullStars = Math.floor(rating);
        const hasHalfStar = (rating - fullStars) >= 0.5;
        
        for (let i = 1; i <= 5; i++) {
            if (i <= fullStars) {
                html += '<i class="fas fa-star rated"></i>';
            } else if (hasHalfStar && i === fullStars + 1) {
                html += '<i class="fas fa-star-half-alt rated"></i>';
            } else {
                html += '<i class="far fa-star"></i>';
            }
        }
        html += `<span class="rating-value">(${rating.toFixed(1)})</span>`;
        return html;
    }

    document.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            setTimeout(() => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }, 100);
        });
    });
    
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('page') && urlParams.get('page') !== '1') {
        const recipesSection = document.getElementById('myRecipe');
        if (recipesSection) {
            setTimeout(() => {
                recipesSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 200);
        }
    }
});