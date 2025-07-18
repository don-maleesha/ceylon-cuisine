<?php
include 'dbconn.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Pagination setup
$recipesPerPage = 6; // Number of recipes per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $recipesPerPage;

// Fetch user's favorite recipes
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest'; // Default to 'newest'

// Base query for counting total favorite recipes
$countQuery = "SELECT COUNT(*) as total FROM favorites f 
               INNER JOIN recipes r ON f.recipe_id = r.id 
               WHERE f.user_id = ?";

// Base query for fetching favorite recipes
$query = "SELECT r.id, r.title, r.description, r.image_url, COALESCE(r.average_rating, 0) AS average_rating 
          FROM favorites f 
          INNER JOIN recipes r ON f.recipe_id = r.id 
          WHERE f.user_id = ?";

// Add search condition
if (!empty($search)) {
    $searchCondition = " AND r.title LIKE ?";
    $countQuery .= $searchCondition;
    $query .= $searchCondition;
    $searchTerm = "%$search%";
}

// Get total count of favorite recipes
$stmt_count = $conn->prepare($countQuery);
if (!empty($search)) {
    $stmt_count->bind_param("is", $user_id, $searchTerm);
} else {
    $stmt_count->bind_param("i", $user_id);
}
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$totalRecipes = $result_count->fetch_assoc()['total'];
$totalPages = ceil($totalRecipes / $recipesPerPage);
$stmt_count->close();

// Add sorting
if ($sort === 'newest') {
    $query .= " ORDER BY f.created_at DESC";
} elseif ($sort === 'oldest') {
    $query .= " ORDER BY f.created_at ASC";
} elseif ($sort === 'rating') {
    $query .= " ORDER BY r.average_rating DESC";
}

// Add pagination limit
$query .= " LIMIT ? OFFSET ?";

// Prepare and execute the query
$stmt_all = $conn->prepare($query);
if (!empty($search)) {
    $stmt_all->bind_param("isii", $user_id, $searchTerm, $recipesPerPage, $offset);
} else {
    $stmt_all->bind_param("iii", $user_id, $recipesPerPage, $offset);
}
$stmt_all->execute();
$result_all = $stmt_all->get_result();
$favoriteRecipes = $result_all->fetch_all(MYSQLI_ASSOC);
$stmt_all->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Favorites - Ceylon Cuisine</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Satisfy&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/recipes.css">
  <script src="../js/ceylon-cuisine.js"></script>
  <script src="../js/favorites.js"></script>
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
                <li><a class="dropdown-item raleway" href="favorites.php"><i class="fas fa-heart"></i>  My Favorites</a></li>
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
    <h1 class="explore-title playfair-display">My <span>Favorite Recipes</span></h1>
    <div class="search-sort">
      <form action="" method="get" class="search-form">
        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
        <div class="search-collection">
          <i class="fas fa-search"></i>
          <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search your favorites">
        </div>
        <button type="submit" name="submit" class="search-btn raleway">Search</button>
      </form>
      <form action="" method="get" class="sort-form">
        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
        <input type="hidden" name="page" value="1">
        <div class="sort-by merriweather-light">
          <label for="sort">Sort by:</label>
          <select name="sort" id="sort" onchange="this.form.submit()">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Recently Added</option>
            <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>First Added</option>
            <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Highest Rating</option>
          </select>
        </div>
      </form> 
    </div>
  </main>
  
  <section id="myFavorites" class="content-section">
    <div class="card-container">
      <?php if (empty($favoriteRecipes)): ?>
        <div class="no-favorites">
          <i class="fas fa-heart-broken" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
          <p class="raleway" style="font-size: 1.2rem; color: #666;">You haven't added any recipes to your favorites yet.</p>
          <a href="recipes.php" class="raleway" style="color: #e74c3c; text-decoration: none; font-weight: 500;">
            <i class="fas fa-arrow-right"></i> Explore Recipes
          </a>
        </div>
      <?php else: ?>
        <div class="recipe-grid">
          <?php foreach ($favoriteRecipes as $recipe) : ?>
            <div class="card" data-recipe="<?= $recipe['id'] ?>">
              <div class="image-box">
                <img src="../uploads/<?= htmlspecialchars($recipe['image_url']) ?>" 
                     alt="<?= htmlspecialchars($recipe['title']) ?>">
                <div class="favorite-heart favorite-active" data-recipe-id="<?= $recipe['id'] ?>" title="Remove from favorites">
                  <i class="fas fa-heart"></i>
                </div>
              </div>
              <div class="title">
                <h2 class="playfair-display"><?= htmlspecialchars($recipe['title']) ?></h2>
              </div>
              <div class="description">
                <p class="merriweather-regular"><?= htmlspecialchars($recipe['description']) ?></p>
              </div>
              <div class="rating-section">
                <div class="average-rating merriweather-regular">
                  <div class="stars">
                    <?php
                    $average = (float)($recipe['average_rating'] ?? 0);
                    $fullStars = floor($average);
                    $hasHalfStar = ($average - $fullStars) >= 0.5;
                    
                    for ($i = 1; $i <= 5; $i++):
                        if ($i <= $fullStars):
                    ?>
                        <i class="fas fa-star rated"></i>
                    <?php elseif ($hasHalfStar && $i == $fullStars + 1): ?>
                        <i class="fas fa-star-half-alt rated"></i>
                    <?php else: ?>
                        <i class="far fa-star"></i>
                    <?php
                        $hasHalfStar = false;
                        endif;
                    endfor;
                    ?>
                  </div>
                  <span class="rating-value" title="<?= number_format($average, 1) ?>">
                    (<?= number_format($average, 1) ?>)
                  </span>
                </div>
              </div>
              <a href="recipes.php?id=<?= htmlspecialchars($recipe['id']) ?>">
                <button class="raleway">View Recipe</button>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    
    <!-- Pagination Controls -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination-container">
      <div class="pagination">
        <!-- Previous Page Button -->
        <?php if ($page > 1): ?>
          <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="pagination-btn prev-btn raleway">
            <i class="fas fa-chevron-left"></i> Previous
          </a>
        <?php endif; ?>

        <!-- Page Numbers -->
        <div class="page-numbers">
          <?php
          if ($page > 3) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '" class="page-number raleway">1</a>';
            if ($page > 4) {
              echo '<span class="pagination-dots">...</span>';
            }
          }

          for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++) {
            if ($i == $page) {
              echo '<span class="page-number current raleway">' . $i . '</span>';
            } else {
              echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '" class="page-number raleway">' . $i . '</a>';
            }
          }

          if ($page < $totalPages - 2) {
            if ($page < $totalPages - 3) {
              echo '<span class="pagination-dots">...</span>';
            }
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $totalPages])) . '" class="page-number raleway">' . $totalPages . '</a>';
          }
          ?>
        </div>

        <!-- Next Page Button -->
        <?php if ($page < $totalPages): ?>
          <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="pagination-btn next-btn raleway">
            Next <i class="fas fa-chevron-right"></i>
          </a>
        <?php endif; ?>
      </div>
      
      <!-- Pagination Info -->
      <div class="pagination-info raleway">
        Showing <?= (($page - 1) * $recipesPerPage) + 1 ?> to <?= min($page * $recipesPerPage, $totalRecipes) ?> of <?= $totalRecipes ?> favorite recipes
      </div>
    </div>
    <?php endif; ?>
  </section>

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
