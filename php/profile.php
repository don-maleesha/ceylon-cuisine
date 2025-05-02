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

        $allowedExtensions = array('jpg', 'jpeg', 'png');
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
if (isset($_POST['submit'])) {
    $name = $_POST['title'];
    $description = $_POST['description'];
    $file_name = $_FILES['image_url']['name'];
    $tempory_name = $_FILES['image_url']['tmp_name'];
    $folder = '../uploads/'.$file_name;

    $errors = array();

    $allowed_extensions = array("jpg", "jpeg", "png");
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if(!in_array($file_extension, $allowed_extensions)){
        $errors[] = "Only JPG, JPEG, PNG files are allowed.";
    }

    if($_FILES['image_url']['size'] > 5000000) {
        $errors[] = "File size should not exceed 5MB.";
    }

    if (empty($errors)) {
        if(move_uploaded_file($tempory_name, $folder)){

            $sql = "INSERT INTO recipes (title, description, image_url) VALUES (?, ?, ?)";
            $statement = mysqli_stmt_init($conn);
            $prepare_statement = mysqli_stmt_prepare($statement, $sql);

            if ($prepare_statement) {
                mysqli_stmt_bind_param($statement, "sss", $name, $description, $file_name);

                if (mysqli_stmt_execute($statement)) {
                    echo "<div class='alert alert-success'>Recipe Added Successfully!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Failed to add recipe. Please try again.</div>";
                }
            } else {
                die("SQL Error: Unable to prepare the statement.");
            }
        }
    } else {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
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
                <button type="submit" class="upload-button raleway" name="submit" aria-label="profile-picture-upload">Upload</button>
            </form>
        </div>
        <div>
            <h2 class="playfair-display"><?php echo htmlspecialchars($_SESSION['name']); ?></h2>
            <p class="raleway"><?php echo htmlspecialchars($_SESSION['email_address']); ?></p>
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
                <div class="form-group upload-image">
                    <label for="image" class="raleway">Upload Image</label>
                    <input type="file" id="image" name="image_url" accept="image/*" class="form-control" required>
                </div>
                <!-- Add inside the form -->
                <div>
                    <button type="submit" name="submit" class="raleway">Submit Recipe</button>
                </div>
            </form>
        </div>
    </section>
    <section id="myRecipe" class="content-section">
        <div class="container">
            <h2 class="playfair-display">About Us</h2>
            <p class="raleway">Ceylon Cuisine is a platform for food lovers to explore and share their favourite recipes. You can find a wide range of recipes from different cuisines around the world. Our mission is to bring people together through food and create a community of food enthusiasts.</p>
        </div>
    </section>
    <section id="myFavourits" class="content-section">
        <div class="container">
            <h2 class="playfair-display">Contact Us</h2>
            <p class="raleway">Ceylon Cuisine is a platform for food lovers to explore and share their favourite recipes. You can find a wide range of recipes from different cuisines around the world. Our mission is to bring people together through food and create a community of food enthusiasts.</p>
        </div>
    </section>
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