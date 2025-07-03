document.addEventListener('DOMContentLoaded', function () {
    console.log('[Registration Debug] Form loaded');
    
    const form = document.getElementById('register-form');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const strengthIndicator = document.getElementById('password-strength');
    const submitBtn = document.getElementById('register-btn');
    const messageDiv = document.getElementById('register-message');

    // Check if all elements are found
    console.log('[Registration Debug] Form elements:', {
        form: !!form,
        passwordInput: !!passwordInput,
        confirmPasswordInput: !!confirmPasswordInput,
        strengthIndicator: !!strengthIndicator,
        submitBtn: !!submitBtn,
        messageDiv: !!messageDiv
    });

    // Password strength checker
    passwordInput.addEventListener('input', function () {
        const password = this.value;
        const strength = checkPasswordStrength(password);

        strengthIndicator.textContent = strength.text;
        strengthIndicator.className = `password-strength ${strength.class}`;
    });

    // Password confirmation checker
    confirmPasswordInput.addEventListener('input', function () {
        const password = passwordInput.value;
        const confirmPassword = this.value;

        if (confirmPassword && password !== confirmPassword) {
            this.style.borderColor = '#dc3545';
        } else {
            this.style.borderColor = '#28a745';
        }
    });

    // Form submission
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        console.log('[Registration Debug] Form submitted');

        // Show loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;

        // Collect form data
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        console.log('[Registration Debug] Form data:', data);

        // Validate passwords match
        if (data.password !== data.confirm_password) {
            showMessage('Passwords do not match!', 'error');
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            return;
        }

        // Validate phone number (basic validation)
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        if (!phoneRegex.test(data.phone.trim())) {
            showMessage('Please enter a valid phone number.', 'error');
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            return;
        }

        // Send registration request
        console.log('[Registration Debug] Sending request to:', 'php/auth/registration.php');
        fetch('php/auth/registration.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log('[Registration Debug] Server response:', result);
                if (result.success) {
                    showMessage('Account created successfully! Redirecting to login...', 'success');
                    setTimeout(() => {
                        window.location.href = '/login.html';
                    }, 2000);
                } else {
                    showMessage(result.message || 'Registration failed. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('[Registration Debug] Error:', error);
                showMessage('Network error. Please check your connection.', 'error');
            })
            .finally(() => {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
    });

    function checkPasswordStrength(password) {
        if (password.length < 6) {
            return { text: 'Password too short', class: 'strength-weak' };
        }

        let score = 0;
        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        if (score < 2) {
            return { text: 'Weak password', class: 'strength-weak' };
        } else if (score < 4) {
            return { text: 'Medium strength', class: 'strength-medium' };
        } else {
            return { text: 'Strong password', class: 'strength-strong' };
        }
    }

    function showMessage(message, type) {
        messageDiv.innerHTML = message;
        messageDiv.className = type === 'error' ? 'error-message' : 'success-message';
        messageDiv.style.display = 'block';

        // Auto-hide after 5 seconds
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    }
});