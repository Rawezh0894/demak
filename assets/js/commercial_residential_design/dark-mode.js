// Dark Mode Toggle for Commercial Residential Design Public Page

(function() {
    'use strict';
    
    function toggleDarkMode() {
        const html = document.documentElement;
        const body = document.body;
        const lightIcon = document.getElementById('lightModeIcon');
        const darkIcon = document.getElementById('darkModeIcon');
        
        if (html.classList.contains('dark') || body.classList.contains('dark-mode')) {
            html.classList.remove('dark');
            body.classList.remove('dark-mode');
            if (lightIcon) lightIcon.classList.remove('hidden');
            if (darkIcon) darkIcon.classList.add('hidden');
            localStorage.setItem('darkMode', 'false');
        } else {
            html.classList.add('dark');
            body.classList.add('dark-mode');
            if (lightIcon) lightIcon.classList.add('hidden');
            if (darkIcon) darkIcon.classList.remove('hidden');
            localStorage.setItem('darkMode', 'true');
        }
    }
    
    function initializeDarkMode() {
        const darkMode = localStorage.getItem('darkMode');
        const html = document.documentElement;
        const body = document.body;
        const lightIcon = document.getElementById('lightModeIcon');
        const darkIcon = document.getElementById('darkModeIcon');
        
        if (darkMode === 'true') {
            html.classList.add('dark');
            body.classList.add('dark-mode');
            if (lightIcon) lightIcon.classList.add('hidden');
            if (darkIcon) darkIcon.classList.remove('hidden');
        } else {
            html.classList.remove('dark');
            body.classList.remove('dark-mode');
            if (lightIcon) lightIcon.classList.remove('hidden');
            if (darkIcon) darkIcon.classList.add('hidden');
        }
    }
    
    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeDarkMode);
    } else {
        initializeDarkMode();
    }
    
    // Make function globally available
    window.toggleDarkMode = toggleDarkMode;
    
})();

