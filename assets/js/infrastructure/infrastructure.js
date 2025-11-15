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
        
        const direction = (slider.dataset.direction || getDocumentDirection()).toLowerCase();
        slider.dataset.direction = direction;
        slider.style.transform = 'translate3d(0, 0, 0)';
        slider.dataset.currentSlide = '0';

        // Set total slides
        const totalSlides = projects.length;
        counter.querySelector('.total-slides').textContent = totalSlides;
        
        // Ensure counter starts at slide 1
        counter.querySelector('.current-slide').textContent = 1;
        
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
            slider.style.transform = 'translate3d(0, 0, 0)';
            console.log('Slider initial transform set to translate3d(0,0,0) with delay');
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

    function goToSlide(categoryKey, slideIndex, syncTabs = true) {
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
        slider.style.transform = `translate3d(${translateX}%, 0, 0)`;
        slider.dataset.currentSlide = slideIndex.toString();
        
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
        
        // Update dots
        dots.querySelectorAll('.slider-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === slideIndex);
        });

        if (syncTabs) {
            const tabsContainer = document.getElementById(`tabs-${categoryKey}`);
            if (tabsContainer) {
                const tabButtons = tabsContainer.querySelectorAll('.tab-button');
                tabButtons.forEach((btn, index) => {
                    btn.classList.toggle('active', index === slideIndex);
                });
                // Ensure active tab is visible
                const activeBtn = tabButtons[slideIndex];
                if (activeBtn && typeof activeBtn.scrollIntoView === 'function') {
                    activeBtn.scrollIntoView({ inline: 'center', block: 'nearest', behavior: 'smooth' });
                }
            }
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

    // Tab switching function
    function showTab(categoryKey, tabIndex) {
        const tabsContainer = document.getElementById(`tabs-${categoryKey}`);
        if (tabsContainer) {
            const tabButtons = tabsContainer.querySelectorAll('.tab-button');
            tabButtons.forEach((btn, index) => {
                btn.classList.toggle('active', index === tabIndex);
            });
        }

        goToSlide(categoryKey, tabIndex, false);
    }

    // Make functions globally available
    window.nextSlide = nextSlide;
    window.prevSlide = prevSlide;
    window.goToSlide = goToSlide;
    window.showTab = showTab;
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