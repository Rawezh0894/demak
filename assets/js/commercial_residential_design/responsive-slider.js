// Responsive Slider System for Commercial Residential Design
// Handles mobile touch/swipe, tablet, and desktop interactions

(function() {
    'use strict';
    
    // Detect device type
    function getDeviceType() {
        const width = window.innerWidth;
        if (width < 600) return 'mobile';
        if (width < 1024) return 'tablet';
        return 'desktop';
    }
    
    // Check if touch device
    function isTouchDevice() {
        return ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
    }
    
    // Initialize responsive slider system
    function initializeResponsiveSliders() {
        const deviceType = getDeviceType();
        const isTouch = isTouchDevice();
        
        console.log('Device type:', deviceType, 'Touch:', isTouch);
        
        // Get all slider containers
        const sliderContainers = document.querySelectorAll('.projects-slider-container');
        
        sliderContainers.forEach(container => {
            const slider = container.querySelector('.projects-slider');
            const categoryKey = container.closest('.category-section')?.getAttribute('data-category');
            
            if (!slider || !categoryKey) return;
            
            // Mobile: Grid layout with swipe support
            if (deviceType === 'mobile') {
                setupMobileLayout(slider, container, categoryKey, isTouch);
            }
            // Tablet: Hybrid (slider with touch)
            else if (deviceType === 'tablet') {
                setupTabletLayout(slider, container, categoryKey, isTouch);
            }
            // Desktop: Full slider with hover effects
            else {
                setupDesktopLayout(slider, container, categoryKey);
            }
        });
    }
    
    // Mobile: Grid layout with swipe support
    function setupMobileLayout(slider, container, categoryKey, isTouch) {
        // Add mobile class for CSS
        container.classList.add('mobile-layout');
        slider.classList.add('mobile-slider');
        
        // Change slider to grid on mobile
        slider.style.display = 'grid';
        slider.style.gridTemplateColumns = '1fr';
        slider.style.gap = '1.5rem';
        slider.style.transform = 'none';
        slider.style.transition = 'none';
        
        // Hide arrows on mobile (use swipe instead)
        const arrows = container.querySelectorAll('.slider-arrow');
        arrows.forEach(arrow => {
            arrow.style.display = 'none';
        });
        
        // Show all projects in grid
        const slides = slider.querySelectorAll('.project-slide');
        slides.forEach(slide => {
            slide.style.display = 'flex';
            slide.style.width = '100%';
            slide.style.flexDirection = 'column';
        });
        
        // Add swipe support for image galleries
        if (isTouch) {
            setupTouchGestures(slider, categoryKey);
        }
        
        // Update counter to show total
        const counter = container.querySelector('.slide-counter');
        if (counter) {
            const totalSlides = counter.querySelector('.total-slides');
            if (totalSlides) {
                totalSlides.textContent = slides.length;
                counter.querySelector('.current-slide').textContent = slides.length;
            }
        }
    }
    
    // Tablet: Hybrid slider with touch support
    function setupTabletLayout(slider, container, categoryKey, isTouch) {
        container.classList.add('tablet-layout');
        slider.classList.add('tablet-slider');
        
        // Keep slider but optimize for tablet
        slider.style.display = 'flex';
        slider.style.transform = 'translateX(0%)';
        
        // Make arrows more prominent
        const arrows = container.querySelectorAll('.slider-arrow');
        arrows.forEach(arrow => {
            arrow.style.display = 'flex';
            arrow.style.width = '3.5rem';
            arrow.style.height = '3.5rem';
        });
        
        // Add touch swipe support
        if (isTouch) {
            setupTouchSwipe(slider, container, categoryKey);
        }
    }
    
    // Desktop: Full slider experience
    function setupDesktopLayout(slider, container, categoryKey) {
        container.classList.add('desktop-layout');
        slider.classList.add('desktop-slider');
        
        // Ensure slider is in flex mode
        slider.style.display = 'flex';
        
        // Make arrows prominent
        const arrows = container.querySelectorAll('.slider-arrow');
        arrows.forEach(arrow => {
            arrow.style.display = 'flex';
            arrow.style.width = '4rem';
            arrow.style.height = '4rem';
        });
    }
    
    // Setup touch gestures for mobile image galleries
    function setupTouchGestures(slider, categoryKey) {
        const imageGalleries = slider.querySelectorAll('.project-image-gallery');
        
        imageGalleries.forEach(gallery => {
            const mainImage = gallery.querySelector('.main-image');
            if (!mainImage) return;
            
            let touchStartX = 0;
            let touchEndX = 0;
            let currentImageIndex = 0;
            
            // Get all images for this gallery
            const projectId = gallery.closest('.project-slide')?.getAttribute('data-project-id');
            if (!projectId) return;
            
            const thumbnails = gallery.querySelectorAll('.thumbnail-item');
            const totalImages = thumbnails.length;
            
            if (totalImages <= 1) return; // No need for swipe if only one image
            
            mainImage.addEventListener('touchstart', function(e) {
                touchStartX = e.touches[0].clientX;
            }, { passive: true });
            
            mainImage.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].clientX;
                handleSwipe();
            }, { passive: true });
            
            function handleSwipe() {
                const swipeDistance = touchStartX - touchEndX;
                const minSwipeDistance = 50; // Minimum distance for swipe
                
                if (Math.abs(swipeDistance) < minSwipeDistance) return;
                
                if (swipeDistance > 0) {
                    // Swipe left - next image
                    currentImageIndex = (currentImageIndex + 1) % totalImages;
                } else {
                    // Swipe right - previous image
                    currentImageIndex = (currentImageIndex - 1 + totalImages) % totalImages;
                }
                
                // Change image
                if (typeof window.changeMainImage === 'function') {
                    window.changeMainImage(parseInt(projectId), currentImageIndex);
                }
            }
        });
    }
    
    // Setup touch swipe for tablet slider
    function setupTouchSwipe(slider, container, categoryKey) {
        let touchStartX = 0;
        let touchEndX = 0;
        let isDraggingSlider = false;
        
        slider.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            isDraggingSlider = true;
            slider.style.transition = 'none';
        }, { passive: true });
        
        slider.addEventListener('touchmove', function(e) {
            if (!isDraggingSlider) return;
            
            const touchX = e.touches[0].clientX;
            const diff = touchStartX - touchX;
            const containerWidth = container.offsetWidth;
            const diffPercent = (diff / containerWidth) * 100;
            const currentTransform = getCurrentTransform(slider);
            
            // Apply transform while dragging (in percentage)
            slider.style.transform = `translateX(${currentTransform - diffPercent}%)`;
        }, { passive: true });
        
        slider.addEventListener('touchend', function(e) {
            if (!isDraggingSlider) return;
            
            isDraggingSlider = false;
            touchEndX = e.changedTouches[0].clientX;
            slider.style.transition = 'transform 0.3s ease';
            
            const swipeDistance = touchStartX - touchEndX;
            const minSwipeDistance = 50;
            
            if (Math.abs(swipeDistance) > minSwipeDistance) {
                if (swipeDistance > 0) {
                    // Swipe left - next slide
                    if (typeof window.nextSlide === 'function') {
                        window.nextSlide(categoryKey);
                    }
                } else {
                    // Swipe right - previous slide
                    if (typeof window.prevSlide === 'function') {
                        window.prevSlide(categoryKey);
                    }
                }
            } else {
                // Reset to current slide
                const counter = container.querySelector('.slide-counter .current-slide');
                if (counter) {
                    const currentIndex = parseInt(counter.textContent) - 1;
                    const translateX = -currentIndex * 100;
                    slider.style.transform = `translateX(${translateX}%)`;
                }
            }
        }, { passive: true });
    }
    
    // Get current transform value as percentage
    function getCurrentTransform(element) {
        const style = window.getComputedStyle(element);
        const transform = style.transform || style.webkitTransform;
        
        if (transform === 'none' || !transform) return 0;
        
        // Check if it's translateX with percentage
        const translateXMatch = transform.match(/translateX\(([^)]+)\)/);
        if (translateXMatch) {
            const value = translateXMatch[1];
            if (value.includes('%')) {
                return parseFloat(value);
            } else {
                // Convert pixels to percentage
                const containerWidth = element.parentElement?.offsetWidth || element.offsetWidth;
                const pixels = parseFloat(value);
                return (pixels / containerWidth) * 100;
            }
        }
        
        // Fallback: try to parse matrix
        const matrixMatch = transform.match(/matrix.*\((.+)\)/);
        if (matrixMatch) {
            const matrixValues = matrixMatch[1].split(', ');
            const containerWidth = element.parentElement?.offsetWidth || element.offsetWidth;
            const pixels = parseFloat(matrixValues[4] || matrixValues[12] || 0);
            return (pixels / containerWidth) * 100;
        }
        
        return 0;
    }
    
    // Handle window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            initializeResponsiveSliders();
        }, 250);
    });
    
    // Initialize on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeResponsiveSliders, 100);
        });
    } else {
        setTimeout(initializeResponsiveSliders, 100);
    }
    
    // Make function globally available for re-initialization after filters
    window.initializeResponsiveSliders = initializeResponsiveSliders;
    
})();

