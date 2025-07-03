// Clean Mobile Menu Functionality
let menubar = document.querySelector('#menu-bars');
let mynav = document.querySelector('.navbar');
let navLinks = document.querySelectorAll('.navbar a');

// Mobile menu toggle function
menubar.onclick = () => {
    menubar.classList.toggle('fa-times');
    mynav.classList.toggle('active');
}

// Close mobile menu when clicking on a navigation link
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        menubar.classList.remove('fa-times');
        mynav.classList.remove('active');
    });
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (!mynav.contains(e.target) && !menubar.contains(e.target)) {
        menubar.classList.remove('fa-times');
        mynav.classList.remove('active');
    }
});

// Handle window resize
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        menubar.classList.remove('fa-times');
        mynav.classList.remove('active');
    }
});

document.getElementById("submitBtn").addEventListener("click", function(e) {
        e.preventDefault(); // prevent default anchor behavior

        const name = document.getElementById("name").value.trim();
        const phone = document.getElementById("phone").value.trim();
        const when = document.getElementById("when").value.trim();
        const date = document.getElementById("date").value.trim();
        const start = document.getElementById("start").value.trim();
        const end = document.getElementById("end").value.trim();

        const phonePattern = /^[0-9]{10}$/;

        if (!name || !phone || !when || !date || !start || !end) {
            alert("All fields are required.");
            return;
        }

        if (!phonePattern.test(phone)) {
            alert("Phone number must be 10 digits.");
            return;
        }

        // If all checks pass
        alert("Form submitted successfully!");
        // You can optionally submit form data via AJAX here
    });