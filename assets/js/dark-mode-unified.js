/**
 * Unified Dark Mode Handler
 * Works consistently across all browsers (Chrome, Edge, Firefox, Safari)
 * Supports both Tailwind CSS 'dark' class and custom 'dark-mode' class
 */

(function() {
    'use strict';

    // Configuration
    const STORAGE_KEY = 'darkMode';
    const HTML_DARK_CLASS = 'dark'; // For Tailwind CSS
    const BODY_DARK_CLASS = 'dark-mode'; // For custom styles
    
    /**
     * Get current dark mode state from localStorage
     * Returns: 'true', 'false', or null (not set)
     */
    function getStoredDarkMode() {
        try {
            return localStorage.getItem(STORAGE_KEY);
        } catch (e) {
            console.warn('localStorage not available:', e);
            return null;
        }
    }

    /**
     * Save dark mode state to localStorage
     */
    function saveDarkMode(isDark) {
        try {
            localStorage.setItem(STORAGE_KEY, isDark ? 'true' : 'false');
        } catch (e) {
            console.warn('Failed to save to localStorage:', e);
        }
    }

    /**
     * Get system preference for dark mode
     * Returns: true if system prefers dark, false otherwise
     */
    function getSystemPreference() {
        if (window.matchMedia) {
            try {
                return window.matchMedia('(prefers-color-scheme: dark)').matches;
            } catch (e) {
                console.warn('matchMedia not supported:', e);
            }
        }
        return false;
    }

    /**
     * Apply dark mode classes to HTML and Body
     * This ensures compatibility with both Tailwind CSS and custom styles
     */
    function applyDarkMode(isDark) {
        const html = document.documentElement;
        const body = document.body;

        if (isDark) {
            html.classList.add(HTML_DARK_CLASS);
            body.classList.add(BODY_DARK_CLASS);
        } else {
            html.classList.remove(HTML_DARK_CLASS);
            body.classList.remove(BODY_DARK_CLASS);
        }

        // Dispatch custom event for other scripts
        const event = new CustomEvent('darkModeChanged', {
            detail: { isDark: isDark }
        });
        document.dispatchEvent(event);
    }

    /**
     * Update icon visibility based on dark mode state
     */
    function updateIcons(isDark) {
        const lightIcon = document.getElementById('lightModeIcon');
        const darkIcon = document.getElementById('darkModeIcon');
        const toggle = document.getElementById('darkModeToggle');

        if (lightIcon && darkIcon) {
            if (isDark) {
                // Dark mode: hide sun, show moon
                lightIcon.classList.add('hidden');
                lightIcon.classList.remove('opacity-100', 'rotate-0', 'scale-100');
                lightIcon.classList.add('opacity-0', 'rotate-90', 'scale-0');
                
                darkIcon.classList.remove('hidden');
                darkIcon.classList.remove('opacity-0', 'rotate-90', 'scale-0');
                darkIcon.classList.add('opacity-100', 'rotate-0', 'scale-100');
            } else {
                // Light mode: show sun, hide moon
                lightIcon.classList.remove('hidden');
                lightIcon.classList.remove('opacity-0', 'rotate-90', 'scale-0');
                lightIcon.classList.add('opacity-100', 'rotate-0', 'scale-100');
                
                darkIcon.classList.add('hidden');
                darkIcon.classList.remove('opacity-100', 'rotate-0', 'scale-100');
                darkIcon.classList.add('opacity-0', 'rotate-90', 'scale-0');
            }
        }

        // Update toggle title if exists
        if (toggle) {
            // You can customize these titles based on your translations
            toggle.title = isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        }
    }

    /**
     * Initialize dark mode on page load
     * Priority: localStorage > system preference > default (light)
     */
    function initializeDarkMode() {
        const stored = getStoredDarkMode();
        let isDark = false;

        if (stored !== null) {
            // Use stored preference
            isDark = stored === 'true';
        } else {
            // Use system preference if available
            isDark = getSystemPreference();
            // Save system preference to localStorage
            saveDarkMode(isDark);
        }

        // Apply dark mode immediately (before page render to prevent flash)
        applyDarkMode(isDark);
        updateIcons(isDark);
    }

    /**
     * Toggle dark mode
     * This is the main function called by toggle buttons
     */
    function toggleDarkMode() {
        const html = document.documentElement;
        const body = document.body;
        
        // Check current state (check both html and body for compatibility)
        const isCurrentlyDark = html.classList.contains(HTML_DARK_CLASS) || 
                               body.classList.contains(BODY_DARK_CLASS);
        
        const newDarkState = !isCurrentlyDark;
        
        // Apply new state
        applyDarkMode(newDarkState);
        updateIcons(newDarkState);
        
        // Save to localStorage
        saveDarkMode(newDarkState);
    }

    /**
     * Listen for system preference changes
     * Only applies if user hasn't manually set a preference
     */
    function setupSystemPreferenceListener() {
        if (window.matchMedia) {
            try {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                
                // Use addEventListener if available (modern browsers)
                if (mediaQuery.addEventListener) {
                    mediaQuery.addEventListener('change', (e) => {
                        const stored = getStoredDarkMode();
                        // Only apply system preference if user hasn't set a preference
                        if (stored === null) {
                            const isDark = e.matches;
                            applyDarkMode(isDark);
                            updateIcons(isDark);
                            saveDarkMode(isDark);
                        }
                    });
                } 
                // Fallback for older browsers (Edge, older Chrome)
                else if (mediaQuery.addListener) {
                    mediaQuery.addListener((e) => {
                        const stored = getStoredDarkMode();
                        if (stored === null) {
                            const isDark = e.matches;
                            applyDarkMode(isDark);
                            updateIcons(isDark);
                            saveDarkMode(isDark);
                        }
                    });
                }
            } catch (e) {
                console.warn('Failed to setup system preference listener:', e);
            }
        }
    }

    /**
     * Apply dark mode immediately in <head> to prevent flash
     * This script should be loaded in <head> before page content
     */
    function applyDarkModeImmediate() {
        const stored = getStoredDarkMode();
        let isDark = false;

        if (stored !== null) {
            isDark = stored === 'true';
        } else {
            isDark = getSystemPreference();
        }

        // Apply to HTML element immediately
        if (isDark) {
            document.documentElement.classList.add(HTML_DARK_CLASS);
        } else {
            document.documentElement.classList.remove(HTML_DARK_CLASS);
        }
    }

    // Apply immediately if script is in <head>
    if (document.readyState === 'loading') {
        applyDarkModeImmediate();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initializeDarkMode();
            setupSystemPreferenceListener();
        });
    } else {
        // DOM already loaded
        initializeDarkMode();
        setupSystemPreferenceListener();
    }

    // Make functions globally available
    window.toggleDarkMode = toggleDarkMode;
    window.initializeDarkMode = initializeDarkMode;
    window.getDarkModeState = function() {
        return document.documentElement.classList.contains(HTML_DARK_CLASS) ||
               document.body.classList.contains(BODY_DARK_CLASS);
    };

    // Export for module systems (if needed)
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = {
            toggleDarkMode: toggleDarkMode,
            initializeDarkMode: initializeDarkMode,
            getDarkModeState: window.getDarkModeState
        };
    }

})();

