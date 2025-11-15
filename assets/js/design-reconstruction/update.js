/**
 * Design Reconstruction Update Project
 * 
 * Handles updating existing design reconstruction projects
 */

// Update project functionality
const UpdateProjectManager = {
    // Initialize update project functionality
    init() {
        this.bindEvents();
    },
    
    // Bind event listeners
    bindEvents() {
        // Form submission for updates
        const form = document.getElementById('projectForm');
        if (form) {
            form.addEventListener('submit', this.handleUpdateSubmit.bind(this));
        }
    },
    
    // Handle update form submission
    async handleUpdateSubmit(event) {
        event.preventDefault();
        
        const formAction = document.getElementById('formAction').value;
        if (formAction !== 'edit_project') {
            return; // Let add manager handle it
        }
        
        // Validate form
        const validation = this.validateUpdateForm();
        if (!validation.valid) {
            this.showError(validation.message);
            return;
        }
        
        // Show loading state
        this.setLoadingState(true);
        
        try {
            const formData = new FormData(event.target);
            
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Show compression info if available
                if (data.compression_info && window.AddProjectManager && window.AddProjectManager.showCompressionInfo) {
                    window.AddProjectManager.showCompressionInfo(data.compression_info);
                }
                
                this.showSuccess(data.message);
                setTimeout(() => {
                    ModalManager.closeModal('projectModal');
                    // Update project in list instead of reloading
                    if (window.AddProjectManager && window.AddProjectManager.updateProjectInList) {
                        window.AddProjectManager.updateProjectInList(data.project);
                    }
                }, 3000); // Increased timeout to show compression info
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('An error occurred. Please try again.');
        } finally {
            this.setLoadingState(false);
        }
    },
    
    // Validate update form
    validateUpdateForm() {
        const requiredFields = [
            'project_id',
            'project_name',
            'project_category',
            'project_price',
            'project_duration',
            'project_description'
        ];
        
        for (const field of requiredFields) {
            const input = document.querySelector(`[name="${field}"]`);
            if (!input || !input.value.trim()) {
                return {
                    valid: false,
                    message: `${this.getFieldLabel(field)} is required`
                };
            }
        }
        
        // Validate features
        const featuresValidation = FeaturesManager.validateFeatures();
        if (!featuresValidation.valid) {
            return featuresValidation;
        }
        
        return { valid: true };
    },
    
    // Get field label
    getFieldLabel(fieldName) {
        const labels = {
            'project_id': 'Project ID',
            'project_name': 'Project Name',
            'project_category': 'Project Category',
            'project_price': 'Project Price',
            'project_duration': 'Project Duration',
            'project_description': 'Project Description'
        };
        return labels[fieldName] || fieldName;
    },
    
    // Set loading state
    setLoadingState(loading) {
        const submitButton = document.querySelector('#projectForm button[type="submit"]');
        if (submitButton) {
            if (loading) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
            } else {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save mr-2"></i>Update Project';
            }
        }
    },
    
    // Show success message
    showSuccess(message) {
        this.showNotification(message, 'success');
    },
    
    // Show error message
    showError(message) {
        this.showNotification(message, 'error');
    },
    
    // Show notification
    showNotification(message, type = 'info') {
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
    },
    
    // Update project card in UI
    updateProjectCard(projectData) {
        const projectCard = document.querySelector(`[data-project-id="${projectData.id}"]`);
        if (!projectCard) return;
        
        // Update project name
        const nameElement = projectCard.querySelector('h3');
        if (nameElement) {
            nameElement.textContent = projectData.name;
        }
        
        // Update project description
        const descriptionElement = projectCard.querySelector('.project-description');
        if (descriptionElement) {
            descriptionElement.textContent = projectData.description;
        }
        
        // Update project price
        const priceElement = projectCard.querySelector('.price-info');
        if (priceElement) {
            priceElement.textContent = projectData.price;
        }
        
        // Update project duration
        const durationElement = projectCard.querySelector('.duration-info');
        if (durationElement) {
            durationElement.textContent = projectData.duration;
        }
        
        // Update category badge
        const categoryBadge = projectCard.querySelector('.category-badge');
        if (categoryBadge) {
            categoryBadge.textContent = projectData.category_title;
        }
        
        // Update main image if provided
        if (projectData.main_image) {
            const imageElement = projectCard.querySelector('img');
            if (imageElement) {
                imageElement.src = '../../' + projectData.main_image;
            }
        }
        
        // Add update animation
        projectCard.style.transition = 'all 0.3s ease';
        projectCard.style.transform = 'scale(1.02)';
        projectCard.style.boxShadow = '0 10px 25px rgba(139, 92, 246, 0.3)';
        
        setTimeout(() => {
            projectCard.style.transform = 'scale(1)';
            projectCard.style.boxShadow = '';
        }, 300);
    },
    
    // Validate image files
    validateImageFiles(files) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        
        for (const file of files) {
            if (!allowedTypes.includes(file.type)) {
                return {
                    valid: false,
                    message: `Invalid file type: ${file.name}. Only PNG, JPG, and JPEG files are allowed.`
                };
            }
            
            if (file.size > maxSize) {
                return {
                    valid: false,
                    message: `File too large: ${file.name}. Maximum size is 5MB.`
                };
            }
        }
        
        return { valid: true };
    }
};

// Initialize update project manager
document.addEventListener('DOMContentLoaded', function() {
    UpdateProjectManager.init();
});

// Make UpdateProjectManager globally available
window.UpdateProjectManager = UpdateProjectManager;
