//welcome message
document.addEventListener("DOMContentLoaded", function() {
  function updateWelcomeMessage() {
    const currentTime = new Date();
    const hours = currentTime.getHours();
    const greeting = hours < 12 ? "Good morning" : hours < 18 ? "Good afternoon" : "Good evening";
    document.getElementById("welcomeMessage").textContent = `${greeting}, welcome to Ceylon Cuisine!`;
  }

  updateWelcomeMessage();
  setInterval(updateWelcomeMessage, 60000);
});

document.addEventListener("DOMContentLoaded", function () {
  const customIcon = document.getElementById("customIcon");
  const dropdownMenu = document.getElementById("dropdownMenu");

  customIcon.addEventListener("click", function (event) {
    event.preventDefault();
    dropdownMenu.classList.toggle("show");
  });

  window.addEventListener("click", function (event) {
    if (!event.target.matches("#customIcon")) {
      if (dropdownMenu.classList.contains("show")) {
        dropdownMenu.classList.remove("show");
      }
    }
  });
});

// Automatically display the modal when the page loads with an ID
// Handle recipe modal functionality
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


// document.getElementById('profile-picture-upload').addEventListener('change', function(event) {
//   const file = event.target.files[0];
//   if (file) {
//       const reader = new FileReader();
//       reader.onload = function(e) {
//           document.getElementById('profile-picture-preview').src = e.target.result;
//       };
//       reader.readAsDataURL(file);
//   }
// });