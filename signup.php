
<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-14">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ceylon-cuisine</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="signup.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Satisfy&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap">
  <script src="ceylon-cuisine.js"></script>
</head>
<body>
  <br>
  <br>
  <br>
  <?php

    if (isset($_POST['submit'])) {

    //retrieves form data
    $name = $_POST['name'];
    $email = $_POST['email_address'];
    $password = $_POST['password'];
    $confirm_pwd = $_POST['password_confirm'];
    
    //hashes the password for security
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    //array to store the error messages
    $errors = array();

    //validates that all fields are filled
    if (empty($name) || empty($email) || empty($password)) {

        array_push($errors, 'All fields must be provided');

    }

    //validates email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        array_push($errors, 'Email must be a valid email address');

    }

    //validates password length
    if (strlen($password) < 8) {

        array_push($errors, 'Password must be at least 8 characters long');

    }

    //validates that the password and confirmation match
    if ($password !== $confirm_pwd) {

        array_push($errors, 'password does not match');

    }

    //include he database connection file
    include "dbconn.php";

    //checks if the email address already exists in the database
    $sql = "SELECT * FROM users WHERE email_address = '$email'";

    $result = mysqli_query($conn, $sql);
    $row_count = mysqli_num_rows($result);

    if($row_count > 0) {

        array_push($errors, "Email address already exists");

    }

    //if there are any errors, display them
    if (count($errors) > 0) {

        foreach ($errors as $error) {

            echo "<div class='alert alert-danger'>$error</div>";

        } 

    } else {

        //prepares the sql statement for inserting a new user
        $sql = "INSERT INTO users (name, email_address, password) VALUES (?, ?, ?)";

        $statement = mysqli_stmt_init($conn);
        $prepare_statement = mysqli_stmt_prepare($statement, $sql);

        if ($prepare_statement) {

            //binds the parameters and executes the statement
            mysqli_stmt_bind_param($statement, "sss", $name, $email, $password_hash);

            mysqli_stmt_execute($statement);

            echo "<div class='alert alert-success'>You are registered successfully.</div>";

        } else {

            die("Something went wrong with your registration. Please try again later.");

        }

    }
  }

?>
<body>
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
  <div class="container rounded-3 col-6 d-flex flex-column justify-content-center mt-1 mb-5 vh-100" id="">
    <div>
        <h1 class="text-center josefin-sans" id="h1">Join Us!</h1>
        <p class="h6 playwrite-gb-s" id="p">Where Your Flavors Meet the World!</p>
    </div>
    <form method="post" id="form-id">
      <div class="row mb-3">
        <label for="name" class="col-sm-2 col-form-label label">Name</label>
        <div class="col-sm-10">
          <input type="text" class="form-control form" id="name" name="name">
        </div>
      </div>
      <div class="row mb-3">
        <label for="email" class="col-sm-2 col-form-label label">Email</label>
        <div class="col-sm-10">
          <input type="email" class="form-control form" id="email" name="email_address">
        </div>
      </div>
      <div class="row mb-3">
        <label for="password" class="col-sm-2 col-form-label label">Password</label>
        <div class="col-sm-10">
          <input type="password" class="form-control form" id="password" name="password">
        </div>
      </div>
      <div class="row mb-3">
        <label for="password_confirm" class="col-sm-2 col-form-label label">Re Enter</label>
        <div class="col-sm-10">
          <input type="password" class="form-control form" id="password_confirm" name="password_confirm">
        </div>
      </div>
      <button type="submit" name="submit">Sign up</button>
    </form>
  </div>
  <footer>
    <div class="container-fluid justify-content-center align-items-center mt-1">
      <div class="row">
          <div class="col">
            <div class="">
              <a href="#" class="nav-link">Privacy Policy</a>
              <a href="#" class="nav-link">Terms of Conditions</a>
            </div>
            <div class="col">
              <div class="mt-2">
                <p class="copy">&copy; ceylon-cuisine 2024</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</body>
</html>