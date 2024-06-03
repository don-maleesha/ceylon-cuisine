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
