<?php

session_start();
require_once "dbconn.php";

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['submit'])) {
    if (isset($_FILES['profile_picture']) && !empty($_FILES['profile_picture']['tmp_name'])) {
        $file_name = $_FILES['profile_picture']['name'];
        $temporary_name = $_FILES['profile_picture']['tmp_name'];
        $folder = '../uploads/' . basename($file_name);

        $errors = array();
        $allowed_extensions = array("jpg", "jpeg", "png");
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = "Only JPG, JPEG, PNG files are allowed.";
        }

        if ($_FILES['profile_picture']['size'] > 5000000) {
            $errors[] = "File size should not exceed 5MB.";
        }

        if (empty($errors)) {
            if (move_uploaded_file($temporary_name, $folder)) {
                $sql = "UPDATE users SET profile_picture = ? WHERE email_address = ?";
                $statement = mysqli_stmt_init($conn);

                if (mysqli_stmt_prepare($statement, $sql)) {
                    mysqli_stmt_bind_param($statement, "ss", $folder, $_SESSION['email_address']);

                    if (mysqli_stmt_execute($statement)) {
                        $_SESSION['profile_picture'] = $folder;
                        echo "<div class='alert alert-success'>Profile Picture Updated Successfully!</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Failed to update profile picture. Please try again.</div>";
                    }
                } else {
                    die("SQL Error: Unable to prepare the statement.");
                }
            } else {
                echo "<div class='alert alert-danger'>Sorry, there was an error uploading your file.</div>";
            }
        } else {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>No file selected or file upload failed.</div>";
    }

    // Fetch the updated profile picture from the database after upload
    $query = "SELECT profile_picture FROM users WHERE email_address = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['email_address']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $profile_picture);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Store the updated image path in session
    if ($profile_picture) {
        $_SESSION['profile_picture'] = $profile_picture;
    }
}

$email = $_SESSION['email_address'] ?? ''; // Ensure user is logged in and session is set

// Fetch profile picture from the database using email
$profile_picture = '../images/user-profile-icon-front-side-with-white-background.jpg'; // Default image
if (!empty($email)) {
    $sql = "SELECT profile_picture FROM users WHERE email_address = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Check if the user has an uploaded profile picture
    if (!empty($row['profile_picture'])) {
        $profile_picture = $row['profile_picture'];
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
    <a href="#" class="active">Add New Recipe</a>
    <a href="#" class="active">My Recipes</a>
    <a href="#" class="active">Favourites</a>
    <a href="#" class="active">Stats</a>
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
</body>
</html>