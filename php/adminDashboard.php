<?php
session_start();
require_once "dbconn.php";

// Check if user is logged in and is admin
if (!isset($_SESSION['email_address']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit();
}

// Get Dashboard statistics
// Total Recipes
$stmt = $conn->prepare("SELECT COUNT(*) FROM recipes");
$stmt->execute();
$stmt->bind_result($totalRecipes);
$stmt->fetch();
$stmt->close();

// Total Users
$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE role = 'user' " );
$stmt->execute();
$stmt->bind_result($totalUsers);
$stmt->fetch();
$stmt->close();

// Total Reviews
$stmt = $conn->prepare("SELECT COUNT(*) FROM ratings");
$stmt->execute();
$stmt->bind_result($totalReviews);
$stmt->fetch();
$stmt->close();

// Check if status column exists in recipes table
$stmt = $conn->prepare("SHOW COLUMNS FROM recipes LIKE 'status'");
$stmt->execute();
$result = $stmt->get_result();
$statusColumnExists = $result->num_rows > 0;
$stmt->close();

// Pending Recipes
if ($statusColumnExists) {
    $status = 'pending';
    $stmt = $conn->prepare("SELECT COUNT(*) FROM recipes WHERE status = ?");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $stmt->bind_result($pendingRecipes);
    $stmt->fetch();
    $stmt->close();
} else {
    // If status column doesn't exist, add it and set all recipes to pending by default
    $stmt = $conn->prepare("ALTER TABLE recipes ADD COLUMN status VARCHAR(20) DEFAULT 'pending'");
    $stmt->execute();
    $stmt->close();
    
    // Now count pending recipes
    $status = 'pending';
    $stmt = $conn->prepare("SELECT COUNT(*) FROM recipes WHERE status = ?");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $stmt->bind_result($pendingRecipes);
    $stmt->fetch();
    $stmt->close();
}

// Recent Activity (last 5 recipes)
$stmt = $conn->prepare("SELECT r.id, r.title, u.name, r.created_at FROM recipes r 
                        JOIN users u ON r.user_id = u.id 
                        ORDER BY r.created_at DESC LIMIT 5");
$stmt->execute();
$result = $stmt->get_result();
$recentActivity = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle tab navigation
$activeTab = $_GET['tab'] ?? 'dashboard';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {    // Recipe approval
    if (isset($_POST['approve_recipe'])) {
        $recipeId = $_POST['recipe_id'];
        $status = 'approved';
        
        // Check if status column exists
        $stmt = $conn->prepare("SHOW COLUMNS FROM recipes LIKE 'status'");
        $stmt->execute();
        $result = $stmt->get_result();
        $statusColumnExists = $result->num_rows > 0;
        $stmt->close();
        
        if ($statusColumnExists) {
            $stmt = $conn->prepare("UPDATE recipes SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $recipeId);
            $stmt->execute();
            $stmt->close();
        } else {
            // If status column doesn't exist, create it first
            $stmt = $conn->prepare("ALTER TABLE recipes ADD COLUMN status VARCHAR(20) DEFAULT 'pending'");
            $stmt->execute();
            $stmt->close();
            
            // Now update the status
            $stmt = $conn->prepare("UPDATE recipes SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $recipeId);
            $stmt->execute();
            $stmt->close();
        }
        
        header("Location: adminDashboard.php?tab=recipes");
        exit();
    }
    
    // Recipe rejection
    if (isset($_POST['reject_recipe'])) {
        $recipeId = $_POST['recipe_id'];
        $status = 'rejected';
        
        // Check if status column exists
        $stmt = $conn->prepare("SHOW COLUMNS FROM recipes LIKE 'status'");
        $stmt->execute();
        $result = $stmt->get_result();
        $statusColumnExists = $result->num_rows > 0;
        $stmt->close();
        
        if ($statusColumnExists) {
            $stmt = $conn->prepare("UPDATE recipes SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $recipeId);
            $stmt->execute();
            $stmt->close();
        } else {
            // If status column doesn't exist, create it first
            $stmt = $conn->prepare("ALTER TABLE recipes ADD COLUMN status VARCHAR(20) DEFAULT 'pending'");
            $stmt->execute();
            $stmt->close();
            
            // Now update the status
            $stmt = $conn->prepare("UPDATE recipes SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $recipeId);
            $stmt->execute();
            $stmt->close();
        }
        
        header("Location: adminDashboard.php?tab=recipes");
        exit();
    }
    
    // Feature recipe
    if (isset($_POST['feature_recipe'])) {
        $recipeId = $_POST['recipe_id'];
        $featured = 1;
        
        $stmt = $conn->prepare("UPDATE recipes SET featured = ? WHERE id = ?");
        $stmt->bind_param("ii", $featured, $recipeId);
        $stmt->execute();
        $stmt->close();
        
        header("Location: adminDashboard.php?tab=recipes");
        exit();
    }
    
    // User role update
    if (isset($_POST['update_role'])) {
        $userId = $_POST['user_id'];
        $role = $_POST['role'];
        
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $role, $userId);
        $stmt->execute();
        $stmt->close();
        
        header("Location: adminDashboard.php?tab=users");
        exit();
    }
    
    // Block/unblock user
    if (isset($_POST['toggle_status'])) {
        $userId = $_POST['user_id'];
        $status = $_POST['status'] == 'active' ? 'blocked' : 'active';
        
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $userId);
        $stmt->execute();
        $stmt->close();
        
        header("Location: adminDashboard.php?tab=users");
        exit();
    }
    
    // Delete review
    if (isset($_POST['delete_review'])) {
        $reviewId = $_POST['review_id'];
        
        $stmt = $conn->prepare("DELETE FROM ratings WHERE id = ?");
        $stmt->bind_param("i", $reviewId);
        $stmt->execute();
        $stmt->close();
        
        header("Location: adminDashboard.php?tab=reviews");
        exit();
    }
    
    // Delete recipe
    if (isset($_POST['delete_recipe'])) {
        $recipeId = $_POST['recipe_id'];
        
        // First get the recipe image to delete the file
        $stmt = $conn->prepare("SELECT image_url FROM recipes WHERE id = ?");
        $stmt->bind_param("i", $recipeId);
        $stmt->execute();
        $stmt->bind_result($imageUrl);
        $stmt->fetch();
        $stmt->close();
        
        // Check if recipe_categories table exists and delete categories
        $stmt = $conn->prepare("SHOW TABLES LIKE 'recipe_categories'");
        $stmt->execute();
        $result = $stmt->get_result();
        $recipeCategoriesExist = $result->num_rows > 0;
        $stmt->close();
        
        if ($recipeCategoriesExist) {
            $stmt = $conn->prepare("DELETE FROM recipe_categories WHERE recipe_id = ?");
            $stmt->bind_param("i", $recipeId);
            $stmt->execute();
            $stmt->close();
        }
        
        // Delete all ratings for this recipe
        $stmt = $conn->prepare("DELETE FROM ratings WHERE recipe_id = ?");
        $stmt->bind_param("i", $recipeId);
        $stmt->execute();
        $stmt->close();
        
        // Delete the recipe
        $stmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
        $stmt->bind_param("i", $recipeId);
        $stmt->execute();
        $stmt->close();
        
        // Delete the image file if it exists
        if ($imageUrl && file_exists("../uploads/" . $imageUrl)) {
            unlink("../uploads/" . $imageUrl);
        }
        
        header("Location: adminDashboard.php?tab=recipes");
        exit();
    }
      // Assign categories
    if (isset($_POST['assign_categories'])) {
        $recipeId = $_POST['recipe_id'];
        $categories = $_POST['categories'];
        
        // Check if recipe_categories table exists
        $stmt = $conn->prepare("SHOW TABLES LIKE 'recipe_categories'");
        $stmt->execute();
        $result = $stmt->get_result();
        $recipeCategoriesExist = $result->num_rows > 0;
        $stmt->close();
        
        if ($recipeCategoriesExist) {
            // First delete existing categories
            $stmt = $conn->prepare("DELETE FROM recipe_categories WHERE recipe_id = ?");
            $stmt->bind_param("i", $recipeId);
            $stmt->execute();
            $stmt->close();
            
            // Insert new categories
            foreach ($categories as $categoryId) {
                $stmt = $conn->prepare("INSERT INTO recipe_categories (recipe_id, category_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $recipeId, $categoryId);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        header("Location: adminDashboard.php?tab=submissions");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ceylon Cuisine</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Satisfy&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/adminDashboard.css">
    <script src="../js/ceylon-cuisine.js"></script>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="../images/Ceylon.png" alt="Logo">
                <span class="company-name josefin-sans">Ceylon Cuisine - Admin</span>
            </div>
            <nav>
                <ul>
                    <li><a href="homePage.php" class="raleway">View Site</a></li>
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
                            <!-- <li><a class="dropdown-item raleway" href="profile.php"><i class="fas fa-user"></i> Profile</a></li> -->
                            <li><a class="dropdown-item raleway" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <div class="admin-container">
        <div class="sidebar">
            <ul class="admin-nav">
                <li class="<?php echo $activeTab == 'dashboard' ? 'active' : ''; ?>">
                    <a href="adminDashboard.php?tab=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <li class="<?php echo $activeTab == 'recipes' ? 'active' : ''; ?>">
                    <a href="adminDashboard.php?tab=recipes"><i class="fas fa-utensils"></i> Recipe Management</a>
                </li>
                <li class="<?php echo $activeTab == 'users' ? 'active' : ''; ?>">
                    <a href="adminDashboard.php?tab=users"><i class="fas fa-users"></i> User Management</a>
                </li>
                <li class="<?php echo $activeTab == 'reviews' ? 'active' : ''; ?>">
                    <a href="adminDashboard.php?tab=reviews"><i class="fas fa-star"></i> Review Management</a>
                </li>                <li class="<?php echo $activeTab == 'submissions' ? 'active' : ''; ?>">
                    <a href="adminDashboard.php?tab=submissions"><i class="fas fa-upload"></i> Recipe Submissions</a>
                </li>
                <li class="logout-item">
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
        
        <div class="admin-content">
            <?php if($activeTab == 'dashboard'): ?>
                <!-- Dashboard Overview -->
                <h1 class="playfair-display">Dashboard Overview</h1>
                
                <div class="stat-cards">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-utensils"></i></div>
                        <div class="stat-info">
                            <h2><?php echo $totalRecipes; ?></h2>
                            <p>Total Recipes</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-info">
                            <h2><?php echo $totalUsers; ?></h2>
                            <p>Registered Users</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-star"></i></div>
                        <div class="stat-info">
                            <h2><?php echo $totalReviews; ?></h2>
                            <p>Reviews Posted</p>
                        </div>
                    </div>
                      <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        <div class="stat-info">
                            <h2><?php echo $pendingRecipes; ?></h2>
                            <p>Pending Recipes</p>
                        </div>
                        <?php if($pendingRecipes > 0): ?>
                        <div class="pending-badge">
                            <span class="status-badge status-pending"><?php echo $pendingRecipes; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="recent-activity">
                    <h2 class="playfair-display">Recent Activity</h2>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Recipe</th>
                                <th>Author</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recentActivity as $activity): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($activity['title']); ?></td>
                                    <td><?php echo htmlspecialchars($activity['name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($activity['created_at'])); ?></td>
                                    <td>
                                        <a href="recipe_detail.php?id=<?php echo $activity['id']; ?>" class="btn-view" target="_blank">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif($activeTab == 'recipes'): ?>
                <!-- Recipe Management -->
                <h1 class="playfair-display">Recipe Management</h1>
                
                <div class="admin-actions">
                    <div class="search-bar">
                        <form action="adminDashboard.php" method="GET">
                            <input type="hidden" name="tab" value="recipes">
                            <input type="text" name="search" placeholder="Search recipes...">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                    
                    <div class="filter-options">
                        <form action="adminDashboard.php" method="GET" class="filter-form">
                            <input type="hidden" name="tab" value="recipes">
                            <?php if (!empty($search)): ?>
                                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                            <?php endif; ?>
                            <select name="status" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="approved" <?php echo $statusFilter == 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="pending" <?php echo $statusFilter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="rejected" <?php echo $statusFilter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </form>
                    </div>
                </div>
                
                <?php                // Recipe listing with search and filtering
                $search = $_GET['search'] ?? '';
                $statusFilter = $_GET['status'] ?? '';
                
                $query = "SELECT r.*, u.name as author FROM recipes r 
                          JOIN users u ON r.user_id = u.id 
                          WHERE 1=1";
                          
                if (!empty($search)) {
                    $searchTerm = "%$search%";
                    $query .= " AND (r.title LIKE ? OR r.description LIKE ?)";
                }
                
                if (!empty($statusFilter) && $statusColumnExists) {
                    $query .= " AND r.status = ?";
                }
                
                $query .= " ORDER BY r.created_at DESC";
                
                $stmt = $conn->prepare($query);
                
                if (!empty($search) && !empty($statusFilter) && $statusColumnExists) {
                    $stmt->bind_param("sss", $searchTerm, $searchTerm, $statusFilter);
                } elseif (!empty($search)) {
                    $stmt->bind_param("ss", $searchTerm, $searchTerm);
                } elseif (!empty($statusFilter) && $statusColumnExists) {
                    $stmt->bind_param("s", $statusFilter);
                }
                
                $stmt->execute();
                $result = $stmt->get_result();
                
                if (!$result) {
                    die("Database error: " . $conn->error);
                }
                
                $recipes = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                
                // Debug: Show total count
                $totalRecipesInList = count($recipes);
                ?>
                
                <div class="recipes-summary">
                    <p class="summary-text">
                        <strong>Total recipes found: <?php echo $totalRecipesInList; ?></strong>
                        <?php if (!empty($search)): ?>
                            | Search: "<?php echo htmlspecialchars($search); ?>"
                        <?php endif; ?>
                        <?php if (!empty($statusFilter)): ?>
                            | Status: <?php echo htmlspecialchars($statusFilter); ?>
                        <?php endif; ?>
                    </p>
                </div>
                
                <?php if(empty($recipes)): ?>
                    <div class="empty-state">
                        <i class="fas fa-utensils"></i>
                        <p>No recipes found.</p>
                        <?php if (!empty($search) || !empty($statusFilter)): ?>
                            <p class="empty-hint">Try adjusting your search criteria or <a href="adminDashboard.php?tab=recipes">view all recipes</a>.</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recipes as $recipe): ?>
                            <tr>
                                <td class="recipe-title">
                                    <h4><?php echo htmlspecialchars($recipe['title']); ?></h4>
                                </td>
                                <td><?php echo htmlspecialchars($recipe['author']); ?></td>                                <td>
                                    <?php if($statusColumnExists): ?>
                                    <span class="status-badge status-<?php echo $recipe['status']; ?>">
                                        <?php echo ucfirst($recipe['status'] ?? 'pending'); ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="status-badge status-pending">
                                        Pending
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($recipe['featured'] ?? 0): ?>
                                        <span class="featured-badge"><i class="fas fa-star"></i> Featured</span>
                                    <?php else: ?>
                                        <span class="not-featured">Not Featured</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($recipe['created_at'])); ?></td>
                                <td class="action-buttons">
                                    <a href="recipe_detail.php?id=<?php echo $recipe['id']; ?>" class="btn-view" target="_blank">View</a>
                                    
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">                                        <?php if(!$statusColumnExists || ($recipe['status'] ?? 'pending') == 'pending'): ?>
                                            <button type="submit" name="approve_recipe" class="btn-approve">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button type="submit" name="reject_recipe" class="btn-reject">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if(!($recipe['featured'] ?? 0)): ?>
                                            <button type="submit" name="feature_recipe" class="btn-feature">
                                                <i class="fas fa-star"></i> Feature
                                            </button>
                                        <?php endif; ?>
                                        
                                        <button type="button" class="btn-delete" 
                                                onclick="showDeleteConfirmation(<?php echo $recipe['id']; ?>, '<?php echo htmlspecialchars($recipe['title'], ENT_QUOTES); ?>')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php endif; ?>
                
            <?php elseif($activeTab == 'users'): ?>
                <!-- User Management -->
                <h1 class="playfair-display">User Management</h1>
                
                <div class="admin-actions">
                    <div class="search-bar">
                        <form action="adminDashboard.php" method="GET">
                            <input type="hidden" name="tab" value="users">
                            <input type="text" name="search" placeholder="Search users...">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                
                <?php
                // User listing with search
                $search = $_GET['search'] ?? '';
                  $query = "SELECT * FROM users WHERE 1=1";
                          
                if (!empty($search)) {
                    $search = "%$search%";
                    $query .= " AND (name LIKE ? OR email_address LIKE ?)";
                }
                
                // Remove ORDER BY created_at since the column doesn't exist
                
                $stmt = $conn->prepare($query);
                
                if (!empty($search)) {
                    $stmt->bind_param("ss", $search, $search);
                }
                
                $stmt->execute();
                $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                ?>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Join Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email_address']); ?></td>
                                <td><?php echo ucfirst($user['role'] ?? 'user'); ?></td>                                <td>                                    <span class="status-badge status-<?php echo strtolower($user['status'] ?? 'active'); ?>">
                                        <?php echo ucfirst($user['status'] ?? 'active'); ?>
                                    </span>
                                </td>
                                <td><?php echo isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A'; ?></td>
                                <td class="action-buttons">
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        
                                        <!-- Role update dropdown
                                        <select name="role" onchange="this.form.submit()" class="role-select">
                                            <option value="user" <?php echo ($user['role'] ?? 'user') == 'user' ? 'selected' : ''; ?>>User</option>
                                            <option value="moderator" <?php echo ($user['role'] ?? 'user') == 'moderator' ? 'selected' : ''; ?>>Moderator</option>
                                            <option value="admin" <?php echo ($user['role'] ?? 'user') == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                        <input type="hidden" name="update_role" value="1"> -->
                                        
                                        <!-- Block/Unblock button -->
                                        <input type="hidden" name="status" value="<?php echo $user['status'] ?? 'active'; ?>">
                                        <button type="submit" name="toggle_status" class="btn-<?php echo ($user['status'] ?? 'active') == 'active' ? 'block' : 'unblock'; ?>">
                                            <i class="fas fa-<?php echo ($user['status'] ?? 'active') == 'active' ? 'lock' : 'unlock'; ?>"></i>
                                            <?php echo ($user['status'] ?? 'active') == 'active' ? 'Block' : 'Unblock'; ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
            <?php elseif($activeTab == 'reviews'): ?>
                <!-- Review Management -->
                <h1 class="playfair-display">Review Management</h1>
                
                <div class="admin-actions">
                    <div class="search-bar">
                        <form action="adminDashboard.php" method="GET">
                            <input type="hidden" name="tab" value="reviews">
                            <input type="text" name="search" placeholder="Search reviews by recipe...">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                    
                    <div class="filter-options">
                        <select name="rating" onchange="this.form.submit()">
                            <option value="">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                </div>
                
                <?php
                // Review listing with search and filtering
                $search = $_GET['search'] ?? '';
                $ratingFilter = $_GET['rating'] ?? '';
                
                $query = "SELECT r.*, u.name as user_name, rc.title as recipe_title 
                          FROM ratings r 
                          JOIN users u ON r.user_id = u.id
                          JOIN recipes rc ON r.recipe_id = rc.id
                          WHERE 1=1";
                          
                if (!empty($search)) {
                    $search = "%$search%";
                    $query .= " AND rc.title LIKE ?";
                }
                
                if (!empty($ratingFilter)) {
                    $query .= " AND r.rating = ?";
                }
                
                $query .= " ORDER BY r.created_at DESC";
                
                $stmt = $conn->prepare($query);
                
                if (!empty($search) && !empty($ratingFilter)) {
                    $stmt->bind_param("si", $search, $ratingFilter);
                } elseif (!empty($search)) {
                    $stmt->bind_param("s", $search);
                } elseif (!empty($ratingFilter)) {
                    $stmt->bind_param("i", $ratingFilter);
                }
                
                $stmt->execute();
                $reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                ?>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Recipe</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($reviews as $review): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($review['recipe_title']); ?></td>
                                <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                                <td>
                                    <div class="star-rating">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="<?php echo $i <= $review['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($review['comment'] ?? ''); ?></td>
                                <td><?php echo date('M d, Y', strtotime($review['created_at'])); ?></td>
                                <td class="action-buttons">
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                        <button type="submit" name="delete_review" class="btn-delete" onclick="return confirm('Are you sure you want to delete this review?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
            <?php elseif($activeTab == 'submissions'): ?>
                <!-- Recipe Submissions -->
                <h1 class="playfair-display">Recipe Submissions</h1>
                
                <?php                // Get all pending recipe submissions
                if ($statusColumnExists) {
                    $status = 'pending';
                    $stmt = $conn->prepare("SELECT r.*, u.name as author 
                                            FROM recipes r 
                                            JOIN users u ON r.user_id = u.id 
                                            WHERE r.status = ? 
                                            ORDER BY r.created_at DESC");
                } else {
                    $stmt = $conn->prepare("SELECT r.*, u.name as author 
                                          FROM recipes r 
                                          JOIN users u ON r.user_id = u.id 
                                          ORDER BY r.created_at DESC LIMIT 10");
                }                if ($statusColumnExists) {
                    $stmt->bind_param("s", $status);
                }                $stmt->execute();
                $submissions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                
                // Check if categories table exists
                $categoriesExist = false;
                $stmt = $conn->prepare("SHOW TABLES LIKE 'categories'");
                $stmt->execute();
                $result = $stmt->get_result();
                $categoriesExist = $result->num_rows > 0;
                $stmt->close();
                
                $categories = [];
                // Get all categories for assignment only if the table exists
                if ($categoriesExist) {
                    $stmt = $conn->prepare("SELECT * FROM categories ORDER BY name");
                    $stmt->execute();
                    $categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                    $stmt->close();
                }
                ?>
                
                <?php if(empty($submissions)): ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>No pending submissions to review!</p>
                    </div>
                <?php else: ?>
                    <div class="submission-cards">
                        <?php foreach($submissions as $submission): ?>
                            <div class="submission-card">
                                <div class="submission-header">
                                    <h2><?php echo htmlspecialchars($submission['title']); ?></h2>
                                    <span class="submission-date">
                                        Submitted <?php echo date('M d, Y', strtotime($submission['created_at'])); ?>
                                    </span>
                                </div>
                                
                                <div class="submission-image">
                                    <img src="../uploads/<?php echo htmlspecialchars($submission['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($submission['title']); ?>">
                                </div>
                                
                                <div class="submission-info">
                                    <p><strong>By:</strong> <?php echo htmlspecialchars($submission['author']); ?></p>
                                    <p class="description"><?php echo htmlspecialchars($submission['description']); ?></p>
                                </div>
                                
                                <div class="submission-actions">
                                    <a href="recipe_detail.php?id=<?php echo $submission['id']; ?>" class="btn-view" target="_blank">
                                        <i class="fas fa-eye"></i> View Full Recipe
                                    </a>
                                      <form method="POST" class="inline-form">
                                        <input type="hidden" name="recipe_id" value="<?php echo $submission['id']; ?>">
                                        
                                        <?php if ($categoriesExist && !empty($categories)): ?>
                                        <div class="category-assignment">
                                            <h3>Assign Categories:</h3>
                                            <div class="category-checkboxes">
                                                <?php foreach($categories as $category): ?>
                                                    <label class="category-option">
                                                        <input type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>">
                                                        <?php echo htmlspecialchars($category['name']); ?>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                            <button type="submit" name="assign_categories" class="btn-categories">
                                                <i class="fas fa-tags"></i> Assign Categories
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="approval-actions">
                                            <button type="submit" name="approve_recipe" class="btn-approve">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button type="submit" name="reject_recipe" class="btn-reject">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h2>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the recipe "<span id="recipeTitle"></span>"?</p>
                <div class="warning-box">
                    <i class="fas fa-warning"></i>
                    <strong>Warning:</strong> This action cannot be undone. The recipe, all its ratings, and associated data will be permanently deleted.
                </div>
                <div class="verification-input">
                    <label for="deleteConfirmation">Type "DELETE" to confirm:</label>
                    <input type="text" id="deleteConfirmation" placeholder="Type DELETE here" autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn-delete" disabled onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Delete Recipe
                </button>
            </div>
        </div>
    </div>
    
    <!-- Hidden form for recipe deletion -->
    <form id="deleteRecipeForm" method="POST" style="display: none;">
        <input type="hidden" name="recipe_id" id="deleteRecipeId" value="">
        <input type="hidden" name="delete_recipe" value="1">
    </form>
    
    <script>
        // Delete confirmation modal functions
        let currentRecipeId = null;
        
        function showDeleteConfirmation(recipeId, recipeTitle) {
            currentRecipeId = recipeId;
            document.getElementById('recipeTitle').textContent = recipeTitle;
            document.getElementById('deleteRecipeId').value = recipeId;
            document.getElementById('deleteConfirmation').value = '';
            document.getElementById('confirmDeleteBtn').disabled = true;
            document.getElementById('deleteModal').style.display = 'block';
            
            // Focus on the input field
            setTimeout(() => {
                document.getElementById('deleteConfirmation').focus();
            }, 100);
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            currentRecipeId = null;
            document.getElementById('deleteConfirmation').value = '';
            document.getElementById('confirmDeleteBtn').disabled = true;
        }
        
        function confirmDelete() {
            if (document.getElementById('deleteConfirmation').value === 'DELETE') {
                document.getElementById('deleteRecipeForm').submit();
            }
        }
        
        // Enable/disable delete button based on confirmation input
        document.addEventListener('DOMContentLoaded', function() {
            const deleteInput = document.getElementById('deleteConfirmation');
            const deleteBtn = document.getElementById('confirmDeleteBtn');
            
            deleteInput.addEventListener('input', function() {
                if (this.value === 'DELETE') {
                    deleteBtn.disabled = false;
                    deleteBtn.classList.add('enabled');
                } else {
                    deleteBtn.disabled = true;
                    deleteBtn.classList.remove('enabled');
                }
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                const modal = document.getElementById('deleteModal');
                if (event.target === modal) {
                    closeDeleteModal();
                }
            });
            
            // Handle Enter key in confirmation input
            deleteInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && this.value === 'DELETE') {
                    confirmDelete();
                }
            });
        });
        
        // Handle tab navigation
        document.addEventListener('DOMContentLoaded', function() {
            // Handle filter form submissions
            const filterSelects = document.querySelectorAll('.filter-options select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const searchParams = new URLSearchParams(window.location.search);
                    searchParams.set(this.name, this.value);
                    window.location.search = searchParams.toString();
                });
            });
            
            // Handle role selection
            const roleSelects = document.querySelectorAll('.role-select');
            roleSelects.forEach(select => {
                select.addEventListener('change', function() {
                    this.closest('form').submit();
                });
            });
        });
    </script>
</body>
</html>