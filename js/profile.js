document.addEventListener("DOMContentLoaded", function () {
    // Handle upload messages
    const messageSpan = document.getElementById("upload-message");
    const uploadMessage = messageSpan?.dataset?.message;
    if (uploadMessage) alert(uploadMessage);

    // Handle initial URL state
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('id')) {
        showSection('myRecipe');
        showModal(urlParams.get('id'));
    }
});

// Tab switching function
function showSection(sectionId) {
    // Hide all sections and remove active class from buttons
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });
    document.querySelectorAll('.tabs button').forEach(button => {
        button.classList.remove('active');
    });

    // Show selected section and activate button
    document.getElementById(sectionId).classList.add('active');
    document.querySelector(`.tabs button[onclick="showSection('${sectionId}')"]`)
        .classList.add('active');
}

// Modal handling functions
function showModal(recipeId) {
    // Update URL without reload
    history.pushState({}, '', `?id=${recipeId}`);
    
    // Ensure My Recipes section is active
    showSection('myRecipe');
    
    // Show modal
    document.getElementById('recipeModal').style.display = 'block';
    document.body.classList.add('modal-open');
}

function closeModal() {
    // Hide modal and clean URL
    document.getElementById('recipeModal').style.display = 'none';
    document.body.classList.remove('modal-open');
    history.replaceState({}, '', window.location.pathname);
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        closeModal();
    }
}

// Handle browser navigation (back/forward buttons)
window.addEventListener('popstate', function(event) {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('id')) {
        showSection('myRecipe');
        showModal(urlParams.get('id'));
    } else {
        closeModal();
    }
});


function openUpdatePanel() {
    document.getElementById('panelOverlay').style.display = 'block';
    setTimeout(() => {
        document.getElementById('updatePanel').classList.add('active');
    }, 10);
}

function closeUpdatePanel() {
    document.getElementById('updatePanel').classList.remove('active');
    setTimeout(() => {
        document.getElementById('panelOverlay').style.display = 'none';
    }, 300);
}

// Close panel when clicking outside
document.getElementById('panelOverlay').addEventListener('click', (e) => {
    if(e.target === document.getElementById('panelOverlay')) {
        closeUpdatePanel();
    }
});

function openProfileModal() {
    document.getElementById('profileModal').style.display = 'block';
}

function closeProfileModal() {
    document.getElementById('profileModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = document.getElementsByClassName('modal');
    for (const modal of modals) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
}