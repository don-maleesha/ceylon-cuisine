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
    
    // Add form names to recipe forms for validation
    const recipeUpdateForm = document.querySelector('#updatePanel form');
    if (recipeUpdateForm) {
        recipeUpdateForm.setAttribute('name', 'update-recipe-form');
        recipeUpdateForm.addEventListener('submit', function(e) {
            if (!validateRecipeUpdateForm()) {
                e.preventDefault();
            } else {
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
                e.preventDefault();
            } else {
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
            const previousError = this.nextElementSibling;
            if (previousError && previousError.classList.contains('validation-error')) {
                previousError.remove();
                this.style.borderColor = '';
                this.classList.remove('is-invalid');
            }
            
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
            const previousError = this.nextElementSibling;
            if (previousError && previousError.classList.contains('validation-error')) {
                previousError.remove();
                this.style.borderColor = '';
                this.classList.remove('is-invalid');
            }
            
            if (this.files.length > 0) {
                const file = this.files[0];
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                
                if (!validTypes.includes(file.type)) {
                    showError(this, 'Please select a valid image file (JPG, JPEG, or PNG)');
                    return;
                }
                
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    showError(this, 'Image size must be less than 5MB');
                    return;
                }
                
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
        profilePictureInput.addEventListener('change', function() {
            const submitBtn = document.getElementById('profile-picture-submit');
            
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                
                if (!validTypes.includes(file.type)) {
                    showError(this, 'Please select a valid image file (JPG, JPEG, or PNG)');
                    return;
                }
                
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    showError(this, 'Image size must be less than 2MB');
                    return;
                }
                
                submitBtn.textContent = 'Upload ' + file.name;
                submitBtn.disabled = false;
            }
        });
        
        profilePictureForm.addEventListener('submit', function(e) {
            if (!profilePictureInput.files || profilePictureInput.files.length === 0) {
                e.preventDefault();
                showError(profilePictureInput, 'Please select an image file first');
                return;
            }
            
            const submitBtn = document.getElementById('profile-picture-submit');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        });
    }
});

// Tab switching function
function showSection(sectionId) {
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });
    document.querySelectorAll('.tabs button').forEach(button => {
        button.classList.remove('active');
    });

    document.getElementById(sectionId).classList.add('active');
    document.querySelector(`.tabs button[onclick="showSection('${sectionId}')"]`)
        .classList.add('active');
}

// Modal handling functions
function showModal(recipeId) {
    history.pushState({}, '', `?id=${recipeId}`);
    showSection('myRecipe');
    
    fetch(`get_recipe.php?id=${recipeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                try {
                    viewRecipe(
                        data.recipe.id,
                        data.recipe.title, 
                        data.recipe.description, 
                        data.recipe.image_url,
                        data.recipe.ingredients,
                        data.recipe.instructions
                    );
                } catch (error) {
                    console.error('Error processing recipe data:', error);
                    alert('There was an error displaying the recipe. Please try again.');
                }
            } else {
                alert('Recipe not found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('recipeModal').style.display = 'block';
            document.body.classList.add('modal-open');
        });
}

function closeModal() {
    document.getElementById('recipeModal').style.display = 'none';
    document.body.classList.remove('modal-open');
    history.replaceState({}, '', window.location.pathname);
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        closeModal();
    }
}

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
    let recipeId;
    
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('id')) {
        recipeId = urlParams.get('id');
    } else {
        const title = document.getElementById('modalRecipeTitle').textContent;
        const recipeCards = document.querySelectorAll('.card');
        
        for (const card of recipeCards) {
            if (card.querySelector('.title h2').textContent === title) {
                const viewBtn = card.querySelector('.view-button');
                if (viewBtn && viewBtn.getAttribute('onclick')) {
                    const onclickAttr = viewBtn.getAttribute('onclick');
                    const idMatch = onclickAttr.match(/viewRecipe\(\s*["']?(\d+)["']?/);
                    if (idMatch && idMatch[1]) {
                        recipeId = idMatch[1];
                        break;
                    }
                }
            }
        }
    }
    
    if (recipeId) {
        const title = document.getElementById('modalRecipeTitle').textContent;
        const description = document.getElementById('modalRecipeDescription').textContent;
        const imgSrc = document.getElementById('modalRecipeImage').src;
        const imageUrl = imgSrc.split('/').pop();
        
        const ingredients = [];
        document.querySelectorAll('#modalRecipeIngredients li').forEach(item => {
            if (!item.classList.contains('no-data-item')) {
                ingredients.push(item.textContent);
            }
        });
        
        const instructions = [];
        document.querySelectorAll('#modalRecipeInstructions li').forEach(item => {
            if (!item.classList.contains('no-data-item')) {
                instructions.push(item.textContent);
            }
        });
        
        document.getElementById('update_recipe_id').value = recipeId;
        document.getElementById('update_title').value = title || '';
        document.getElementById('update_description').value = description || '';
        document.getElementById('update_ingredients').value = ingredients.join('\n');
        document.getElementById('update_instructions').value = instructions.join('\n');
        
        const currentImg = document.getElementById('current_recipe_image');
        if (currentImg) {
            currentImg.src = imgSrc;
        }
        
        const newImageInput = document.getElementById('new_image');
        const newImagePreview = document.querySelector('.new-image-preview');
        if (newImageInput && newImagePreview) {
            newImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        newImagePreview.src = e.target.result;
                        newImagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
        
        document.getElementById('panelOverlay').style.display = 'block';
        setTimeout(() => {
            document.getElementById('updatePanel').classList.add('active');
        }, 10);
    } else {
        alert('Could not determine which recipe to update. Please try again.');
    }
}

function closeUpdatePanel() {
    document.getElementById('updatePanel').classList.remove('active');
    setTimeout(() => {
        document.getElementById('panelOverlay').style.display = 'none';
    }, 300);
}

document.getElementById('panelOverlay').addEventListener('click', (e) => {
    if (e.target === document.getElementById('panelOverlay')) {
        closeUpdatePanel();
    }
});

function openProfileModal() {
    document.getElementById('profileModal').style.display = 'block';
}

function closeProfileModal() {
    document.getElementById('profileModal').style.display = 'none';
}

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
            document.querySelector('.average-rating').innerHTML = 
                `Average: ${data.average_rating} ★`;
            
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

function showError(inputElement, message) {
    const previousError = inputElement.nextElementSibling;
    if (previousError && previousError.classList.contains('validation-error')) {
        previousError.remove();
    }
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'validation-error';
    errorDiv.textContent = message;
    
    inputElement.parentNode.insertBefore(errorDiv, inputElement.nextElementSibling);
    
    inputElement.style.borderColor = '#dc3545';
    inputElement.classList.add('is-invalid');
}

function showSuccessMessage(message, container) {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
    
    container.insertBefore(successDiv, container.firstChild);
    
    setTimeout(() => {
        if (successDiv.parentNode) {
            successDiv.classList.add('fade-out');
            setTimeout(() => successDiv.remove(), 500);
        }
    }, 5000);
}

function showErrorMessage(message, container) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    
    container.insertBefore(errorDiv, container.firstChild);
    
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
    const form = document.querySelector('#updatePanel form');
    const title = document.getElementById('update_title');
    const description = document.getElementById('update_description');
    const ingredients = document.getElementById('update_ingredients');
    const instructions = document.getElementById('update_instructions');
    const newImage = document.getElementById('new_image');
    
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

// Function to view a specific recipe
function viewRecipe(recipeId, title, description, imageUrl, ingredients, instructions) {
    // Store current recipe ID for future use
    currentRecipeId = recipeId;
    
    // Set recipe details in the modal
    document.getElementById('modalRecipeTitle').textContent = title || 'Recipe Title';
    document.getElementById('modalRecipeDescription').innerHTML = (description || 'No description available').replace(/\n/g, '<br>');
    document.getElementById('modalRecipeImage').src = "../uploads/" + (imageUrl || 'default.jpg');
    document.getElementById('modalRecipeImage').alt = title || 'Recipe image';
    
    // Parse ingredients
    let ingredientsArray = [];
    
    try {
        // If it's already an array, use it directly
        if (Array.isArray(ingredients)) {
            ingredientsArray = ingredients;
        } 
        // If it's a string that might be JSON
        else if (typeof ingredients === 'string') {
            try {
                const parsed = JSON.parse(ingredients);
                if (Array.isArray(parsed)) {
                    ingredientsArray = parsed;
                } else if (parsed !== null && parsed !== undefined) {
                    // If parsed but not an array, handle as single item
                    ingredientsArray = [String(parsed)];
                }
            } catch (e) {
                // Try different delimiters for string splitting
                if (ingredients.includes('|')) {
                    ingredientsArray = ingredients.split('|').map(item => item.trim());
                }
                // Try semicolon delimiter
                else if (ingredients.includes(';')) {
                    ingredientsArray = ingredients.split(';').map(item => item.trim());
                }
                // If it's not valid JSON but has commas, try splitting by commas
                else if (ingredients.includes(',')) {
                    ingredientsArray = ingredients.split(',').map(item => item.trim());
                }
                // If it's not valid JSON but has newlines, try splitting by newlines
                else if (ingredients.includes('\n')) {
                    ingredientsArray = ingredients.split('\n').map(item => item.trim());
                }
                // Otherwise treat as a single item
                else if (ingredients.trim() !== '') {
                    ingredientsArray = [ingredients];
                }
            }
        }
        // Fallback for any other type
        else if (ingredients) {
            ingredientsArray = [String(ingredients)];
        }
    } catch (e) {
        console.error('Error processing ingredients:', e);
    }
    
    // Filter out empty items
    ingredientsArray = ingredientsArray.filter(item => item && String(item).trim() !== '');
    
    // Clear and populate ingredients list
    const ingredientsList = document.getElementById('modalRecipeIngredients');
    ingredientsList.innerHTML = '';
    
    if (ingredientsArray.length === 0) {
        const li = document.createElement('li');
        li.textContent = 'No ingredients available';
        li.className = 'no-data-item';
        ingredientsList.appendChild(li);
    } else {
        ingredientsArray.forEach(ingredient => {
            const li = document.createElement('li');
            li.className = 'ingredient-item';
            li.textContent = ingredient;
            ingredientsList.appendChild(li);
        });
    }
      // Parse instructions
    let instructionsArray = [];
    
    try {
        // If it's already an array, use it directly
        if (Array.isArray(instructions)) {
            instructionsArray = instructions;
        } 
        // If it's a string that might be JSON
        else if (typeof instructions === 'string') {
            try {
                const parsed = JSON.parse(instructions);
                if (Array.isArray(parsed)) {
                    instructionsArray = parsed;
                } else if (parsed !== null && parsed !== undefined) {
                    // If parsed but not an array, handle as single item
                    instructionsArray = [String(parsed)];
                }
            } catch (e) {
                // Try different delimiters for string splitting
                if (instructions.includes('|')) {
                    instructionsArray = instructions.split('|').map(item => item.trim());
                }
                // Try semicolon delimiter
                else if (instructions.includes(';')) {
                    instructionsArray = instructions.split(';').map(item => item.trim());
                }
                // If it's not valid JSON but has commas, try splitting by commas
                else if (instructions.includes(',')) {
                    instructionsArray = instructions.split(',').map(item => item.trim());
                }
                // If it's not valid JSON but has newlines, try splitting by newlines
                else if (instructions.includes('\n')) {
                    instructionsArray = instructions.split('\n').map(item => item.trim());
                }
                // Otherwise treat as a single item
                else if (instructions.trim() !== '') {
                    instructionsArray = [instructions];
                }
            }
        }
        // Fallback for any other type
        else if (instructions) {
            instructionsArray = [String(instructions)];
        }
    } catch (e) {
        console.error('Error processing instructions:', e);
    }
    
    // Filter out empty items
    instructionsArray = instructionsArray.filter(item => item && String(item).trim() !== '');
    
    // Clear and populate instructions list
    const instructionsList = document.getElementById('modalRecipeInstructions');
    instructionsList.innerHTML = '';
    
    if (instructionsArray.length === 0) {
        const li = document.createElement('li');
        li.textContent = 'No instructions available';
        li.className = 'no-data-item';
        instructionsList.appendChild(li);
    } else {
        instructionsArray.forEach((instruction, index) => {
            const li = document.createElement('li');
            li.className = 'instruction-step';
            li.textContent = instruction;
            instructionsList.appendChild(li);
        });
    }
    
    // Show the modal
    document.getElementById('recipeModal').style.display = 'block';
    document.body.classList.add('modal-open');
    
    // Log for debugging
    console.log('Recipe modal opened with data:', {
        id: recipeId,
        title,
        ingredients: ingredientsArray,
        instructions: instructionsArray
    });
}

// Global variable to store current recipe ID
let currentRecipeId = null;