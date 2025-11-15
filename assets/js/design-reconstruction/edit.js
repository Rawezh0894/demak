/**
 * Design Reconstruction Edit Project
 * 
 * Handles editing existing design reconstruction projects
 */

// Edit project functionality
const EditProjectManager = {
    // Initialize edit project functionality
    init() {
        this.bindEvents();
    },
    
    // Bind event listeners
    bindEvents() {
        // Edit buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('[onclick*="editProject"]')) {
                const projectId = this.extractProjectId(e.target.closest('[onclick*="editProject"]').getAttribute('onclick'));
                this.openEditModal(projectId);
            }
        });
    },
    
    // Extract project ID from onclick attribute
    extractProjectId(onclickAttr) {
        const match = onclickAttr.match(/editProject\((\d+)\)/);
        return match ? parseInt(match[1]) : null;
    },
    
    // Open edit modal
    async openEditModal(projectId) {
        try {
            // Show loading
            this.showLoading();
            
            // Fetch project data
            const projectData = await this.fetchProjectData(projectId);
            
            if (projectData) {
                this.populateEditForm(projectData);
                this.openModal();
            } else {
                this.showError('Project not found');
            }
        } catch (error) {
            console.error('Error fetching project:', error);
            this.showError('Error loading project data');
        } finally {
            this.hideLoading();
        }
    },
    
    // Fetch project data
    async fetchProjectData(projectId) {
        const response = await fetch(`../../process/design-reconstruction/get_project.php?id=${projectId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            return data.project;
        } else {
            throw new Error(data.message);
        }
    },
    
    // Populate edit form
    populateEditForm(projectData) {
        // Set form values
        document.getElementById('projectName').value = projectData.name || '';
        document.getElementById('projectCategory').value = projectData.category_key || '';
        document.getElementById('projectPrice').value = projectData.price || '';
        document.getElementById('projectDuration').value = projectData.duration || '';
        document.getElementById('projectDescription').value = projectData.description || '';
        
        // Set modal title and action
        document.getElementById('modalTitle').textContent = translations.editProject;
        document.getElementById('formAction').value = 'edit_project';
        document.getElementById('projectId').value = projectData.id;
        
        // Handle features
        if (projectData.features && projectData.features.length > 0) {
            FeaturesManager.setFeatures(projectData.features);
        } else {
            FeaturesManager.setFeatures([]);
        }
        
        // Handle images
        if (projectData.main_image) {
            ModalManager.showMainImagePreview(projectData.main_image);
        }
        
        // Handle additional images (exclude main image)
        if (projectData.images && Array.isArray(projectData.images) && projectData.images.length > 0) {
            // Filter out main image from additional images
            // Images are now objects with id and path, or strings
            const additionalImages = projectData.images.filter(img => {
                const imgPath = typeof img === 'object' ? img.path : img;
                return imgPath !== projectData.main_image;
            });
            
            if (additionalImages.length > 0) {
                ModalManager.showAdditionalImagesPreview(additionalImages);
            } else {
                // Clear preview if no additional images
                const previewContainer = document.getElementById('additionalImagesPreview');
                if (previewContainer) {
                    previewContainer.innerHTML = '';
                    previewContainer.classList.add('hidden');
                }
            }
        } else {
            // Clear preview if no images
            const previewContainer = document.getElementById('additionalImagesPreview');
            if (previewContainer) {
                previewContainer.innerHTML = '';
                previewContainer.classList.add('hidden');
            }
        }
    },
    
    // Open modal
    openModal() {
        ModalManager.openModal('projectModal');
    },
    
    // Show loading
    showLoading() {
        const loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'editLoadingOverlay';
        loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        loadingOverlay.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center">
                <i class="fas fa-spinner fa-spin mr-3 text-purple-600"></i>
                <span class="text-gray-700 dark:text-gray-300">Loading project data...</span>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
    },
    
    // Hide loading
    hideLoading() {
        const loadingOverlay = document.getElementById('editLoadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    },
    
    // Show error
    showError(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg bg-red-500 text-white';
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
};

// Initialize edit project manager
document.addEventListener('DOMContentLoaded', function() {
    EditProjectManager.init();
});

// Make EditProjectManager globally available
window.EditProjectManager = EditProjectManager;
