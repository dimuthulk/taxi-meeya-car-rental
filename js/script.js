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

// User Profile JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // User profile dropdown functionality
    const userProfile = document.getElementById('user-profile');
    const profileDropdown = document.getElementById('profile-dropdown');
    
    // Toggle dropdown on click
    userProfile.addEventListener('click', function(e) {
        e.stopPropagation();
        profileDropdown.style.opacity = profileDropdown.style.opacity === '1' ? '0' : '1';
        profileDropdown.style.visibility = profileDropdown.style.visibility === 'visible' ? 'hidden' : 'visible';
        profileDropdown.style.transform = profileDropdown.style.opacity === '1' ? 'translateY(0)' : 'translateY(-10px)';
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!userProfile.contains(e.target)) {
            profileDropdown.style.opacity = '0';
            profileDropdown.style.visibility = 'hidden';
            profileDropdown.style.transform = 'translateY(-10px)';
        }
    });
    
    // Update user name dynamically (you can connect this to your auth system)
    function updateUserProfile(userName, userRole = 'Customer') {
        const userNameElement = document.getElementById('user-name');
        const userRoleElement = document.querySelector('.user-role');
        
        if (userNameElement) {
            userNameElement.textContent = userName;
        }
        if (userRoleElement) {
            userRoleElement.textContent = userRole;
        }
    }
    
    // Example: Update profile with logged-in user data
    // You can replace this with actual user data from your authentication system
    const currentUser = localStorage.getItem('currentUser');
    if (currentUser) {
        const userData = JSON.parse(currentUser);
        updateUserProfile(userData.name || 'John Doe', userData.role || 'Customer');
    }
});