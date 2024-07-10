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
  <link rel="stylesheet" type="text/css" href="aboutus.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Satisfy&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap">
  <script src="ceylon-cuisine.js"></script>
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
        <div class="col-3 text-center">
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
                  <a href="#" class="nav-link ubuntu-light-italic">Recipes</a>
                </li>
              </ul>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </header>

  <section>
  <div class="container rounded-3 col-6 d-flex flex-column justify-content-center align-items-center mt-5 mb-5 vh-100" id="about">
    <div class="row">
      <div class="col">
        <h2 class="fw-semibold">About Us</h2>
        <p class="fw-medium">Welcome to Ceylon Cuisine, your ultimate destination for authentic Ceylon cuisine! We are passionate about bringing the rich, diverse, and vibrant flavors of Sri Lankan food to your kitchen. Our mission is to preserve and share the traditional recipes that have been passed down through generations, offering a true taste of Ceylon's culinary heritage.</p>
      </div>
      <div class="col">
        <h2 class="fw-semibold">Our Story</h2>
        <p class="fw-medium">Our journey began with a love for the aromatic spices, fresh ingredients, and unique cooking techniques that define Ceylon cuisine. We are a team of food enthusiasts, chefs, and storytellers who believe that food is more than just sustenance; it's a gateway to culture, history, and connection. Inspired by the bustling markets, fragrant spice gardens, and the warm hospitality of Sri Lanka, we set out to create a platform where anyone can experience the magic of Ceylon food.</p>
      </div>
    </div>
    <div class="row">
        <h2 class="fw-semibold">What We Offer</h2>
        <p class="fw-medium">At Ceylon Cuisine, we offer a curated collection of traditional Sri Lankan recipes, each carefully tested and crafted to ensure authenticity and ease of preparation. Whether you're looking for a hearty curry, a tangy sambol, or a sweet treat, our recipes cover a wide range of dishes to suit every palate. Alongside our recipes, we provide detailed cooking tips, ingredient guides, and cultural insights to enhance your culinary journey.</p>
    </div>
    <div class="row">
        <h2 class="text-center fw-semibold">Join Us</h2>
        <p class="text-center fw-medium">We invite you to join us on this culinary adventure. Whether you are a seasoned cook or a curious beginner, Ceylon Cuisine is here to guide you through the enchanting world of Sri Lankan food. Explore our recipes, share your creations, and become part of our vibrant community.
            ,<br>
            <br>Thank you for visiting Ceylon Cuisine. Let’s cook, share, and celebrate the flavors of Ceylon together!
        </p>
    </div>
  </div>
  </section>

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
    </div>
  </footer>

</body>

</html>