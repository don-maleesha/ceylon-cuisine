
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
  <div class="container">
    <div class="search-wraapp">
      <input type="text" class="search-bar" placeholder="Search for recipes...">
      <button class="search-btn">Search</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/336919407_5806908546074945_1170120185552864888_n.jpg" alt="ambul thiyal" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Fish Ambul Thiyal</h2>
      </div>
      <div class="description">
        <p>Fish ambul thiyal is a traditional Sri Lankan sour fish curry made with spices and tamarind. It's known for its tangy flavor and is often served with rice.</p>
      </div>
      <button>View Recipe</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/istockphoto-543978354-612x612.jpg" alt="hoppers" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Hoppers</h2>
      </div>
      <div class="description">
        <p>Hoppers are a Sri Lankan food made from rice flour and coconut milk. They are eaten for breakfast or dinner, plain or with toppings like eggs or spicy sauces.</p>
      </div>
      <button>View Recipe</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/451436608_122211276014008560_2715113146278636003_n.jpg" alt="recipe1" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Cashew Curry</h2>
      </div>
      <div class="description">
        <p>Cashew curry is a creamy Sri Lankan dish made with tender cashews cooked in a rich coconut milk gravy, flavored with spices like turmeric, cumin, and curry leaves.</p>
      </div>
      <button>View Recipe</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/Konda_Kavum_02.JPG" alt="konda kavum" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Konda Kavum</h2>
      </div>
      <div class="description">
        <p>Konda Kavum is a traditional Sri Lankan sweet made from rice flour and treacle. It's deep-fried into a round, golden shape with a soft, spongy center and crispy edges.</p>
      </div>
      <button>View Recipe</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/Asmi-1-768x590.jpg" alt="aasmee" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Aasmi</h2>
      </div>
      <div class="description">
        <p>Asmi is a traditional Sri Lankan sweet made from rice flour and cinnamon, deep-fried into lace-like shapes, and topped with sweet treacle syrup. It’s crispy and delicious!</p>
      </div>
      <button>View Recipe</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/s-l1200.jpg" alt="achchary" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Achcharu(Pickle)</h2>
      </div>
      <div class="description">
        <p>Achcharu is a popular Sri Lankan pickle made from vegetables mixed with chili, vinegar, and spices. It’s tangy, spicy, and often enjoyed as a snack or side dish.</p>
      </div>
      <button>View Recipe</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/d1d53dac007b62e5396b076e96fcc8ef.jpg" alt="watalappan" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Watalappan</h2>
      </div>
      <div class="description">
        <p>Watalappan is a rich Sri Lankan dessert made with coconut milk, jaggery, and spices like cardamom. It's a creamy pudding often served at special occasions and festivals.</p>
      </div>
      <button>View Recipe</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/Polos-slow-cooker-low-q.jpg" alt="jack fruit" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Jackfruit Curry</h2>
      </div>
      <div class="description">
        <p>Jackfruit curry is a flavorful Sri Lankan dish made from tender jackfruit chunks cooked in a spiced coconut milk gravy. It's rich, aromatic, and perfect with rice or roti.</p>
      </div>
      <button>View Recipe</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/LK94011111-06-E.JPG" alt="helap" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Halapa</h2>
      </div>
      <div class="description">
        <p>Halapa is a traditional Sri Lankan sweet made from kurakkan (finger millet) flour, coconut, and jaggery, wrapped in kanda leaves and steamed for a rich, earthy flavor.</p>
      </div>
      <button>View Recipe</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/01-odiyal-kool-ig-cjkit_chen-3.jpg" alt="odiyal kool" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Odiyal Kool</h2>
      </div>
      <div class="description">
        <p>Odiyal Kool is a spicy seafood soup from Jaffna, featuring fish, prawns, squid, and crab. It's a flavorful dish that's sure to delight!</p>
      </div>
      <button>View Recipe</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/kola-kanda-6322021_1280.jpg" alt="kola kanda" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Kola Kanda</h2>
      </div>
      <div class="description">
        <p>Kola Kanda is a nutritious Sri Lankan herbal porridge made from rice, leafy greens, and spices. This flavorful dish is often enjoyed as a wholesome breakfast.</p>
      </div>
      <button>View Recipe</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/360_F_902576047_SfbN3V8fN6ZhNNcSwVq9UH3yVm5O0CEI.jpg" alt="pani walalu" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Pani Walalu</h2>
      </div>
      <div class="description">
        <p>Pani Walalu is a traditional Sri Lankan sweet made from a mix of rice flour and urad dal flour. It’s deep-fried into spiral shapes and soaked in a syrup made with treacle.</p>
      </div>
      <button>View Recipe</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/kurakkan-kanda.jpg" alt="kurakkan kanda" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Porridge</h2>
      </div>
      <div class="description">
        <p>Finger Millet Porridge is a healthy Sri Lankan drink made from finger millet flour, water, and coconut milk. It's smooth, filling, and usually served warm.</p>
      </div>
      <button>View Recipe</button>
    </div>
    <div class="card">
      <div class="image-box">
        <img src="./images/309064641_176054931607113_208558741360292147_n.jpg" alt="dodol" class="img-fluid">
      </div>
      <div class="title">
        <h2 class="noto-sans">Dodol</h2>
      </div>
      <div class="description">
        <p>Dodol is a traditional Sri Lankan sweet made from coconut milk, jaggery, and rice flour. Sticky and rich, it's enjoyed during festivals and often flavored with spices or nuts.</p>
      </div>
      <button>View Recipe</button>
    </div>
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