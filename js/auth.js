// Authentication handler
class AuthManager {
    constructor() {
        this.init();
    }

    async init() {
        // Only run authentication check on index.html
        if (window.location.pathname.includes('index.html') || window.location.pathname.endsWith('/')) {
            await this.checkAuthStatus();
        }
        this.setupLogoutHandler();
    }

    async checkAuthStatus() {
        try {
            console.log('Checking authentication status...');
            const response = await fetch('php/auth/check_session.php');
            const data = await response.json();
            
            console.log('Auth check response:', data);

            if (data.authenticated) {
                // User is logged in, show main content
                this.showMainContent(data.user);
            } else {
                // User is not logged in, redirect to login
                this.redirectToLogin();
            }
        } catch (error) {
            console.error('Error checking authentication:', error);
            this.redirectToLogin();
        }
    }

    showMainContent(user) {
        // Hide loading screen
        const loadingScreen = document.getElementById('loading-screen');
        const mainContent = document.getElementById('main-content');
        
        if (loadingScreen) {
            loadingScreen.style.display = 'none';
            console.log('Loading screen hidden');
        }
        if (mainContent) {
            mainContent.style.display = 'block';
            console.log('Main content shown');
        }

        // Update user welcome message
        const userWelcome = document.getElementById('user-welcome');
        if (userWelcome && user.name) {
            userWelcome.textContent = `Welcome, ${user.name}`;
            console.log('User welcome updated:', user.name);
        }
    }

    redirectToLogin() {
        console.log('Redirecting to login...');
        // Only redirect if not already on login page
        if (!window.location.pathname.includes('login.html')) {
            window.location.href = 'login.html';
        }
    }

    setupLogoutHandler() {
        const logoutLink = document.getElementById('logout-link');
        if (logoutLink) {
            logoutLink.addEventListener('click', (e) => {
                e.preventDefault();
                this.logout();
            });
            console.log('Logout handler setup complete');
        }
    }

    async logout() {
        try {
            console.log('Logging out...');
            const response = await fetch('php/auth/logout.php', {
                method: 'POST'
            });
            
            const data = await response.json();
            console.log('Logout response:', data);
            
            if (data.success) {
                // Redirect to login page
                window.location.href = 'login.html';
            } else {
                alert('Logout failed. Please try again.');
            }
        } catch (error) {
            console.error('Logout error:', error);
            // Force redirect even if logout request fails
            window.location.href = 'login.html';
        }
    }
}

// Initialize authentication manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, initializing AuthManager');
    new AuthManager();
});