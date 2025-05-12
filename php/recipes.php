<?php
include 'dbconn.php';
session_start();

// Fetch all recipes for the listing
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest'; // Default to 'newest'
$recipes = [];

// Base query
$query = "SELECT id, title, description, image_url FROM recipes";

// Add search condition
if (!empty($search)) {
    $query .= " WHERE title LIKE ?";
    $searchTerm = "%$search%";
}

// Add sorting
if ($sort === 'newest') {
    $query .= " ORDER BY created_at DESC";
} elseif ($sort === 'oldest') {
    $query .= " ORDER BY created_at ASC";
}

// Prepare and execute the query
$stmt_all = $conn->prepare($query);
if (!empty($search)) {
    $stmt_all->bind_param("s", $searchTerm);
}
$stmt_all->execute();
$result_all = $stmt_all->get_result();
$recipes = $result_all->fetch_all(MYSQLI_ASSOC);

// Existing code for fetching a single recipe (for modal)
$recipe_id = $_GET['id'] ?? null;
$ingredients = [];
$instructions = [];
$recipe = null;

if ($recipe_id) {
    $stmt = $conn->prepare("SELECT title, description, image_url, ingredients, instructions FROM recipes WHERE id = ?");
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipe = $result->fetch_assoc();

    if ($recipe) {
        $ingredients = json_decode($recipe['ingredients'], true);
        $instructions = json_decode($recipe['instructions'], true);
    }
}
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
          <input type="text" name="search" placeholder="Search for recipes">
        </div>
        <button type="submit" name="submit" class="search-btn raleway">Search</button>
      </form>
      <div class="sort-by merriweather-light">
        <label for="sort">Sort by:</label>
        <select name="sort" id="sort" onchange="this.form.submit()">
          <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
          <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest</option>
        </select>
      </div>
    </div>
  </main>
  <section id="myRecipe" class="content-section">
        <div class="card-container">
            <div class="recipe-list">
                <?php if (empty($recipes)): ?>
                    <p class="raleway">No recipes found.</p>
                <?php else: ?>
                    <?php foreach ($recipes as $myrecipe) : ?>
                        <div class="card">
                            <div class="image-box">
                                <img src="../uploads/<?= htmlspecialchars($myrecipe['image_url']) ?>" 
                                    alt="<?= htmlspecialchars($recipe['title']) ?>">
                            </div>
                            <div class="title">
                                <h2 class="playfair-display"><?= htmlspecialchars($myrecipe['title']) ?></h2>
                            </div>
                            <div class="description">
                                <p class="merriweather-regular"><?= htmlspecialchars($myrecipe['description']) ?></p>
                            </div>
                            <!-- In your recipe cards -->
                            <a href="recipes.php?id=<?= htmlspecialchars($myrecipe['id']) ?>">
                                <button class="raleway">View Recipe</button>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section></li>
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

  <?php if ($recipe): ?>
<div id="recipeModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 class="playfair-display"><?= htmlspecialchars($recipe['title']) ?></h2>
        <img src="../uploads/<?= htmlspecialchars($recipe['image_url']) ?>" 
             alt="<?= htmlspecialchars($recipe['title']) ?>" 
             class="recipe-image">
        <p><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>

        <div class="columns">
            <div class="ingredients-column">
                <h3>🍴 Ingredients</h3>
                <ul class="ingredient-list">
                    <?php foreach ($ingredients as $ingredient): ?>
                        <?php if (trim($ingredient) !== ''): ?>
                            <li class="ingredient-item"><?= htmlspecialchars(trim($ingredient)) ?></li>
                        <?php endif; ?>
                     <?php endforeach; ?>
                </ul>
            </div>
            <div class="instructions-column">
                <h3>📝 Instructions</h3>
                <ol class="instruction-list">
                    <?php foreach ($instructions as $index => $instruction): ?>
                        <?php if (trim($instruction) !== ''): ?>
                            <li class="instruction-step">
                                <div class="step-content">
                                    <?= htmlspecialchars(trim($instruction)) ?>
                                </div>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
</body>
</html>
