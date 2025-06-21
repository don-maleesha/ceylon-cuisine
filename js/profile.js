document.addEventListener("DOMContentLoaded", function () {
    // Handle upload messages
    const messageSpan = document.getElementById("upload-message");
    const uploadMessage = messageSpan?.dataset?.message;
    if (uploadMessage) {
        // Show as in-page message instead of alert
        const container = document.querySelector('.container');
        if (container) {
            // Determine if it's a success or error message based on content
            if (uploadMessage.toLowerCase().includes('successfully') || 
                uploadMessage.toLowerCase().includes('success')) {
                showSuccessMessage(uploadMessage, container);
            } else {
                showErrorMessage(uploadMessage, container);
            }
        } else {
            alert(uploadMessage);
        }
    }
      // Handle recipe submission messages
    const recipeMessageContainer = document.getElementById("recipe-message-container");
    if (recipeMessageContainer && recipeMessageContainer.dataset.message) {
        const message = recipeMessageContainer.dataset.message;
        const status = recipeMessageContainer.dataset.status;
        const container = document.querySelector('#newRecipe .container');
        
        if (status === 'success') {
            showSuccessMessage(message, container);
        } else if (status === 'error') {
            showErrorMessage(message, container);
        }
    }
    
    // Handle recipe update messages
    const updateMessageContainer = document.getElementById("update-message-container");
    if (updateMessageContainer && updateMessageContainer.dataset.message) {
        const message = updateMessageContainer.dataset.message;
        const status = updateMessageContainer.dataset.status;
        const container = document.querySelector('#updatePanel');
        
        if (status === 'success') {
            showSuccessMessage(message, container);
        } else if (status === 'error') {
            showErrorMessage(message, container);
        }
    }

    // Handle initial URL state
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('id')) {
        showSection('myRecipe');
        showModal(urlParams.get('id'));
    }
    
    // Add form names to recipe forms for validation    const recipeUpdateForm = document.querySelector('#updatePanel form');
    if (recipeUpdateForm) {
        recipeUpdateForm.setAttribute('name', 'update-recipe-form');
        recipeUpdateForm.addEventListener('submit', function(e) {
            if (!validateRecipeUpdateForm()) {
                e.preventDefault(); // Prevent form submission if validation fails
            } else {
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                }
            }
        });
        
        // Initialize image preview for update form
        const fileInput = recipeUpdateForm.querySelector('input[type="file"]');
        const previewElement = recipeUpdateForm.querySelector('.image-preview');
        if (fileInput && previewElement) {
            initImagePreview(fileInput, previewElement);
        }
    }
      const recipeCreateForm = document.querySelector('#newRecipe form');
    if (recipeCreateForm) {
        recipeCreateForm.setAttribute('name', 'create-recipe-form');
        recipeCreateForm.addEventListener('submit', function(e) {
            if (!validateRecipeCreationForm()) {
                e.preventDefault(); // Prevent form submission if validation fails
            } else {
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                }
            }
        });
        
        // Initialize image preview for create form
        const fileInput = recipeCreateForm.querySelector('input[type="file"]');
        const previewElement = recipeCreateForm.querySelector('.image-preview');
        if (fileInput && previewElement) {
            initImagePreview(fileInput, previewElement);
        }
    }
    
    // Add real-time validation for text inputs
    document.querySelectorAll('input[type="text"], textarea').forEach(input => {
        input.addEventListener('blur', function() {
            // Clear previous error on this element
            const previousError = this.nextElementSibling;
            if (previousError && previousError.classList.contains('validation-error')) {
                previousError.remove();
                this.style.borderColor = '';
                this.classList.remove('is-invalid');
            }
            
            // Simple validation on blur
            if (this.required && !this.value.trim()) {
                showError(this, 'This field is required');
            } else if (this.name === 'title' && this.value.trim().length < 3) {
                showError(this, 'Title must be at least 3 characters');
            } else if (this.name === 'description' && this.value.trim().length < 20) {
                showError(this, 'Description must be at least 20 characters');
            }
        });
    });
    
    // Add validation for file inputs
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            // Clear previous error
            const previousError = this.nextElementSibling;
            if (previousError && previousError.classList.contains('validation-error')) {
                previousError.remove();
                this.style.borderColor = '';
                this.classList.remove('is-invalid');
            }
            
            if (this.files.length > 0) {
                const file = this.files[0];
                
                // Check file type
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    showError(this, 'Please select a valid image file (JPG, JPEG, or PNG)');
                    return;
                }
                
                // Check file size (5MB max)
                const maxSize = 5 * 1024 * 1024; // 5MB in bytes
                if (file.size > maxSize) {
                    showError(this, 'Image size must be less than 5MB');
                    return;
                }
                
                // Show preview if possible
                if (this.parentNode.querySelector('.image-preview')) {
                    const preview = this.parentNode.querySelector('.image-preview');
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = 'block';
                }
            }
        });
    });
    
    // Add form handling for profile picture upload
    const profilePictureForm = document.getElementById('profile-picture-form');
    const profilePictureInput = document.getElementById('profile-picture-upload');
    
    if (profilePictureForm && profilePictureInput) {
        // Add file selection feedback
        profilePictureInput.addEventListener('change', function() {
            const submitBtn = document.getElementById('profile-picture-submit');
            
            if (this.files && this.files[0]) {
                // File selected - check it
                const file = this.files[0];
                
                // Check file type
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    showError(this, 'Please select a valid image file (JPG, JPEG, or PNG)');
                    return;
                }
                
                // Check file size (2MB max)
                const maxSize = 2 * 1024 * 1024; // 2MB in bytes
                if (file.size > maxSize) {
                    showError(this, 'Image size must be less than 2MB');
                    return;
                }
                
                // Valid file - enable submit button
                submitBtn.textContent = 'Upload ' + file.name;
                submitBtn.disabled = false;
            }
        });
        
        // Add submit handling
        profilePictureForm.addEventListener('submit', function(e) {
            // Check if a file is selected
            if (!profilePictureInput.files || profilePictureInput.files.length === 0) {
                e.preventDefault();
                showError(profilePictureInput, 'Please select an image file first');
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('profile-picture-submit');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        });
    }
});

// Tab switching function
function showSection(sectionId) {
    // Hide all sections and remove active class from buttons
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });
    document.querySelectorAll('.tabs button').forEach(button => {
        button.classList.remove('active');
    });

    // Show selected section and activate button
    document.getElementById(sectionId).classList.add('active');
    document.querySelector(`.tabs button[onclick="showSection('${sectionId}')"]`)
        .classList.add('active');
}

// Modal handling functions
function showModal(recipeId) {
    // Update URL without reload
    history.pushState({}, '', `?id=${recipeId}`);
    
    // Ensure My Recipes section is active
    showSection('myRecipe');
    
    // Show modal
    document.getElementById('recipeModal').style.display = 'block';
    document.body.classList.add('modal-open');
}

function closeModal() {
    // Hide modal and clean URL
    document.getElementById('recipeModal').style.display = 'none';
    document.body.classList.remove('modal-open');
    history.replaceState({}, '', window.location.pathname);
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        closeModal();
    }
}

// Handle browser navigation (back/forward buttons)
window.addEventListener('popstate', function(event) {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('id')) {
        showSection('myRecipe');
        showModal(urlParams.get('id'));
    } else {
        closeModal();
    }
});


function openUpdatePanel() {
    document.getElementById('panelOverlay').style.display = 'block';
    setTimeout(() => {
        document.getElementById('updatePanel').classList.add('active');
    }, 10);
}

function closeUpdatePanel() {
    document.getElementById('updatePanel').classList.remove('active');
    setTimeout(() => {
        document.getElementById('panelOverlay').style.display = 'none';
    }, 300);
}

// Close panel when clicking outside
document.getElementById('panelOverlay').addEventListener('click', (e) => {
    if(e.target === document.getElementById('panelOverlay')) {
        closeUpdatePanel();
    }
});

function openProfileModal() {
    document.getElementById('profileModal').style.display = 'block';
}

function closeProfileModal() {
    document.getElementById('profileModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = document.getElementsByClassName('modal');
    for (const modal of modals) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
}

function rateRecipe(recipeId, rating) {
    if (!confirm(`Are you sure you want to rate this recipe ${rating} stars?`)) return;
    
    fetch('rate_recipe.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            recipe_id: recipeId,
            rating: rating
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update displayed ratings
            document.querySelector('.average-rating').innerHTML = 
                `Average: ${data.average_rating} ★`;
            
            // Update user stars
            document.querySelectorAll('.star-rating .fa-star').forEach(star => {
                const starValue = parseInt(star.dataset.rating);
                star.classList.toggle('rated', starValue <= rating);
            });
        } else {
            alert('Error: ' + (data.message || 'Failed to submit rating'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting your rating');
    });
}

// Display error message under an input field
function showError(inputElement, message) {
    // Remove any existing error for this element
    const previousError = inputElement.nextElementSibling;
    if (previousError && previousError.classList.contains('validation-error')) {
        previousError.remove();
    }
    
    // Create and insert error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'validation-error';
    errorDiv.textContent = message;
    
    // Add after the input element
    inputElement.parentNode.insertBefore(errorDiv, inputElement.nextElementSibling);
    
    // Highlight the input
    inputElement.style.borderColor = '#dc3545';
    inputElement.classList.add('is-invalid');
}

// Display success message in the form
function showSuccessMessage(message, container) {
    // Create success message element
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
    
    // Insert at the top of the container
    container.insertBefore(successDiv, container.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (successDiv.parentNode) {
            successDiv.classList.add('fade-out');
            setTimeout(() => successDiv.remove(), 500);
        }
    }, 5000);
}

// Display error message in the form
function showErrorMessage(message, container) {
    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    
    // Insert at the top of the container
    container.insertBefore(errorDiv, container.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.classList.add('fade-out');
            setTimeout(() => errorDiv.remove(), 500);
        }
    }, 5000);
}

// Function to initialize the image preview
function initImagePreview(fileInput, previewElement) {
    if (!fileInput || !previewElement) return;
    
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewElement.src = e.target.result;
                previewElement.style.display = 'block';
            };
            
            reader.readAsDataURL(this.files[0]);
        } else {
            previewElement.src = '';
            previewElement.style.display = 'none';
        }
    });
}

// Recipe Update Form Validation
function validateRecipeUpdateForm() {
    // Get form elements
    const form = document.querySelector('form[name="update-recipe-form"]');
    const title = form.querySelector('input[name="title"]');
    const description = form.querySelector('textarea[name="description"]');
    const ingredients = form.querySelector('textarea[name="ingredients"]');
    const instructions = form.querySelector('textarea[name="instructions"]');
    const newImage = form.querySelector('input[name="new_image"]');
    
    // Reset previous error messages
    clearValidationErrors();
    
    // Validation flags
    let isValid = true;
    
    // Validate title (min 3 characters, max 100)
    if (!title.value.trim() || title.value.trim().length < 3 || title.value.trim().length > 100) {
        showError(title, 'Recipe title must be between 3 and 100 characters');
        isValid = false;
    }
    
    // Validate description (min 20 characters)
    if (!description.value.trim() || description.value.trim().length < 20) {
        showError(description, 'Description must be at least 20 characters');
        isValid = false;
    }
    
    // Validate ingredients (at least 1 non-empty line)
    const ingredientLines = ingredients.value.split('\n').filter(line => line.trim().length > 0);
    if (ingredientLines.length < 1) {
        showError(ingredients, 'Please add at least one ingredient');
        isValid = false;
    }
    
    // Validate instructions (at least 1 non-empty line)
    const instructionLines = instructions.value.split('\n').filter(line => line.trim().length > 0);
    if (instructionLines.length < 1) {
        showError(instructions, 'Please add at least one instruction step');
        isValid = false;
    }
    
    // Validate image if one is selected
    if (newImage.files.length > 0) {
        const file = newImage.files[0];
        
        // Check file type
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            showError(newImage, 'Please select a valid image file (JPG, JPEG, or PNG)');
            isValid = false;
        }
        
        // Check file size (5MB max)
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if (file.size > maxSize) {
            showError(newImage, 'Image size must be less than 5MB');
            isValid = false;
        }
    }
    
    return isValid;
}

// Helper function to clear all validation errors
function clearValidationErrors() {
    // Remove all error messages
    document.querySelectorAll('.validation-error').forEach(error => error.remove());
    
    // Reset all input styling
    document.querySelectorAll('.is-invalid').forEach(input => {
        input.style.borderColor = '';
        input.classList.remove('is-invalid');
    });
}

// Recipe Creation Form Validation
function validateRecipeCreationForm() {
    // Get form elements
    const form = document.querySelector('form[name="create-recipe-form"]');
    const title = form.querySelector('input[name="title"]');
    const description = form.querySelector('textarea[name="description"]');
    const ingredients = form.querySelector('textarea[name="ingredients[]"]');
    const instructions = form.querySelector('textarea[name="instructions[]"]');
    const image = form.querySelector('input[name="image_url"]');
    
    // Reset previous error messages
    clearValidationErrors();
    
    // Validation flags
    let isValid = true;
    
    // Validate title (min 3 characters, max 100)
    if (!title.value.trim() || title.value.trim().length < 3 || title.value.trim().length > 100) {
        showError(title, 'Recipe title must be between 3 and 100 characters');
        isValid = false;
    }
    
    // Validate description (min 20 characters)
    if (!description.value.trim() || description.value.trim().length < 20) {
        showError(description, 'Description must be at least 20 characters');
        isValid = false;
    }
    
    // Validate ingredients (at least 1 non-empty line)
    if (!ingredients.value.trim()) {
        showError(ingredients, 'Please add at least one ingredient');
        isValid = false;
    }
    
    // Validate instructions (at least 1 non-empty line)
    if (!instructions.value.trim()) {
        showError(instructions, 'Please add at least one instruction step');
        isValid = false;
    }
    
    // Validate image is selected
    if (image.files.length === 0) {
        showError(image, 'Please select an image for your recipe');
        isValid = false;
    } else {
        const file = image.files[0];
        
        // Check file type
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            showError(image, 'Please select a valid image file (JPG, JPEG, or PNG)');
            isValid = false;
        }
        
        // Check file size (5MB max)
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if (file.size > maxSize) {
            showError(image, 'Image size must be less than 5MB');
            isValid = false;
        }
    }
    
    return isValid;
}