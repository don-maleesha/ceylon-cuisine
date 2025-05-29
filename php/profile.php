<?php
session_start();
require_once "dbconn.php";

// Check if the user is logged in
if (!isset($_SESSION['email_address'])) {
    die("User not logged in.");
}

// Fetch user information from the session
$email = $_SESSION['email_address'];
$profile_picture = '../images/user-profile-icon-front-side-with-white-background.jpg';
$uploadMessage = ''; // Initialize upload message

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'jfif');
        $maxFileSize = 2 * 1024 * 1024;

        if (!in_array($fileExtension, $allowedExtensions)) {
            $uploadMessage = 'Only JPG, JPEG, and PNG files are allowed.';
        } elseif ($fileSize > $maxFileSize) {
            $uploadMessage = 'File size exceeds 2MB limit.';
        } else {
            $uploadFileDir = '../uploads/';
            $destPath = $uploadFileDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {

                $relativePath = '../uploads/' . $fileName;
                $sql = "UPDATE users SET profile_picture = ? WHERE email_address = ?";
                $updateStmt = $conn->prepare($sql);
                if ($updateStmt) {
                    $updateStmt->bind_param("ss", $relativePath, $email);
                    $updateStmt->execute();
                    $updateStmt->close();
                } else {
                    $uploadMessage = 'Database error: ' . $conn->error;
                }
                
                $profile_picture = $relativePath; // Update the profile picture path
                $uploadMessage = 'Profile picture uploaded successfully!';
            } else {
                $uploadMessage = 'There was an error moving the uploaded file.';
            }
        }
    } else {
        $uploadMessage = 'No file uploaded or upload error occurred.';
    }
}

// Fetch the updated profile picture from the database
$sql = "SELECT profile_picture FROM users WHERE email_address = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($profile_picture_db);
    $stmt->fetch();
    $stmt->close();

    if (!empty($profile_picture_db)) {
        $profile_picture = $profile_picture_db;
    }
}

// handle recipe submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit-recipe'])){

    $email = $_SESSION['email_address'];
    $sql = "SELECT id FROM users WHERE email_address = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0 ) die("User not found in database");
    $user_id = $result->fetch_assoc()['id'];
    $stmt->close();

    // Sanitize inputs
    $name = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);

    // Proceess ingredients and instructions from textareas
    $ingredients = array_filter(array_map('trim', explode("\n", $_POST['ingredients'][0] ?? '')));
    $instructions = array_filter(array_map('trim', explode("\n", $_POST['instructions'][0] ?? '')));

    // Validate
    $errors = [];
    if (empty($ingredients)) $errors[] = "Ingredients cannot be empty.";
    if (empty($instructions)) $errors[] = "Instructions cannot be empty.";

    // Handle image upload
    if(empty($errors)) {
        $file_name = $_FILES['image_url']['name'];
        $temporary_name = $_FILES['image_url']['tmp_name'];
        $unique_file_name = uniqid() . '_' . $file_name;
        $folder = '../uploads/' . $unique_file_name;

        //Validate image
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'jfif'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if(!in_array($file_extension, $allowed_extensions)) {
            $errors[] = "Invalid image format";
        } elseif ($_FILES['image_url']['size'] > 5 * 1024 * 1024) {
            $errors[] = "Image size exceeds 5MB";
        }
    }

    // Insert recipe into the database
    if(empty($errors)) {
        if(move_uploaded_file($temporary_name, $folder)) {
            $ingredients_json = json_encode($ingredients);
            $instructions_json = json_encode($instructions);

            $sql = "INSERT INTO recipes (user_id, title, description, image_url, ingredients, instructions) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssss", $user_id, $name, $description, $unique_file_name, $ingredients_json, $instructions_json);
            if ($stmt->execute()) {
                header("Location: profile.php");
                exit();
            } else {
                die("Error inserting recipe: " . $stmt->error);
            }
        } else {
            die("Error uploading image.");
        }
    }
}

// Fetch user ID from the session
$sql = "SELECT id FROM users WHERE email_address = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['email_address']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found in database");
}
$user = $result->fetch_assoc();
$user_id = $user['id'];
$stmt->close();

//display user recipes
$sql = "SELECT * FROM recipes WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$recipes = array();
while ($row = $result->fetch_assoc()) {
    $recipes[] = $row; 
}


//display recipe data
$recipe_id = $_GET['id'] ?? null;
$ingredients = [];
$instructions = [];
$recipe = null;

if ($recipe_id) {
    // Get user ID if logged in
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Prepare the SQL query
    $stmt = $conn->prepare("SELECT 
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
    
    // Bind parameters based on login status
    if ($user_id) {
        $stmt->bind_param("ii", $user_id, $recipe_id);
    } else {
        // Use dummy value for non-logged-in users
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
        $recipe['average_rating'] = $recipe['average_rating'] ?? 0;
        $recipe['user_rating'] = $recipe['user_rating'] ?? 0;
    }
}

//update user profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $newName = htmlspecialchars(trim($_POST['name']));
    $newEmail = htmlspecialchars(trim($_POST['email']));
    
    // Validate email
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $uploadMessage = "Invalid email format";
    } else {
        // Check if email exists (excluding current user)
        $checkEmail = $conn->prepare("SELECT email_address FROM users WHERE email_address = ? AND email_address != ?");
        $checkEmail->bind_param("ss", $newEmail, $email);
        $checkEmail->execute();
        $checkEmail->store_result();
        
        if ($checkEmail->num_rows > 0) {
            $uploadMessage = "Email already exists!";
        } else {
            // Update user info
            $updateSql = "UPDATE users SET name = ?, email_address = ? WHERE email_address = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("sss", $newName, $newEmail, $email);
            
            if ($stmt->execute()) {
                // Update session variables
                $_SESSION['name'] = $newName;
                $_SESSION['email_address'] = $newEmail;
                $email = $newEmail; // Update local variable for subsequent queries
                $uploadMessage = "Profile updated successfully!";
            } else {
                $uploadMessage = "Error updating profile: " . $conn->error;
            }
            $stmt->close();
        }
        $checkEmail->close();
    }
}

// Handle recipe update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update-recipe'])) {
    $recipe_id = $_POST['recipe_id'];
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    
    // Process ingredients and instructions
    $ingredients = array_filter(array_map('trim', explode("\n", $_POST['ingredients'])));
    $instructions = array_filter(array_map('trim', explode("\n", $_POST['instructions'])));
    
    // Handle image update
    $image_url = $recipe['image_url']; // Keep existing image by default
    
    if (!empty($_FILES['new_image']['name'])) {
        // Similar to your existing image upload logic
        $file_name = $_FILES['new_image']['name'];
        $temporary_name = $_FILES['new_image']['tmp_name'];
        $unique_file_name = uniqid() . '_' . $file_name;
        $folder = '../uploads/' . $unique_file_name;
        
        // Validate and move file
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'jfif'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_extensions) && 
            $_FILES['new_image']['size'] <= 5 * 1024 * 1024) {
            if (move_uploaded_file($temporary_name, $folder)) {
                $image_url = $unique_file_name;
            }
        }
    }
    
    // Update database
    $ingredients_json = json_encode($ingredients);
    $instructions_json = json_encode($instructions);
    
    $sql = "UPDATE recipes SET 
            title = ?, 
            description = ?, 
            image_url = ?, 
            ingredients = ?, 
            instructions = ? 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $title, $description, $image_url, 
                     $ingredients_json, $instructions_json, $recipe_id);
    
    if ($stmt->execute()) {
        header("Location: profile.php?id=$recipe_id");
        exit();
    } else {
        die("Error updating recipe: " . $stmt->error);
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
    <link rel="stylesheet" href="../css/profile.css">
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
<div class="container">
    <div class="breadcrumb raleway">
        <a href="#">Home</a>&gt;<a href="#">Profile</a>
    </div>
    <div class="profile-info">
        <div class="profile-picture">
            <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
            <span id="upload-message" data-message="<?= htmlspecialchars($uploadMessage) ?>"></span>
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <label for="profile-picture-upload" class="change-picture-button">
                    <i class="fas fa-camera"></i>
                    <p class="raleway">Change Picture</p>
                </label>
                <input type="file" id="profile-picture-upload" name="profile_picture" accept="image/*" class="upload-input">
                <button type="submit" class="upload-button raleway" name="submit-picture" aria-label="profile-picture-upload">Upload</button>
            </form>
        </div>
        <div>
            <h2 class="playfair-display"><?php echo htmlspecialchars($_SESSION['name']); ?></h2>
            <p class="raleway"><?php echo htmlspecialchars($_SESSION['email_address']); ?></p>
            <button onclick="openProfileModal()" class="raleway profile-update-btn"><i class="fas fa-edit"></i></button>
        </div>
    </div>
</div>
<div class="tabs">
    <button class="active raleway" onclick="showSection('newRecipe')">Add New Recipe</button>
    <button class="raleway" onclick="showSection('myRecipe')">My Receips</button>
    <button class="raleway" onclick="showSection('myFavourits')">My Favourites</button>
    <button class="raleway" onclick="showSection('stats')">Stats</button>
</div>
<div class="content">
    <section id="newRecipe" class="content-section active">
        <div class="container">
            <h2 class="playfair-display">Share Your Delicious Recipe with the World!</h2>
            <p class="raleway">We are excited to see what you have created in your kitchen. Please fill out the form below to share your recipe with our community.</p>
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name" class="raleway">Recipe Name</label>
                    <input type="text" id="name" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="description" class="raleway">Description</label>
                    <textarea id="description" name="description" class="form-control" required></textarea> 
                </div>
                <div class="form-group">
                    <label for="" class="raleway">Ingredients</label>
                    <div id="ingredients-container">
                        <div class="ingredient-input">
                        <textarea name="ingredients[]" class="form-control"
                            placeholder="Enter one step per line:
                                        Step 1: Mix ingredients
                                        Step 2: Bake at 350°F
                                        Step 3: Let cool before serving"
                            rows="2"required></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="raleway">Instructions</label>
                    <div id="instructions-container">
                        <div class="instruction-input">
                        <textarea name="instructions[]" class="form-control" 
                            placeholder="Enter one step per line:
                                        Step 1: Mix ingredients
                                        Step 2: Bake at 350°F
                                        Step 3: Let cool before serving" rows="4" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group upload-image">
                    <label for="image" class="raleway">Upload Image</label>
                    <input type="file" id="image" name="image_url" accept="image/*" class="form-control" required>
                </div>
                <!-- Add inside the form -->
                <div>
                    <button type="submit" name="submit-recipe" class="raleway">Submit Recipe</button>
                </div>
            </form>
        </div>
    </section>
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
                            <div class="rating-section">
              <!-- <h3 class="playfair-display">Rating</h3> -->
                                <div class="average-rating merriweather-regular">
                                    <!-- Star display for average rating -->
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
                                            $hasHalfStar = false; // Only show one half-star
                                            endif;
                                        endfor;
                                        ?>
                                    </div>
                                    <!-- Optional: Display numeric value as tooltip -->
                                    <span class="rating-value" title="<?= number_format($average, 1) ?>">
                                        (<?= number_format($average, 1) ?>)
                                    </span>
                                </div>
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <div class="user-rating">
                                        <p class="raleway">Your Rating:</p>
                                        <div class="star-rating">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= $i <= (int)($recipe['user_rating'] ?? 0) ? 'rated' : '' ?>" 
                                                    data-rating="<?= $i ?>" 
                                                    onclick="rateRecipe(<?= $recipe_id ?>, <?= $i ?>)"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- In your recipe cards -->
                            <a href="profile.php?id=<?= htmlspecialchars($myrecipe['id']) ?>">
                                <button class="raleway">View Recipe</button>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <section id="myFavourits" class="content-section">
        <div class="container">
            <h2 class="playfair-display">Contact Us</h2>
            <p class="raleway">Ceylon Cuisine is a platform for food lovers to explore and share their favourite recipes. You can find a wide range of recipes from different cuisines around the world. Our mission is to bring people together through food and create a community of food enthusiasts.</p>
        </div>
    </section>
</div>

<!-- model -->
<?php if ($recipe): ?>
<div id="recipeModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="update">
            <h2 class="playfair-display"><?= htmlspecialchars($recipe['title']) ?> </h2>
            <span><button onclick="openUpdatePanel()" class="edit-button"><i class="fas fa-edit"></i></button></span>
        </div>
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

<div class="panel-overlay" id="panelOverlay">
<div id="updatePanel" class="update-panel">
    <span class="close-btn" onclick="closeUpdatePanel()">&times;</span>
    <h2 class="playfair-display">Update Recipe</h2>
    <form action="profile.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="recipe_id" value="<?= $recipe_id ?>">
        
        <div class="form-group">
            <label class="raleway">Title</label>
            <input type="text" name="title" class="form-control" 
                   value="<?= htmlspecialchars($recipe['title']) ?>" required>
        </div>

        <div class="form-group">
            <label class="raleway">Description</label>
            <textarea name="description" class="form-control" required><?= 
                htmlspecialchars($recipe['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label class="raleway">Ingredients (one per line)</label>
            <textarea name="ingredients" class="form-control" rows="5" required><?= 
                implode("\n", $ingredients) ?></textarea>
        </div>

        <div class="form-group">
            <label class="raleway">Instructions (one per line)</label>
            <textarea name="instructions" class="form-control" rows="5" required><?= 
                implode("\n", $instructions) ?></textarea>
        </div>

        <div class="form-group">
            <label class="raleway">Current Image</label>
            <img src="../uploads/<?= $recipe['image_url'] ?>" 
                 alt="Current image" style="max-width: 200px; display: block;">
        </div>

        <div class="form-group">
            <label class="raleway">New Image (optional)</label>
            <input type="file" name="new_image" accept="image/*" class="form-control">
        </div>

        <div class="form-group">
            <button type="submit" name="update-recipe" class="raleway">
                Update Recipe
            </button>
            <button type="button" class="raleway" onclick="closeUpdatePanel()">
                Cancel
            </button>
        </div>
    </form>
</div>
</div>

<!-- Profile Update Modal -->
<div id="profileModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeProfileModal()">&times;</span>
        <h2 class="playfair-display">Update Profile</h2>
        <form action="profile.php" method="POST">
            <div class="form-group">
                <label class="raleway">Name:</label>
                <input type="text" name="name" 
                    value="<?php echo htmlspecialchars($_SESSION['name']); ?>" 
                    class="form-control" required>
            </div>
            <div class="form-group">
                <label class="raleway">Email:</label>
                <input type="email" name="email" 
                    value="<?php echo htmlspecialchars($_SESSION['email_address']); ?>" 
                    class="form-control" required>
            </div>
            <div class="form-group">
                <button type="submit" name="update_profile" class="raleway">Save Changes</button>
                <button type="button" class="raleway" onclick="closeProfileModal()">Cancel</button>
            </div>
        </form>
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

<script src="../js/profile.js"></script>
<script src="../js/ceylon-cuisine.js"></script>
</body>
</html>