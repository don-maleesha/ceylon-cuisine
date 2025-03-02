<?php
  include 'dbconn.php';
  session_start();

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
  <link rel="stylesheet" href="../css/recipes.css">
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
  <main>
    <h1 class="explore-title playfair-display">Explore<span> collection</span></h1>
    <div class="filter-bar">
      <div class="categories merriweather-light">
        <a href="#">All</a>
        <a href="#">Breakfast</a>
        <a href="#">Lunch</a>
        <a href="#">Dinner</a>
        <a href="#">Main Dishes</a>
        <a href="#">Snacks</a>
        <a href="#">Desserts</a>
        <a href="#">Sweets</a>
        <a href="#">Drinks</a>
      </div>
    </div>
    <div class="search-sort">
      <form action="" method="get">
        <div class="search-collection">
          <i class="fas fa-search"></i>
          <input type="text" name="search" placeholder="Search for recipes" class="merriweather-light">
        </div>
        <button type="submit" name="submit" class="search-btn raleway">Search</button>
      </form>
      <div class="sort-by merriweather-light">
        <label for="sort">Sort by:</label>
        <select name="sort" id="sort">
          <option value="newest" class="merriweather-light">Newest</option>
          <option value="oldest" class="merriweather-light">Oldest</option>
        </select>
      </div>
    </div>
  </main>
  <?php

  $output = '';

  function displayRecipes($result){
    // foreach($result as $row){
    //   echo $row['title'];
    //   echo $row['description'];
    //   echo $row['image_url'];
    // }
    //     if(!$result) {
    //       die('Error: ' . mysqli_error($conn));
    // }
    $output = '';

    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_array($result)){
          $name = htmlspecialchars($row['title']);
          $description = htmlspecialchars($row['description']);
          $file_name = htmlspecialchars($row['image_url']);
  
          $output .= '
            <div class="card">
                      <div class="image-box">
                          <img src="../uploads/' . $file_name . '" alt="' . $name . '" class="img-fluid">
                      </div>
                      <div class="title">
                          <h2 class="playfair-display">' . $name . '</h2>
                      </div>
                      <div class="description">
                          <p class="merriweather-regular">' . $description . '</p>
                      </div>
                      <button>View Recipe</button>
                  </div>';
        }
      } else {
          $output = '<h2 class="no-recipe">No recipes found</h2>';
    }

    return $output;
  }

  if(isset($_GET['submit'])){
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $stmt = $conn->prepare("SELECT * FROM recipes WHERE title LIKE ? OR description LIKE ?");
    $likeSearch = '%' . $search . '%';
    $stmt->bind_param('ss', $likeSearch, $likeSearch);
    $stmt->execute();

    $result = $stmt->get_result();
    $output = displayRecipes($result);
    $stmt->close();

  } else {
    $sql = "SELECT * FROM recipes";
    $result = mysqli_query($conn, $sql);

    if(!$result) {
      die('Error: ' . mysqli_error($conn));
    }

    $output = displayRecipes($result);
  }
    
  ?>
      <?php echo $output; ?>
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
