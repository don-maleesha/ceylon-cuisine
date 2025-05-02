// document.addEventListener("DOMContentLoaded", function () {
//     const messageSpan = document.getElementById("upload-message");
//     const uploadMessage = messageSpan?.dataset?.message;

//     if (uploadMessage) {
//         alert(uploadMessage);
//     }
// });


function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });

    // Show selected section
    const activeSection = document.getElementById(sectionId);
    if (activeSection) {
        activeSection.style.display = 'block';
    }

    // Update button states
    document.querySelectorAll('.tabs button').forEach(button => {
        button.classList.remove('active');
    });
    event.target.classList.add('active');
}