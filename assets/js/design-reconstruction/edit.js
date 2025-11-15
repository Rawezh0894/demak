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
        console.log('🚀 openEditModal called with projectId:', projectId);
        try {
            // Show loading
            this.showLoading();
            
            // Fetch project data
            console.log('📡 Fetching project data...');
            const projectData = await this.fetchProjectData(projectId);
            console.log('📡 Received projectData:', projectData);
            
            if (projectData) {
                console.log('✅ Project data received, populating form...');
                this.populateEditForm(projectData);
                console.log('✅ Form populated, opening modal...');
                this.openModal();
                console.log('✅ Modal opened');
            } else {
                console.error('❌ Project data is null or undefined');
                this.showError('Project not found');
            }
        } catch (error) {
            console.error('❌ Error fetching project:', error);
            console.error('❌ Error stack:', error.stack);
            this.showError('Error loading project data');
        } finally {
            this.hideLoading();
        }
    },
    
    // Fetch project data
    async fetchProjectData(projectId) {
        console.log('📡 Fetching project data for ID:', projectId);
        const response = await fetch(`../../process/design-reconstruction/get_project.php?id=${projectId}`);
        
        if (!response.ok) {
            console.error('❌ HTTP error! status:', response.status);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('📡 Received project data:', data);
        console.log('📡 Project images:', data.project ? data.project.images : 'N/A');
        console.log('📡 Project main_image:', data.project ? data.project.main_image : 'N/A');
        
        if (data.success) {
            return data.project;
        } else {
            console.error('❌ API error:', data.message);
            throw new Error(data.message);
        }
    },
    
    // Populate edit form
    populateEditForm(projectData) {
        console.log('🔧 populateEditForm called with projectData:', projectData);
        console.log('🔧 projectData.images:', projectData.images);
        console.log('🔧 projectData.main_image:', projectData.main_image);
        
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
            console.log('🖼️ Showing main image:', projectData.main_image);
            ModalManager.showMainImagePreview(projectData.main_image);
        } else {
            console.log('⚠️ No main image found');
        }
        
        // Handle additional images (exclude main image)
        console.log('🖼️ Processing additional images...');
        console.log('🖼️ projectData.images type:', typeof projectData.images);
        console.log('🖼️ projectData.images is array:', Array.isArray(projectData.images));
        console.log('🖼️ projectData.images length:', projectData.images ? projectData.images.length : 0);
        
        if (projectData.images && Array.isArray(projectData.images) && projectData.images.length > 0) {
            // Filter out main image from additional images
            // Images are now objects with id and path, or strings
            const additionalImages = projectData.images.filter(img => {
                const imgPath = typeof img === 'object' ? img.path : img;
                const isMain = imgPath === projectData.main_image;
                console.log('🖼️ Checking image:', img, 'path:', imgPath, 'isMain:', isMain);
                return !isMain;
            });
            
            console.log('🖼️ Filtered additional images:', additionalImages);
            console.log('🖼️ Additional images count:', additionalImages.length);
            
            if (additionalImages.length > 0) {
                console.log('✅ Calling ModalManager.showAdditionalImagesPreview with:', additionalImages);
                ModalManager.showAdditionalImagesPreview(additionalImages);
            } else {
                console.log('⚠️ No additional images after filtering');
                // Clear preview if no additional images
                const previewContainer = document.getElementById('additionalImagesPreview');
                if (previewContainer) {
                    previewContainer.innerHTML = '';
                    previewContainer.classList.add('hidden');
                }
            }
        } else {
            console.log('⚠️ No images array or empty array');
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
