/**
 * Design Reconstruction Management JavaScript
 * 
 * Main functionality for design reconstruction admin panel
 */

// Global variables
let currentProjectId = null;
let projectsData = [];

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    initializeImageUploads();
    
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
    // This would typically fetch project data and populate the form
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

// Remove additional image
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