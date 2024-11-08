document.addEventListener("DOMContentLoaded", function () {
  // 1. Welcome Message
  function updateWelcomeMessage() {
      const welcomeMessageElement = document.getElementById("welcomeMessage");
      if (welcomeMessageElement) { // Check if element exists
          const currentTime = new Date();
          const hours = currentTime.getHours();
          const greeting = hours < 12 ? "Good morning" : hours < 18 ? "Good afternoon" : "Good evening";
          welcomeMessageElement.textContent = `${greeting}, welcome to Ceylon Cuisine!`;
      }
  }
  
  updateWelcomeMessage();
  setInterval(updateWelcomeMessage, 60000);

  // Remaining code for recipe cards, search, and character count
  const card = [
      // Your recipe objects go here (unchanged from your code)
  ];
  
  const categories = [...new Set(card.map(item => item.title))].map(title => {
      return card.find(item => item.title === title);
  });

  const displayItem = (items) => {
      const rootElement = document.getElementById('root');
      if (items.length === 0) {
          rootElement.innerHTML = `<p>No recipes found. Try a different search term!</p>`;
          return;
      }

      rootElement.innerHTML = items.map(item => {
          const { image, title, description } = item;
          return `
              <div class="card">
                  <div class="image-box">
                      <img src="${image}" alt="${title}" class="img-fluid">
                  </div>
                  <div class="title">
                      <h2 class="noto-sans">${title}</h2>
                  </div>
                  <div class="description">
                      <p>${description}</p>
                  </div>
                  <button>View Recipe</button>
              </div>
          `;
      }).join('');
  };

  displayItem(categories); // Initial display

  // Recipe Search
  const searchInput = document.getElementById('search-bar');
  if (searchInput) {
      searchInput.addEventListener('keyup', (e) => {
          const searchData = e.target.value.toLowerCase();
          const filteredData = categories.filter(item => item.title.toLowerCase().includes(searchData));
          displayItem(filteredData);
      });
  }

  // Character Count for Textarea
  const descriptionTextarea = document.getElementById('description');
  const charCount = document.getElementById('char-count');
  const maxLength = 158;

  if (descriptionTextarea && charCount) {
      descriptionTextarea.addEventListener('input', function () {
          const remaining = maxLength - descriptionTextarea.value.length;
          charCount.textContent = `${remaining} characters remaining`;

          // Optional: Change color when limit is near or exceeded
          charCount.style.color = remaining <= 10 ? 'red' : '#888';
      });
  }
});
