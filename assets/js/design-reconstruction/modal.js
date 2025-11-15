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
            
            // Clear deleted images container
            const deletedImagesContainer = document.getElementById('deleted_additional_images_container');
            if (deletedImagesContainer) {
                deletedImagesContainer.innerHTML = '';
            }
            
            // Reset existing images array
            window.existingAdditionalImages = [];
            
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
        console.log('🖼️ showAdditionalImagesPreview called with:', images);
        console.log('🖼️ images type:', typeof images);
        console.log('🖼️ images is array:', Array.isArray(images));
        console.log('🖼️ images length:', images ? images.length : 0);
        
        const previewContainer = document.getElementById('additionalImagesPreview');
        console.log('🖼️ previewContainer found:', !!previewContainer);
        
        if (!previewContainer) {
            console.error('❌ additionalImagesPreview container not found!');
            return;
        }
        
        // Clear existing preview
        previewContainer.innerHTML = '';
        
        if (images && images.length > 0) {
            console.log('✅ Showing', images.length, 'additional images');
            previewContainer.classList.remove('hidden');
            
            // Store images for tracking
            if (!window.existingAdditionalImages) {
                window.existingAdditionalImages = [];
            }
            window.existingAdditionalImages = images.map(img => {
                const mapped = {
                    id: typeof img === 'object' ? img.id : null,
                    path: typeof img === 'object' ? img.path : img
                };
                console.log('🖼️ Mapped image:', mapped);
                return mapped;
            });
            
            console.log('🖼️ Stored existingAdditionalImages:', window.existingAdditionalImages);
            
            images.forEach((image, index) => {
                // Handle both string paths and object with id/path
                const imagePath = typeof image === 'object' ? image.path : image;
                const imageId = typeof image === 'object' ? image.id : null;
                
                console.log(`🖼️ Processing image ${index}:`, { image, imagePath, imageId });
                
                const thumbItem = document.createElement('div');
                thumbItem.className = 'thumb-item relative';
                thumbItem.setAttribute('data-image-id', imageId || '');
                thumbItem.setAttribute('data-image-index', index);
                thumbItem.innerHTML = `
                    <img src="../../${imagePath}" alt="Preview ${index + 1}" class="w-full h-20 object-cover rounded-lg">
                    <button type="button" onclick="removeExistingAdditionalImage(${index})" 
                            class="absolute -top-1 -right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs transition-colors duration-200 z-10"
                            title="Remove image">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                previewContainer.appendChild(thumbItem);
                console.log(`✅ Added image ${index} to preview`);
            });
            
            console.log('✅ Total images in preview:', previewContainer.children.length);
        } else {
            console.log('⚠️ No images to show, hiding container');
            previewContainer.classList.add('hidden');
        }
    }
};

// Make ModalManager globally available
window.ModalManager = ModalManager;
