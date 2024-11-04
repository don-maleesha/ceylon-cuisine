document.addEventListener("DOMContentLoaded", function() {
    function updateWelcomeMessage() {
      const currentTime = new Date();
      const hours = currentTime.getHours();
      const greeting = hours < 12 ? "Good morning" : hours < 18 ? "Good afternoon" : "Good evening";
      document.getElementById("welcomeMessage").textContent = `${greeting}, welcome to Ceylon Cuisine!`;
    }
  
    updateWelcomeMessage();
    setInterval(updateWelcomeMessage, 60000);
  });
  
  const card = [
    {
        image: './images/336919407_5806908546074945_1170120185552864888_n.jpg',
        title: 'Fish Ambul Thiyal',
        description: 'Fish ambul thiyal is a traditional Sri Lankan sour fish curry made with spices and tamarind. It\'s known for its tangy flavor and is often served with rice.',
    },
    {
        image: './images/istockphoto-543978354-612x612.jpg',
        title: 'Hoppers',
        description: 'Hoppers are a Sri Lankan food made from rice flour and coconut milk. They are eaten for breakfast or dinner, plain or with toppings like eggs or spicy sauces.',
    },
    {
        image: './images/451436608_122211276014008560_2715113146278636003_n.jpg',
        title: 'Cashew Curry',
        description: 'Cashew curry is a creamy Sri Lankan dish made with tender cashews cooked in a rich coconut milk gravy, flavored with spices like turmeric, cumin, and curry leaves.',
    },
    {
        image: './images/Konda_Kavum_02.JPG',
        title: 'Konda Kavum',
        description: 'Konda Kavum is a traditional Sri Lankan sweet made from rice flour and treacle. It\'s deep-fried into a round, golden shape with a soft, spongy center and crispy edges.',
    },
    {
        image: './images/Asmi-1-768x590.jpg',
        title: 'Aasmi',
        description: 'Asmi is a traditional Sri Lankan sweet made from rice flour and cinnamon, deep-fried into lace-like shapes, and topped with sweet treacle syrup. It’s crispy and delicious!',
    },
    {
        image: './images/s-l1200.jpg',
        title: 'Achcharu (Pickle)',
        description: 'Achcharu is a popular Sri Lankan pickle made from vegetables mixed with chili, vinegar, and spices. It’s tangy, spicy, and often enjoyed as a snack or side dish.',
    },
    {
        image: './images/d1d53dac007b62e5396b076e96fcc8ef.jpg',
        title: 'Watalappan',
        description: 'Watalappan is a rich Sri Lankan dessert made with coconut milk, jaggery, and spices like cardamom. It\'s a creamy pudding often served at special occasions and festivals.',
    },
    {
        image: './images/Polos-slow-cooker-low-q.jpg',
        title: 'Jackfruit Curry',
        description: 'Jackfruit curry is a flavorful Sri Lankan dish made from tender jackfruit chunks cooked in a spiced coconut milk gravy. It\'s rich, aromatic, and perfect with rice or roti.',
    },
    {
        image: './images/LK94011111-06-E.JPG',
        title: 'Halapa',
        description: 'Halapa is a traditional Sri Lankan sweet made from kurakkan (finger millet) flour, coconut, and jaggery, wrapped in kanda leaves and steamed for a rich, earthy flavor.',
    },
    {
        image: './images/01-odiyal-kool-ig-cjkit_chen-3.jpg',
        title: 'Odiyal Kool',
        description: 'Odiyal Kool is a spicy seafood soup from Jaffna, featuring fish, prawns, squid, and crab. It\'s a flavorful dish that\'s sure to delight!',
    },
    {
        image: './images/kola-kanda-6322021_1280.jpg',
        title: 'Kola Kanda',
        description: 'Kola Kanda is a nutritious Sri Lankan herbal porridge made from rice, leafy greens, and spices. This flavorful dish is often enjoyed as a wholesome breakfast.',
    },
    {
        image: './images/360_F_902576047_SfbN3V8fN6ZhNNcSwVq9UH3yVm5O0CEI.jpg',
        title: 'Pani Walalu',
        description: 'Pani Walalu is a traditional Sri Lankan sweet made from a mix of rice flour and urad dal flour. It’s deep-fried into spiral shapes and soaked in a syrup made with treacle.',
    },
    {
        image: './images/kurakkan-kanda.jpg',
        title: 'Porridge',
        description: 'Finger Millet Porridge is a healthy Sri Lankan drink made from finger millet flour, water, and coconut milk. It\'s smooth, filling, and usually served warm.',
    },
    {
        image: './images/309064641_176054931607113_208558741360292147_n.jpg',
        title: 'Dodol',
        description: 'Dodol is a traditional Sri Lankan sweet made from coconut milk, jaggery, and rice flour. Sticky and rich, it\'s enjoyed during festivals and often flavored with spices or nuts.',
    }
];
  
  const categories = [...new Set(card.map(item => item.title))].map(title => {
    return card.find(item => item.title === title);
  });
  
  document.getElementById('search-bar').addEventListener('keyup', (e) => {
    const searchData = e.target.value.toLowerCase();
    const filteredData = categories.filter(item => item.title.toLowerCase().includes(searchData));
    displayItem(filteredData);
  });
  
  const displayItem = (items) => {
    const rootElement = document.getElementById('root');
    if (items.length === 0) {
      rootElement.innerHTML = `<p id="root">No recipes found. Try a different search term!</p>`;
      return;
    }
    
    rootElement.innerHTML = items.map(item => {
      const { image, title, description } = item;
      return `
        <div class="card">
          <div class="image-box">
            <img src="${image}" alt="${title}" class="img-fluid">
          </div>
          <div class="title">
            <h2 class="noto-sans">${title}</h2>
          </div>
          <div class="description">
            <p>${description}</p>
          </div>
          <button>View Recipe</button>
        </div>
      `;
    }).join('');
  };

const searchRecipes = () => {
    const searchData = document.getElementById('search-bar').value.toLowerCase();
    const filteredData = categories.filter(item => item.title.toLowerCase().includes(searchData));
    displayItem(filteredData);
};
  
displayItem(categories);
  