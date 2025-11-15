// Infrastructure Page JavaScript

(function() {
    'use strict';
    
    // Project data from PHP (will be set by infrastructure.php)
    let projectsData = {};

    // Global variables for current project
    let currentProjectId = null;
    let currentImageIndex = 0;
    let currentProjectImages = [];

    // Image zoom modal variables
    let currentZoomScale = 1;
    let isDraggingImage = false; // Changed from isDragging to avoid conflict
    let dragStart = { x: 0, y: 0 };
    let imageOffset = { x: 0, y: 0 };
    
    // Global variable to track initialization
    let slidersInitialized = false;

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

    // Make setProjectsData available globally immediately
    window.setProjectsData = setProjectsData;
    
    // Make changeMainImage available immediately (before full script loads)
    // This is a placeholder that will be replaced by the real function
    window.changeMainImage = function(projectId, imageIndex) {
        console.log('changeMainImage placeholder called, waiting for full script load...', projectId, imageIndex);
    };

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Infrastructure JS: DOM Content Loaded');
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

    function getDocumentDirection() {
        return (document.documentElement.getAttribute('dir') || document.body.getAttribute('dir') || 'ltr').toLowerCase();
    }

    // Initialize grid layout for each category
    function initializeSliders() {
        Object.keys(projectsData).forEach(categoryKey => {
            const category = projectsData[categoryKey];
            if (category.projects && category.projects.length > 0) {
                initializeCategoryGrid(categoryKey, category.projects);
            }
        });
    }

    // Initialize grid for a specific category
    function initializeCategoryGrid(categoryKey, projects) {
        const gridContainer = document.getElementById(`projects-grid-${categoryKey}`);
        
        console.log(`Initializing grid for category: ${categoryKey}`);
        console.log('Grid container:', gridContainer);
        console.log('Projects count:', projects.length);
        
        if (!gridContainer) {
            console.error(`Grid container not found for category ${categoryKey}`);
            return;
        }
        
        // Initialize image optimization for grid cards
        const projectCards = gridContainer.querySelectorAll('.project-card img');
        projectCards.forEach(img => {
            optimizeImage(img);
        });
        
        console.log(`Grid ${categoryKey} initialized with ${projects.length} projects`);
    }

    // Open image gallery for a project
    function openImageGallery(projectId) {
        const project = findProjectById(projectId);
        if (!project) return;
        
        // Get all images (main image + gallery images)
        const allImages = [project.image];
        if (project.images && project.images.length > 0) {
            allImages.push(...project.images);
        }
        
        // Open the first image in zoom modal
        if (allImages.length > 0) {
            openImageZoom(allImages[0]);
        }
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
            } else if (project.images && project.images.length > 0) {
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
        currentProjectImages = project.images || [];
        currentImageIndex = 0;
        
        // Update modal content
        document.getElementById('modalTitle').textContent = project.name;
        document.getElementById('modalPrice').textContent = project.price;
        document.getElementById('modalDuration').textContent = project.duration;
        document.getElementById('modalDescription').textContent = project.description;

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

    function requestQuote() {
        // Implementation for quote request
        alert('Quote request sent!');
    }

    function downloadProject() {
        // Implementation for project download
        alert('Download started!');
    }

    // Back to top functionality
    function initializeBackToTop() {
        const backToTopBtn = document.getElementById('backToTop');
        if (!backToTopBtn) return;
        
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
        const modal = document.getElementById('projectModal');
        if (!modal || modal.classList.contains('hidden')) return;
        
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
        // Add click listeners to all slider images
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('project-image') || e.target.classList.contains('main-image')) {
                e.preventDefault();
                openImageZoom(e.target.src);
            }
        });
    }

    function openImageZoom(imageSrc) {
        const modal = document.getElementById('imageZoomModal');
        const zoomImage = document.getElementById('zoomImage');
        
        if (modal && zoomImage) {
            zoomImage.src = imageSrc;
            resetZoom();
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeImageZoom() {
        const modal = document.getElementById('imageZoomModal');
        
        if (modal) {
            modal.classList.remove('active');
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

    // Make functions globally available
    window.openImageGallery = openImageGallery;
    window.openImageZoom = openImageZoom;
    window.closeImageZoom = closeImageZoom;
    window.zoomIn = zoomIn;
    window.zoomOut = zoomOut;
    window.resetZoom = resetZoom;
    window.toggleImageGallery = toggleImageGallery;
    window.changeMainImage = changeMainImage;
    window.showProjectDetails = showProjectDetails;
    window.closeProjectModal = closeProjectModal;
    window.requestQuote = requestQuote;
    window.downloadProject = downloadProject;
    
    // Override placeholder changeMainImage with real function
    window.changeMainImage = changeMainImage;
    
})();