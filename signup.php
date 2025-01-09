<?php
  include 'dbconn.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Cuisine</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Satisfy&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./signup.css">
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
        <a href="signin.php" class="sign-in raleway">Sign in</a>
        <a href="signup.php" class="sign-up raleway">Sign up</a>
      </div>
    </div>
  </header>
  <div class="main-container">
    <div class="left">
      <div class="logo">
        <div class="logo-container">
          <img src="./images/Ceylon.png" alt="Logo">
          <span class="company-name josefin-sans">Ceylon Cuisine</span>
        </div>
        <span><p>Already have an account?</p><a href="#">Sign in</a></span>
      </div>
      <div class="form-container">
        <h2>Create an account</h2>
        <input type="text" placeholder="Enter your name" aria-label="Full name">
        <input type="email" placeholder="Enter your email" aria-label="Email">
        <input type="password" placeholder="Enter your password" aria-label="Password">
        <input type="password" placeholder="Confirm your password" aria-label="Confirm password">
        <button type="submit">Sign up</button>
        <p class="terms">
          By signing up, you agree to our
          <a href="https://google.com">Terms of Conditions</a> & 
          <a href="#">Privacy Policy</a>.
        </p>
      </div>
    </div>
    <div class="right">
      <img src="./images/istockphoto-1603613324-612x612.jpg" alt="">
      <!--
      <div class="caption">
        <h3>Where Every Dish Tells a Story</h3>
        <p>Join our community of food lovers and discover new recipes, cooking tips, and more.</p>
      </div>
      -->
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
          <li><a href="#" class="raleway">Food Pairing Tips</a></li>
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
        <h3 class="josefin-sans">Follow Us</h3>
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
