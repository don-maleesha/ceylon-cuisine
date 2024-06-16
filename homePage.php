<?php
include "dbconn.php";
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
  <script src="./bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>

  <header class="text-white align-items-center">
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
                  <a href="homePage.php" class="nav-link" target="_blank">Home</a>
                </li>
                <li class="nav-item ubuntu-light-italic">
                  <a href="#" class="nav-link">About Us</a>
                </li>
                <li class="nav-item ubuntu-light-italic">
                  <a href="#" class="nav-link">Contact</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link ubuntu-light-italic">Recipes</a>
                </li>
              </ul>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </header>

  <div class="container rounded-3 mt-3 col-6 d-flex flex-column justify-content-center align-items-center mt-5 mb-5" id="welcome">
    <div class="row">
      <div class="col">
        <h2 id="welcomeMessage" class="h4 mt-2"></h2>
        <p class="h6">We are the largest Sri Lankan traditional food recipes collection.</p>
      </div>
      <div class="row mt-5 mb-5">
        <div class="col-6">
          <a href="#" class="link-offset-2 link-offset-3-hover">sign-in</a>
        </div>
        <div class="col-6">
          <a href="#" class="link-offset-2 link-offset-3-hover">sign-up</a>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <div class="container-fluid mt-5 d-inline">
      <div class="row">
        <div class="col">
            <p class="text-center">&copy; Ceylon Cuisine 2024</p>
            <p class="text-center"><a href="#">Privacy Policy</a></p>
            <p class="text-center"><a href="#">Terms of Conditions</a></p>
        </div>
      </div>
    </div>
  </footer>

</body>

</html>