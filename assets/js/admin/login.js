/**
 * Admin Login JavaScript functionality
 * Handles form validation, password toggle, and dark mode
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize dark mode from localStorage
    initializeDarkMode();
    
    // Initialize form validation
    initializeFormValidation();
    
    // Initialize password toggle
    initializePasswordToggle();
    
    // Initialize language dropdown
    initializeLanguageDropdown();
    
    // Auto-focus on username field
    setTimeout(focusUsername, 100);
});

/**
 * Initialize dark mode functionality
 */
function initializeDarkMode() {
    const darkMode = localStorage.getItem('darkMode');
    const body = document.body;
    const lightIcon = document.getElementById('lightModeIcon');
    const darkIcon = document.getElementById('darkModeIcon');
    
    if (darkMode === 'true') {
        body.classList.add('dark-mode');
        if (lightIcon) lightIcon.classList.add('hidden');
        if (darkIcon) darkIcon.classList.remove('hidden');
    } else {
        body.classList.remove('dark-mode');
        if (lightIcon) lightIcon.classList.remove('hidden');
        if (darkIcon) darkIcon.classList.add('hidden');
    }
}

/**
 * Toggle dark mode
 */
function toggleDarkMode() {
    const body = document.body;
    const lightIcon = document.getElementById('lightModeIcon');
    const darkIcon = document.getElementById('darkModeIcon');
    
    if (body.classList.contains('dark-mode')) {
        body.classList.remove('dark-mode');
        if (lightIcon) lightIcon.classList.remove('hidden');
        if (darkIcon) darkIcon.classList.add('hidden');
        localStorage.setItem('darkMode', 'false');
    } else {
        body.classList.add('dark-mode');
        if (lightIcon) lightIcon.classList.add('hidden');
        if (darkIcon) darkIcon.classList.remove('hidden');
        localStorage.setItem('darkMode', 'true');
    }
}

/**
 * Initialize password toggle functionality
 */
function initializePasswordToggle() {
    const passwordInput = document.getElementById('password');
    const toggleButton = document.querySelector('[onclick="togglePassword()"]');
    
    if (passwordInput && toggleButton) {
        toggleButton.addEventListener('click', function(e) {
            e.preventDefault();
            togglePassword();
        });
    }
}

/**
 * Toggle password visibility
 */
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggleIcon');
    
    if (passwordInput && toggleIcon) {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
    const form = document.querySelector('form');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Real-time validation
    if (usernameInput) {
        usernameInput.addEventListener('blur', validateUsername);
        usernameInput.addEventListener('input', clearError);
    }
    
    if (passwordInput) {
        passwordInput.addEventListener('blur', validatePassword);
        passwordInput.addEventListener('input', clearError);
    }
}

/**
 * Validate the entire form
 */
function validateForm() {
    const username = document.getElementById('username')?.value.trim();
    const password = document.getElementById('password')?.value;
    
    let isValid = true;
    
    if (!username) {
        showError('username', 'Username is required');
        isValid = false;
    } else if (username.length < 3) {
        showError('username', 'Username must be at least 3 characters');
        isValid = false;
    }
    
    if (!password) {
        showError('password', 'Password is required');
        isValid = false;
    } else if (password.length < 6) {
        showError('password', 'Password must be at least 6 characters');
        isValid = false;
    }
    
    return isValid;
}

/**
 * Validate username field
 */
function validateUsername() {
    const username = document.getElementById('username')?.value.trim();
    
    if (!username) {
        showError('username', 'Username is required');
        return false;
    } else if (username.length < 3) {
        showError('username', 'Username must be at least 3 characters');
        return false;
    } else {
        clearError('username');
        return true;
    }
}

/**
 * Validate password field
 */
function validatePassword() {
    const password = document.getElementById('password')?.value;
    
    if (!password) {
        showError('password', 'Password is required');
        return false;
    } else if (password.length < 6) {
        showError('password', 'Password must be at least 6 characters');
        return false;
    } else {
        clearError('password');
        return true;
    }
}

/**
 * Show error message for a field
 */
function showError(fieldName, message) {
    const field = document.getElementById(fieldName);
    if (!field) return;
    
    // Remove existing error
    clearError(fieldName);
    
    // Add error class
    field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
    
    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-red-500 text-sm mt-1 error-message';
    errorDiv.textContent = message;
    
    // Insert after the field
    field.parentNode.insertBefore(errorDiv, field.nextSibling);
}

/**
 * Clear error message for a field
 */
function clearError(fieldName) {
    const field = document.getElementById(fieldName);
    if (!field) return;
    
    // Remove error classes
    field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
    
    // Remove error message
    const errorMessage = field.parentNode.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
}

/**
 * Initialize language dropdown
 */
function initializeLanguageDropdown() {
    const dropdown = document.getElementById('languageDropdown');
    const toggleButton = document.querySelector('[onclick="toggleLanguageDropdown()"]');
    
    if (dropdown && toggleButton) {
        toggleButton.addEventListener('click', function(e) {
            e.preventDefault();
            toggleLanguageDropdown();
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (dropdown && !e.target.closest('.language-switcher')) {
            dropdown.classList.add('hidden');
        }
    });
}

/**
 * Toggle language dropdown
 */
function toggleLanguageDropdown() {
    const dropdown = document.getElementById('languageDropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

/**
 * Show loading state
 */
function showLoading() {
    const submitButton = document.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Logging in...';
    }
}

/**
 * Hide loading state
 */
function hideLoading() {
    const submitButton = document.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Login';
    }
}

/**
 * Show success message
 */
function showSuccess(message) {
    const container = document.querySelector('.login-card');
    if (container) {
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message rounded-lg p-4 mb-6';
        successDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <span>${message}</span>
            </div>
        `;
        container.insertBefore(successDiv, container.firstChild);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            successDiv.remove();
        }, 5000);
    }
}

/**
 * Show error message
 */
function showError(message) {
    const container = document.querySelector('.login-card');
    if (container) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message rounded-lg p-4 mb-6';
        errorDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <span>${message}</span>
            </div>
        `;
        container.insertBefore(errorDiv, container.firstChild);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }
}

/**
 * Auto-focus on username field
 */
function focusUsername() {
    const usernameField = document.getElementById('username');
    if (usernameField) {
        usernameField.focus();
    }
}