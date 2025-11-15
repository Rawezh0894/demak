// Design & Reconstruction Page JavaScript

// Prevent redeclaration if script is loaded multiple times
(function() {
    'use strict';
    
    // Check if already initialized
    if (typeof window.designReconstructionInitialized !== 'undefined') {
        console.log('Design Reconstruction JS already loaded, skipping re-initialization');
        return;
    }
    
    window.designReconstructionInitialized = true;

// Project data from PHP (will be set by design-reconstruction.php)
var projectsData = {};

// Global variables for current project
var currentProjectId = null;
var currentImageIndex = 0;
var currentProjectImages = [];

// Image zoom modal variables
var currentZoomScale = 1;
var isDragging = false;
var dragStart = { x: 0, y: 0 };
var imageOffset = { x: 0, y: 0 };

// Function to set projects data (called from PHP) - Moved to top for early availability
function setProjectsData(data) {
    console.log('Setting projects data:', data);
    projectsData = data;
    
    // Initialize sliders if not already initialized
    if (!slidersInitialized) {
        console.log('Initializing sliders from setProjectsData...');
        initializeSliders();
        initializeBackToTop();
        initializeImageOptimization();
        initializeImageZoom();
        slidersInitialized = true;
    } else {
        console.log('Sliders already initialized, skipping...');
    }
}

// Make setProjectsData available globally
window.setProjectsData = setProjectsData;

// Global variable to track initialization
var slidersInitialized = false;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Design Reconstruction JS: DOM Content Loaded');
    // Only initialize if projectsData is already set and not already initialized
    if (Object.keys(projectsData).length > 0 && !slidersInitialized) {
        console.log('Projects data already set, initializing...');
    initializeSliders();
    initializeBackToTop();
        initializeImageOptimization();
        initializeImageZoom();
        slidersInitialized = true;
    } else if (slidersInitialized) {
        console.log('Sliders already initialized, skipping...');
    } else {
        console.log('No projects data yet, waiting for setProjectsData...');
    }
});

// Initialize sliders for each category
function initializeSliders() {
    Object.keys(projectsData).forEach(categoryKey => {
        const category = projectsData[categoryKey];
        if (category.projects && category.projects.length > 0) {
            initializeCategorySlider(categoryKey, category.projects);
        }
    });
}

// Initialize slider for a specific category
function initializeCategorySlider(categoryKey, projects) {
    const slider = document.getElementById(`slider-${categoryKey}`);
    const counter = document.getElementById(`counter-${categoryKey}`);
    const dots = document.getElementById(`dots-${categoryKey}`);
    
    console.log(`Initializing slider for category: ${categoryKey}`);
    console.log('Slider element:', slider);
    console.log('Counter element:', counter);
    console.log('Dots element:', dots);
    console.log('Projects count:', projects.length);
    
    if (!slider || !counter || !dots) {
        console.error(`Missing elements for category ${categoryKey}:`, {
            slider: !!slider,
            counter: !!counter,
            dots: !!dots
        });
        return;
    }
    
    // Set total slides
    const totalSlides = projects.length;
    counter.querySelector('.total-slides').textContent = totalSlides;
    
    // Ensure counter starts at slide 1
    counter.querySelector('.current-slide').textContent = 1;
    updateSlideProgressElement(counter, 0);
    
    // Initialize dots
    dots.innerHTML = '';
    for (let i = 0; i < totalSlides; i++) {
        const dot = document.createElement('button');
        dot.className = `slider-dot ${i === 0 ? 'active' : ''}`;
        dot.onclick = () => goToSlide(categoryKey, i);
        dots.appendChild(dot);
    }
    
    // Ensure slider starts at first slide with a small delay to override any CSS
    setTimeout(() => {
        slider.style.transform = 'translateX(0%)';
        console.log('Slider initial transform set to translateX(0%) with delay');
    }, 10);
    
    // Debug slider positioning
    console.log(`Slider ${categoryKey} initialized with ${totalSlides} slides`);
}

// Navigation functions
function nextSlide(categoryKey) {
    const slider = document.getElementById(`slider-${categoryKey}`);
    const counter = document.getElementById(`counter-${categoryKey}`);
    const dots = document.getElementById(`dots-${categoryKey}`);
    
    if (!slider || !counter || !dots) {
        return;
    }
    
    const currentSlide = parseInt(counter.querySelector('.current-slide').textContent) - 1;
    const totalSlides = parseInt(counter.querySelector('.total-slides').textContent);
    const nextSlide = (currentSlide + 1) % totalSlides;
    
    goToSlide(categoryKey, nextSlide);
}

function prevSlide(categoryKey) {
    const slider = document.getElementById(`slider-${categoryKey}`);
    const counter = document.getElementById(`counter-${categoryKey}`);
    const dots = document.getElementById(`dots-${categoryKey}`);
    
    if (!slider || !counter || !dots) {
        return;
    }
    
    const currentSlide = parseInt(counter.querySelector('.current-slide').textContent) - 1;
    const totalSlides = parseInt(counter.querySelector('.total-slides').textContent);
    const prevSlide = currentSlide === 0 ? totalSlides - 1 : currentSlide - 1;
    
    goToSlide(categoryKey, prevSlide);
}

function goToSlide(categoryKey, slideIndex) {
    const slider = document.getElementById(`slider-${categoryKey}`);
    const counter = document.getElementById(`counter-${categoryKey}`);
    const dots = document.getElementById(`dots-${categoryKey}`);
    
    if (!slider || !counter || !dots) {
        console.error('Missing elements in goToSlide');
        return;
    }
    
    const totalSlides = parseInt(counter.querySelector('.total-slides').textContent);
    
    if (slideIndex < 0 || slideIndex >= totalSlides) {
        console.error(`Invalid slide index: ${slideIndex}, total slides: ${totalSlides}`);
        return;
    }
    
    // Update slider position
    const translateX = -slideIndex * 100;
    slider.style.transform = `translateX(${translateX}%)`;
    
    // Force reload images in the target slide
    const targetSlide = slider.children[slideIndex];
    if (targetSlide) {
        const images = targetSlide.querySelectorAll('img');
        images.forEach(img => {
            if (!img.complete) {
                const src = img.src;
                img.src = '';
                img.src = src;
            }
            optimizeImage(img);
        });
    }
    
    // Update counter
    counter.querySelector('.current-slide').textContent = slideIndex + 1;
    updateSlideProgressElement(counter, slideIndex);
    
    // Update dots
    dots.querySelectorAll('.slider-dot').forEach((dot, index) => {
        dot.classList.toggle('active', index === slideIndex);
    });
}

function updateSlideProgressElement(counter, slideIndex) {
    if (!counter) return;

    const totalEl = counter.querySelector('.total-slides');
    const fillEl = counter.querySelector('.slide-progress-fill');

    if (!totalEl || !fillEl) return;

    const totalSlides = parseInt(totalEl.textContent || '1', 10);
    const safeTotal = Math.max(totalSlides, 1);
    const progress = ((slideIndex + 1) / safeTotal) * 100;

    fillEl.style.width = `${Math.min(Math.max(progress, 0), 100)}%`;
}

// Image gallery functions
function toggleImageGallery(projectId) {
    const thumbnails = document.getElementById(`thumbnails-${projectId}`);
    if (thumbnails) {
        thumbnails.style.display = thumbnails.style.display === 'none' ? 'flex' : 'none';
    }
}

function changeMainImage(projectId, imageIndex) {
    const project = findProjectById(projectId);
    if (!project) return;

    const galleryImages = getProjectGalleryImages(project);
    if (!galleryImages.length) return;

    const safeIndex = Math.max(0, Math.min(imageIndex, galleryImages.length - 1));
    const newImageSrc = galleryImages[safeIndex];

    const projectRoot = document.querySelector(`[data-project-id="${projectId}"]`);
    if (!projectRoot) return;

    const mainImage = projectRoot.querySelector('.main-image');
    const imageCounter = projectRoot.querySelector('.image-counter');

    if (mainImage && newImageSrc) {
        mainImage.src = newImageSrc;
        optimizeImage(mainImage);
    }

    if (imageCounter) {
        imageCounter.querySelector('.current-image').textContent = safeIndex + 1;
        imageCounter.querySelector('.total-images').textContent = galleryImages.length;
    }

    const thumbnails = document.getElementById(`thumbnails-${projectId}`);
    if (thumbnails) {
        thumbnails.querySelectorAll('.thumbnail-item').forEach((thumb, index) => {
            thumb.classList.toggle('active', index === safeIndex);
        });
    }
}

// Project details modal functions
function showProjectDetails(projectId) {
    const project = findProjectById(projectId);
    if (!project) return;

    currentProjectId = projectId;
    currentProjectImages = getProjectGalleryImages(project);
    currentImageIndex = 0;
    
    // Update modal content
    const projectName = getLocalizedValue(project, 'name');
    const projectDescription = getLocalizedValue(project, 'description');
    const projectType = getLocalizedValue(project, 'project_type');
    const projectEngineer = getLocalizedValue(project, 'engineer');

    document.getElementById('modalTitle').textContent = projectName || '-';
    document.getElementById('modalPrice').textContent = project.price || '-';
    document.getElementById('modalDuration').textContent = project.duration || '-';
    document.getElementById('modalDescription').textContent = projectDescription || '-';
    const modalType = document.getElementById('modalType');
    const modalEngineer = document.getElementById('modalEngineer');
    if (modalType) {
        modalType.textContent = projectType || '-';
    }
    if (modalEngineer) {
        modalEngineer.textContent = projectEngineer || '-';
    }

    // Update features
    const featuresList = document.getElementById('modalFeatures');
    featuresList.innerHTML = '';
    if (project.features && project.features.length > 0) {
        project.features.forEach(feature => {
            const li = document.createElement('li');
            li.textContent = feature;
            featuresList.appendChild(li);
        });
    }
    
    // Update images
    updateModalImages();

    // Show modal
    document.getElementById('projectModal').classList.remove('hidden');
}

function updateModalImages() {
    if (!currentProjectImages || currentProjectImages.length === 0) return;
    
    const modalImage = document.getElementById('modalImage');
    const imageCounter = document.querySelector('.modal-image-counter');
    const thumbnails = document.getElementById('modalThumbnails');
    
    if (modalImage) {
        modalImage.src = currentProjectImages[currentImageIndex];
    }
    
    if (imageCounter) {
        imageCounter.querySelector('.modal-current-image').textContent = currentImageIndex + 1;
        imageCounter.querySelector('.modal-total-images').textContent = currentProjectImages.length;
    }
    
    if (thumbnails) {
        thumbnails.innerHTML = '';
        currentProjectImages.forEach((image, index) => {
            const thumb = document.createElement('div');
            thumb.className = `thumbnail-item ${index === currentImageIndex ? 'active' : ''}`;
            thumb.onclick = () => {
                currentImageIndex = index;
                updateModalImages();
            };
            
            const img = document.createElement('img');
            img.src = image;
            img.className = 'thumbnail-image';
            thumb.appendChild(img);
            
            thumbnails.appendChild(thumb);
        });
    }
}

function toggleModalImageGallery() {
    const thumbnails = document.getElementById('modalThumbnails');
    if (thumbnails) {
        thumbnails.style.display = thumbnails.style.display === 'none' ? 'flex' : 'none';
    }
}

function prevModalImage() {
    if (currentProjectImages.length === 0) return;
    currentImageIndex = currentImageIndex === 0 ? currentProjectImages.length - 1 : currentImageIndex - 1;
    updateModalImages();
}

function nextModalImage() {
    if (currentProjectImages.length === 0) return;
    currentImageIndex = (currentImageIndex + 1) % currentProjectImages.length;
    updateModalImages();
}

function closeProjectModal() {
    document.getElementById('projectModal').classList.add('hidden');
}

// Utility functions
function findProjectById(projectId) {
    for (const categoryKey in projectsData) {
        const category = projectsData[categoryKey];
        if (category.projects) {
            const project = category.projects.find(p => p.id == projectId);
            if (project) return project;
        }
    }
    return null;
}

function getProjectGalleryImages(project) {
    if (!project) return [];
    const gallery = [];

    if (project.image) {
        gallery.push(project.image);
    }

    if (Array.isArray(project.images)) {
        project.images.forEach(img => {
            if (img && img !== project.image) {
                gallery.push(img);
            }
        });
    }

    if (!gallery.length) {
        gallery.push('https://via.placeholder.com/1200x800?text=No+Image');
    }

    return gallery;
}

function getLocalizedValue(project, field) {
    if (!project) return '';
    const lang = (document.documentElement && document.documentElement.lang) ? document.documentElement.lang : 'en';
    const localizedKey = `${field}_${lang}`;

    if (project[localizedKey] !== undefined && project[localizedKey] !== null && project[localizedKey] !== '') {
        return project[localizedKey];
    }

    return project[field] || '';
}

function requestQuote() {
    // Implementation for quote request
    alert('Quote request sent!');
}

function downloadProject() {
    // Implementation for project download
    alert('Download started!');
}

function contactUs() {
    // Scroll to contact section
    const contactSection = document.getElementById('contact-section');
    if (contactSection) {
        contactSection.scrollIntoView({ behavior: 'smooth' });
    }
}

// Back to top functionality
function initializeBackToTop() {
    const backToTopBtn = document.getElementById('backToTop');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        closeProjectModal();
    }
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (document.getElementById('projectModal').classList.contains('hidden')) return;
    
    switch(e.key) {
        case 'Escape':
            closeProjectModal();
            break;
        case 'ArrowLeft':
            prevModalImage();
            break;
        case 'ArrowRight':
            nextModalImage();
            break;
    }
});


// Image Optimization Functions
function initializeImageOptimization() {
    // Optimize all images on page load
    const images = document.querySelectorAll('img');
        images.forEach(img => {
        optimizeImage(img);
    });
    
    // Add intersection observer for lazy loading
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    optimizeImage(img);
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => {
            imageObserver.observe(img);
        });
    }
}

function optimizeImage(img) {
    if (!img) return;

    const isProjectVisual = img.classList.contains('project-image') ||
        img.classList.contains('main-image') ||
        img.classList.contains('main-project-image') ||
        img.classList.contains('thumbnail-image');

    if (isProjectVisual) {
        img.style.imageRendering = 'auto';
        img.style.objectFit = 'contain';
        img.style.objectPosition = 'center';
        img.style.maxWidth = '100%';
        img.style.maxHeight = '100%';
        img.style.transition = img.style.transition || 'opacity 0.3s ease';
        img.dataset.loaded = 'true';

        if (img.complete) {
            img.style.opacity = '1';
        } else {
            img.addEventListener('load', function handleLoad() {
                img.style.opacity = '1';
                img.removeEventListener('load', handleLoad);
            });
        }

        img.addEventListener('error', function handleError() {
            console.error('Image failed to load:', img.src);
            img.style.backgroundColor = '#f0f0f0';
            img.style.border = '2px dashed #ccc';
            img.alt = 'Image failed to load: ' + img.src;
            img.removeEventListener('error', handleError);
        });
    }
}

// Image Zoom Modal Functions
function initializeImageZoom() {
    const zoomModal = document.getElementById('imageZoomModal');
    if (!zoomModal) return;
    
    // Add click handler to slider images
    const images = document.querySelectorAll('.project-slide-image .project-image, .project-slide-image .main-image, .project-content-card .project-image, .project-content-card .main-image');
    images.forEach(img => {
        // Set cursor to pointer
        img.style.cursor = 'pointer';
        
        // Prevent any hover effects
        img.addEventListener('mouseenter', function(e) {
            e.stopPropagation();
        });
        
        // Add click handler to open zoom modal
        img.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openImageZoom(this.src);
        });
    });
}

function openImageZoom(imageSrc) {
    const modal = document.getElementById('imageZoomModal');
    const zoomImage = document.getElementById('zoomImage');
    
    if (modal && zoomImage) {
        zoomImage.src = imageSrc;
        resetZoom();
        // Use 'active' class if available, otherwise toggle 'hidden'
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        }
        modal.classList.add('active');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeImageZoom() {
    const modal = document.getElementById('imageZoomModal');
    
    if (modal) {
        modal.classList.remove('active');
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = '';
        resetZoom();
    }
}

function zoomIn() {
    currentZoomScale = Math.min(currentZoomScale * 1.2, 5);
    applyZoom();
}

function zoomOut() {
    currentZoomScale = Math.max(currentZoomScale / 1.2, 0.5);
    applyZoom();
}

function resetZoom() {
    currentZoomScale = 1;
    imageOffset = { x: 0, y: 0 };
    applyZoom();
}

function applyZoom() {
    const zoomImage = document.getElementById('zoomImage');
    
    if (zoomImage) {
        zoomImage.style.transform = `scale(${currentZoomScale}) translate(${imageOffset.x}px, ${imageOffset.y}px)`;
    }
}

// Tab switching function
function showTab(categoryKey, tabIndex) {
    const tabsContainer = document.getElementById(`tabs-${categoryKey}`);
    const contentContainer = document.getElementById(`content-${categoryKey}`);
    
    if (!tabsContainer || !contentContainer) {
        console.error('Missing tab elements for category:', categoryKey);
        return;
    }
    
    // Update tab buttons
    const tabButtons = tabsContainer.querySelectorAll('.tab-button');
    tabButtons.forEach((btn, index) => {
        if (index === tabIndex) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    // Update tab content
    const tabContents = contentContainer.querySelectorAll('.tab-content');
    tabContents.forEach((content, index) => {
        if (index === tabIndex) {
            content.classList.add('active');
        } else {
            content.classList.remove('active');
        }
    });
}

// Make functions globally available
window.nextSlide = nextSlide;
window.prevSlide = prevSlide;
window.goToSlide = goToSlide;
window.showTab = showTab;
window.toggleImageGallery = toggleImageGallery;
window.changeMainImage = changeMainImage;
window.showProjectDetails = showProjectDetails;
window.closeProjectModal = closeProjectModal;
window.toggleModalImageGallery = toggleModalImageGallery;
window.prevModalImage = prevModalImage;
window.nextModalImage = nextModalImage;
window.scrollToTop = scrollToTop;
window.openImageZoom = openImageZoom;
window.closeImageZoom = closeImageZoom;
window.zoomIn = zoomIn;
window.zoomOut = zoomOut;
window.resetZoom = resetZoom;
window.requestQuote = requestQuote;
window.contactUs = contactUs;

})(); // End IIFE