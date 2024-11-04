document.addEventListener("DOMContentLoaded", function() {
  function updateWelcomeMessage() {
      var currentTime = new Date();
      var hours = currentTime.getHours();
      var greeting;

      if (hours < 12) {
          greeting = "Good morning";
      } else if (hours < 18) {
          greeting = "Good afternoon";
      } else {
          greeting = "Good evening";
      }

      document.getElementById("welcomeMessage").textContent = greeting + ", welcome to Ceylon Cuisine!";
  }

  updateWelcomeMessage();
  setInterval(updateWelcomeMessage, 60000);
});

const userCardTemplate = document.querySelector("[data-user-template]")
//const userCardContainer = document.querySelector("[data-user-cards-container]")
const searchInput = document.querySelector("[data-search]")
