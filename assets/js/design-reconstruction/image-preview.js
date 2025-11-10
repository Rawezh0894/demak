/**
 * Design Reconstruction Image Preview Management
 * 
 * Handles image upload and preview functionality
 */

// Image preview management functions
const ImagePreviewManager = {
    // Handle main image upload
    handleMainImageUpload(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        // Validate file type
        if (!this.validateImageFile(file)) {
            this.showImageError('Please select a valid image file (PNG, JPG, JPEG)');
            return;
        }
        
        // Validate file size
        if (!this.validateFileSize(file)) {
            this.showImageError('Image size must be less than 5MB');
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            this.showMainImagePreview(e.target.result);
        };
        reader.readAsDataURL(file);
    },
    
    // Handle additional images upload
    handleAdditionalImagesUpload(event) {
        const files = Array.from(event.target.files);
        const previewContainer = document.getElementById('additionalImagesPreview');
        
        if (!previewContainer) return;
        
        // Clear existing previews
        previewContainer.innerHTML = '';
        
        if (files.length === 0) {
            previewContainer.classList.add('hidden');
            return;
        }
        
        // Validate files
        const validFiles = files.filter(file => {
            if (!this.validateImageFile(file)) {
                this.showImageError(`Invalid file type: ${file.name}`);
                return false;
            }
            if (!this.validateFileSize(file)) {
                this.showImageError(`File too large: ${file.name}`);
                return false;
            }
            return true;
        });
        
        if (validFiles.length === 0) {
            previewContainer.classList.add('hidden');
            return;
        }
        
        // Show previews
        previewContainer.classList.remove('hidden');
        
        validFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.addImagePreview(e.target.result, index, previewContainer);
            };
            reader.readAsDataURL(file);
        });
    },
    
    // Show main image preview
    showMainImagePreview(imageSrc) {
        const preview = document.getElementById('mainImagePreview');
        const previewImg = document.getElementById('mainImagePreviewImg');
        
        if (preview && previewImg) {
            previewImg.src = imageSrc;
            preview.classList.remove('hidden');
            
            // Add animation
            preview.style.opacity = '0';
            preview.style.transform = 'scale(0.8)';
            setTimeout(() => {
                preview.style.transition = 'all 0.3s ease';
                preview.style.opacity = '1';
                preview.style.transform = 'scale(1)';
            }, 10);
        }
    },
    
    // Add image preview to container
    addImagePreview(imageSrc, index, container) {
        const thumbItem = document.createElement('div');
        thumbItem.className = 'thumb-item';
        thumbItem.innerHTML = `
            <img src="${imageSrc}" alt="Preview ${index + 1}" class="w-full h-20 object-cover rounded-lg">
            <button type="button" 
                    onclick="removeAdditionalImage(${index})" 
                    title="Remove image">
                <i class="fas fa-times text-xs"></i>
            </button>
        `;
        
        container.appendChild(thumbItem);
        
        // Add animation
        thumbItem.style.opacity = '0';
        thumbItem.style.transform = 'scale(0.8)';
        setTimeout(() => {
            thumbItem.style.transition = 'all 0.3s ease';
            thumbItem.style.opacity = '1';
            thumbItem.style.transform = 'scale(1)';
        }, index * 100);
    },
    
    // Remove main image
    removeMainImage() {
        const preview = document.getElementById('mainImagePreview');
        const input = document.getElementById('mainImage');
        
        if (preview) {
            preview.style.transition = 'all 0.3s ease';
            preview.style.opacity = '0';
            preview.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                preview.classList.add('hidden');
            }, 300);
        }
        
        if (input) {
            input.value = '';
        }
    },
    
    // Remove additional image
    removeAdditionalImage(index) {
        const previewContainer = document.getElementById('additionalImagesPreview');
        if (!previewContainer) return;
        
        const thumbItem = previewContainer.children[index];
        if (thumbItem) {
            thumbItem.style.transition = 'all 0.3s ease';
            thumbItem.style.opacity = '0';
            thumbItem.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                thumbItem.remove();
                
                // Hide container if no images left
                if (previewContainer.children.length === 0) {
                    previewContainer.classList.add('hidden');
                }
            }, 300);
        }
        
        // Clear the file input
        const fileInput = document.getElementById('additionalImages');
        if (fileInput) {
            fileInput.value = '';
        }
    },
    
    // Validate image file type
    validateImageFile(file) {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        return allowedTypes.includes(file.type);
    },
    
    // Validate file size
    validateFileSize(file) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        return file.size <= maxSize;
    },
    
    // Show image error
    showImageError(message) {
        // Create error notification
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
    },
    
    // Initialize drag and drop
    initializeDragAndDrop() {
        const dropzones = document.querySelectorAll('.upload-dropzone');
        
        dropzones.forEach(dropzone => {
            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('border-purple-500', 'bg-purple-50');
            });
            
            dropzone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-purple-500', 'bg-purple-50');
            });
            
            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-purple-500', 'bg-purple-50');
                
                const files = e.dataTransfer.files;
                const input = dropzone.querySelector('input[type="file"]');
                
                if (input && files.length > 0) {
                    input.files = files;
                    
                    // Trigger change event
                    const event = new Event('change', { bubbles: true });
                    input.dispatchEvent(event);
                }
            });
        });
    }
};

// Track if event listeners are already attached
let eventListenersAttached = false;

// Initialize image preview management
document.addEventListener('DOMContentLoaded', function() {
    ImagePreviewManager.initializeDragAndDrop();
    
    // Bind event listeners only once
    if (!eventListenersAttached) {
        const mainImageInput = document.getElementById('mainImage');
        if (mainImageInput) {
            mainImageInput.addEventListener('change', ImagePreviewManager.handleMainImageUpload.bind(ImagePreviewManager));
        }
        
        const additionalImagesInput = document.getElementById('additionalImages');
        if (additionalImagesInput) {
            additionalImagesInput.addEventListener('change', ImagePreviewManager.handleAdditionalImagesUpload.bind(ImagePreviewManager));
        }
        
        eventListenersAttached = true;
    }
});

// Make ImagePreviewManager globally available
window.ImagePreviewManager = ImagePreviewManager;
window.removeMainImage = ImagePreviewManager.removeMainImage.bind(ImagePreviewManager);
window.removeAdditionalImage = ImagePreviewManager.removeAdditionalImage.bind(ImagePreviewManager);
