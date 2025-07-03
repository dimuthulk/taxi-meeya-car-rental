document.addEventListener('DOMContentLoaded', function () {
    console.log('[Login Debug] Form loaded');
    
    const form = document.getElementById('login-form');
    const emailInput = document.getElementById('login-email');
    const passwordInput = document.getElementById('login-password');
    const submitBtn = form ? form.querySelector('button[type="submit"]') : null;
    const messageDiv = document.getElementById('login-message');

    // Check if all elements are found
    console.log('[Login Debug] Form elements:', {
        form: !!form,
        emailInput: !!emailInput,
        passwordInput: !!passwordInput,
        submitBtn: !!submitBtn,
        messageDiv: !!messageDiv
    });

    if (!form) {
        console.log('[Login Debug] Login form not found - probably not on login page');
        return;
    }

    // Form submission
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        console.log('[Login Debug] Form submitted');

        // Show loading state
        if (submitBtn) {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Logging in...';
        }

        // Collect form data
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        console.log('[Login Debug] Form data:', data);

        // Basic validation
        if (!data.email || !data.password) {
            showMessage('Please fill in all fields.', 'error');
            resetButton();
            return;
        }

        // Send login request
        console.log('[Login Debug] Sending request to:', 'php/auth/login.php');
        fetch('php/auth/login.php', {
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
                console.log('[Login Debug] Server response:', result);
                if (result.success) {
                    showMessage('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        // Redirect to dashboard or home page
                        window.location.href = result.redirect || 'index.html';
                    }, 1500);
                } else {
                    showMessage(result.message || 'Login failed. Please try again.', 'error');
                    resetButton();
                }
            })
            .catch(error => {
                console.error('[Login Debug] Error:', error);
                showMessage('Network error. Please check your connection.', 'error');
                resetButton();
            });
    });

    function showMessage(message, type) {
        if (messageDiv) {
            messageDiv.innerHTML = message;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';

            // Auto-hide after 5 seconds
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 5000);
        }
    }

    function resetButton() {
        if (submitBtn) {
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Sign In';
        }
    }
});