<?php 
  session_start();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Ceylon Cuisine</title>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
      <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"><link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Satisfy&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="./aboutus.css">
      <script src="ceylon-cuisine.js"></script>
  </head>
  <body>
    <header>
      <div class="container">
        <div class="logo">
          <img src="./images/Ceylon.png" alt="Logo">
          <span class="company-name josefin-sans">Ceylon Cuisine</span>
        </div>
        <nav>
          <ul>
            <li><a href="homePage.php" class="raleway">Home</a></li>
            <li><a href="aboutus.php" class="raleway">About</a></li>
            <li><a href="contacts.php" class="raleway">Contact</a></li>
            <li><a href="recipes.php" class="raleway">Recipes</a></li>
          </ul>
        </nav>
        <div class="auth-buttons">
          <?php if(isset($_SESSION["email_address"])): ?>
            <div class="dropdown">
              <a href="#" class="custom-icon">
                <div class="user-info">
                  <i class="fas fa-user-circle" aria-hidden="true"></i>
                  <span class="username raleway"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                </div>
                <i id="customIcon" class="fas fa-chevron-down" aria-hidden="true"></i> 
              </a>
              <ul id="dropdownMenu" class="dropdown-menu">
                <li><a class="dropdown-item raleway" href="profile.php"><i class="fas fa-user"></i>  Profile</a></li>
                <li><a class="dropdown-item raleway" href="logout.php"><i class="fas fa-sign-out-alt"></i>  Logout</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a href="signin.php" class="sign-in raleway">Sign in</a>
            <a href="signup.php" class="sign-up raleway">Sign up</a>
          <?php endif; ?>
        </div>
      </div>
    </header>
    <div class="hero">
      <img src="./images/Exploring-Sri-Lanka-Desktop.jpg" alt="">
      <div class="hero-text">
          <h1 class="playfair-display">About Ceylon Cuisine</h1>
          <p class="merriweather-regular"> Welcome to Ceylon Cuisine, where we bring the vibrant flavors of Sri Lanka to life!<br>Our mission is to celebrate the rich culinary heritage of this beautiful island by sharing recipes, stories, and cooking tips with food enthusiasts around the world.</p>
          <h3 class="playfair-display"><strong>Our Story</strong></h3>
          <p class="merriweather-light">Born from a passion for authentic Sri Lankan food, Ceylon Cuisine was created to preserve and promote the unique flavors that make our dishes unforgettable. From traditional family recipes passed down through generations to modern twists on classic favorites, we aim to connect people with the soul of Sri Lankan cooking.</p>
          <a href="#" class="learn-more raleway">Learn More</a>
      </div>
    </div>
    <div class="section">
      <div class="section-text">
        <h2 class="playfair-display">Authentic Origins,<br>Modern Flavors</h2>
        <p class="merriweather-regular">Sri Lanka's rich culinary heritage is a harmonious mix of tradition and creativity. Timeless practices rooted in culture continue to captivate, while contemporary approaches bring fresh perspectives, offering a dynamic and evolving experience for everyone.</p>
      </div>
      <img src="./images/Pol-Roti__ResizedImageWzYwMCwzOTld.jpg" alt="">
    </div>
    <div class="follow-us">
      <div class="text">
        <h2 class="playfair-display">Follow Us</h2>
        <p class="merriweather-regular">@ceyloncuisine</p>
        <p class="merriweather-regular">Stay updated with the latest recipes, cooking tips and Sri Lankan food culture by following us on social meida. Don't miss out on the excitement!</p>
        <div class="social-icons">
          <a href="#" class="raleway"><i class="fab fa-twitter"></i></a>
          <a href="#" class="raleway"><i class="fab fa-facebook"></i></a>
          <a href="#" class="raleway"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      <div class="images">
        <img src="./images/item_35.jpg" alt="">
        <img src="./images/istockphoto-806863994-612x612.jpg" alt="">
        <img src="./images/istockphoto-913987750-612x612.jpg" alt="">
      </div>
    </div>
    <footer class="footer">
        <div class="container">
            <div class="logo">
                <img src="./images/Ceylon.png" alt="Logo">
            </div>
            <div class="links">
                <h3 class="josefin-sans">Recipes</h3>
                <ul>
                    <li><a href="#" class="raleway">Explore Recipes</a></li>
                    <li><a href="#" class="raleway">Submit Your Recipe</a></li>
                    <li><a href="#" class="raleway">Top Rated Dishes</a></li>
                </ul>
            </div>
            <div class="resources">
                <h3 class="josefin-sans">Kitchen Tips</h3>
                <ul>
                    <li><a href="#" class="raleway">Cooking Techniques</a></li>
                    <li><a href="#" class="raleway">Spice Guide</a></li>
                    <li><a href="#" class="raleway">Food Pairing TIps</a></li>
                </ul>
            </div>
            <div class="company">
                <h3 class="josefin-sans">About Ceylon Cuisine</h3>
                <ul>
                    <li><a href="#" class="raleway">Our Story</a></li>
                    <li><a href="#" class="raleway">Contact Us</a></li>
                    <li><a href="#" class="raleway">Privacy Policy</a></li>
                    <li><a href="#" class="raleway">Terms of Conditions</a></li>
                </ul>
            </div>
            <div class="social">
                <h3  class="josefin-sans">Follow us</h3>
                <ul>
                    <li><a href="#" class="raleway"><i class="fab fa-twitter"></i></a></li>
                    <li><a href="#" class="raleway"><i class="fab fa-facebook"></i></a></li>
                    <li><a href="#" class="raleway"><i class="fab fa-instagram"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2024 Ceylon Cuisine. All rights reserved.</p>
        </div>
    </footer>
  </body>
</html>