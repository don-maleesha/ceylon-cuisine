document.addEventListener("DOMContentLoaded", function () {
    console.log('DOM Content Loaded - Starting initialization...');
    
    // Check for PHP session messages first
    console.log('=== CHECKING FOR PHP MESSAGES ===');
    const messageElements = document.querySelectorAll('.alert, .message, .error, .success, [class*="alert"], [class*="message"]');
    console.log('Found message elements:', messageElements.length);
    messageElements.forEach((el, index) => {
        console.log(`Message ${index}:`, el.textContent.trim(), 'Classes:', el.className);
    });
    
    // Debug: Check if all required elements exist
    console.log('=== DEBUGGING FORM ELEMENTS ===');
    const form = document.querySelector('#newRecipe form');
    console.log('Form found:', form);
    
    const submitBtn = document.querySelector('button[name="submit-recipe"]');
    console.log('Submit button found:', submitBtn);
    if (submitBtn) {
        console.log('Submit button properties:', {
            disabled: submitBtn.disabled,
            style: submitBtn.style.cssText,
            classList: Array.from(submitBtn.classList)
        });
    }
    
    // Check form fields
    const titleField = document.querySelector('input[name="title"]');
    const descField = document.querySelector('textarea[name="description"]');
    const ingredientsField = document.querySelector('textarea[name="ingredients[]"]');
    const instructionsField = document.querySelector('textarea[name="instructions[]"]');
    const imageField = document.querySelector('input[name="image_url"]');
    
    console.log('Form fields:', {
        title: titleField,
        description: descField,
        ingredients: ingredientsField,
        instructions: instructionsField,
        image: imageField
    });
    
    // Handle profile picture upload messages
    const profileMessageContainer = document.getElementById("profile-message-container");
    if (profileMessageContainer && profileMessageContainer.dataset.message) {
        const message = profileMessageContainer.dataset.message;
        const status = profileMessageContainer.dataset.status;
        const container = document.querySelector('.container');
        
        if (container && message.trim()) {
            if (status === 'success') {
                showSuccessMessage(message, container);
            } else if (status === 'error') {
                showErrorMessage(message, container);
            }
        }
    }

    // Handle recipe submission messages
    const recipeMessageContainer = document.getElementById("recipe-message-container");
    if (recipeMessageContainer && recipeMessageContainer.dataset.message) {
        const message = recipeMessageContainer.dataset.message;
        const status = recipeMessageContainer.dataset.status;
        const form = document.getElementById("recipe-form");
        
        if (form && message.trim()) {
            if (status === 'success') {
                showSuccessMessage(message, form);
                // Clear the form on success
                form.reset();
                const imagePreview = document.querySelector('.image-preview');
                if (imagePreview) {
                    imagePreview.style.display = 'none';
                }
            } else if (status === 'error') {
                showErrorMessage(message, form);
                
                // If there are specific field errors, highlight them
                if (message.includes("Title")) {
                    highlightField('title');
                }
                if (message.includes("Description")) {
                    highlightField('description');
                }
                if (message.includes("Ingredients")) {
                    highlightField('ingredients');
                }
                if (message.includes("Instructions")) {
                    highlightField('instructions');
                }
                if (message.includes("image")) {
                    highlightField('image_url');
                }
            }
        }
    }
    
    // Handle recipe update messages
    const updateMessageContainer = document.getElementById("update-message-container");
    if (updateMessageContainer && updateMessageContainer.dataset.message) {
        const message = updateMessageContainer.dataset.message;
        const status = updateMessageContainer.dataset.status;
        const container = document.querySelector('#updatePanel');
        
        if (message.trim()) {
            if (status === 'success') {
                showSuccessMessage(message, container);
                // Also show at top of page
                showSuccessMessage(message, document.querySelector('.container'));
            } else if (status === 'error') {
                showErrorMessage(message, container);
                // Also show at top of page
                showErrorMessage(message, document.querySelector('.container'));
            } else if (status === 'info') {
                showInfoMessage(message, container);
                // Also show at top of page  
                showInfoMessage(message, document.querySelector('.container'));
            }
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
            // Validate form before submission
            if (!validateRecipeUpdateForm()) {
                e.preventDefault();
                return;
            }
            
            // Show loading state on submit button
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            }
            
            // Form will submit normally - no AJAX needed
        });
        
        // Initialize image preview for update form
        const fileInput = recipeUpdateForm.querySelector('input[type="file"]');
        const previewElement = recipeUpdateForm.querySelector('.new-image-preview');
        if (fileInput && previewElement) {
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
    }

    const recipeCreateForm = document.querySelector('#newRecipe form');
    if (recipeCreateForm) {
        console.log('Recipe create form found:', recipeCreateForm);
        recipeCreateForm.setAttribute('name', 'create-recipe-form');
        recipeCreateForm.setAttribute('id', 'recipe-form'); // Add ID for validation
        
        // Add click event to submit button specifically
        const submitButton = recipeCreateForm.querySelector('button[type="submit"]');
        if (submitButton) {
            console.log('Submit button found:', submitButton);
            
            // Make sure button is not disabled
            submitButton.disabled = false;
            
            submitButton.addEventListener('click', function(e) {
                console.log('Submit button clicked!');
                console.log('Button disabled?', this.disabled);
                console.log('Form element:', recipeCreateForm);
                
                // Simple test: Try to submit form manually if needed
                if (e.defaultPrevented) {
                    console.log('Default action was prevented');
                }
            });
            
            // Add double-click handler as backup
            submitButton.addEventListener('dblclick', function(e) {
                console.log('Submit button double-clicked - forcing submission');
                e.preventDefault();
                recipeCreateForm.submit();
            });
            
        } else {
            console.error('Submit button NOT found in form');
        }
        
        recipeCreateForm.addEventListener('submit', function(e) {
            console.log('=== FORM SUBMISSION EVENT TRIGGERED ===');
            console.log('Event:', e);
            console.log('Form element:', this);
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);
            
            // Prevent default form submission temporarily
            e.preventDefault();
            
            // Get form data for debugging
            const formData = new FormData(this);
            console.log('=== FORM DATA INSPECTION (BEFORE ADDING SUBMIT PARAM) ===');
            for (let [key, value] of formData.entries()) {
                if (value instanceof File) {
                    console.log(`${key}: FILE - ${value.name} (${value.size} bytes)`);
                } else {
                    console.log(`${key}: ${value}`);
                }
            }
            
            // Ensure submit-recipe parameter is included
            const hasSubmitRecipe = formData.has('submit-recipe');
            console.log('Has submit-recipe parameter (initial):', hasSubmitRecipe);
            
            if (!hasSubmitRecipe) {
                console.log('Adding submit-recipe parameter manually');
                formData.append('submit-recipe', '1');
            }
            
            console.log('=== FORM DATA INSPECTION (AFTER ADDING SUBMIT PARAM) ===');
            for (let [key, value] of formData.entries()) {
                if (value instanceof File) {
                    console.log(`${key}: FILE - ${value.name} (${value.size} bytes)`);
                } else {
                    console.log(`${key}: ${value}`);
                }
            }
            
            // TEMPORARILY DISABLE ALL VALIDATION - SUBMIT DIRECTLY USING FETCH
            console.log('=== BYPASSING VALIDATION - SUBMITTING VIA FETCH ===');
            
            // Disable submit button to prevent double submission
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                console.log('Disabling submit button to prevent double submission');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            }
            
            // Submit using fetch to ensure the data gets sent correctly
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response received:', response);
                return response.text();
            })
            .then(html => {
                console.log('Response HTML:', html.substring(0, 500) + '...');
                // Reload the page to see the result
                window.location.reload();
            })
            .catch(error => {
                console.error('Submission error:', error);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Recipe';
                }
                alert('Error submitting recipe. Please try again.');
            });
            
            // Don't return true - we handled submission manually
            return false;
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
    
    // Profile Picture Upload Handling
    const profilePictureForm = document.getElementById('profile-picture-form');
    const profilePictureInput = document.getElementById('profile-picture-upload');
    const profilePictureSubmit = document.getElementById('profile-picture-submit');
    const profilePicturePreview = document.querySelector('.profile-picture img');

    if (profilePictureForm && profilePictureInput && profilePictureSubmit) {
        // Handle file selection
        profilePictureInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/jfif'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                // Validate file type
                if (!validTypes.includes(file.type)) {
                    showError(this, 'Please select a valid image file (JPG, JPEG, PNG, or JFIF)');
                    this.value = '';
                    profilePictureSubmit.disabled = true;
                    profilePictureSubmit.textContent = 'Select Image';
                    return;
                }

                // Validate file size
                if (file.size > maxSize) {
                    showError(this, 'Image size must be less than 2MB');
                    this.value = '';
                    profilePictureSubmit.disabled = true;
                    profilePictureSubmit.textContent = 'Select Image';
                    return;
                }

                // Clear any previous errors
                const previousError = this.nextElementSibling;
                if (previousError && previousError.classList.contains('validation-error')) {
                    previousError.remove();
                    this.style.borderColor = '';
                    this.classList.remove('is-invalid');
                }

                // Preview the image
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePicturePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);

                // Enable the submit button
                profilePictureSubmit.disabled = false;
                profilePictureSubmit.textContent = 'Upload ' + 
                    file.name.substring(0, 20) + 
                    (file.name.length > 20 ? '...' : '');
            } else {
                profilePictureSubmit.disabled = true;
                profilePictureSubmit.textContent = 'Select Image';
            }
        });

        // Update the profile picture form submission handler
        profilePictureForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!profilePictureInput.files || profilePictureInput.files.length === 0) {
                showError(profilePictureInput, 'Please select an image file first');
                return;
            }

            const formData = new FormData(this);
            profilePictureSubmit.disabled = true;
            profilePictureSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';

            fetch('profile.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json' // Explicitly ask for JSON response
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                // Check if response is actually JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('Server returned non-JSON response:', text);
                        throw new Error('Server returned HTML instead of JSON. Check PHP file for errors.');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update ALL profile picture images on the page immediately
                    if (data.profile_picture) {
                        const timestamp = '?' + new Date().getTime();
                        const newImagePath = data.profile_picture + timestamp;
                        
                        // Update the main profile picture in the header
                        const mainProfileImg = document.querySelector('.profile-picture img');
                        if (mainProfileImg) {
                            mainProfileImg.src = newImagePath;
                        }
                        
                        // Update any other profile images that might exist on the page
                        const allProfileImages = document.querySelectorAll('img[alt*="Profile"], img[src*="' + data.profile_picture.replace(timestamp, '').split('?')[0] + '"]');
                        allProfileImages.forEach(img => {
                            if (img !== mainProfileImg) { // Avoid double-updating the main image
                                img.src = newImagePath;
                            }
                        });
                        
                        console.log('Profile picture updated successfully:', newImagePath);
                    }
                    showSuccessMessage(data.message || 'Profile picture updated successfully!', profilePictureForm);
                    
                    // Reset the form
                    profilePictureForm.reset();
                } else {
                    throw new Error(data.message || 'Upload failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage(error.message || 'An error occurred during upload. Please try again.', profilePictureForm);
            })
            .finally(() => {
                profilePictureSubmit.disabled = true;
                profilePictureSubmit.textContent = 'Select Image';
                // Clear the file input
                profilePictureInput.value = '';
            });
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
    
    // Try to get recipe ID from currentRecipeId first
    if (currentRecipeId) {
        recipeId = currentRecipeId;
        console.log('Using currentRecipeId:', recipeId);
    } else {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('id')) {
            recipeId = urlParams.get('id');
            console.log('Using URL param ID:', recipeId);
        } else {
            const title = document.getElementById('modalRecipeTitle').textContent;
            const recipeCards = document.querySelectorAll('.card');
            
            for (const card of recipeCards) {
                const cardTitle = card.querySelector('.title h2');
                if (cardTitle && cardTitle.textContent === title) {
                    const viewBtn = card.querySelector('.view-button');
                    if (viewBtn && viewBtn.getAttribute('onclick')) {
                        const onclickAttr = viewBtn.getAttribute('onclick');
                        const idMatch = onclickAttr.match(/viewRecipe\(\s*["']?(\d+)["']?/);
                        if (idMatch && idMatch[1]) {
                            recipeId = idMatch[1];
                            console.log('Found recipe ID from card:', recipeId);
                            break;
                        }
                    }
                }
            }
        }
    }
    
    console.log('Final recipe ID for update:', recipeId);
    
    if (recipeId) {
        // Check if form is already populated by viewRecipe function
        const updateRecipeIdField = document.getElementById('update_recipe_id');
        const updateTitleField = document.getElementById('update_title');
        
        // Only populate if not already populated (fallback)
        if (!updateRecipeIdField.value || !updateTitleField.value) {
            const title = document.getElementById('modalRecipeTitle').textContent;
            const description = document.getElementById('modalRecipeDescription').innerHTML;
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
            // Convert HTML breaks back to newlines for textarea
            document.getElementById('update_description').value = (description || '').replace(/<br>/g, '\n');
            document.getElementById('update_ingredients').value = ingredients.join('\n');
            document.getElementById('update_instructions').value = instructions.join('\n');
            
            console.log('Form populated with fallback logic:', {
                recipe_id: recipeId,
                title: title,
                description: description,
                ingredients: ingredients,
                instructions: instructions
            });
            
            const currentImg = document.getElementById('current_recipe_image');
            if (currentImg) {
                currentImg.src = imgSrc;
            }
        } else {
            console.log('Form already populated by viewRecipe function');
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
    // Remove any existing messages first
    const existingMessages = container.querySelectorAll('.success-message, .error-message');
    existingMessages.forEach(msg => msg.remove());
    
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

function showErrorMessage(message, container = document.body) {
    // Remove any existing messages first
    const existingMessages = container.querySelectorAll('.error-message');
    existingMessages.forEach(msg => msg.remove());

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    
    // Insert at the beginning of the container
    if (container.firstChild) {
        container.insertBefore(errorDiv, container.firstChild);
    } else {
        container.appendChild(errorDiv);
    }

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.classList.add('fade-out');
            setTimeout(() => errorDiv.remove(), 500);
        }
    }, 5000);
}

function showInfoMessage(message, container = document.body) {
    // Remove any existing messages first
    const existingMessages = container.querySelectorAll('.info-message');
    existingMessages.forEach(msg => msg.remove());

    const infoDiv = document.createElement('div');
    infoDiv.className = 'info-message';
    infoDiv.innerHTML = `<i class="fas fa-info-circle"></i> ${message}`;
    
    // Insert at the beginning of the container
    if (container.firstChild) {
        container.insertBefore(infoDiv, container.firstChild);
    } else {
        container.appendChild(infoDiv);
    }

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (infoDiv.parentNode) {
            infoDiv.classList.add('fade-out');
            setTimeout(() => infoDiv.remove(), 500);
        }
    }, 5000);
}

// Function to initialize the image preview
function initImagePreview(fileInput, previewElement) {
    if (!fileInput || !previewElement) return;

    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) {
            previewElement.src = '';
            previewElement.style.display = 'none';
            return;
        }

        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            showError(this, 'Invalid image type. Please select JPG, JPEG, or PNG.');
            this.value = '';
            previewElement.src = '';
            previewElement.style.display = 'none';
            return;
        }

        // Validate file size
        if (file.size > 5 * 1024 * 1024) {
            showError(this, 'Image must be less than 5MB');
            this.value = '';
            previewElement.src = '';
            previewElement.style.display = 'none';
            return;
        }

        // Clear previous errors
        const error = this.nextElementSibling;
        if (error && error.classList.contains('validation-error')) {
            error.remove();
            this.style.borderColor = '';
            this.classList.remove('is-invalid');
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            previewElement.src = e.target.result;
            previewElement.style.display = 'block';
        };
        reader.onerror = () => {
            showError(this, 'Error reading file. Please try again.');
            this.value = '';
            previewElement.src = '';
            previewElement.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
}

// Recipe Update Form Validation
function validateRecipeUpdateForm() {
    let isValid = true;
    
    // Clear previous errors
    clearValidationErrors();
    
    // Get form elements
    const title = document.getElementById('update_title');
    const description = document.getElementById('update_description');
    const ingredients = document.getElementById('update_ingredients');
    const instructions = document.getElementById('update_instructions');
    const newImage = document.getElementById('new_image');
    
    // Validate title (3-100 chars)
    if (!title.value.trim() || title.value.trim().length < 3 || title.value.trim().length > 100) {
        showError(title, 'Title must be between 3-100 characters');
        isValid = false;
    }
    
    // Validate description (min 20 chars)
    if (!description.value.trim() || description.value.trim().length < 20) {
        showError(description, 'Description must be at least 20 characters');
        isValid = false;
    }
    
    // Validate ingredients (at least 1 line)
    const ingredientLines = ingredients.value.split('\n').filter(line => line.trim());
    if (ingredientLines.length === 0) {
        showError(ingredients, 'Please add at least one ingredient');
        isValid = false;
    }
    
    // Validate instructions (at least 1 line)
    const instructionLines = instructions.value.split('\n').filter(line => line.trim());
    if (instructionLines.length === 0) {
        showError(instructions, 'Please add at least one instruction');
        isValid = false;
    }
    
    // Validate new image if selected
    if (newImage.files.length > 0) {
        const file = newImage.files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!validTypes.includes(file.type)) {
            showError(newImage, 'Only JPG, JPEG, or PNG images allowed');
            isValid = false;
        } else if (file.size > maxSize) {
            showError(newImage, 'Image must be less than 5MB');
            isValid = false;
        }
    }
    
    return isValid;
}

// Helper function to clear all validation errors
function clearValidationErrors() {
    document.querySelectorAll('.validation-error').forEach(el => el.remove());
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
        el.style.borderColor = '';
    });
}

// Recipe Form Validation with specific field IDs
function validateRecipeForm() {
    let isValid = true;
    clearValidationErrors();
    
    console.log('Starting recipe form validation...');
    
    // Validate title - check both possible IDs
    let title = document.getElementById('name');
    if (!title) {
        title = document.querySelector('input[name="title"]');
    }
    
    if (!title) {
        console.error('Title input not found');
        return false;
    }
    
    if (!title.value.trim() || title.value.trim().length < 3 || title.value.trim().length > 100) {
        showError(title, 'Title must be between 3-100 characters');
        isValid = false;
    }
    
    // Validate description - check both possible IDs
    let description = document.getElementById('description');
    if (!description) {
        description = document.querySelector('textarea[name="description"]');
    }
    
    if (!description) {
        console.error('Description textarea not found');
        return false;
    }
    
    if (!description.value.trim() || description.value.trim().length < 20) {
        showError(description, 'Description must be at least 20 characters');
        isValid = false;
    }
    
    // Validate ingredients - check both possible IDs
    let ingredients = document.getElementById('recipe-ingredients');
    if (!ingredients) {
        ingredients = document.querySelector('textarea[name="ingredients[]"]');
    }
    
    if (!ingredients) {
        console.error('Ingredients textarea not found');
        return false;
    }
    
    const ingredientLines = ingredients.value.split('\n').filter(line => line.trim());
    if (ingredientLines.length === 0) {
        showError(ingredients, 'Please add at least one ingredient');
        isValid = false;
    }
    
    // Validate instructions - check both possible IDs
    let instructions = document.getElementById('recipe-instructions');
    if (!instructions) {
        instructions = document.querySelector('textarea[name="instructions[]"]');
    }
    
    if (!instructions) {
        console.error('Instructions textarea not found');
        return false;
    }
    
    const instructionLines = instructions.value.split('\n').filter(line => line.trim());
    if (instructionLines.length === 0) {
        showError(instructions, 'Please add at least one instruction');
        isValid = false;
    }
    
    // Validate image - check both possible IDs
    let image = document.getElementById('image');
    if (!image) {
        image = document.querySelector('input[name="image_url"]');
    }
    
    if (!image) {
        console.error('Image input not found');
        return false;
    }
    
    if (!image.files || image.files.length === 0) {
        showError(image, 'Please select an image');
        isValid = false;
    } else {
        const file = image.files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/jfif'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!validTypes.includes(file.type)) {
            showError(image, 'Only JPG, JPEG, PNG, or JFIF files allowed');
            isValid = false;
        } else if (file.size > maxSize) {
            showError(image, 'Image must be less than 5MB');
            isValid = false;
        }
    }
    
    console.log('Recipe form validation result:', isValid);
    return isValid;
}

// Recipe Creation Form Validation (fallback for different field names)
function validateRecipeCreationForm() {
    // Get form elements
    const form = document.querySelector('form[name="create-recipe-form"]');
    const title = form.querySelector('input[name="title"]');
    const description = form.querySelector('textarea[name="description"]');
    const ingredients = form.querySelector('textarea[name="ingredients"]');
    const instructions = form.querySelector('textarea[name="instructions"]');
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

// Helper function to standardize recipe data
function standardizeRecipeData(data) {
    if (Array.isArray(data)) return data.filter(item => item.trim());
    if (typeof data === 'string') {
        try {
            const parsed = JSON.parse(data);
            if (Array.isArray(parsed)) return parsed.filter(item => item.trim());
        } catch (e) {
            // Try splitting by common delimiters
            const delimiters = ['\n', '|', ';', ','];
            for (const delim of delimiters) {
                if (data.includes(delim)) {
                    return data.split(delim).map(item => item.trim()).filter(item => item);
                }
            }
            return [data.trim()].filter(item => item);
        }
    }
    return [];
}

// Function to view a specific recipe
function viewRecipe(recipeId, title, description, imageUrl, ingredients, instructions) {
    // Store current recipe ID
    currentRecipeId = recipeId;
    
    // Standardize ingredients and instructions data
    const standardizedIngredients = standardizeRecipeData(ingredients);
    const standardizedInstructions = standardizeRecipeData(instructions);
    
    // Set recipe details in the modal
    document.getElementById('modalRecipeTitle').textContent = title || 'Recipe Title';
    document.getElementById('modalRecipeDescription').innerHTML = (description || 'No description available').replace(/\n/g, '<br>');
    document.getElementById('modalRecipeImage').src = "../uploads/" + (imageUrl || 'default.jpg');
    document.getElementById('modalRecipeImage').alt = title || 'Recipe image';
    
    // Clear and populate ingredients list
    const ingredientsList = document.getElementById('modalRecipeIngredients');
    ingredientsList.innerHTML = '';
    
    if (standardizedIngredients.length === 0) {
        const li = document.createElement('li');
        li.textContent = 'No ingredients available';
        li.className = 'no-data-item';
        ingredientsList.appendChild(li);
    } else {
        standardizedIngredients.forEach(ingredient => {
            const li = document.createElement('li');
            li.className = 'ingredient-item';
            li.textContent = ingredient;
            ingredientsList.appendChild(li);
        });
    }
    
    // Clear and populate instructions list
    const instructionsList = document.getElementById('modalRecipeInstructions');
    instructionsList.innerHTML = '';
    
    if (standardizedInstructions.length === 0) {
        const li = document.createElement('li');
        li.textContent = 'No instructions available';
        li.className = 'no-data-item';
        instructionsList.appendChild(li);
    } else {
        standardizedInstructions.forEach((instruction, index) => {
            const li = document.createElement('li');
            li.className = 'instruction-step';
            li.textContent = instruction;
            instructionsList.appendChild(li);
        });
    }
    
    // Populate update form fields
    document.getElementById('update_recipe_id').value = recipeId;
    document.getElementById('update_title').value = title || '';
    document.getElementById('update_description').value = description || '';
    document.getElementById('update_ingredients').value = standardizedIngredients.join('\n');
    document.getElementById('update_instructions').value = standardizedInstructions.join('\n');
    
    // Set current image preview
    const currentImg = document.getElementById('current_recipe_image');
    if (currentImg && imageUrl) {
        currentImg.src = '../uploads/' + imageUrl;
        currentImg.style.display = 'block';
    }
    
    // Clear any new image preview
    const newImagePreview = document.querySelector('.new-image-preview');
    if (newImagePreview) {
        newImagePreview.style.display = 'none';
        newImagePreview.src = '';
    }
    
    // Reset new image input
    const newImageInput = document.getElementById('new_image');
    if (newImageInput) {
        newImageInput.value = '';
    }
    
    // Show the modal
    document.getElementById('recipeModal').style.display = 'block';
    document.body.classList.add('modal-open');
    
    // Log for debugging
    console.log('Recipe modal opened with data:', {
        id: recipeId,
        title,
        ingredients: standardizedIngredients,
        instructions: standardizedInstructions
    });
}

// Function to refresh recipes list
function refreshRecipesList() {
    // This would ideally make an AJAX call to reload the recipes
    // For now, we'll just reload the page
    window.location.reload();
}

function highlightField(fieldName) {
    const field = document.querySelector(`[name="${fieldName}"]`);
    if (field) {
        field.style.borderColor = '#dc3545';
        field.classList.add('is-invalid');
        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Remove highlight after 5 seconds
        setTimeout(() => {
            field.style.borderColor = '';
            field.classList.remove('is-invalid');
        }, 5000);
    }
}

// Basic form validation as fallback
function basicValidateForm(form) {
    console.log('Running basic form validation...');
    let isValid = true;
    
    // Find title field
    const titleField = form.querySelector('input[name="title"]') || form.querySelector('#name');
    if (!titleField || !titleField.value.trim() || titleField.value.trim().length < 3) {
        console.log('Title validation failed');
        if (titleField) {
            titleField.style.borderColor = '#dc3545';
            titleField.focus();
        }
        isValid = false;
    }
    
    // Find description field
    const descField = form.querySelector('textarea[name="description"]') || form.querySelector('#description');
    if (!descField || !descField.value.trim() || descField.value.trim().length < 20) {
        console.log('Description validation failed');
        if (descField) {
            descField.style.borderColor = '#dc3545';
            if (isValid) descField.focus(); // Focus only if no previous error
        }
        isValid = false;
    }
    
    // Find ingredients field
    const ingredientsField = form.querySelector('textarea[name="ingredients[]"]') || form.querySelector('#recipe-ingredients');
    if (!ingredientsField || !ingredientsField.value.trim()) {
        console.log('Ingredients validation failed');
        if (ingredientsField) {
            ingredientsField.style.borderColor = '#dc3545';
            if (isValid) ingredientsField.focus();
        }
        isValid = false;
    }
    
    // Find instructions field
    const instructionsField = form.querySelector('textarea[name="instructions[]"]') || form.querySelector('#recipe-instructions');
    if (!instructionsField || !instructionsField.value.trim()) {
        console.log('Instructions validation failed');
        if (instructionsField) {
            instructionsField.style.borderColor = '#dc3545';
            if (isValid) instructionsField.focus();
        }
        isValid = false;
    }
    
    // Find image field
    const imageField = form.querySelector('input[name="image_url"]') || form.querySelector('#image');
    if (!imageField || !imageField.files || imageField.files.length === 0) {
        console.log('Image validation failed');
        if (imageField) {
            imageField.style.borderColor = '#dc3545';
            if (isValid) imageField.focus();
        }
        isValid = false;
    }
    
    console.log('Basic validation result:', isValid);
    return isValid;
}

// Manual field check as ultimate fallback
function manualFieldCheck(form) {
    console.log('Running manual field check...');
    let isValid = true;
    let errorMessages = [];
    
    // Check title field
    const titleField = form.querySelector('input[name="title"]');
    if (!titleField) {
        errorMessages.push('Title field not found');
        isValid = false;
    } else if (!titleField.value || titleField.value.trim().length < 3) {
        errorMessages.push('Title must be at least 3 characters');
        titleField.focus();
        isValid = false;
    }
    
    // Check description field
    const descField = form.querySelector('textarea[name="description"]');
    if (!descField) {
        errorMessages.push('Description field not found');
        isValid = false;
    } else if (!descField.value || descField.value.trim().length < 20) {
        errorMessages.push('Description must be at least 20 characters');
        if (isValid) descField.focus();
        isValid = false;
    }
    
    // Check ingredients field
    const ingredientsField = form.querySelector('textarea[name="ingredients[]"]');
    if (!ingredientsField) {
        errorMessages.push('Ingredients field not found');
        isValid = false;
    } else if (!ingredientsField.value || ingredientsField.value.trim().length === 0) {
        errorMessages.push('Please add at least one ingredient');
        if (isValid) ingredientsField.focus();
        isValid = false;
    }
    
    // Check instructions field
    const instructionsField = form.querySelector('textarea[name="instructions[]"]');
    if (!instructionsField) {
        errorMessages.push('Instructions field not found');
        isValid = false;
    } else if (!instructionsField.value || instructionsField.value.trim().length === 0) {
        errorMessages.push('Please add at least one instruction');
        if (isValid) instructionsField.focus();
        isValid = false;
    }
    
    // Check image field
    const imageField = form.querySelector('input[name="image_url"]');
    if (!imageField) {
        errorMessages.push('Image field not found');
        isValid = false;
    } else if (!imageField.files || imageField.files.length === 0) {
        errorMessages.push('Please select an image');
        if (isValid) imageField.focus();
        isValid = false;
    }
    
    if (!isValid) {
        console.log('Manual validation errors:', errorMessages);
        alert('Please fix the following issues:\n• ' + errorMessages.join('\n• '));
    }
    
    console.log('Manual field check result:', isValid);
    return isValid;
}

// Emergency function to submit form (callable from console)
function forceSubmitRecipeForm() {
    console.log('Force submitting recipe form...');
    const form = document.querySelector('#newRecipe form');
    if (form) {
        console.log('Form found, submitting...');
        form.submit();
    } else {
        console.error('Form not found');
    }
}

// Test function to check form data
function checkFormData() {
    const form = document.querySelector('#newRecipe form');
    if (form) {
        const formData = new FormData(form);
        console.log('Current form data:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}:`, typeof value === 'object' ? (value.name || 'File selected') : value);
        }
        return true;
    } else {
        console.error('Form not found');
        return false;
    }
}

// Make functions globally available for debugging
window.forceSubmitRecipeForm = forceSubmitRecipeForm;
window.checkFormData = checkFormData;
window.manualFieldCheck = manualFieldCheck;

// Global variable to store current recipe ID
let currentRecipeId = null;