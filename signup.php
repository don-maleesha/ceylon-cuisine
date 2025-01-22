<?php
include 'dbconn.php';

if (isset($_GET['submit'])) {
  $name = isset($_GET['name']) ? $_GET['name'] : '';
  $email = isset($_GET['email_address']) ? $_GET['email_address'] : '';
  $password = isset($_GET['password']) ? $_GET['password'] : '';
  $confirm_password = isset($_GET['confirm_password']) ? $_GET['confirm_password'] : '';

  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  $errors = array();

  if(empty($name) || empty($email) || empty($password)){
    array_push($errors, "All fields are required");
  }

  if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    array_push($errors, "Invalid email address");
  }

  if(strlen($password) < 8){
    array_push($errors, "Password must be at least 8 characters long");
  }

  if($password !== $confirm_password){
    array_push($errors, "Passwords do not match");
  }

  $sql = "SELECT * FROM users WHERE email_address = '$email'";
  $result = mysqli_query($conn, $sql);
  $row_count = mysqli_num_rows($result);

  if($row_count > 0){
    array_push($errors, "Email address already exists");
  }

  if(count($errors) > 0){

      foreach($errors as $error){
  
        echo "<div class='error'>$error</div>";
  
      }
  
    } else {

      $sql = "INSERT INTO users (name, email_address, password) VALUES (?, ?, ?)";
      $statement = mysqli_stmt_init($conn);
      $prepare_statement = mysqli_stmt_prepare($statement, $sql);

      if($prepare_statement){
        mysqli_stmt_bind_param($statement, "sss", $name, $email, $hashed_password);

        mysqli_stmt_execute($statement);

        header("Location: signin.php?message=Account created successfully. Please log in.");
        exit();

      } else {

        header("Location: signup.php?error=Error inserting data: " . $stmt->error);
        exit();
    }
  }

  $sql = "INSERT INTO users (name, email_address, password) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sss", $name, $email, $hashed_password);

  if ($stmt->execute()) {
    // Redirect to login page after successful sign-up
    header("Location: signin.php?message=Account created successfully. Please log in.");
    exit();
  } else {
    header("Location: signup.php?error=Error inserting data: " . $stmt->error);
    exit();
  }

  $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ceylon Cuisine</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
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
        <span><p>Already have an account?</p><a href="signin.php">Sign in</a></span>
      </div>
      <div class="form-container">
        <h2>Create an account</h2>
        <form action="" method="GET">
          <input type="text" name="name" placeholder="Enter your name" aria-label="Full name" required>
          <input type="email" name="email_address" placeholder="Enter your email" aria-label="Email" required>
          <input type="password" name="password" placeholder="Enter your password" aria-label="Password" required>
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
      <img src="./images/istockphoto-1603613324-612x612.jpg" alt="">
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
