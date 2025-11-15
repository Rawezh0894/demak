// Edit Project Functionality

// Edit project functions
function editProject(projectId) {
    const modalTitle = document.getElementById('modalTitle');
    const formAction = document.getElementById('formAction');
    const projectIdInput = document.getElementById('projectId');
    const projectForm = document.getElementById('projectForm');
    const mainImagePreview = document.getElementById('mainImagePreview');
    const additionalImagesPreview = document.getElementById('additionalImagesPreview');
    const projectModal = document.getElementById('projectModal');
    
    if (modalTitle) modalTitle.textContent = 'Edit Project';
    if (formAction) formAction.value = 'edit_project';
    if (projectIdInput) projectIdInput.value = projectId;
    if (projectForm) projectForm.reset();
    
    // Clear deleted images container
    const deletedImagesContainer = document.getElementById('deleted_additional_images_container');
    if (deletedImagesContainer) {
        deletedImagesContainer.innerHTML = '';
    }
    
    // Reset existing images array
    window.existingAdditionalImages = [];
    
    if (mainImagePreview) mainImagePreview.classList.add('hidden');
    if (additionalImagesPreview) {
        additionalImagesPreview.innerHTML = '';
        additionalImagesPreview.classList.add('hidden');
    }
    if (projectModal) projectModal.classList.remove('hidden');
    
    // Load project data via AJAX
    loadProjectData(projectId);
}

// Load project data for editing
function loadProjectData(projectId) {
    // Show loading state
    const modalTitle = document.getElementById('modalTitle');
    if (modalTitle) {
        modalTitle.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>بارکردنی داتا...';
    }
    
    // AJAX call to load project data
    fetch(`../../process/infrastructure_management/get_project.php?project_id=${projectId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateForm(data);
                // Restore modal title
                if (modalTitle) {
                    modalTitle.textContent = 'Edit Project';
                }
            } else {
                throw new Error(data.message || 'Failed to load project data');
            }
        })
        .catch(error => {
            console.error('Error loading project data:', error);
            showErrorMessage('هەڵەیەک ڕوویدا لە بارکردنی داتای پڕۆژە: ' + error.message);
            
            // Restore modal title
            if (modalTitle) {
                modalTitle.textContent = 'Edit Project';
            }
        });
}

// Populate form with project data
function populateForm(data) {
    const project = data.project;
    
    const projectName = document.getElementById('projectName');
    const projectCategory = document.getElementById('projectCategory');
    const projectPrice = document.getElementById('projectPrice');
    const projectDuration = document.getElementById('projectDuration');
    const projectDescription = document.getElementById('projectDescription');
    
    if (projectName) projectName.value = project.name || '';
    if (projectCategory) projectCategory.value = project.category_key || '';
    if (projectPrice) projectPrice.value = project.price || '';
    if (projectDuration) projectDuration.value = project.duration || '';
    if (projectDescription) projectDescription.value = project.description || '';
    
    // Load features
    if (data.features && data.features.length > 0) {
        loadProjectFeatures(data.features);
    }
    
    // Show main image if exists
    if (project.main_image) {
        const mainImagePreview = document.getElementById('mainImagePreview');
        const mainImagePreviewImg = document.getElementById('mainImagePreviewImg');
        if (mainImagePreview && mainImagePreviewImg) {
            mainImagePreviewImg.src = '../../' + project.main_image;
            mainImagePreview.classList.remove('hidden');
        }
    }
    
    // Load additional images (exclude main image)
    if (data.images && data.images.length > 0) {
        // Filter out main image from additional images
        const additionalImages = data.images.filter(img => {
            // Check if this image is the main image by comparing paths
            if (project.main_image) {
                return img.path !== project.main_image;
            }
            return true;
        });
        
        if (additionalImages.length > 0) {
            loadProjectImages(additionalImages);
        }
    }
}

// Load project features
function loadProjectFeatures(features) {
    const featuresContainer = document.getElementById('featuresContainer');
    if (!featuresContainer) return;
    
    // Clear existing features
    featuresContainer.innerHTML = '';
    
    features.forEach((feature, index) => {
        const featureDiv = document.createElement('div');
        featureDiv.className = 'flex items-center space-x-2 mb-2';
        featureDiv.innerHTML = `
            <input type="text" 
                   name="project_features[]" 
                   value="${feature}"
                   placeholder="Add feature"
                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <button type="button" 
                    onclick="removeFeature(this)"
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg">
                <i class="fas fa-trash"></i>
            </button>
        `;
        featuresContainer.appendChild(featureDiv);
    });
}

// Store images to track which ones to delete
window.existingAdditionalImages = [];

// Load project images
function loadProjectImages(images) {
    const additionalImagesPreview = document.getElementById('additionalImagesPreview');
    if (!additionalImagesPreview) return;
    
    // Clear existing preview
    additionalImagesPreview.innerHTML = '';
    
    // Store images for tracking
    window.existingAdditionalImages = images.map(img => ({
        id: img.id || null,
        path: img.path
    }));
    
    if (images.length > 0) {
        additionalImagesPreview.classList.remove('hidden');
        
        images.forEach((image, index) => {
            const thumbItem = document.createElement('div');
            thumbItem.className = 'thumb-item relative';
            thumbItem.setAttribute('data-image-id', image.id || '');
            thumbItem.setAttribute('data-image-index', index);
            thumbItem.innerHTML = `
                <img src="../../${image.path}" class="w-full h-20 object-cover rounded-lg" alt="Project image">
                <button type="button" onclick="removeExistingAdditionalImage(${index})" class="absolute -top-1 -right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs transition-colors duration-200 z-10">
                    <i class="fas fa-times"></i>
                </button>
            `;
            additionalImagesPreview.appendChild(thumbItem);
        });
    } else {
        additionalImagesPreview.classList.add('hidden');
    }
}

// Remove existing additional image (from database)
function removeExistingAdditionalImage(index) {
    const additionalImagesPreview = document.getElementById('additionalImagesPreview');
    if (!additionalImagesPreview) return;
    
    const thumbItem = additionalImagesPreview.querySelector(`[data-image-index="${index}"]`);
    if (thumbItem) {
        // Mark image for deletion by adding to hidden input
        const imageId = thumbItem.getAttribute('data-image-id');
        if (imageId) {
            // Get or create container for deleted image IDs
            let deletedImagesContainer = document.getElementById('deleted_additional_images_container');
            if (!deletedImagesContainer) {
                deletedImagesContainer = document.createElement('div');
                deletedImagesContainer.id = 'deleted_additional_images_container';
                deletedImagesContainer.style.display = 'none';
                document.getElementById('projectForm').appendChild(deletedImagesContainer);
            }
            
            // Check if this image ID is already marked for deletion
            const existingInput = deletedImagesContainer.querySelector(`input[value="${imageId}"]`);
            if (!existingInput) {
                // Create hidden input for this deleted image
                const deletedImageInput = document.createElement('input');
                deletedImageInput.type = 'hidden';
                deletedImageInput.name = 'deleted_additional_images[]';
                deletedImageInput.value = imageId;
                deletedImagesContainer.appendChild(deletedImageInput);
            }
        }
        
        // Remove from preview
        thumbItem.remove();
        
        // Remove from stored array
        if (window.existingAdditionalImages && window.existingAdditionalImages[index]) {
            window.existingAdditionalImages.splice(index, 1);
            // Update indices for remaining items
            const allThumbItems = additionalImagesPreview.querySelectorAll('.thumb-item');
            allThumbItems.forEach((item, newIndex) => {
                item.setAttribute('data-image-index', newIndex);
                const button = item.querySelector('button');
                if (button) {
                    button.setAttribute('onclick', `removeExistingAdditionalImage(${newIndex})`);
                }
            });
        }
        
        // If no more images, hide the preview container
        if (additionalImagesPreview.children.length === 0) {
            additionalImagesPreview.classList.add('hidden');
        }
    }
}

// Error message display
function showErrorMessage(message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    messageDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(messageDiv);
    
    // Remove message after 5 seconds
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

// Store the edit function for modal.js to use
window.editProjectFromEdit = editProject;

// Override global functions - this ensures editProject always works
window.editProject = editProject;
window.loadProjectData = loadProjectData;
window.populateForm = populateForm;
window.loadProjectFeatures = loadProjectFeatures;
window.loadProjectImages = loadProjectImages;
window.removeExistingAdditionalImage = removeExistingAdditionalImage;

// Ensure editProject is always available, even after dynamic updates
(function ensureEditProject() {
    // Store the original function
    const originalEditProject = editProject;
    
    // Create a wrapper that always works
    const editProjectWrapper = function(projectId) {
        console.log('🔧 editProject called with ID:', projectId);
        try {
            return originalEditProject(projectId);
        } catch (error) {
            console.error('❌ Error in editProject:', error);
            // Fallback: try to open modal manually
            const projectModal = document.getElementById('projectModal');
            const projectIdInput = document.getElementById('projectId');
            const formAction = document.getElementById('formAction');
            if (projectModal && projectIdInput && formAction) {
                projectIdInput.value = projectId;
                formAction.value = 'edit_project';
                projectModal.classList.remove('hidden');
                // Try to load data
                if (typeof loadProjectData === 'function') {
                    loadProjectData(projectId);
                }
            }
        }
    };
    
    // Always set it on window
    window.editProject = editProjectWrapper;
    
    // Also store the original for restoration if needed
    window.editProjectOriginal = originalEditProject;
    
    // Re-bind after a short delay to ensure it's available
    setTimeout(() => {
        if (typeof window.editProject === 'undefined' || window.editProject !== editProjectWrapper) {
            window.editProject = editProjectWrapper;
            console.log('✅ editProject re-bound after delay');
        }
    }, 100);
})();
