
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ceylon-cuisine</title>
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="recipes.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Satisfy&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
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
                  <a href="contacts.php" class="nav-link">Contacts</a>
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
  <div>
    <input type="text" class="search-bar" id="search-bar" placeholder="Search for recipes...">
    <button class="add-btn" id="add-btn"><a href="addrecipes.php">Add Recipe</a></button>
  </div>
  <div class="container" id="root">
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