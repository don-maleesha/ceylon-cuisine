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
  <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
  <script type="text/javascript" src="ceylon-cuisine.css"></script>
</head>

<body>
  <header class="text-white">
    <div class="container-fluid">
      <div class="row align-items-center ">
        <div class="col d-flex align-items-center">
          <img src="./images/logo.png" alt="logo" class="logo-img">
        </div>
        <div class="col d-flex align-items-center">
          <h1 class="display-4 m-0 josefin-sans">Ceylon Cuisine</h1>
          <!--<p class="tagline">Experience the Taste of Tradition</p>-->
        </div>
        <div class="col-auto text-end">
          <nav class="navbar navbar-expand-md navbar-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNav">
              <ul class="navbar-nav">
                <li class="nav-item  ubuntu-light-italic">
                  <a href="#" class="nav-link">About Us</a>
                </li>
                <li class="nav-item  ubuntu-light-italic">
                  <a href="#" class="nav-link">Contact</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link  ubuntu-light-italic">Recipe Categories</a>
                </li>
              </ul>
            </div>
          </nav>
        </div>
      </div>
  </header>
  <div>
    <h2 id="welcomeMessage">Welcome</h2>
  </div>
  </div>

</body>

</html>