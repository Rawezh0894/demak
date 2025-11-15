/**
 * Design Reconstruction Management JavaScript
 * 
 * Main functionality for design reconstruction admin panel
 */

// Global variables
let currentProjectId = null;
let projectsData = [];
let currentPage = 1;
let totalPages = 1;
let currentCategory = '';
let currentSearch = '';

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    initializeImageUploads();
    initializePagination();
    
    // Ensure "no projects found" message is hidden if projects exist
    const projectsContainer = document.getElementById('projectsContainer');
    const noProjectsFound = document.getElementById('noProjectsFound');
    
    if (projectsContainer && noProjectsFound) {
        const projectCards = projectsContainer.querySelectorAll('.project-card');
        if (projectCards.length > 0) {
            // Projects exist, hide the "no projects found" message
            noProjectsFound.classList.add('hidden');
        }
    }
});

// Initialize event listeners
function initializeEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterProjects);
    }
    
    // Category filter
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterProjects);
    }
    
    // Sort functionality
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', sortProjects);
    }
    
    // Form submission is handled by AddProjectManager and FormManager
    // No need to add event listener here to avoid duplicate submissions
}

// Initialize image upload functionality
function initializeImageUploads() {
    // Note: Image upload is handled by ImagePreviewManager in image-preview.js
    // We don't need to add event listeners here to avoid duplicate handlers
}

// Note: Image upload handlers are now in image-preview.js (ImagePreviewManager)
// These functions are kept for backward compatibility but are not used

// Filter projects based on search and category
function filterProjects() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    
    if (!searchInput || !categoryFilter) {
        return; // Elements not found, exit early
    }
    
    const searchTerm = searchInput.value.toLowerCase();
    const categoryFilterValue = categoryFilter.value;
    const projectCards = document.querySelectorAll('.project-card');
    let visibleCount = 0;
    
    projectCards.forEach(card => {
        const projectName = (card.dataset.name || '').toLowerCase();
        const projectCategory = card.dataset.category || '';
        
        const matchesSearch = !searchTerm || projectName.includes(searchTerm);
        const matchesCategory = !categoryFilterValue || projectCategory === categoryFilterValue;
        
        if (matchesSearch && matchesCategory) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide "no projects found" message
    // Only show if there are project cards in the DOM and none are visible
    const noProjectsFound = document.getElementById('noProjectsFound');
    const projectsContainer = document.getElementById('projectsContainer');
    
    if (noProjectsFound && projectsContainer) {
        // Check if there are any project cards in the container
        const totalCards = projectsContainer.querySelectorAll('.project-card').length;
        
        if (totalCards > 0 && visibleCount === 0) {
            // There are projects but none match the filter
            noProjectsFound.classList.remove('hidden');
        } else if (totalCards === 0) {
            // No projects at all - this should be handled by PHP, but show it anyway
            noProjectsFound.classList.remove('hidden');
        } else {
            // Projects are visible
            noProjectsFound.classList.add('hidden');
        }
    }
}

// Sort projects
function sortProjects() {
    const sortValue = document.getElementById('sortSelect').value;
    const projectsGrid = document.getElementById('projectsGrid');
    const projectCards = Array.from(document.querySelectorAll('.project-card'));
    
    projectCards.sort((a, b) => {
        switch (sortValue) {
            case 'name_asc':
                return (a.dataset.name || '').localeCompare(b.dataset.name || '');
            case 'name_desc':
                return (b.dataset.name || '').localeCompare(a.dataset.name || '');
            case 'price_asc':
                return parseFloat(a.dataset.price || '0') - parseFloat(b.dataset.price || '0');
            case 'price_desc':
                return parseFloat(b.dataset.price || '0') - parseFloat(a.dataset.price || '0');
            case 'date_created':
                return new Date(b.dataset.createdAt || '') - new Date(a.dataset.createdAt || '');
            default:
                return 0;
        }
    });
    
    // Re-append sorted cards
    projectCards.forEach(card => projectsGrid.appendChild(card));
}

// Handle form submission
function handleFormSubmit(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const action = formData.get('action');
    
    // Show loading state
    const submitButton = event.target.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
    submitButton.disabled = true;
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification(data.message, 'success');
            
            // Close modal and update list dynamically
            setTimeout(() => {
                closeProjectModal();
                // Update project list dynamically instead of reloading
                if (window.AddProjectManager && window.AddProjectManager.updateProjectInList) {
                    window.AddProjectManager.updateProjectInList(data.project);
                }
            }, 1500);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        // Restore button state
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Update projects list dynamically with pagination
function updateProjectsList(page = 1, category = '', search = '') {
    currentPage = page;
    currentCategory = category || '';
    currentSearch = search || '';
    
    // Build query string
    const params = new URLSearchParams();
    if (page > 1) params.append('page', page);
    if (category) params.append('category', category);
    if (search) params.append('search', search);
    
    const url = '../../process/design-reconstruction/get_projects_list.php' + 
                (params.toString() ? '?' + params.toString() : '');
    
    // Show loading state
    const projectsContainer = document.getElementById('projectsContainer');
    if (projectsContainer) {
        projectsContainer.innerHTML = '<div class="col-span-full text-center py-16"><div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div></div>';
    }
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return response.text().then(text => {
                    throw new Error('Server returned HTML instead of JSON.');
                });
            }
        })
        .then(data => {
            if (data.success) {
                renderProjectsList(data.projects);
                if (data.pagination) {
                    totalPages = data.pagination.total_pages;
                    renderPagination(data.pagination);
                }
                // Update URL without reload
                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.pushState({ page: page }, '', newUrl);
            } else {
                console.error('API Error:', data.message);
                showNotification('هەڵەیەک ڕوویدا لە بارکردنی پڕۆژەکان', 'error');
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            showNotification('هەڵەیەک ڕوویدا لە بارکردنی پڕۆژەکان', 'error');
            // Fallback to page reload if dynamic update fails
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        });
}

// Render projects list
function renderProjectsList(projects) {
    const projectsContainer = document.getElementById('projectsContainer');
    const noProjectsFound = document.getElementById('noProjectsFound');
    
    if (!projectsContainer) {
        console.error('Projects container not found');
        return;
    }
    
    if (!projects || projects.length === 0) {
        projectsContainer.innerHTML = '';
        if (noProjectsFound) {
            noProjectsFound.classList.remove('hidden');
        }
        return;
    }
    
    if (noProjectsFound) {
        noProjectsFound.classList.add('hidden');
    }
    
    // Generate HTML for projects
    let projectsHTML = '';
    projects.forEach(project => {
        const categoryName = project.category_title_ku || project.category_title || 'Unknown';
        const mainImage = project.main_image ? `../../${project.main_image}` : null;
        
        projectsHTML += `
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden project-card transition-all duration-300 hover:shadow-2xl hover:-translate-y-2" 
                 data-project-id="${project.id}"
                 data-category="${project.category_key || ''}" 
                 data-name="${(project.name || '').toLowerCase()}"
                 data-price="${project.price || '0'}">
                <!-- Project Image -->
                <div class="relative h-56 bg-gray-200 dark:bg-gray-700">
                    ${mainImage ? 
                        `<img src="${mainImage}" alt="${project.name}" class="w-full h-full object-cover">` :
                        `<div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-5xl"></i>
                        </div>`
                    }
                    <div class="absolute top-4 right-4">
                        <span class="bg-purple-600 text-white px-3 py-2 rounded-xl text-sm font-medium shadow-lg">
                            ${categoryName}
                        </span>
                    </div>
                </div>
                
                <!-- Project Content -->
                <div class="p-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        ${project.name || 'No Name'}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-base mb-6 line-clamp-2">
                        ${project.description || ''}
                    </p>
                    
                    <!-- Project Info -->
                    <div class="flex items-center justify-between text-base text-gray-500 dark:text-gray-400 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-dollar-sign mr-2 text-green-600"></i>
                            <span class="font-semibold">${project.price || 'N/A'}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-clock mr-2 text-blue-600"></i>
                            <span class="font-semibold">${project.duration || 'N/A'}</span>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-3">
                        <button onclick="editProject(${project.id})" 
                                class="flex-1 action-btn action-btn-edit">
                            <i class="fas fa-edit"></i>
                            <span>دەستکاری</span>
                        </button>
                        <button onclick="deleteProject(${project.id})" 
                                class="action-btn action-btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    projectsContainer.innerHTML = projectsHTML;
}

// Render pagination controls
function renderPagination(pagination) {
    const paginationContainer = document.getElementById('paginationContainer');
    if (!paginationContainer) {
        console.warn('⚠️ Pagination container not found');
        return;
    }
    
    if (pagination.total_pages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    const { current_page, total_pages, total_projects, has_prev, has_next } = pagination;
    
    let paginationHTML = `
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium">${total_projects}</span> پڕۆژە لە کۆی 
                <span class="font-medium">${total_pages}</span> پەڕە
            </div>
            <div class="flex items-center gap-2">
    `;
    
    // Previous button
    if (has_prev) {
        paginationHTML += `
            <button onclick="updateProjectsList(${current_page - 1}, '${currentCategory}', '${currentSearch}')" 
                    class="pagination-btn pagination-btn-prev">
                <i class="fas fa-chevron-right"></i>
                <span>پێشوو</span>
            </button>
        `;
    } else {
        paginationHTML += `
            <button disabled class="pagination-btn pagination-btn-disabled">
                <i class="fas fa-chevron-right"></i>
                <span>پێشوو</span>
            </button>
        `;
    }
    
    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, current_page - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(total_pages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage < maxVisiblePages - 1) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    if (startPage > 1) {
        paginationHTML += `
            <button onclick="updateProjectsList(1, '${currentCategory}', '${currentSearch}')" 
                    class="pagination-btn pagination-btn-number ${current_page == 1 ? 'pagination-btn-active' : ''}">
                1
            </button>
        `;
        if (startPage > 2) {
            paginationHTML += `<span class="px-2 text-gray-400">...</span>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
            <button onclick="updateProjectsList(${i}, '${currentCategory}', '${currentSearch}')" 
                    class="pagination-btn pagination-btn-number ${current_page == i ? 'pagination-btn-active' : ''}">
                ${i}
            </button>
        `;
    }
    
    if (endPage < total_pages) {
        if (endPage < total_pages - 1) {
            paginationHTML += `<span class="px-2 text-gray-400">...</span>`;
        }
        paginationHTML += `
            <button onclick="updateProjectsList(${total_pages}, '${currentCategory}', '${currentSearch}')" 
                    class="pagination-btn pagination-btn-number ${current_page == total_pages ? 'pagination-btn-active' : ''}">
                ${total_pages}
            </button>
        `;
    }
    
    // Next button
    if (has_next) {
        paginationHTML += `
            <button onclick="updateProjectsList(${current_page + 1}, '${currentCategory}', '${currentSearch}')" 
                    class="pagination-btn pagination-btn-next">
                <span>دواتر</span>
                <i class="fas fa-chevron-left"></i>
            </button>
        `;
    } else {
        paginationHTML += `
            <button disabled class="pagination-btn pagination-btn-disabled">
                <span>دواتر</span>
                <i class="fas fa-chevron-left"></i>
            </button>
        `;
    }
    
    paginationHTML += `
            </div>
        </div>
    `;
    
    paginationContainer.innerHTML = paginationHTML;
}

// Initialize pagination from URL
function initializePagination() {
    const urlParams = new URLSearchParams(window.location.search);
    const page = parseInt(urlParams.get('page')) || 1;
    const category = urlParams.get('category') || '';
    const search = urlParams.get('search') || '';
    
    if (page > 1 || category || search) {
        updateProjectsList(page, category, search);
    }
}

// Handle browser back/forward buttons
window.addEventListener('popstate', function(event) {
    initializePagination();
});

// Open add project modal
function openAddProjectModal() {
    currentProjectId = null;
    
    const modalTitle = document.getElementById('modalTitle');
    if (modalTitle) {
        modalTitle.textContent = translations.addNewProject || 'Add New Project';
    }
    
    const formAction = document.getElementById('formAction');
    if (formAction) {
        formAction.value = 'add_project';
    }
    
    const projectId = document.getElementById('projectId');
    if (projectId) {
        projectId.value = '';
    }
    
    // Reset form
    const form = document.getElementById('projectForm');
    if (form) {
        form.reset();
    }
    
    const mainImagePreview = document.getElementById('mainImagePreview');
    if (mainImagePreview) {
        mainImagePreview.classList.add('hidden');
    }
    
    const additionalImagesPreview = document.getElementById('additionalImagesPreview');
    if (additionalImagesPreview) {
        additionalImagesPreview.classList.add('hidden');
        additionalImagesPreview.innerHTML = '';
    }
    
    // Reset features - use FeaturesManager if available, otherwise check if container exists
    const featuresContainer = document.getElementById('featuresContainer');
    if (featuresContainer) {
        if (window.FeaturesManager && window.FeaturesManager.setFeatures) {
            window.FeaturesManager.setFeatures([]);
        } else {
            featuresContainer.innerHTML = `
                <div class="flex items-center space-x-2 mb-2">
                    <input type="text" 
                           name="project_features[]" 
                           placeholder="${translations.addFeature || 'Add feature'}"
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <button type="button" 
                            onclick="removeFeature(this)"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        }
    }
    
    const projectModal = document.getElementById('projectModal');
    if (projectModal) {
        projectModal.classList.remove('hidden');
    }
}

// Close project modal
function closeProjectModal() {
    const projectModal = document.getElementById('projectModal');
    if (projectModal) {
        projectModal.classList.add('hidden');
    }
}

// Edit project
function editProject(projectId) {
    console.log('🔧 editProject called with projectId:', projectId);
    console.log('🔧 EditProjectManager available:', typeof EditProjectManager !== 'undefined');
    
    // Use EditProjectManager if available
    if (typeof EditProjectManager !== 'undefined' && EditProjectManager.openEditModal) {
        console.log('✅ Using EditProjectManager.openEditModal');
        EditProjectManager.openEditModal(projectId);
        return;
    }
    
    console.log('⚠️ EditProjectManager not available, using fallback');
    // Fallback: This would typically fetch project data and populate the form
    // For now, we'll just open the modal in edit mode
    currentProjectId = projectId;
    
    const modalTitle = document.getElementById('modalTitle');
    if (modalTitle) {
        modalTitle.textContent = translations.editProject || 'Edit Project';
    }
    
    const formAction = document.getElementById('formAction');
    if (formAction) {
        formAction.value = 'edit_project';
    }
    
    const projectIdInput = document.getElementById('projectId');
    if (projectIdInput) {
        projectIdInput.value = projectId;
    }
    
    // TODO: Fetch and populate project data
    
    const projectModal = document.getElementById('projectModal');
    if (projectModal) {
        projectModal.classList.remove('hidden');
    }
}

// Delete project - use DeleteProjectManager if available
function deleteProject(projectId) {
    if (!projectId) {
        console.error('Project ID is required for deletion');
        return;
    }
    
    // Use DeleteProjectManager if available
    if (window.DeleteProjectManager && window.DeleteProjectManager.openDeleteModal) {
        window.DeleteProjectManager.openDeleteModal(projectId);
        return;
    }
    
    // Fallback to simple modal open
    currentProjectId = projectId;
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.classList.remove('hidden');
    }
}

// Close delete modal - use DeleteProjectManager if available
function closeDeleteModal() {
    if (window.DeleteProjectManager && window.DeleteProjectManager.closeDeleteModal) {
        window.DeleteProjectManager.closeDeleteModal();
        return;
    }
    
    // Fallback
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.classList.add('hidden');
    }
    currentProjectId = null;
}

// Confirm delete - use DeleteProjectManager if available
function confirmDelete() {
    // Use DeleteProjectManager if available
    if (window.DeleteProjectManager && window.DeleteProjectManager.handleDelete) {
        window.DeleteProjectManager.handleDelete();
        return;
    }
    
    // Fallback implementation
    if (!currentProjectId) {
        console.error('No project ID available for deletion');
        return;
    }
    
    const formData = new FormData();
    formData.append('csrf_token', window.csrfToken);
    formData.append('action', 'delete_project');
    formData.append('project_id', currentProjectId);
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                closeDeleteModal();
                // Remove project from list dynamically
                const projectIdToRemove = data.project_id || currentProjectId;
                if (window.DeleteProjectManager && window.DeleteProjectManager.removeProjectCard && projectIdToRemove) {
                    window.DeleteProjectManager.removeProjectCard(projectIdToRemove);
                    window.DeleteProjectManager.updateProjectCount();
                }
            }, 1500);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

// Add feature input
function addFeature() {
    // Use FeaturesManager if available
    if (window.FeaturesManager && window.FeaturesManager.addFeature) {
        window.FeaturesManager.addFeature();
        return;
    }
    
    // Fallback to manual implementation
    const featuresContainer = document.getElementById('featuresContainer');
    if (!featuresContainer) {
        console.warn('featuresContainer not found');
        return;
    }
    
    const newFeature = document.createElement('div');
    newFeature.className = 'flex items-center space-x-2 mb-2';
    newFeature.innerHTML = `
        <input type="text" 
               name="project_features[]" 
               placeholder="${translations.addFeature || 'Add feature'}"
               class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
        <button type="button" 
                onclick="removeFeature(this)"
                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg">
            <i class="fas fa-trash"></i>
        </button>
    `;
    featuresContainer.appendChild(newFeature);
}

// Remove feature input
function removeFeature(button) {
    // Use FeaturesManager if available
    if (window.FeaturesManager && window.FeaturesManager.removeFeature) {
        window.FeaturesManager.removeFeature(button);
        return;
    }
    
    // Fallback to manual implementation
    if (button && button.parentElement) {
        button.parentElement.remove();
    }
}

// Remove main image
function removeMainImage() {
    const preview = document.getElementById('mainImagePreview');
    const input = document.getElementById('mainImage');
    
    if (preview) {
        preview.classList.add('hidden');
    }
    
    if (input) {
        input.value = '';
    }
}

// Remove additional image (for newly uploaded images)
function removeAdditionalImage(index) {
    const previewContainer = document.getElementById('additionalImagesPreview');
    if (!previewContainer) return;
    
    const thumbItem = previewContainer.children[index];
    if (thumbItem) {
        thumbItem.remove();
        
        // Hide container if no images left
        if (previewContainer.children.length === 0) {
            previewContainer.classList.add('hidden');
        }
    }
    
    // Clear the file input
    const fileInput = document.getElementById('additionalImages');
    if (fileInput) {
        fileInput.value = '';
    }
}

// Remove existing additional image (from database)
function removeExistingAdditionalImage(index) {
    console.log('🗑️ removeExistingAdditionalImage called with index:', index);
    const additionalImagesPreview = document.getElementById('additionalImagesPreview');
    console.log('🗑️ additionalImagesPreview found:', !!additionalImagesPreview);
    
    if (!additionalImagesPreview) {
        console.error('❌ additionalImagesPreview container not found!');
        return;
    }
    
    const thumbItem = additionalImagesPreview.querySelector(`[data-image-index="${index}"]`);
    console.log('🗑️ thumbItem found:', !!thumbItem);
    
    if (thumbItem) {
        // Mark image for deletion by adding to hidden input
        const imageId = thumbItem.getAttribute('data-image-id');
        console.log('🗑️ Image ID to delete:', imageId);
        
        if (imageId) {
            // Get or create container for deleted image IDs
            let deletedImagesContainer = document.getElementById('deleted_additional_images_container');
            if (!deletedImagesContainer) {
                console.log('🗑️ Creating deleted_additional_images_container');
                deletedImagesContainer = document.createElement('div');
                deletedImagesContainer.id = 'deleted_additional_images_container';
                deletedImagesContainer.style.display = 'none';
                const projectForm = document.getElementById('projectForm');
                if (projectForm) {
                    projectForm.appendChild(deletedImagesContainer);
                } else {
                    console.error('❌ projectForm not found!');
                }
            }
            
            // Check if this image ID is already marked for deletion
            const existingInput = deletedImagesContainer.querySelector(`input[value="${imageId}"]`);
            if (!existingInput) {
                console.log('🗑️ Marking image for deletion:', imageId);
                // Create hidden input for this deleted image
                const deletedImageInput = document.createElement('input');
                deletedImageInput.type = 'hidden';
                deletedImageInput.name = 'deleted_additional_images[]';
                deletedImageInput.value = imageId;
                deletedImagesContainer.appendChild(deletedImageInput);
            } else {
                console.log('🗑️ Image already marked for deletion');
            }
        } else {
            console.log('⚠️ No image ID found, skipping deletion mark');
        }
        
        // Remove from preview
        console.log('🗑️ Removing thumbItem from preview');
        thumbItem.remove();
        
        // Remove from stored array
        if (window.existingAdditionalImages && window.existingAdditionalImages[index]) {
            console.log('🗑️ Removing from existingAdditionalImages array');
            window.existingAdditionalImages.splice(index, 1);
            // Update indices for remaining items
            const allThumbItems = additionalImagesPreview.querySelectorAll('.thumb-item');
            console.log('🗑️ Updating indices for', allThumbItems.length, 'remaining items');
            allThumbItems.forEach((item, newIndex) => {
                item.setAttribute('data-image-index', newIndex);
                const button = item.querySelector('button');
                if (button) {
                    button.setAttribute('onclick', `removeExistingAdditionalImage(${newIndex})`);
                }
            });
        } else {
            console.log('⚠️ existingAdditionalImages not found or index out of range');
        }
        
        // If no more images, hide the preview container
        if (additionalImagesPreview.children.length === 0) {
            console.log('🗑️ No more images, hiding container');
            additionalImagesPreview.classList.add('hidden');
        } else {
            console.log('🗑️ Remaining images:', additionalImagesPreview.children.length);
        }
    } else {
        console.error('❌ thumbItem not found for index:', index);
    }
}

// Make functions globally available
window.openAddProjectModal = openAddProjectModal;
window.closeProjectModal = closeProjectModal;
window.editProject = editProject;
window.deleteProject = deleteProject;
window.closeDeleteModal = closeDeleteModal;
window.confirmDelete = confirmDelete;
window.addFeature = addFeature;
window.removeFeature = removeFeature;
window.removeMainImage = removeMainImage;
window.removeAdditionalImage = removeAdditionalImage;
window.removeExistingAdditionalImage = removeExistingAdditionalImage;
window.updateProjectsList = updateProjectsList;