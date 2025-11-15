/**
 * Design Reconstruction Add Project
 * 
 * Handles adding new design reconstruction projects
 */

// Track if event listeners are already attached
let addProjectEventListenersAttached = false;

// Add project functionality
const AddProjectManager = {
    // Initialize add project functionality
    init() {
        this.isSubmitting = false;
        this.bindEvents();
    },
    
    // Bind event listeners
    bindEvents() {
        // Add project button
        const addButton = document.querySelector('[onclick="openAddProjectModal()"]');
        if (addButton && !addProjectEventListenersAttached) {
            addButton.addEventListener('click', this.openAddModal.bind(this));
        }
        
        // Form submission - only attach once
        const form = document.getElementById('projectForm');
        const saveButton = document.getElementById('saveProjectBtn');
        if (form && saveButton && !addProjectEventListenersAttached) {
            saveButton.addEventListener('click', this.handleSubmit.bind(this));
            addProjectEventListenersAttached = true;
        }
    },
    
    // Open add modal
    openAddModal() {
        // Reset form
        this.resetForm();
        
        // Set modal title and action
        document.getElementById('modalTitle').textContent = translations.addNewProject;
        document.getElementById('formAction').value = 'add_project';
        document.getElementById('projectId').value = '';
        
        // Open modal
        ModalManager.openModal('projectModal');
    },
    
    // Reset form
    resetForm() {
        const form = document.getElementById('projectForm');
        if (form) {
            form.reset();
        }
        
        // Reset image previews
        const mainImagePreview = document.getElementById('mainImagePreview');
        const additionalImagesPreview = document.getElementById('additionalImagesPreview');
        
        if (mainImagePreview) {
            mainImagePreview.classList.add('hidden');
        }
        
        if (additionalImagesPreview) {
            additionalImagesPreview.classList.add('hidden');
            additionalImagesPreview.innerHTML = ''; // Clear any existing previews
        }
        
        // Reset file inputs
        const mainImageInput = document.getElementById('mainImage');
        const additionalImagesInput = document.getElementById('additionalImages');
        
        if (mainImageInput) {
            mainImageInput.value = '';
        }
        
        if (additionalImagesInput) {
            additionalImagesInput.value = '';
        }
        
        // Reset features
        FeaturesManager.setFeatures([]);
    },
    
    // Handle form submission
    async handleSubmit(event) {
        event.preventDefault();
        
        // Prevent double submission
        if (this.isSubmitting) {
            return;
        }
        this.isSubmitting = true;
        
        // Validate form
        const validation = this.validateForm();
        if (!validation.valid) {
            this.showError(validation.message);
            this.isSubmitting = false;
            return;
        }
        
        // Show loading state
        this.setLoadingState(true);
        
        try {
            const form = document.getElementById('projectForm');
            const formData = new FormData(form);
            
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
             if (data.success) {
                 // Show compression info if available
                 if (data.compression_info) {
                     this.showCompressionInfo(data.compression_info);
                 }
                 
                 this.showSuccess(data.message);
                 setTimeout(() => {
                     ModalManager.closeModal('projectModal');
                     
                     // Check if it's an edit or add operation
                     const formAction = document.getElementById('formAction').value;
                     
                     if (formAction === 'edit_project') {
                         this.updateProjectInList(data.project);
                     } else {
                         this.addProjectToList(data.project);
                     }
                 }, 3000); // Increased timeout to show compression info
             } else {
                 this.showError(data.message);
             }
        } catch (error) {
            this.showError('An error occurred. Please try again.');
        } finally {
            this.setLoadingState(false);
            this.isSubmitting = false;
        }
    },
    
    // Validate form
    validateForm() {
        const requiredFields = [
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
        
        // Validate main image (only required for add mode, not edit mode)
        const formAction = document.getElementById('formAction').value;
        const mainImage = document.getElementById('mainImage');
        const mainImagePreview = document.getElementById('mainImagePreview');
        const hasMainImagePreview = mainImagePreview && !mainImagePreview.classList.contains('hidden');
        
        // Main image is required only when adding a new project
        // In edit mode, if no new image is uploaded, the existing image will be kept
        if (formAction === 'add_project' && !mainImage.files.length) {
            return {
                valid: false,
                message: 'Main image is required'
            };
        }
        
        // In edit mode, we need either a new image or an existing preview
        if (formAction === 'edit_project' && !mainImage.files.length && !hasMainImagePreview) {
            return {
                valid: false,
                message: 'Main image is required'
            };
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
        const submitButton = document.getElementById('saveProjectBtn');
        if (submitButton) {
            if (loading) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            } else {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save mr-2"></i>Save Project';
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
     
     // Show compression info
     showCompressionInfo(compressionInfo) {
         let infoHTML = '<div class="fixed top-20 right-4 z-50 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-4 max-w-md">';
         infoHTML += '<div class="flex items-center justify-between mb-3">';
         infoHTML += '<h3 class="text-lg font-semibold text-gray-900 dark:text-white">زانیاری کۆمپرێسکردنی وێنە</h3>';
         infoHTML += '<button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">';
         infoHTML += '<i class="fas fa-times"></i>';
         infoHTML += '</button>';
         infoHTML += '</div>';
         
         // Main image compression info
         if (compressionInfo.main_image && compressionInfo.main_image.success) {
             const main = compressionInfo.main_image;
             infoHTML += '<div class="mb-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">';
             infoHTML += '<h4 class="font-medium text-gray-900 dark:text-white mb-2">وێنەی سەرەکی:</h4>';
             infoHTML += `<div class="text-sm text-gray-600 dark:text-gray-300">`;
             infoHTML += `<div>سایزی پێش کۆمپرێس: <span class="font-semibold">${main.original_size_formatted}</span></div>`;
             infoHTML += `<div>سایزی دوای کۆمپرێس: <span class="font-semibold text-green-600 dark:text-green-400">${main.compressed_size_formatted}</span></div>`;
             infoHTML += `<div>کەمبوونەوە: <span class="font-semibold text-purple-600 dark:text-purple-400">${main.savings_formatted} (${main.savings_percent}%)</span></div>`;
             infoHTML += `</div>`;
             infoHTML += `</div>`;
         }
         
         // Additional images compression info
         if (compressionInfo.additional_images && compressionInfo.additional_images.length > 0) {
             infoHTML += '<div class="mb-3 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">';
             infoHTML += `<h4 class="font-medium text-gray-900 dark:text-white mb-2">وێنە زیادەکان (${compressionInfo.additional_images.length}):</h4>`;
             
             let totalOriginal = 0;
             let totalCompressed = 0;
             
             compressionInfo.additional_images.forEach((img, index) => {
                 if (img && img.success) {
                     totalOriginal += img.original_size || 0;
                     totalCompressed += img.compressed_size || 0;
                     
                     infoHTML += `<div class="text-sm text-gray-600 dark:text-gray-300 mb-2 pb-2 border-b border-gray-200 dark:border-gray-700 last:border-0">`;
                     infoHTML += `<div class="font-medium mb-1">وێنەی ${index + 1}:</div>`;
                     infoHTML += `<div>پێش: <span class="font-semibold">${img.original_size_formatted}</span> → دوای: <span class="font-semibold text-green-600 dark:text-green-400">${img.compressed_size_formatted}</span></div>`;
                     infoHTML += `<div>کەمبوونەوە: <span class="text-purple-600 dark:text-purple-400">${img.savings_formatted} (${img.savings_percent}%)</span></div>`;
                     infoHTML += `</div>`;
                 }
             });
             
             if (totalOriginal > 0) {
                 const totalSavings = totalOriginal - totalCompressed;
                 const totalSavingsPercent = totalOriginal > 0 ? Math.round((totalSavings / totalOriginal) * 100 * 100) / 100 : 0;
                 const formatSize = (bytes) => {
                     if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
                     if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
                     if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
                     return bytes + ' bytes';
                 };
                 
                 infoHTML += `<div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">`;
                 infoHTML += `<div class="font-semibold text-gray-900 dark:text-white">کۆی گشتی:</div>`;
                 infoHTML += `<div class="text-sm">پێش: <span class="font-semibold">${formatSize(totalOriginal)}</span> → دوای: <span class="font-semibold text-green-600 dark:text-green-400">${formatSize(totalCompressed)}</span></div>`;
                 infoHTML += `<div class="text-sm">کەمبوونەوەی گشتی: <span class="font-semibold text-purple-600 dark:text-purple-400">${formatSize(totalSavings)} (${totalSavingsPercent}%)</span></div>`;
                 infoHTML += `</div>`;
             }
             
             infoHTML += `</div>`;
         }
         
         infoHTML += '</div>';
         
         const infoDiv = document.createElement('div');
         infoDiv.innerHTML = infoHTML;
         document.body.appendChild(infoDiv);
         
         // Auto remove after 8 seconds
         setTimeout(() => {
             if (infoDiv.parentElement) {
                 infoDiv.remove();
             }
         }, 8000);
     },
     
    // Add new project to the list dynamically
    addProjectToList(projectData) {
        // Try multiple selectors
        let projectsContainer = document.getElementById('projectsContainer');
        if (!projectsContainer) {
            projectsContainer = document.getElementById('projectsGrid');
        }
        
        if (!projectsContainer) {
            console.error('Projects container not found');
            return;
        }
        
        // Create project card HTML
        const projectCard = this.createProjectCard(projectData);
        
        // Add to the beginning of the container
        projectsContainer.insertAdjacentHTML('afterbegin', projectCard);
        
        // Update project count
        this.updateProjectCount();
    },
     
    // Create project card HTML
    createProjectCard(project) {
        const categoryName = project.category_name || 'Unknown';
        const mainImage = project.main_image ? 
            `../../${project.main_image}` : 
            null;
        
        const cardHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden project-card transition-all duration-300 hover:shadow-2xl hover:-translate-y-2" 
                 data-project-id="${project.id}"
                 data-category="${project.category_key || ''}" 
                 data-name="${project.name ? project.name.toLowerCase() : ''}"
                 data-price="${project.price || ''}">
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
                        ${project.name || 'Untitled'}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-base mb-6 line-clamp-2">
                        ${project.description || 'No description available'}
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
                    <div class="flex space-x-3">
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
        
        return cardHTML;
    },
     
    // Update project count
    updateProjectCount() {
        // Try multiple selectors
        let projectsContainer = document.getElementById('projectsContainer');
        if (!projectsContainer) {
            projectsContainer = document.getElementById('projectsGrid');
        }
        
        if (!projectsContainer) {
            console.error('Projects container not found');
            return;
        }
        
        const projectCards = projectsContainer.querySelectorAll('.project-card');
        const countElement = document.querySelector('.projects-count');
        
        if (countElement) {
            countElement.textContent = projectCards.length;
        }
    },
     
    // Update existing project in the list
    updateProjectInList(projectData) {
        const projectCard = document.querySelector(`[data-project-id="${projectData.id}"]`);
        if (projectCard) {
            // Replace the existing card with updated data
            const newProjectCard = this.createProjectCard(projectData);
            projectCard.outerHTML = newProjectCard;
        }
    }
 };

// Initialize add project manager
document.addEventListener('DOMContentLoaded', function() {
    AddProjectManager.init();
});

// Make AddProjectManager globally available
window.AddProjectManager = AddProjectManager;
