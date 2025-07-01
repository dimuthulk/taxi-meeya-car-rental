// Profile page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in
    const userId = localStorage.getItem('user_id');
    if (!userId) {
        alert('Please login to access your profile.');
        window.location.href = 'login.html';
        return;
    }

    // Load user profile
    loadUserProfile();
    
    // Initialize profile navigation
    initProfileNavigation();
    
    // Initialize forms
    initProfileForm();
    initPasswordForm();
    initPreferencesForm();
});

function loadUserProfile() {
    const userId = localStorage.getItem('user_id');
    
    fetch(`php/profile/get_profile.php?user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateProfile(data.user);
            } else {
                alert('Error loading profile: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error loading profile:', error);
            alert('Error loading profile data.');
        });
}

function populateProfile(user) {
    // Update display elements
    document.getElementById('user-display-name').textContent = `${user.first_name} ${user.last_name}`;
    document.getElementById('user-email').textContent = user.email;
    document.getElementById('user-initials').textContent = 
        (user.first_name.charAt(0) + user.last_name.charAt(0)).toUpperCase();
    
    // Populate form fields
    document.getElementById('first-name').value = user.first_name || '';
    document.getElementById('last-name').value = user.last_name || '';
    document.getElementById('email').value = user.email || '';
    document.getElementById('phone').value = user.phone || '';
    document.getElementById('drivers-license').value = user.drivers_license || '';
    document.getElementById('license-expiry').value = user.license_expiry || '';
    
    // Set preferences if available
    if (user.email_notifications !== undefined) {
        document.getElementById('email-notifications').checked = user.email_notifications;
    }
    if (user.sms_notifications !== undefined) {
        document.getElementById('sms-notifications').checked = user.sms_notifications;
    }
    if (user.preferred_language) {
        document.getElementById('preferred-language').value = user.preferred_language;
    }
}

function initProfileNavigation() {
    const navLinks = document.querySelectorAll('.profile-nav-link');
    const sections = document.querySelectorAll('.profile-section');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links and sections
            navLinks.forEach(l => l.classList.remove('active'));
            sections.forEach(s => s.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Show corresponding section
            const targetId = this.getAttribute('href').substring(1);
            document.getElementById(targetId).classList.add('active');
        });
    });
}

function initProfileForm() {
    const form = document.getElementById('profile-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const userId = localStorage.getItem('user_id');
        formData.append('user_id', userId);
        
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.textContent = 'Updating...';
        submitBtn.disabled = true;
        
        fetch('php/profile/update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Profile updated successfully!');
                // Update localStorage if name changed
                localStorage.setItem('user_name', `${formData.get('first_name')} ${formData.get('last_name')}`);
            } else {
                alert('Error updating profile: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating profile.');
        })
        .finally(() => {
            submitBtn.textContent = 'Update Profile';
            submitBtn.disabled = false;
        });
    });
}

function initPasswordForm() {
    const form = document.getElementById('password-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const newPassword = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-new-password').value;
        
        if (newPassword !== confirmPassword) {
            alert('New passwords do not match!');
            return;
        }
        
        const formData = new FormData(form);
        const userId = localStorage.getItem('user_id');
        formData.append('user_id', userId);
        
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.textContent = 'Changing...';
        submitBtn.disabled = true;
        
        fetch('php/profile/change_password.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Password changed successfully!');
                form.reset();
            } else {
                alert('Error changing password: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error changing password.');
        })
        .finally(() => {
            submitBtn.textContent = 'Change Password';
            submitBtn.disabled = false;
        });
    });
}

function initPreferencesForm() {
    const form = document.getElementById('preferences-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const userId = localStorage.getItem('user_id');
        formData.append('user_id', userId);
        
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.textContent = 'Saving...';
        submitBtn.disabled = true;
        
        fetch('php/profile/update_preferences.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Preferences saved successfully!');
            } else {
                alert('Error saving preferences: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving preferences.');
        })
        .finally(() => {
            submitBtn.textContent = 'Save Preferences';
            submitBtn.disabled = false;
        });
    });
}
