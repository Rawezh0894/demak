// Interior Design Page JavaScript

// Project data from PHP (will be set by interior-design.php)
let projectsData = {};

// Global variables for current project
let currentProjectId = null;
let currentImageIndex = 0;
let currentProjectImages = [];

// Image zoom modal variables
let currentZoomScale = 1;
let isDragging = false;
let dragStart = { x: 0, y: 0 };
let imageOffset = { x: 0, y: 0 };

// Function to set projects data (called from PHP)
function setProjectsData(data) {
    console.log('Setting interior design projects data:', data);
    projectsData = data;
    
    // Initialize sliders if not already initialized
    if (!slidersInitialized) {
        console.log('Initializing interior design sliders from setProjectsData...');
        initializeSliders();
        initializeBackToTop();
        initializeImageOptimization();
        initializeImageZoom();
        slidersInitialized = true;
    }
}

// Make setProjectsData available globally
window.setProjectsData = setProjectsData;

// Global variable to track initialization
let slidersInitialized = false;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Interior Design JS: DOM Content Loaded');
    if (Object.keys(projectsData).length > 0 && !slidersInitialized) {
        console.log('Projects data already set, initializing...');
        initializeSliders();
        initializeBackToTop();
        initializeImageOptimization();
        initializeImageZoom();
        slidersInitialized = true;
    } else if (!slidersInitialized) {
        console.log('No projects data yet, waiting for setProjectsData...');
    }
});

// Initialize slider
function initializeSliders() {
    if (projectsData.projects && projectsData.projects.projects) {
        const projects = projectsData.projects.projects;
        if (projects.length > 0) {
            initializeCategorySlider('projects', projects);
        }
    }
}

// Initialize slider for projects
function initializeCategorySlider(categoryKey, projects) {
    const slider = document.getElementById(`slider-${categoryKey}`);
    const dots = document.getElementById(`dots-${categoryKey}`);
    
    if (!slider || !dots) {
        console.error(`Missing elements for ${categoryKey}`);
        return;
    }
    
    // Initialize dots
    dots.innerHTML = '';
    for (let i = 0; i < projects.length; i++) {
        const dot = document.createElement('button');
        dot.className = `slider-dot ${i === 0 ? 'active' : ''}`;
        dot.onclick = () => goToSlide(categoryKey, i);
        dots.appendChild(dot);
    }
    
    // Ensure slider starts at first slide
    setTimeout(() => {
        slider.style.transform = 'translateX(0%)';
    }, 10);
    
    console.log(`Slider ${categoryKey} initialized with ${projects.length} slides`);
}

// Navigation functions
function nextSlide(categoryKey) {
    const slider = document.getElementById(`slider-${categoryKey}`);
    const dots = document.getElementById(`dots-${categoryKey}`);
    
    if (!slider || !dots) return;
    
    const projects = getProjects(categoryKey);
    if (!projects || projects.length === 0) return;
    
    const currentSlide = Array.from(dots.querySelectorAll('.slider-dot')).findIndex(dot => dot.classList.contains('active'));
    const nextSlide = (currentSlide + 1) % projects.length;
    
    goToSlide(categoryKey, nextSlide);
}

function prevSlide(categoryKey) {
    const slider = document.getElementById(`slider-${categoryKey}`);
    const dots = document.getElementById(`dots-${categoryKey}`);
    
    if (!slider || !dots) return;
    
    const projects = getProjects(categoryKey);
    if (!projects || projects.length === 0) return;
    
    const currentSlide = Array.from(dots.querySelectorAll('.slider-dot')).findIndex(dot => dot.classList.contains('active'));
    const prevSlide = currentSlide === 0 ? projects.length - 1 : currentSlide - 1;
    
    goToSlide(categoryKey, prevSlide);
}

function goToSlide(categoryKey, slideIndex) {
    const slider = document.getElementById(`slider-${categoryKey}`);
    const dots = document.getElementById(`dots-${categoryKey}`);
    
    if (!slider || !dots) return;
    
    const projects = getProjects(categoryKey);
    if (!projects || slideIndex < 0 || slideIndex >= projects.length) return;
    
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
    
    // Update dots
    dots.querySelectorAll('.slider-dot').forEach((dot, index) => {
        dot.classList.toggle('active', index === slideIndex);
    });
    
    // Update tabs
    const tabs = document.getElementById(`tabs-${categoryKey}`);
    if (tabs) {
        tabs.querySelectorAll('.tab-button').forEach((tab, index) => {
            tab.classList.toggle('active', index === slideIndex);
        });
    }
}

// Tab navigation
function showTab(categoryKey, tabIndex) {
    goToSlide(categoryKey, tabIndex);
}

// Get projects for category
function getProjects(categoryKey) {
    if (projectsData.projects && projectsData.projects.projects) {
        return projectsData.projects.projects;
    }
    return [];
}

// Find project by ID
function findProjectById(projectId) {
    const projects = getProjects('projects');
    return projects.find(p => p.id == projectId);
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
    
    const mainImage = document.querySelector(`[data-project-id="${projectId}"] .main-image`);
    const imageCounter = document.querySelector(`[data-project-id="${projectId}"] .image-counter`);
    
    if (mainImage && imageCounter) {
        // Use the main image as the first image, then gallery images
        if (imageIndex === 0) {
            mainImage.src = project.image;
        } else {
            mainImage.src = project.images[imageIndex - 1];
        }
        
        // Optimize the new image
        optimizeImage(mainImage);
        
        imageCounter.querySelector('.current-image').textContent = imageIndex + 1;
        
        // Update thumbnail active state
        const thumbnails = document.getElementById(`thumbnails-${projectId}`);
        if (thumbnails) {
            thumbnails.querySelectorAll('.thumbnail-item').forEach((thumb, index) => {
                thumb.classList.toggle('active', index === imageIndex);
            });
        }
    }
}

// Project details modal functions
function showProjectDetails(projectId) {
    const project = findProjectById(projectId);
    if (!project) return;

    currentProjectId = projectId;
    currentImageIndex = 0;
    
    // Prepare images array (main image + gallery images)
    currentProjectImages = [project.image];
    if (project.images && project.images.length > 0) {
        currentProjectImages = currentProjectImages.concat(project.images);
    }
    
    // Update modal content
    document.getElementById('modalTitle').textContent = project.name;
    document.getElementById('modalPrice').textContent = project.price || '-';
    document.getElementById('modalDuration').textContent = project.duration || '-';
    document.getElementById('modalDescription').textContent = project.description || '-';

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

function closeProjectModal() {
    document.getElementById('projectModal').classList.add('hidden');
    currentProjectId = null;
    currentImageIndex = 0;
    currentProjectImages = [];
}

// Modal image navigation
function prevModalImage() {
    if (currentProjectImages.length === 0) return;
    currentImageIndex = (currentImageIndex - 1 + currentProjectImages.length) % currentProjectImages.length;
    updateModalImages();
}

function nextModalImage() {
    if (currentProjectImages.length === 0) return;
    currentImageIndex = (currentImageIndex + 1) % currentProjectImages.length;
    updateModalImages();
}

function toggleModalImageGallery() {
    const thumbnails = document.getElementById('modalThumbnails');
    if (thumbnails) {
        thumbnails.style.display = thumbnails.style.display === 'none' ? 'flex' : 'none';
    }
}

// Image optimization
function initializeImageOptimization() {
    const images = document.querySelectorAll('img');
    images.forEach(img => optimizeImage(img));
}

function optimizeImage(img) {
    if (img.complete && img.naturalWidth > 0) {
        img.style.opacity = '1';
    }
}

// Image zoom functions
function initializeImageZoom() {
    const zoomModal = document.getElementById('imageZoomModal');
    if (!zoomModal) return;
    
    // Add click handler to slider images
    const images = document.querySelectorAll('.project-slide .project-image, .project-slide .main-image');
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
    const zoomModal = document.getElementById('imageZoomModal');
    const zoomImage = document.getElementById('zoomImage');
    
    if (zoomModal && zoomImage) {
        zoomImage.src = imageSrc;
        currentZoomScale = 1;
        imageOffset = { x: 0, y: 0 };
        zoomImage.style.transform = `scale(${currentZoomScale}) translate(${imageOffset.x}px, ${imageOffset.y}px)`;
        // Use 'active' class if available, otherwise toggle 'hidden'
        if (zoomModal.classList.contains('hidden')) {
            zoomModal.classList.remove('hidden');
        }
        zoomModal.classList.add('active');
        zoomModal.style.display = 'flex';
        // Prevent body scroll when modal is open
        document.body.style.overflow = 'hidden';
    }
}

function closeImageZoom() {
    const zoomModal = document.getElementById('imageZoomModal');
    if (zoomModal) {
        zoomModal.classList.remove('active');
        zoomModal.classList.add('hidden');
        zoomModal.style.display = 'none';
        // Restore body scroll
        document.body.style.overflow = '';
    }
}

function zoomIn() {
    currentZoomScale = Math.min(currentZoomScale + 0.25, 3);
    updateZoom();
}

function zoomOut() {
    currentZoomScale = Math.max(currentZoomScale - 0.25, 0.5);
    updateZoom();
}

function resetZoom() {
    currentZoomScale = 1;
    imageOffset = { x: 0, y: 0 };
    updateZoom();
}

function updateZoom() {
    const zoomImage = document.getElementById('zoomImage');
    if (zoomImage) {
        zoomImage.style.transform = `scale(${currentZoomScale}) translate(${imageOffset.x}px, ${imageOffset.y}px)`;
    }
}

// Back to top
function initializeBackToTop() {
    const backToTopBtn = document.getElementById('backToTop');
    if (!backToTopBtn) return;
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Contact functions
function requestQuote() {
    window.location.href = '../../index.php#contact-section';
}

function contactUs() {
    window.location.href = '../../index.php#contact-section';
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
window.prevModalImage = prevModalImage;
window.nextModalImage = nextModalImage;
window.toggleModalImageGallery = toggleModalImageGallery;
window.openImageZoom = openImageZoom;
window.closeImageZoom = closeImageZoom;
window.zoomIn = zoomIn;
window.zoomOut = zoomOut;
window.resetZoom = resetZoom;
window.scrollToTop = scrollToTop;
window.requestQuote = requestQuote;
window.contactUs = contactUs;
