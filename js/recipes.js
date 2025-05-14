
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('recipeModal');
    const urlParams = new URLSearchParams(window.location.search);
    const recipeId = urlParams.get('id');
  
    // Show modal if ID exists in URL and modal exists
    if (recipeId && modal) {
        modal.style.display = 'block';
    }
  
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
  });
  
  // Global function to close modal
  function closeModal() {
    const modal = document.getElementById('recipeModal');
    if (modal) {
        modal.style.display = 'none';
        // Clean URL without reload
        history.replaceState({}, document.title, window.location.pathname);
    }
  }

// 
