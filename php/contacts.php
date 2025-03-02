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
      <link rel="stylesheet" href="../css/contacts.css">
      <script src="../js/ceylon-cuisine.js"></script>
  </head>
  <body>
    <header>
      <div class="container">
        <div class="logo">
          <img src="../images/Ceylon.png" alt="Logo">
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
    <div class="contacts-container">
      <div class="contact-info">
        <h1 class="playfair-display">Contact Us</h1>
        <p class="merriweather-light"><i class="fas fa-map-marker-alt"></i>  47, Yapa Patumanga, Walala, Sri Lanka.</p>
        <p class="merriweather-light"><i class="fas fa-phone"></i>  (+94) 81 490 0439</p>
        <p class="merriweather-light"><i class="fas fa-envelope"></i>  info@ceyloncuisine.com</p>
        <img alt="Office interior with a conference table and chairs" src="../images/lankafood3.webp">
      </div>
      <div class="contact-form">
        <input placeholder="Your name" type="text"/>
        <input placeholder="Your Email" type="email"/>
        <input placeholder="Your phone number" type="text"/>
        <textarea placeholder="Your message" rows="5"></textarea>
        <button class="raleway">Send message</button>
      </div>
    </div>
    <img alt="Cityscape view from an office window with people silhouettes" class="footer-image" src="../images/img86980.whqc_1426x713q80.jpg"/>
    <footer class="footer">
        <div class="container">
            <div class="logo">
                <img src="../images/Ceylon.png" alt="Logo">
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
                <h3  class="josefin-sans">Follow Us</h3>
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