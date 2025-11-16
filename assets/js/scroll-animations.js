/**
 * Scroll Animations for Sections
 * ئەنیمەیشنی سکڕۆڵ بۆ بەشەکان
 */

(function() {
    'use strict';

    // Intersection Observer Options
    const observerOptions = {
        threshold: 0.15, // Trigger when 15% of element is visible
        rootMargin: '0px 0px -80px 0px' // Start animation slightly before element enters viewport
    };

    // Create Intersection Observer
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                // Add delay based on index for staggered effect
                setTimeout(() => {
                    entry.target.classList.add('animate-in');
                    // Stop observing once animated
                    sectionObserver.unobserve(entry.target);
                }, index * 100); // 100ms delay between each section
            }
        });
    }, observerOptions);

    // Initialize scroll animations
    function initScrollAnimations() {
        // Select all sections that should animate
        const sections = document.querySelectorAll('section.section-animate');
        
        sections.forEach((section) => {
            // Start observing - sections already have initial state from CSS
            sectionObserver.observe(section);
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initScrollAnimations);
    } else {
        initScrollAnimations();
    }

    // Re-initialize if new sections are added dynamically
    window.reinitScrollAnimations = function() {
        initScrollAnimations();
    };
})();

