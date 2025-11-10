/**
 * Design Reconstruction Modal Management
 * 
 * Handles modal operations for design reconstruction projects
 */

// Modal management functions
const ModalManager = {
    // Open modal with animation
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.9)';
            
            // Animate in
            setTimeout(() => {
                modal.style.transition = 'all 0.3s ease';
                modal.style.opacity = '1';
                modal.style.transform = 'scale(1)';
            }, 10);
        }
    },
    
    // Close modal with animation
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.transition = 'all 0.3s ease';
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.9)';
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    },
    
    // Reset modal form
    resetForm(formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.reset();
            
            // Reset image previews
            const mainImagePreview = document.getElementById('mainImagePreview');
            const additionalImagesPreview = document.getElementById('additionalImagesPreview');
            
            if (mainImagePreview) mainImagePreview.classList.add('hidden');
            if (additionalImagesPreview) {
                additionalImagesPreview.classList.add('hidden');
                additionalImagesPreview.innerHTML = '';
            }
            
            // Reset features
            this.resetFeatures();
        }
    },
    
    // Reset features container
    resetFeatures() {
        const featuresContainer = document.getElementById('featuresContainer');
        if (featuresContainer) {
            featuresContainer.innerHTML = `
                <div class="flex items-center space-x-2 mb-2">
                    <input type="text" 
                           name="project_features[]" 
                           placeholder="${translations.addFeature}"
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <button type="button" 
                            onclick="removeFeature(this)"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        }
    },
    
    // Populate form with project data
    populateForm(projectData) {
        document.getElementById('projectName').value = projectData.name || '';
        document.getElementById('projectCategory').value = projectData.category_key || '';
        document.getElementById('projectPrice').value = projectData.price || '';
        document.getElementById('projectDuration').value = projectData.duration || '';
        document.getElementById('projectDescription').value = projectData.description || '';
        
        // Populate features
        if (projectData.features && projectData.features.length > 0) {
            this.resetFeatures();
            const featuresContainer = document.getElementById('featuresContainer');
            
            projectData.features.forEach((feature, index) => {
                if (index === 0) {
                    // Update first feature input
                    const firstInput = featuresContainer.querySelector('input[name="project_features[]"]');
                    if (firstInput) {
                        firstInput.value = feature;
                    }
                } else {
                    // Add new feature inputs
                    this.addFeatureInput(feature);
                }
            });
        }
        
        // Handle images
        if (projectData.main_image) {
            this.showMainImagePreview(projectData.main_image);
        }
        
        if (projectData.images && projectData.images.length > 1) {
            this.showAdditionalImagesPreview(projectData.images.slice(1));
        }
    },
    
    // Add feature input
    addFeatureInput(value = '') {
        const featuresContainer = document.getElementById('featuresContainer');
        const newFeature = document.createElement('div');
        newFeature.className = 'flex items-center space-x-2 mb-2';
        newFeature.innerHTML = `
            <input type="text" 
                   name="project_features[]" 
                   value="${value}"
                   placeholder="${translations.addFeature}"
                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <button type="button" 
                    onclick="removeFeature(this)"
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg">
                <i class="fas fa-trash"></i>
            </button>
        `;
        featuresContainer.appendChild(newFeature);
    },
    
    // Show main image preview
    showMainImagePreview(imagePath) {
        const preview = document.getElementById('mainImagePreview');
        const previewImg = document.getElementById('mainImagePreviewImg');
        
        if (preview && previewImg) {
            previewImg.src = '../../' + imagePath;
            preview.classList.remove('hidden');
        }
    },
    
    // Show additional images preview
    showAdditionalImagesPreview(images) {
        const previewContainer = document.getElementById('additionalImagesPreview');
        
        if (previewContainer && images.length > 0) {
            previewContainer.innerHTML = '';
            previewContainer.classList.remove('hidden');
            
            images.forEach((image, index) => {
                const thumbItem = document.createElement('div');
                thumbItem.className = 'thumb-item';
                thumbItem.innerHTML = `
                    <img src="../../${image}" alt="Preview ${index + 1}" class="w-full h-20 object-cover rounded-lg">
                    <button type="button" onclick="removeAdditionalImage(${index})" 
                            title="Remove image">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                `;
                previewContainer.appendChild(thumbItem);
            });
        }
    }
};

// Make ModalManager globally available
window.ModalManager = ModalManager;
