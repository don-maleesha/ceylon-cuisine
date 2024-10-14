<?php
    session_start();
    if (isset($_SESSION["user"])) {
        header("Location: signin.php");
    }
?>
<!DOCTYPE html>
<html lang="en"> 

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ceylon-cuisine</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="ceylon-cuisine.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Satisfy&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap">
    <script src="ceylon-cuisine.js"></script>
</head>

<body>
    <br>
    <br>
    <?php
        if(isset($_POST["login"])){

            $email = $_POST["email_address"];
            $password = $_POST["password"];

            require_once "dbconn.php";  // Include the database connection file

            // Query to find the user by their email
            $sql = "SELECT * FROM users WHERE email_address = '$email'";
            $result = mysqli_query( $conn, $sql);


            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

            if ($user) {
                // checks if the password is correct
                if (password_verify($password, $user["password"])) {
                        session_start();
                        $_SESSION["user"] = "yes";
                        header("Location: homePage.php");
                        die();
                } else {
                    echo "<div class='alert alert-danger'>Invalid password</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>User not found</div>";
            }
        }
    ?>

    <script src="./bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>

    <header class="text-white align-items-center fixed-top">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-3 d-flex align-items-center">
                    <img src="./images/logo.png" alt="logo" class="logo-img img-fluid rounded-circle">
                </div>
                <div class="col-6 d-flex flex-column justify-content-center align-items-center">
                    <h1 class="display-4 m-0 josefin-sans mt-2">Ceylon Cuisine</h1>
                    <p class="tagline text-center">Experience the Taste of Tradition</p>
                </div>
                <div class="col-3 text-end">
                    <nav class="navbar navbar-expand-md navbar-light">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav ml-5">
                                <li class="nav-item ubuntu-light-italic">
                                    <a href="homePage.php" class="nav-link" target="_top">Home</a>
                                </li>
                                <li class="nav-item ubuntu-light-italic">
                                    <a href="aboutus.php" class="nav-link" target="_top">About Us</a>
                                </li>
                                <li class="nav-item ubuntu-light-italic">
                                    <a href="contacts.php" class="nav-link" target="_top">Contacts</a>
                                </li>
                                <li class="nav-item">
                                    <a href="recipes.php" class="nav-link ubuntu-light-italic">Recipes</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div class="container rounded-3 col-6 d-flex flex-column justify-content-center align-items-center mt-5 mb-5 vh-100" id="welcome">
        <form action="signin.php" method="post">
            <div class="row mb-3">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" id="email" name="email_address">
                </div>
            </div>
            <div class="row mb-3">
                <label for="password" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="password" name="password">
                </div>
            </div>
            <button type="submit" name="login" class="btn btn-primary">Sign in</button>
        </form>
    </div>

    <footer>
        <div class="container-fluid justify-content-center align-items-center mt-1">
            <div class="row">
                <div class="col">
                    <div class="">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Conditions</a>
                    </div>
                    <div class="col">
                        <div class="mt-2">
                            <p>&copy; ceylon-cuisine 2024</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>