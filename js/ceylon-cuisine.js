// Welcome message functionality
document.addEventListener("DOMContentLoaded", function() {
    function updateWelcomeMessage() {
        const currentTime = new Date();
        const hours = currentTime.getHours();
        let greeting;
        
        if (hours < 12) {
            greeting = "Good morning";
        } else if (hours < 18) {
            greeting = "Good afternoon";
        } else {
            greeting = "Good evening";
        }
        
        document.getElementById("welcomeMessage").textContent = `${greeting}, welcome to Ceylon Cuisine!`;
    }

    updateWelcomeMessage();
    setInterval(updateWelcomeMessage, 60000);
});

// Custom dropdown menu functionality
document.addEventListener("DOMContentLoaded", function() {
    const customIcon = document.getElementById("customIcon");
    const dropdownMenu = document.getElementById("dropdownMenu");

    if (customIcon && dropdownMenu) {
        customIcon.addEventListener("click", function(event) {
            event.preventDefault();
            dropdownMenu.classList.toggle("show");
        });

        window.addEventListener("click", function(event) {
            if (!event.target.matches("#customIcon") && dropdownMenu.classList.contains("show")) {
                dropdownMenu.classList.remove("show");
            }
        });
    }
});

// Mobile menu toggle functionality
document.addEventListener("DOMContentLoaded", function() {
    const mobileMenuToggle = document.getElementById("mobileMenuToggle");
    const navigation = document.querySelector("header nav");

    if (mobileMenuToggle && navigation) {
        mobileMenuToggle.addEventListener("click", function(event) {
            event.preventDefault();
            navigation.classList.toggle("mobile-nav-open");
            mobileMenuToggle.classList.toggle("active");
        });

        // Close mobile menu when clicking on nav links
        const navLinks = document.querySelectorAll("header nav ul li a");
        navLinks.forEach(link => {
            link.addEventListener("click", function() {
                navigation.classList.remove("mobile-nav-open");
                mobileMenuToggle.classList.remove("active");
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener("click", function(event) {
            if (!navigation.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                navigation.classList.remove("mobile-nav-open");
                mobileMenuToggle.classList.remove("active");
            }
        });
    }
});

// Custom confirmation modal functionality
function createConfirmationModal(title, message, confirmText = 'Delete', cancelText = 'Cancel') {
    return new Promise((resolve) => {
        // Create modal elements
        const modalOverlay = document.createElement('div');
        modalOverlay.className = 'custom-modal-overlay';
        
        const modal = document.createElement('div');
        modal.className = 'custom-modal';
        
        modal.innerHTML = `
            <div class="custom-modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> ${title}</h3>
            </div>
            <div class="custom-modal-body">
                <p>${message}</p>
            </div>
            <div class="custom-modal-footer">
                <button class="btn-cancel" onclick="closeCustomModal(false)">${cancelText}</button>
                <button class="btn-confirm" onclick="closeCustomModal(true)">${confirmText}</button>
            </div>
        `;
        
        modalOverlay.appendChild(modal);
        document.body.appendChild(modalOverlay);
        
        // Store resolve function globally for button handlers
        window.customModalResolve = resolve;
        
        // Show modal with animation
        setTimeout(() => {
            modalOverlay.classList.add('show');
        }, 10);
        
        // Close on overlay click
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                closeCustomModal(false);
            }
        });
        
        // Close on Escape key
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                closeCustomModal(false);
                document.removeEventListener('keydown', handleEscape);
            }
        };
        document.addEventListener('keydown', handleEscape);
    });
}

function closeCustomModal(result) {
    const modalOverlay = document.querySelector('.custom-modal-overlay');
    if (modalOverlay) {
        modalOverlay.classList.remove('show');
        setTimeout(() => {
            modalOverlay.remove();
        }, 300);
    }
    
    if (window.customModalResolve) {
        window.customModalResolve(result);
        window.customModalResolve = null;
    }
}

// User delete confirmation function
async function confirmUserDelete(userName) {
    const message = `
        <strong>Are you sure you want to delete user "${userName}"?</strong>
        <br><br>
        <div class="warning-list">
            <div class="warning-item"><i class="fas fa-user-minus"></i> Delete the user account permanently</div>
            <div class="warning-item"><i class="fas fa-utensils"></i> Delete all their recipes and associated data</div>
            <div class="warning-item"><i class="fas fa-star"></i> Delete all their ratings and favorites</div>
            <div class="warning-item"><i class="fas fa-image"></i> Remove their profile picture</div>
        </div>
        <br>
        <div class="final-warning">
            <i class="fas fa-exclamation-circle"></i> 
            <strong>This action cannot be undone!</strong>
        </div>
    `;
    
    return await createConfirmationModal('Delete User', message, 'Delete User', 'Cancel');
}

// Handle delete user button clicks
document.addEventListener("DOMContentLoaded", function() {
    const deleteButtons = document.querySelectorAll('.delete-user-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const form = this.closest('.delete-user-form');
            const userName = form.getAttribute('data-user-name');
            
            const confirmed = await confirmUserDelete(userName);
            
            if (confirmed) {
                // Add hidden input for delete_user action
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_user';
                deleteInput.value = '1';
                form.appendChild(deleteInput);
                
                // Submit the form
                form.submit();
            }
        });
    });
});

// Recipe delete confirmation function
async function confirmRecipeDelete(recipeName) {
    const message = `
        <strong>Are you sure you want to delete the recipe "${recipeName}"?</strong>
        <br><br>
        <div class="warning-list">
            <div class="warning-item"><i class="fas fa-utensils"></i> Delete the recipe permanently</div>
            <div class="warning-item"><i class="fas fa-star"></i> Delete all ratings and associated data</div>
            <div class="warning-item"><i class="fas fa-heart"></i> Remove from all user favorites</div>
            <div class="warning-item"><i class="fas fa-image"></i> Delete the recipe image</div>
        </div>
        <br>
        <div class="final-warning">
            <i class="fas fa-exclamation-circle"></i> 
            <strong>This action cannot be undone!</strong>
        </div>
    `;
    
    return await createConfirmationModal('Delete Recipe', message, 'Delete Recipe', 'Cancel');
}

// Handle delete recipe button clicks
document.addEventListener("DOMContentLoaded", function() {
    const deleteRecipeButtons = document.querySelectorAll('.delete-recipe-btn');
    
    deleteRecipeButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const form = this.closest('.delete-recipe-form');
            const recipeName = form.getAttribute('data-recipe-name');
            
            const confirmed = await confirmRecipeDelete(recipeName);
            
            if (confirmed) {
                // Add hidden input for delete_recipe action
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_recipe';
                deleteInput.value = '1';
                form.appendChild(deleteInput);
                
                // Submit the form
                form.submit();
            }
        });
    });
});

// Review delete confirmation function
async function confirmReviewDelete(userName, recipeTitle) {
    const message = `
        <strong>Are you sure you want to delete this review?</strong>
        <br><br>
        <div class="warning-list">
            <div class="warning-item"><i class="fas fa-user"></i> User: ${userName}</div>
            <div class="warning-item"><i class="fas fa-utensils"></i> Recipe: ${recipeTitle}</div>
            <div class="warning-item"><i class="fas fa-star"></i> Delete the rating permanently</div>
            <div class="warning-item"><i class="fas fa-comment"></i> Delete the review comment</div>
        </div>
        <br>
        <div class="final-warning">
            <i class="fas fa-exclamation-circle"></i> 
            <strong>This action cannot be undone!</strong>
        </div>
    `;
    
    return await createConfirmationModal('Delete Review', message, 'Delete Review', 'Cancel');
}

// Handle delete review button clicks
document.addEventListener("DOMContentLoaded", function() {
    const deleteReviewButtons = document.querySelectorAll('.delete-review-btn');
    
    deleteReviewButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const form = this.closest('.delete-review-form');
            const userName = form.getAttribute('data-review-user');
            const recipeTitle = form.getAttribute('data-recipe-title');
            
            const confirmed = await confirmReviewDelete(userName, recipeTitle);
            
            if (confirmed) {
                // Add hidden input for delete_review action
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_review';
                deleteInput.value = '1';
                form.appendChild(deleteInput);
                
                // Submit the form
                form.submit();
            }
        });
    });
});

// Auto-hide alert messages
document.addEventListener("DOMContentLoaded", function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000); // Hide after 5 seconds
    });
});
