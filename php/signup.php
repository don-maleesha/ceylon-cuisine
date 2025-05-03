<?php
session_start();
include 'dbconn.php';

$_SESSION['errors'] = [];

if (isset($_POST['submit'])) {
    // UNSANITIZED VERSION - RISKY
    $name = $_POST['name'];
    $email = $_POST['email_address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        array_push($_SESSION['errors'], "All fields are required");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($_SESSION['errors'], "Invalid email address");
    }

    if (strlen($password) < 8) {
        array_push($_SESSION['errors'], "Password must be at least 8 characters long");
    }

    if ($password !== $confirm_password) {
        array_push($_SESSION['errors'], "Passwords do not match");
    }

    // Check email existence
    if (empty($_SESSION['errors'])) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email_address = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            array_push($_SESSION['errors'], "Email address already exists");
        }
        $stmt->close();
    }

    // Registration
    if (empty($_SESSION['errors'])) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (name, email_address, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            header("Location: signin.php");
            exit();
        } else {
            array_push($_SESSION['errors'], "Registration failed. Please try again.");
        }
        $stmt->close();
    }

    header("Location: signin.php");
    exit();
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Cuisine</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/signup.css">
  <!-- <script src="../js/signup.js"></script> -->
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
        <a href="signin.php" class="sign-in raleway">Sign in</a>
        <a href="signup.php" class="sign-up raleway">Sign up</a>
      </div>
    </div>
  </header>
  <div class="main-container">
    <div class="left">
      <div class="logo">
        <div class="logo-container">
          <img src="../images/Ceylon.png" alt="Logo">
          <span class="company-name josefin-sans">Ceylon Cuisine</span>
        </div>
        <span><p>Already have an account?</p><a href="signin.php">Sign in</a></span>
      </div>
      <div class="form-container">
        <h2>Create an account</h2>
        <!-- Message container for JavaScript -->
        <div id="messageContainer"></div>
        <!-- PHP error messages -->
        <?php
          if (isset($_SESSION['errors'])) {
            echo '<div class="error-container">';
            foreach ($_SESSION['errors'] as $error) {
            echo "<div class='error'>$error</div>";
            }
            echo '</div>';
            unset($_SESSION['errors']); // Clear errors after displaying
          }
        ?>
        <form id="signupForm" action="signup.php" method="POST"  onsubmit="validateForm(event)">
          <input type="text" name="name" placeholder="Enter your name" aria-label="Full name" required>
          <input type="email" name="email_address" placeholder="Enter your email" aria-label="Email" required>
          <input type="password" name="password" placeholder="Enter your password" aria-label="Password" required  minlength="8">
          <input type="password" name="confirm_password" placeholder="Confirm your password" aria-label="Confirm password" required>
          <button type="submit" name="submit">Sign up</button>
        </form>
        <p class="terms">
          By signing up, you agree to our
          <a href="https://google.com">Terms and Conditions</a> & 
          <a href="#">Privacy Policy</a>.
        </p>
      </div>
    </div>
    <div class="right">
      <img src="../images/istockphoto-1603613324-612x612.jpg" alt="">
    </div>
  </div>

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
