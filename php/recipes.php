<?php
include 'dbconn.php';
session_start();

// Pagination setup
$recipesPerPage = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $recipesPerPage;

// Fetch all recipes for the listing
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$recipes = [];

// Check if status column exists
$statusColumnExists = false;
$stmt = $conn->prepare("SHOW COLUMNS FROM recipes LIKE 'status'");
$stmt->execute();
$result = $stmt->get_result();
$statusColumnExists = $result->num_rows > 0;
$stmt->close();

// Base query for counting total recipes
$countQuery = "SELECT COUNT(*) as total FROM recipes r";

// Base query for fetching recipes
$query = "SELECT r.id, r.title, r.description, r.image_url, COALESCE(r.average_rating, 0) AS average_rating";
if (isset($_SESSION['user_id'])) {
    $query .= ", (SELECT COUNT(*) FROM favorites f WHERE f.recipe_id = r.id AND f.user_id = ?) AS is_favorite";
}
$query .= " FROM recipes r";

// Add status condition to only show approved recipes
if ($statusColumnExists) {
    $whereClause = " WHERE status = 'approved'";
    $countQuery .= $whereClause;
    $query .= $whereClause;
    
    if (!empty($search)) {
        $searchCondition = " AND title LIKE ?";
        $countQuery .= $searchCondition;
        $query .= $searchCondition;
        $searchTerm = "%$search%";
    }
} else {
    if (!empty($search)) {
        $searchCondition = " WHERE title LIKE ?";
        $countQuery .= $searchCondition;
        $query .= $searchCondition;
        $searchTerm = "%$search%";
    }
}

// Get total count of recipes
$stmt_count = $conn->prepare($countQuery);
if (!empty($search)) {
    $stmt_count->bind_param("s", $searchTerm);
}
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$totalRecipes = $result_count->fetch_assoc()['total'];
$totalPages = ceil($totalRecipes / $recipesPerPage);
$stmt_count->close();

// Add sorting
if ($sort === 'newest') {
    $query .= " ORDER BY created_at DESC";
} elseif ($sort === 'oldest') {
    $query .= " ORDER BY created_at ASC";
}

// Add pagination limit
$query .= " LIMIT ? OFFSET ?";

// Prepare and execute the query
$stmt_all = $conn->prepare($query);
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    if (!empty($search)) {
        $stmt_all->bind_param("isii", $user_id, $searchTerm, $recipesPerPage, $offset);
    } else {
        $stmt_all->bind_param("iii", $user_id, $recipesPerPage, $offset);
    }
} else {
    if (!empty($search)) {
        $stmt_all->bind_param("sii", $searchTerm, $recipesPerPage, $offset);
    } else {
        $stmt_all->bind_param("ii", $recipesPerPage, $offset);
    }
}
$stmt_all->execute();
$result_all = $stmt_all->get_result();
$recipes = $result_all->fetch_all(MYSQLI_ASSOC);

// Fetch single recipe for modal display
$recipe_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$ingredients = [];
$instructions = [];
$recipe = null;

if ($recipe_id) {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Prepare SQL query with status check for approved recipes
    if ($statusColumnExists) {
        $stmt = $conn->prepare("SELECT 
            r.id,
            r.title,
            r.description,
            r.image_url,
            r.ingredients,
            r.instructions,
            COALESCE(r.average_rating, 0) AS average_rating,
            ur.rating AS user_rating
        FROM recipes r
        LEFT JOIN ratings ur ON ur.recipe_id = r.id AND ur.user_id = ?
        WHERE r.id = ? AND r.status = 'approved'");
    } else {
        $stmt = $conn->prepare("SELECT 
            r.id,
            r.title,
            r.description,
            r.image_url,
            r.ingredients,
            r.instructions,
            COALESCE(r.average_rating, 0) AS average_rating,
            ur.rating AS user_rating
        FROM recipes r
        LEFT JOIN ratings ur ON ur.recipe_id = r.id AND ur.user_id = ?
        WHERE r.id = ?");
    }
    
    // Bind parameters based on login status
    if ($user_id) {
        $stmt->bind_param("ii", $user_id, $recipe_id);
    } else {
        $dummy_user_id = 0;
        $stmt->bind_param("ii", $dummy_user_id, $recipe_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $recipe = $result->fetch_assoc();

    if ($recipe) {
        // Handle JSON decoding
        $ingredients = json_decode($recipe['ingredients'] ?? '[]', true);
        $instructions = json_decode($recipe['instructions'] ?? '[]', true);
        
        // Ensure rating fields exist
        $recipe['average_rating'] = (float)($recipe['average_rating'] ?? 0);
        $recipe['user_rating'] = (int)($recipe['user_rating'] ?? 0);
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
    <script src="../js/recipes.js"></script>
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
            <form action="" method="get" class="search-form">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                <div class="search-collection">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search for recipes">
                </div>
                <button type="submit" name="submit" class="search-btn raleway">Search</button>
            </form>
            <form action="" method="get" class="sort-form">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                <input type="hidden" name="page" value="1">
                <div class="sort-by merriweather-light">
                    <label for="sort">Sort by:</label>
                    <select name="sort" id="sort" onchange="this.form.submit()">
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                        <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest</option>
                    </select>
                </div>
            </form> 
        </div>
    </main>
    <section id="myRecipe" class="content-section">
        <div class="card-container">
            <?php if (empty($recipes)): ?>
                <p class="raleway">No recipes found.</p>
            <?php else: ?>
                <div class="recipe-grid">
                    <?php foreach ($recipes as $myrecipe) : ?>
                        <div class="card" data-recipe="<?= $myrecipe['id'] ?>">
                            <div class="image-box">
                                <img src="../uploads/<?= htmlspecialchars($myrecipe['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($myrecipe['title']) ?>">
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <div class="favorite-heart <?= isset($myrecipe['is_favorite']) && $myrecipe['is_favorite'] > 0 ? 'favorite-active' : '' ?>" 
                                         data-recipe-id="<?= $myrecipe['id'] ?>" 
                                         title="<?= isset($myrecipe['is_favorite']) && $myrecipe['is_favorite'] > 0 ? 'Remove from favorites' : 'Add to favorites' ?>">
                                        <i class="<?= isset($myrecipe['is_favorite']) && $myrecipe['is_favorite'] > 0 ? 'fas' : 'far' ?> fa-heart"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="title">
                                <h2 class="playfair-display"><?= htmlspecialchars($myrecipe['title']) ?></h2>
                            </div>
                            <div class="description">
                                <p class="merriweather-regular"><?= htmlspecialchars($myrecipe['description']) ?></p>
                            </div>
                            <div class="rating-section">
                                <div class="average-rating merriweather-regular">
                                    <div class="stars">
                                        <?php
                                        $average = (float)($myrecipe['average_rating'] ?? 0);
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
                            <a href="recipes.php?id=<?= htmlspecialchars($myrecipe['id']) ?>">
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
                        // Show first page if we're not close to the beginning
                        if ($page > 3) {
                          echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '" class="page-number raleway">1</a>';
                          if ($page > 4) {
                            echo '<span class="pagination-dots">...</span>';
                          }
                        }

                        // Show pages around current page
                        for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++) {
                          if ($i == $page) {
                            echo '<span class="page-number current raleway">' . $i . '</span>';
                          } else {
                            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '" class="page-number raleway">' . $i . '</a>';
                          }
                        }

                        // Show last page if we're not close to the end
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
                    Showing <?= (($page - 1) * $recipesPerPage) + 1 ?> to <?= min($page * $recipesPerPage, $totalRecipes) ?> of <?= $totalRecipes ?> recipes
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

    <!-- Recipe Modal -->
    <?php if ($recipe): ?>
        <div id="recipeModal" class="modal" data-recipe-id="<?= $recipe_id ?>">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 class="playfair-display"><?= htmlspecialchars($recipe['title']) ?></h2>
                <img src="../uploads/<?= htmlspecialchars($recipe['image_url']) ?>" 
                     alt="<?= htmlspecialchars($recipe['title']) ?>" 
                     class="recipe-image">
                <p><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>

                <div class="average-rating-section">
                    <h3>Overall Rating</h3>
                    <div class="stars">
                        <?php
                        $average = (float)$recipe['average_rating'];
                        $fullStars = floor($average);
                        $hasHalfStar = ($average - $fullStars) >= 0.5;
                        
                        for ($i = 1; $i <= 5; $i++):
                            if ($i <= $fullStars):
                                echo '<i class="fas fa-star rated"></i>';
                            elseif ($hasHalfStar && $i == $fullStars + 1):
                                echo '<i class="fas fa-star-half-alt rated"></i>';
                                $hasHalfStar = false;
                            else:
                                echo '<i class="far fa-star"></i>';
                            endif;
                        endfor;
                        ?>
                        <span class="rating-value">(<?= number_format($average, 1) ?>)</span>
                    </div>
                </div>

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
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="user-rating-section">
                        <h3>Your Rating:</h3>
                        <div class="star-rating interactive-stars" data-recipe-id="<?= $recipe_id ?>">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="far fa-star <?= $i <= (int)($recipe['user_rating'] ?? 0) ? 'rated' : '' ?>" 
                                  data-rating="<?= $i ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>