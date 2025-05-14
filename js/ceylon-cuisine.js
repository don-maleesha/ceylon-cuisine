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
