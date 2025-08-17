// Login and User Registration JavaScript

class AuthManager {
    constructor(apiEndpoint = 'login.php') {
        this.apiEndpoint = apiEndpoint;
    }

    // Create new user account
    async createUser(username, password) {
        try {
            if (!username || !password) {
                throw new Error('Username and password are required');
            }

            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'LoginUserCreate',
                    username: username.trim(),
                    password: password.trim()
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'Failed to create user');
            }

            return {
                success: true,
                message: result.message
            };

        } catch (error) {
            console.error('Error creating user:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Authenticate user login
    async authenticateUser(username, password) {
        try {
            if (!username || !password) {
                throw new Error('Username and password are required');
            }

            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'LoginAuthenticate',
                    username: username.trim(),
                    password: password.trim()
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'Authentication failed');
            }

            // Store user session (optional)
            if (result.username) {
                sessionStorage.setItem('loggedInUser', result.username);
                sessionStorage.setItem('loginTime', new Date().toISOString());
            }

            return {
                success: true,
                message: result.message,
                username: result.username
            };

        } catch (error) {
            console.error('Error authenticating user:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Check if user is currently logged in
    isLoggedIn() {
        return sessionStorage.getItem('loggedInUser') !== null;
    }

    // Get current logged in user
    getCurrentUser() {
        return sessionStorage.getItem('loggedInUser');
    }

    // Logout user
    logout() {
        sessionStorage.removeItem('loggedInUser');
        sessionStorage.removeItem('loginTime');
    }

    // Show notification messages
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            max-width: 300px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        `;

        // Set background color based on type
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            info: '#17a2b8',
            warning: '#ffc107'
        };
        notification.style.backgroundColor = colors[type] || colors.info;

        notification.textContent = message;
        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
}

// DOM Ready initialization
document.addEventListener('DOMContentLoaded', function() {
    const authManager = new AuthManager();

    // Handle login form
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username')?.value;
            const password = document.getElementById('password')?.value;
            
            if (!username || !password) {
                authManager.showNotification('Please enter both username and password', 'error');
                return;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Logging in...';
            submitBtn.disabled = true;

            try {
                const result = await authManager.authenticateUser(username, password);
                
                if (result.success) {
                    authManager.showNotification(result.message, 'success');
                    // Redirect or update UI as needed
                    setTimeout(() => {
                        window.location.href = 'top.html'; // or wherever you want to redirect
                    }, 1500);
                } else {
                    authManager.showNotification(result.error, 'error');
                }
            } catch (error) {
                authManager.showNotification('Login failed: ' + error.message, 'error');
            } finally {
                // Restore button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // Handle registration form
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('regUsername')?.value;
            const password = document.getElementById('regPassword')?.value;
            const confirmPassword = document.getElementById('confirmPassword')?.value;
            
            if (!username || !password) {
                authManager.showNotification('Please enter both username and password', 'error');
                return;
            }

            if (password !== confirmPassword) {
                authManager.showNotification('Passwords do not match', 'error');
                return;
            }

            if (password.length < 6) {
                authManager.showNotification('Password must be at least 6 characters long', 'error');
                return;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Creating Account...';
            submitBtn.disabled = true;

            try {
                const result = await authManager.createUser(username, password);
                
                if (result.success) {
                    authManager.showNotification(result.message, 'success');
                    // Clear form
                    this.reset();
                } else {
                    authManager.showNotification(result.error, 'error');
                }
            } catch (error) {
                authManager.showNotification('Registration failed: ' + error.message, 'error');
            } finally {
                // Restore button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // Check login status on page load
    if (authManager.isLoggedIn()) {
        const user = authManager.getCurrentUser();
        console.log('User is logged in:', user);
        
        // Update UI to show logged in state
        const loginStatus = document.getElementById('loginStatus');
        if (loginStatus) {
            loginStatus.innerHTML = `Welcome, ${user}! <button onclick="logout()">Logout</button>`;
        }
    }
});

// Global logout function
function logout() {
    const authManager = new AuthManager();
    authManager.logout();
    authManager.showNotification('Logged out successfully', 'info');
    
    // Redirect to login page or refresh
    setTimeout(() => {
        window.location.reload();
    }, 1500);
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AuthManager;
}