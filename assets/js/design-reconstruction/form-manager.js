/**
 * Design Reconstruction API Manager
 * 
 * Professional API management for design reconstruction projects
 */

class DesignReconstructionAPI {
    constructor() {
        this.baseURL = window.location.href;
        this.csrfToken = document.querySelector('input[name="csrf_token"]')?.value;
    }
    
    /**
     * Send API request
     */
    async request(endpoint, options = {}) {
        const defaultOptions = {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        const config = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(this.baseURL, config);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    }
    
    /**
     * Add new project
     */
    async addProject(formData) {
        formData.append('csrf_token', this.csrfToken);
        formData.append('action', 'add_project');
        
        return await this.request(this.baseURL, {
            method: 'POST',
            body: formData
        });
    }
    
    /**
     * Update existing project
     */
    async updateProject(formData) {
        formData.append('csrf_token', this.csrfToken);
        formData.append('action', 'edit_project');
        
        return await this.request(this.baseURL, {
            method: 'POST',
            body: formData
        });
    }
    
    /**
     * Delete project
     */
    async deleteProject(projectId) {
        const formData = new FormData();
        formData.append('csrf_token', this.csrfToken);
        formData.append('action', 'delete_project');
        formData.append('project_id', projectId);
        
        return await this.request(this.baseURL, {
            method: 'POST',
            body: formData
        });
    }
    
    /**
     * Get project data
     */
    async getProject(projectId) {
        const response = await fetch(`get_project.php?id=${projectId}`);
        return await response.json();
    }
}

/**
 * Design Reconstruction Form Manager
 * 
 * Professional form management and validation
 */

class DesignReconstructionFormManager {
    constructor() {
        this.form = document.getElementById('projectForm');
        this.isSubmitting = false;
        this.api = new DesignReconstructionAPI();
    }
    
    /**
     * Initialize form manager
     */
    init() {
        this.bindEvents();
        this.setupValidation();
    }
    
    /**
     * Bind form events
     * Note: Form submission is handled by AddProjectManager to avoid duplicate submissions
     */
    bindEvents() {
        // Form submission is handled by AddProjectManager in add.js
        // No need to add event listener here to avoid duplicate submissions
    }
    
    /**
     * Setup form validation
     */
    setupValidation() {
        if (this.form) {
            this.form.addEventListener('input', this.validateField.bind(this));
            this.form.addEventListener('change', this.validateField.bind(this));
        }
    }
    
    /**
     * Validate individual field
     */
    validateField(event) {
        const field = event.target;
        const fieldName = field.name;
        const value = field.value.trim();
        
        // Remove existing error styling
        this.removeFieldError(field);
        
        // Validate based on field type
        switch (fieldName) {
            case 'project_name':
                if (value.length < 3) {
                    this.showFieldError(field, 'Project name must be at least 3 characters');
                }
                break;
                
            case 'project_price':
                // Allow text and numbers (for prices like "100,000 دینار" or "100000")
                // No strict validation - just check if field is not empty
                // The required attribute will handle empty field validation
                break;
                
            case 'project_description':
                if (value.length < 10) {
                    this.showFieldError(field, 'Description must be at least 10 characters');
                }
                break;
        }
    }
    
    /**
     * Show field error
     */
    showFieldError(field, message) {
        field.classList.add('border-red-500', 'focus:border-red-500');
        
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error text-red-500 text-sm mt-1';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }
    
    /**
     * Remove field error
     */
    removeFieldError(field) {
        field.classList.remove('border-red-500', 'focus:border-red-500');
        
        const errorDiv = field.parentNode.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
    
    /**
     * Validate entire form
     */
    validateForm() {
        const errors = [];
        const requiredFields = [
            'project_name',
            'project_category',
            'project_price',
            'project_duration',
            'project_description'
        ];
        
        requiredFields.forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field && !field.value.trim()) {
                this.showFieldError(field, `${this.getFieldLabel(fieldName)} is required`);
                errors.push(`${this.getFieldLabel(fieldName)} is required`);
            }
        });
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    }
    
    /**
     * Get field label
     */
    getFieldLabel(fieldName) {
        const labels = {
            'project_name': 'Project Name',
            'project_category': 'Project Category',
            'project_price': 'Project Price',
            'project_duration': 'Project Duration',
            'project_description': 'Project Description'
        };
        
        return labels[fieldName] || fieldName;
    }
    
    /**
     * Handle form submission
     */
    async handleSubmit(event) {
        event.preventDefault();
        
        if (this.isSubmitting) {
            return;
        }
        
        // Validate form
        const validation = this.validateForm();
        if (!validation.valid) {
            this.showNotification('Please fix the errors before submitting', 'error');
            return;
        }
        
        this.isSubmitting = true;
        this.setLoadingState(true);
        
        try {
            const formData = new FormData(this.form);
            const formAction = document.getElementById('formAction').value;
            
            // Debug: Check additional images
            const additionalImagesInput = document.getElementById('additionalImages');
            console.log('📤 Form submission - additionalImagesInput:', additionalImagesInput);
            console.log('📤 Form submission - additionalImagesInput.files:', additionalImagesInput ? additionalImagesInput.files : 'N/A');
            console.log('📤 Form submission - additionalImagesInput.files.length:', additionalImagesInput ? additionalImagesInput.files.length : 'N/A');
            
            // Check FormData for additional_images
            const additionalImagesInFormData = formData.getAll('additional_images[]');
            console.log('📤 FormData - additional_images[] count:', additionalImagesInFormData.length);
            for (let i = 0; i < additionalImagesInFormData.length; i++) {
                const file = additionalImagesInFormData[i];
                console.log(`📤 FormData - additional_images[${i}]:`, file instanceof File ? file.name : file);
            }
            
            // Log all FormData entries
            console.log('📤 FormData entries:');
            for (let pair of formData.entries()) {
                if (pair[1] instanceof File) {
                    console.log(`📤 ${pair[0]}: File - ${pair[1].name} (${pair[1].size} bytes)`);
                } else {
                    console.log(`📤 ${pair[0]}: ${pair[1]}`);
                }
            }
            
            let response;
            if (formAction === 'edit_project') {
                response = await this.api.updateProject(formData);
            } else {
                response = await this.api.addProject(formData);
            }
            
            if (response.success) {
                this.showNotification(response.message, 'success');
                this.handleSuccess(response);
            } else {
                this.showNotification(response.message, 'error');
            }
        } catch (error) {
            this.showNotification('An error occurred. Please try again.', 'error');
        } finally {
            this.setLoadingState(false);
            this.isSubmitting = false;
        }
    }
    
    /**
     * Handle successful submission
     */
    handleSuccess(response) {
        setTimeout(() => {
            ModalManager.closeModal('projectModal');
            
            const formAction = document.getElementById('formAction').value;
            if (formAction === 'edit_project') {
                if (window.AddProjectManager && window.AddProjectManager.updateProjectInList) {
                    window.AddProjectManager.updateProjectInList(response.project);
                }
            } else {
                if (window.AddProjectManager && window.AddProjectManager.addProjectToList) {
                    window.AddProjectManager.addProjectToList(response.project);
                }
            }
        }, 1500);
    }
    
    /**
     * Set loading state
     */
    setLoadingState(loading) {
        const saveButton = document.getElementById('saveProjectBtn');
        if (saveButton) {
            if (loading) {
                saveButton.disabled = true;
                saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            } else {
                saveButton.disabled = false;
                saveButton.innerHTML = '<i class="fas fa-save mr-2"></i>Save Project';
            }
        }
    }
    
    /**
     * Show notification
     */
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
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
}

// Initialize form manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const formManager = new DesignReconstructionFormManager();
    formManager.init();
    
    // Make form manager globally available
    window.DesignReconstructionFormManager = formManager;
});
