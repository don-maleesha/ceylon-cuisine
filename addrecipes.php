
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
</head>
<body>
  <br>
  <br>
  <br>
  <?php

    

?>
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
  <div class="container rounded-3 col-6 d-flex flex-column justify-content-center mt-5 mb-5 vh-100" id="welcome">
    <div>
        <h1 class="text-center josefin-sans" id="h1">Share Your Recipe with Us!</h1>
        <p class="h6 playwrite-gb-s" id="p">Let’s Taste Your Creation!</p>
    </div>
    <form method="post" id="form-id">
      <div class="row mb-3">
        <label for="name" class="col-sm-2 col-form-label label">Name</label>
        <div class="col-sm-10">
          <input type="text" class="form-control form" id="name" name="name" required>
        </div>
      </div>
      <div class="row mb-3">
        <label for="description" class="col-sm-2 col-form-label label">Description</label>
        <div class="col-sm-10">
          <textarea class="form-control form textarea" id="description" name="description" maxlength="158"></textarea>
          <p id="char-count">158 characters remaining</p>
        </div>
      </div>
      <div class="row mb-3">
        <label for="image" class="col-sm-2 col-form-label label">Image</label>
        <div class="col-sm-10">
          <input type="file" class="form-control form file-btn" id="image" name="image" required>
        </div>
      </div>
      <button type="submit" name="submit">Add Recipe</button>
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
  <script src="ceylon-cuisine.js"></script>
</body>
</html>