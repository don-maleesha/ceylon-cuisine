<?php
session_start();
include 'dbconn.php';

// Fetch user's favorite recipes if logged in
$favoriteRecipes = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Fetch user's favorite recipes (limited to 6 for homepage display)
    $query = "SELECT r.id, r.title, r.description, r.image_url, COALESCE(r.average_rating, 0) AS average_rating 
              FROM favorites f 
              INNER JOIN recipes r ON f.recipe_id = r.id 
              WHERE f.user_id = ? 
              ORDER BY f.created_at DESC 
              LIMIT 6";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $favoriteRecipes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
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
    <link rel="stylesheet" href="../css/ceylon-cuisine.css">
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
            
            <!-- Mobile Menu Toggle Button -->
            <button id="mobileMenuToggle" class="mobile-menu-toggle" aria-label="Toggle navigation menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <nav>
                <ul>
                    <li><a href="homePage.php" class="raleway">Home</a></li>
                    <li><a href="aboutus.php" class="raleway">About</a></li>
                    <li><a href="contacts.php" class="raleway">Contact</a></li>
                    <li><a href="recipes.php" class="raleway">Recipes</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if (isset($_SESSION["email_address"])): ?>
                    <div class="dropdown">
                        <a href="#" class="custom-icon">
                            <div class="user-info">
                                <i class="fas fa-user-circle" aria-hidden="true"></i>
                                <span class="username raleway"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                            </div>
                            <i id="customIcon" class="fas fa-chevron-down" aria-hidden="true"></i>
                        </a>
                        <ul id="dropdownMenu" class="dropdown-menu">
                            <li><a class="dropdown-item raleway" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a class="dropdown-item raleway" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="signin.php" class="sign-in raleway">Sign in</a>
                    <a href="signup.php" class="sign-up raleway">Sign up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <section class="hero">
        <div class="text">
            <h1 id="welcomeMessage" class="josefin-sans">Welcome to Ceylon Cuisine!</h1>
            <p class="merriweather-regular">We are home to the largest collection of traditional Sri Lankan food recipes.</p>
            <div class="buttons">
                <a href="#" class="explore-recipes raleway">Explore</a>
                <a href="#" class="submit-recipe raleway">Submit</a>
            </div>
        </div>
        <img src="../images/istockphoto-2169029066-612x612.jpg" alt="" class="src">
    </section>

    <section class="features">
        <h2 class="playfair-display">Recipe Features</h2>
        <p class="merriweather-regular">Discover the unique flavors of Sri Lanka with our carefully curated recipes, user-friendly tools, and a community that celebrates culinary creativity.</p>
        <div class="feature-list">
            <div class="feature">
                <img src="../images/istockphoto-1350375887-612x612.jpg" alt="" class="src">
                <h3 class="playfair-display">Local ingredients guide</h3>
                <p class="merriweather-regular">Learn about sourcing fresh, authentic ingredients to bring Sri Lankan flavors to your kitchen.</p>
            </div>
            <div class="feature">
                <img src="../images/images (4).jpg" alt="" class="src">
                <h3 class="playfair-display">Recipe customization based on dietary needs.</h3>
                <p class="merriweather-regular">Tailor recipes to suit your dietary needs, whether you're vegan, gluten-free, or keto.</p>
            </div>
            <div class="feature">
                <img src="../images/images (5).jpg" alt="" class="src">
                <h3 class="playfair-display">Video tutorials for traditional cooking methods.</h3>
                <p class="merriweather-regular">Master traditional cooking methods with step-by-step video demonstrations.</p>
            </div>
        </div>
    </section>

    <section class="testimonial">
        <img alt="Testimonial person" height="100" src="../images/pexels-danxavier-1102341.jpg" width="100"/>
        <div class="text">
            <p class="merriweather-regular">"Ceylon Cuisine has completely transformed the way I cook and explore new dishes! The recipes are simple to follow, and the step-by-step guides make even the most complex meals achievable. I especially love the feature that lets me rate and review recipes, as it feels like being part of a vibrant foodie community. Thank you for bringing the flavors of Sri Lanka to life!"</p>
            <div class="name">– Amara Perera, Food Enthusiast</div>
        </div>
    </section>
    <?php if (isset($_SESSION['user_id'])): ?>
    <section class="my-favorites-section">
        <div class="container">
            <h2 class="playfair-display">My Favorites</h2>
            <p class="merriweather-regular">Your personally curated collection of beloved recipes</p>
            
            <?php if (empty($favoriteRecipes)): ?>
                <div class="no-favorites-home">
                    <i class="fas fa-heart-broken"></i>
                    <p class="raleway">You haven't added any recipes to your favorites yet.</p>
                    <a href="recipes.php" class="explore-btn raleway">
                        <i class="fas fa-search"></i> Explore Recipes
                    </a>
                </div>
            <?php else: ?>
                <div class="favorites-grid">
                    <?php foreach ($favoriteRecipes as $recipe): ?>
                        <div class="favorite-card" data-recipe="<?= $recipe['id'] ?>">
                            <div class="image-box">
                                <img src="../uploads/<?= htmlspecialchars($recipe['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($recipe['title']) ?>">
                                <div class="favorite-heart favorite-active" data-recipe-id="<?= $recipe['id'] ?>" title="Remove from favorites">
                                    <i class="fas fa-heart"></i>
                                </div>
                            </div>
                            <div class="content">
                                <h3 class="playfair-display"><?= htmlspecialchars($recipe['title']) ?></h3>
                                <p class="merriweather-regular"><?= htmlspecialchars(substr($recipe['description'], 0, 100)) ?>...</p>
                                <div class="rating">
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
                                    <span class="rating-value">(<?= number_format($average, 1) ?>)</span>
                                </div>
                                <a href="recipes.php?id=<?= htmlspecialchars($recipe['id']) ?>" class="view-recipe-btn raleway">
                                    View Recipe
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($favoriteRecipes) >= 6): ?>
                    <div class="view-all-favorites">
                        <a href="my_favorites.php" class="view-all-btn raleway">
                            <i class="fas fa-heart"></i> View All My Favorites
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>
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
                <h3 class="josefin-sans">Follow us</h3>
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