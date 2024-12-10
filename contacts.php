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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                  <a href="recipes.php" class="nav-link ubuntu-light-italic">Recipes</a>
                </li>
              </ul>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </header>

  <section>
    <div class="container rounded-3 col-6 d-flex flex-column justify-content-center align-items-center mt-5 mb-5 vh-100 contact-selection" id="">
      <div class="left-side">
        <div class="address details mb-4">
          <i class="fas fa-map-marker-alt icon"></i>
          <div class="topic">Address: </div>
          <div class="text-one">47, Yapa Patumaga, Walala,</div>
          <div class="text-two">Menikhinna, Kandy. 20170</div>
        </div>
        <div class="phone details mb-4">
          <i class="fas fa-phone-alt icon"></i>
          <div class="topic">Phone: </div>
          <div class="text-one">+94 71 349 8849</div>
        </div>
        <div class="email details mb-4">
          <i class="fas fa-envelope icon"></i>
          <div class="topic">Email: </div>
          <div class="text-one">info@ceyloncuisine.com</div>
        </div>
      </div>
      <div class="righ-side">
        <div class="topic-text">Leave us a message</div>
        <form action="#">
        <div class="input-box">
          <input type="text" placeholder="Enter your name" class="input-field"/>
        </div>
        <div class="input-box">
          <input type="text" placeholder="Enter your email" class="input-field"/>
        </div>
        <div class="input-box message-box">
          <textarea placeholder="Enter your message" class="input-field"></textarea>
        </div>
        <div class="button">
          <input type="button" value="Send Now"/>
        </div>
        </form>
      </div>
    </div>
  </section>

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