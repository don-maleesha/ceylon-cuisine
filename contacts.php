<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <title>ceylon-cuisine</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="contacts.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Satisfy&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap">
    <script src="ceylon-cuisine.js"></script>
</head>
<body>
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
    <div class="container rounded-3 col-6 d-flex flex-column justify-content-center align-items-center mt-5 mb-5 vh-100 contact-selection" id="about">
        <div class="row">
            <div class="col">
                <h2>Contact Us</h2>
                <p>If you have any questions, feel free to reach out to us.</p>
                <ul class="list-unstyled">
                    <li class="contact-button">
                        <a href="tel:+94 81 234 5678" class="btn btn-primary fw-medium">Call Us: +94 812345678</a>  
                    </li>
                    <li class="contact-button">
                        <a href="mailto:info.@ceylon-cuisine.com" class="btn btn-primary fw-medium">Email Us: info@ceylon-cuisine.com</a>
                    </li>
                    <li class="mt-4">
                        <h5>Our Address: </h5>
                        <p class="fw-medium">47, Medagammedda, Walala, Menikhinna, 20170.</p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h5 class="mt-4">Find Us Here</h5>
                <div id="map-container" class="mb-4">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15829.013225234494!2d80.6868241!3d7.3254202!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae367004524764b%3A0x50ba5d328f3044c5!2sCeylon%20Cuisine!5e0!3m2!1sen!2slk!4v1718807400414!5m2!1sen!2slk" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
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