// Commercial & Residential Design Public Page JavaScript

(function() {
    'use strict';
    
    // Global variables
    let projectsDataForJS = {};

    function updateSliderProgress(counterEl, slideIndex, totalSlidesOverride) {
        if (!counterEl) return;

        const totalEl = counterEl.querySelector('.total-slides');
        const currentEl = counterEl.querySelector('.current-slide');
        const fillEl = counterEl.querySelector('.slide-progress-fill');

        if (typeof totalSlidesOverride === 'number' && totalEl) {
            totalEl.textContent = totalSlidesOverride;
        }

        if (currentEl) {
            currentEl.textContent = slideIndex + 1;
        }

        if (!fillEl || !totalEl) return;

        const totalSlides = parseInt(totalEl.textContent || '1', 10);
        const safeTotal = Math.max(totalSlides, 1);
        const progress = ((slideIndex + 1) / safeTotal) * 100;

        fillEl.style.width = `${Math.min(Math.max(progress, 0), 100)}%`;
    }
    
    // Initialize function
    function initializeCommercialResidentialDesign(projectsData) {
        projectsDataForJS = projectsData || {};
        
        // Call setProjectsData when DOM is ready
        if (typeof setProjectsData === 'function') {
            setProjectsData(projectsDataForJS);
        } else {
            // Wait a bit for script to load
            setTimeout(function() {
                if (typeof setProjectsData === 'function') {
                    setProjectsData(projectsDataForJS);
                }
            }, 100);
        }
        
        // Ensure changeMainImage is available
        if (typeof window.changeMainImage === 'undefined') {
            // Fallback function
            window.changeMainImage = function(projectId, imageIndex) {
                console.log('changeMainImage called but not yet available, projectId:', projectId, 'imageIndex:', imageIndex);
            };
        }
        
        // Override showProjectDetails to include area and floors
        document.addEventListener('DOMContentLoaded', function() {
            const originalShowProjectDetails = window.showProjectDetails;
            if (originalShowProjectDetails) {
                window.showProjectDetails = function(projectId) {
                    // Find project in data
                    let foundProject = null;
                    for (const categoryKey in projectsDataForJS) {
                        const category = projectsDataForJS[categoryKey];
                        if (category.projects) {
                            foundProject = category.projects.find(p => p.id == projectId);
                            if (foundProject) break;
                        }
                    }
                    
                    if (foundProject) {
                        // Set area and floors
                        const modalArea = document.getElementById('modalArea');
                        const modalFloors = document.getElementById('modalFloors');
                        
                        if (modalArea) {
                            modalArea.textContent = (foundProject.area || '-') + (foundProject.area ? ' م²' : '');
                        }
                        if (modalFloors) {
                            modalFloors.textContent = foundProject.floors || '-';
                        }
                    }
                    
                    // Call original function
                    originalShowProjectDetails(projectId);
                };
            }
        });
    }
    
    // Search and Filter Functions
    function applyFilters() {
        console.log('applyFilters called');
        
        const searchInput = document.getElementById('searchInput');
        const categoryFilterEl = document.getElementById('categoryFilter');
        const areaMinEl = document.getElementById('areaMin');
        const areaMaxEl = document.getElementById('areaMax');
        const floorsMinEl = document.getElementById('floorsMin');
        const floorsMaxEl = document.getElementById('floorsMax');
        
        const searchTerm = searchInput?.value.toLowerCase().trim() || '';
        const categoryFilter = categoryFilterEl?.value || '';
        const areaMin = parseFloat(areaMinEl?.value) || 0;
        const areaMax = parseFloat(areaMaxEl?.value) || Infinity;
        const floorsMin = parseInt(floorsMinEl?.value) || 0;
        const floorsMax = parseInt(floorsMaxEl?.value) || Infinity;
        
        console.log('Filter values:', {
            searchTerm,
            categoryFilter,
            areaMin,
            areaMax,
            floorsMin,
            floorsMax
        });
        
        let visibleCount = 0;
        
        const categorySections = document.querySelectorAll('.category-section');
        console.log('Category sections found:', categorySections.length);
        
        // Filter projects in each category section
        categorySections.forEach(section => {
            const categoryKey = section.getAttribute('data-category');
            const projects = section.querySelectorAll('.project-slide');
            console.log(`Category ${categoryKey}: ${projects.length} projects`);
            let categoryVisibleCount = 0;
            
            projects.forEach(project => {
                const projectName = project.getAttribute('data-name') || '';
                const projectCategory = project.getAttribute('data-category') || '';
                const projectArea = parseFloat(project.getAttribute('data-area')) || 0;
                const projectFloors = parseInt(project.getAttribute('data-floors')) || 0;
                
                // Check search term
                const matchesSearch = !searchTerm || projectName.includes(searchTerm);
                
                // Check category
                const matchesCategory = !categoryFilter || projectCategory === categoryFilter;
                
                // Check area range
                const matchesArea = projectArea >= areaMin && projectArea <= areaMax;
                
                // Check floors range
                const matchesFloors = projectFloors >= floorsMin && projectFloors <= floorsMax;
                
                // Show/hide project
                if (matchesSearch && matchesCategory && matchesArea && matchesFloors) {
                    project.style.display = 'grid';
                    categoryVisibleCount++;
                    visibleCount++;
                } else {
                    project.style.display = 'none';
                }
                
                // Debug for first project
                if (project === projects[0]) {
                    console.log('First project filter check:', {
                        projectName,
                        projectCategory,
                        projectArea,
                        projectFloors,
                        matchesSearch,
                        matchesCategory,
                        matchesArea,
                        matchesFloors,
                        finalMatch: matchesSearch && matchesCategory && matchesArea && matchesFloors
                    });
                }
            });
            
            // Hide/show category section based on visible projects
            if (categoryVisibleCount === 0) {
                section.style.display = 'none';
            } else {
                section.style.display = 'block';
                
                // Update slider counter
                const counterEl = section.querySelector(`#counter-${categoryKey}`) || section.querySelector('.slide-counter');
                if (counterEl) {
                    updateSliderProgress(counterEl, 0, categoryVisibleCount);
                }
                
                // Update tabs - hide tabs for hidden projects
                const tabsContainer = section.querySelector('.tabs-nav-container');
                if (tabsContainer) {
                    const tabs = tabsContainer.querySelectorAll('.tab-button');
                    tabs.forEach((tab, index) => {
                        const project = projects[index];
                        if (project && project.style.display !== 'none') {
                            tab.style.display = 'inline-flex';
                        } else {
                            tab.style.display = 'none';
                        }
                    });
                    
                    // Activate first visible tab
                    const firstVisibleTab = Array.from(tabs).find(tab => tab.style.display !== 'none');
                    if (firstVisibleTab) {
                        tabs.forEach(tab => tab.classList.remove('active'));
                        firstVisibleTab.classList.add('active');
                        
                        // Update slider position
                        const slider = section.querySelector(`#slider-${categoryKey}`) || section.querySelector('.projects-slider');
                        if (slider) {
                            const firstVisibleIndex = Array.from(projects).findIndex(p => p.style.display !== 'none');
                            if (firstVisibleIndex >= 0) {
                                const translateX = -firstVisibleIndex * 100;
                                slider.style.transform = `translateX(${translateX}%)`;
                                
                                // Update counter
                                const counterEl = section.querySelector(`#counter-${categoryKey}`) || section.querySelector('.slide-counter');
                                if (counterEl) {
                                    updateSliderProgress(counterEl, firstVisibleIndex, visibleProjects.length);
                                }
                                
                                // Update dots
                                const dots = section.querySelector(`#dots-${categoryKey}`) || section.querySelectorAll('.slider-dot');
                                if (dots && dots.length > 0) {
                                    dots.forEach((dot, index) => {
                                        dot.classList.toggle('active', index === firstVisibleIndex);
                                    });
                                }
                            }
                        }
                    }
                }
            }
        });
        
        // Update results count
        const resultsCount = document.getElementById('resultsCount');
        if (resultsCount) {
            resultsCount.textContent = visibleCount;
        }
        
        console.log('Filtering complete. Visible projects:', visibleCount);
        
        // Re-initialize responsive sliders after filtering
        if (typeof window.initializeResponsiveSliders === 'function') {
            setTimeout(function() {
                window.initializeResponsiveSliders();
            }, 50);
        }
    }
    
    function clearFilters() {
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const areaMin = document.getElementById('areaMin');
        const areaMax = document.getElementById('areaMax');
        const floorsMin = document.getElementById('floorsMin');
        const floorsMax = document.getElementById('floorsMax');
        
        if (searchInput) searchInput.value = '';
        if (categoryFilter) categoryFilter.value = '';
        if (areaMin) areaMin.value = '';
        if (areaMax) areaMax.value = '';
        if (floorsMin) floorsMin.value = '';
        if (floorsMax) floorsMax.value = '';
        
        applyFilters();
    }
    
    function goToVisibleSlide(categoryKey, slideIndex) {
        const section = document.querySelector(`#${categoryKey}-section`);
        if (!section) return;
        
        const slider = section.querySelector(`#slider-${categoryKey}`);
        const counter = section.querySelector(`#counter-${categoryKey}`);
        const dots = section.querySelector(`#dots-${categoryKey}`);
        
        if (!slider || !counter || !dots) return;
        
        const projects = Array.from(section.querySelectorAll('.project-slide'));
        const visibleProjects = projects.filter(p => p.style.display !== 'none');
        
        if (slideIndex < 0 || slideIndex >= projects.length || projects[slideIndex].style.display === 'none') {
            return;
        }
        
        // Update slider position
        const translateX = -slideIndex * 100;
        slider.style.transform = `translateX(${translateX}%)`;
        
        // Update counter
        const visibleIndex = visibleProjects.indexOf(projects[slideIndex]);
        updateSliderProgress(counter, visibleIndex, visibleProjects.length);
        
        // Update dots
        dots.querySelectorAll('.slider-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === slideIndex);
        });
        
        // Update active tab
        const tabsContainer = section.querySelector(`#tabs-${categoryKey}`);
        if (tabsContainer) {
            const tabs = tabsContainer.querySelectorAll('.tab-button');
            tabs.forEach((tab, index) => {
                tab.classList.toggle('active', index === slideIndex);
            });
        }
    }
    
    // Override slider navigation to work with filtered projects
    function overrideSliderNavigation() {
        const originalNextSlide = window.nextSlide;
        const originalPrevSlide = window.prevSlide;
        const originalGoToSlide = window.goToSlide;
        const originalShowTab = window.showTab;
        
        window.nextSlide = function(categoryKey) {
            const section = document.querySelector(`#${categoryKey}-section`);
            if (!section) {
                if (originalNextSlide) originalNextSlide(categoryKey);
                return;
            }
            
            const projects = Array.from(section.querySelectorAll('.project-slide'));
            const visibleProjects = projects.filter(p => p.style.display !== 'none');
            const currentSlide = parseInt(section.querySelector('.slide-counter .current-slide').textContent) - 1;
            
            // Find next visible project
            let nextIndex = -1;
            for (let i = currentSlide + 1; i < projects.length; i++) {
                if (projects[i].style.display !== 'none') {
                    nextIndex = i;
                    break;
                }
            }
            
            // If no next visible, loop to first
            if (nextIndex === -1 && visibleProjects.length > 0) {
                nextIndex = projects.findIndex(p => p.style.display !== 'none');
            }
            
            if (nextIndex >= 0) {
                goToVisibleSlide(categoryKey, nextIndex);
            } else if (originalNextSlide) {
                originalNextSlide(categoryKey);
            }
        };
        
        window.prevSlide = function(categoryKey) {
            const section = document.querySelector(`#${categoryKey}-section`);
            if (!section) {
                if (originalPrevSlide) originalPrevSlide(categoryKey);
                return;
            }
            
            const projects = Array.from(section.querySelectorAll('.project-slide'));
            const currentSlide = parseInt(section.querySelector('.slide-counter .current-slide').textContent) - 1;
            
            // Find previous visible project
            let prevIndex = -1;
            for (let i = currentSlide - 1; i >= 0; i--) {
                if (projects[i].style.display !== 'none') {
                    prevIndex = i;
                    break;
                }
            }
            
            // If no previous visible, loop to last
            if (prevIndex === -1) {
                for (let i = projects.length - 1; i >= 0; i--) {
                    if (projects[i].style.display !== 'none') {
                        prevIndex = i;
                        break;
                    }
                }
            }
            
            if (prevIndex >= 0) {
                goToVisibleSlide(categoryKey, prevIndex);
            } else if (originalPrevSlide) {
                originalPrevSlide(categoryKey);
            }
        };
        
        // Override showTab to work with filtered projects
        window.showTab = function(categoryKey, tabIndex) {
            const section = document.querySelector(`#${categoryKey}-section`);
            if (!section) {
                if (originalShowTab) originalShowTab(categoryKey, tabIndex);
                return;
            }
            
            const projects = Array.from(section.querySelectorAll('.project-slide'));
            
            // Check if the project at tabIndex is visible
            if (tabIndex >= 0 && tabIndex < projects.length && projects[tabIndex].style.display !== 'none') {
                goToVisibleSlide(categoryKey, tabIndex);
            } else {
                // Find next visible project
                let visibleIndex = -1;
                for (let i = tabIndex; i < projects.length; i++) {
                    if (projects[i].style.display !== 'none') {
                        visibleIndex = i;
                        break;
                    }
                }
                if (visibleIndex === -1) {
                    for (let i = tabIndex - 1; i >= 0; i--) {
                        if (projects[i].style.display !== 'none') {
                            visibleIndex = i;
                            break;
                        }
                    }
                }
                if (visibleIndex >= 0) {
                    goToVisibleSlide(categoryKey, visibleIndex);
                } else if (originalShowTab) {
                    originalShowTab(categoryKey, tabIndex);
                }
            }
        };
    }
    
    // Initialize event listeners
    function initializeEventListeners() {
        console.log('Initializing event listeners for filters...');
        
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const areaMin = document.getElementById('areaMin');
        const areaMax = document.getElementById('areaMax');
        const floorsMin = document.getElementById('floorsMin');
        const floorsMax = document.getElementById('floorsMax');
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');
        
        console.log('Filter elements found:', {
            searchInput: !!searchInput,
            categoryFilter: !!categoryFilter,
            areaMin: !!areaMin,
            areaMax: !!areaMax,
            floorsMin: !!floorsMin,
            floorsMax: !!floorsMax,
            clearFiltersBtn: !!clearFiltersBtn
        });
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                console.log('Search input changed');
                applyFilters();
            });
            searchInput.addEventListener('keyup', function() {
                console.log('Search keyup');
                applyFilters();
            });
        }
        
        if (categoryFilter) {
            categoryFilter.addEventListener('change', function() {
                console.log('Category filter changed:', categoryFilter.value);
                applyFilters();
            });
        }
        
        if (areaMin) {
            areaMin.addEventListener('input', function() {
                console.log('Area min changed:', areaMin.value);
                applyFilters();
            });
            areaMin.addEventListener('change', function() {
                console.log('Area min change event');
                applyFilters();
            });
        }
        
        if (areaMax) {
            areaMax.addEventListener('input', function() {
                console.log('Area max changed:', areaMax.value);
                applyFilters();
            });
            areaMax.addEventListener('change', function() {
                console.log('Area max change event');
                applyFilters();
            });
        }
        
        if (floorsMin) {
            floorsMin.addEventListener('input', function() {
                console.log('Floors min changed:', floorsMin.value);
                applyFilters();
            });
            floorsMin.addEventListener('change', function() {
                console.log('Floors min change event');
                applyFilters();
            });
        }
        
        if (floorsMax) {
            floorsMax.addEventListener('input', function() {
                console.log('Floors max changed:', floorsMax.value);
                applyFilters();
            });
            floorsMax.addEventListener('change', function() {
                console.log('Floors max change event');
                applyFilters();
            });
        }
        
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function() {
                console.log('Clear filters clicked');
                clearFilters();
            });
        }
        
        // Initial count
        const allProjects = document.querySelectorAll('.project-slide');
        const resultsCount = document.getElementById('resultsCount');
        console.log('Total projects found:', allProjects.length);
        if (resultsCount && allProjects.length > 0) {
            resultsCount.textContent = allProjects.length;
        }
        
        // Override slider navigation
        overrideSliderNavigation();
        
        // Initialize responsive sliders after filters are applied
        if (typeof window.initializeResponsiveSliders === 'function') {
            setTimeout(function() {
                window.initializeResponsiveSliders();
            }, 100);
        }
        
        console.log('Event listeners initialized successfully');
    }
    
    // Initialize when DOM is ready
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initializeEventListeners();
            });
        } else {
            // DOM already loaded
            initializeEventListeners();
        }
    }
    
    // Start initialization
    init();
    
    // Make functions globally available
    window.initializeCommercialResidentialDesign = initializeCommercialResidentialDesign;
    window.applyFilters = applyFilters;
    window.clearFilters = clearFilters;
    window.goToVisibleSlide = goToVisibleSlide;
    
})();

