// Sidebar toggle function
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');
    const hamburgerIcon = document.getElementById('hamburgerIcon');
    const closeIcon = document.getElementById('closeIcon');
    
    if (!sidebar) return;
    
    // Get direction from sidebar data attribute
    const direction = sidebar.getAttribute('data-direction') || 'ltr';
    const isRTL = direction === 'rtl';
    
    // Use appropriate transform class based on direction
    const hideClass = isRTL ? 'translate-x-full' : '-translate-x-full';
    const isOpen = !sidebar.classList.contains(hideClass);
    
    // Toggle sidebar
    sidebar.classList.toggle(hideClass);
    overlay.classList.toggle('hidden');
    
    // Toggle icons with smooth animation
    if (hamburgerIcon && closeIcon) {
        if (isOpen) {
            // Closing: Show hamburger, hide close
            hamburgerIcon.classList.remove('opacity-0', 'rotate-90');
            hamburgerIcon.classList.add('opacity-100', 'rotate-0');
            closeIcon.classList.remove('opacity-100', 'rotate-0');
            closeIcon.classList.add('opacity-0', 'rotate-90');
        } else {
            // Opening: Hide hamburger, show close
            hamburgerIcon.classList.remove('opacity-100', 'rotate-0');
            hamburgerIcon.classList.add('opacity-0', 'rotate-90');
            closeIcon.classList.remove('opacity-0', 'rotate-90');
            closeIcon.classList.add('opacity-100', 'rotate-0');
        }
    }
    
    // Adjust main content margin
    if (sidebar.classList.contains(hideClass)) {
        mainContent.classList.remove('main-content-with-sidebar');
        mainContent.classList.add('main-content-without-sidebar');
    } else {
        mainContent.classList.remove('main-content-without-sidebar');
        mainContent.classList.add('main-content-with-sidebar');
    }
}

// Project filtering
function filterProjects(type) {
    const projects = document.querySelectorAll('.project-item');
    const buttons = document.querySelectorAll('.filter-btn');
    
    // Update button states
    buttons.forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    });
    
    event.target.classList.add('active', 'bg-blue-600', 'text-white');
    event.target.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    
    // Filter projects
    projects.forEach(project => {
        if (type === 'all' || project.dataset.type === type) {
            project.style.display = 'block';
            project.classList.add('fade-in');
        } else {
            project.style.display = 'none';
        }
    });
}

// Smooth scrolling for anchor links (only for pure hash links, not handled by navbar)
document.querySelectorAll('a[href^="#"]:not(.nav-link)').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        // Only handle if it's a pure hash link (not index.php#section)
        if (href && href.startsWith('#') && href.length > 1) {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                const navbarHeight = 64;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        }
    });
});

// Initialize animations on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
        }
    });
}, observerOptions);

// Observe all project cards
document.querySelectorAll('.project-card').forEach(card => {
    observer.observe(card);
});

// Hero Image Slider
class HeroSlider {
    constructor() {
        this.currentSlide = 0;
        this.slides = document.querySelectorAll('.slide');
        this.dots = document.querySelectorAll('.slider-dot');
        this.prevBtn = document.querySelector('.slider-arrow.prev');
        this.nextBtn = document.querySelector('.slider-arrow.next');
        this.autoSlideInterval = null;
        
        this.init();
    }
    
    init() {
        if (this.slides.length === 0) return;
        
        this.showSlide(0);
        this.bindEvents();
        this.startAutoSlide();
    }
    
    bindEvents() {
        // Navigation dots
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => this.goToSlide(index));
        });
        
        // Arrow navigation
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => this.previousSlide());
        }
        
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => this.nextSlide());
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.previousSlide();
            if (e.key === 'ArrowRight') this.nextSlide();
        });
        
        // Pause on hover
        const sliderContainer = document.querySelector('.slider-container');
        if (sliderContainer) {
            sliderContainer.addEventListener('mouseenter', () => this.stopAutoSlide());
            sliderContainer.addEventListener('mouseleave', () => this.startAutoSlide());
        }
    }
    
    showSlide(index) {
        // Hide all slides
        this.slides.forEach(slide => slide.classList.remove('active'));
        this.dots.forEach(dot => dot.classList.remove('active'));
        
        // Show current slide
        if (this.slides[index]) {
            this.slides[index].classList.add('active');
        }
        if (this.dots[index]) {
            this.dots[index].classList.add('active');
        }
        
        this.currentSlide = index;
    }
    
    nextSlide() {
        const nextIndex = (this.currentSlide + 1) % this.slides.length;
        this.goToSlide(nextIndex);
    }
    
    previousSlide() {
        const prevIndex = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
        this.goToSlide(prevIndex);
    }
    
    goToSlide(index) {
        this.showSlide(index);
        this.restartAutoSlide();
    }
    
    startAutoSlide() {
        this.stopAutoSlide();
        this.autoSlideInterval = setInterval(() => {
            this.nextSlide();
        }, 5000); // Change slide every 5 seconds
    }
    
    stopAutoSlide() {
        if (this.autoSlideInterval) {
            clearInterval(this.autoSlideInterval);
            this.autoSlideInterval = null;
        }
    }
    
    restartAutoSlide() {
        this.stopAutoSlide();
        this.startAutoSlide();
    }
}

// Initialize sidebar state
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');
    const hamburgerIcon = document.getElementById('hamburgerIcon');
    const closeIcon = document.getElementById('closeIcon');
    
    if (!sidebar) return;
    
    // Get direction from sidebar data attribute
    const direction = sidebar.getAttribute('data-direction') || 'ltr';
    const isRTL = direction === 'rtl';
    const hideClass = isRTL ? 'translate-x-full' : '-translate-x-full';
    
    // Ensure sidebar starts closed - use appropriate transform based on direction
    sidebar.classList.remove('-translate-x-full', 'translate-x-full');
    sidebar.classList.add(hideClass);
    overlay.classList.add('hidden');
    mainContent.classList.add('main-content-without-sidebar');
    
    // Reset icons to default state (hamburger visible, close hidden)
    if (hamburgerIcon && closeIcon) {
        hamburgerIcon.classList.remove('opacity-0', 'rotate-90');
        hamburgerIcon.classList.add('opacity-100', 'rotate-0');
        closeIcon.classList.remove('opacity-100', 'rotate-0');
        closeIcon.classList.add('opacity-0', 'rotate-90');
    }
    
    // Initialize hero slider
    new HeroSlider();
    
});
