// Welcome message functionality
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
