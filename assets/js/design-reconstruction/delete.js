/**
 * Design Reconstruction Delete Project
 * 
 * Handles deleting design reconstruction projects
 */

// Delete project functionality
const DeleteProjectManager = {
    // Current project ID for deletion
    currentProjectId: null,
    
    // Initialize delete project functionality
    init() {
        this.currentProjectId = null;
        this.bindEvents();
    },
    
    // Bind event listeners
    bindEvents() {
        // Delete buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('[onclick*="deleteProject"]')) {
                const projectId = this.extractProjectId(e.target.closest('[onclick*="deleteProject"]').getAttribute('onclick'));
                this.openDeleteModal(projectId);
            }
        });
        
        // Confirm delete button
        const confirmButton = document.querySelector('[onclick="confirmDelete()"]');
        if (confirmButton) {
            confirmButton.addEventListener('click', this.handleDelete.bind(this));
        }
        
        // Cancel delete button
        const cancelButton = document.querySelector('[onclick="closeDeleteModal()"]');
        if (cancelButton) {
            cancelButton.addEventListener('click', this.closeDeleteModal.bind(this));
        }
    },
    
    // Extract project ID from onclick attribute
    extractProjectId(onclickAttr) {
        const match = onclickAttr.match(/deleteProject\((\d+)\)/);
        return match ? parseInt(match[1]) : null;
    },
    
    // Open delete modal
    openDeleteModal(projectId) {
        if (!projectId) {
            console.error('Project ID is required to open delete modal');
            return;
        }
        
        this.currentProjectId = projectId;
        
        // Get project name for confirmation
        const projectCard = document.querySelector(`[data-project-id="${projectId}"]`);
        const projectName = projectCard ? (projectCard.querySelector('h3')?.textContent || 'this project') : 'this project';
        
        // Update modal content
        const modalContent = document.querySelector('#deleteModal p');
        if (modalContent) {
            modalContent.textContent = `Are you sure you want to delete "${projectName}"? This action cannot be undone.`;
        }
        
        // Open modal
        ModalManager.openModal('deleteModal');
    },
    
    // Close delete modal
    closeDeleteModal() {
        ModalManager.closeModal('deleteModal');
        this.currentProjectId = null;
    },
    
    // Handle delete
    async handleDelete(event) {
        if (event) {
            event.preventDefault();
        }
        
        if (!this.currentProjectId) {
            console.error('No project selected for deletion');
            this.showError('No project selected for deletion');
            return;
        }
        
        try {
            // Show loading
            this.setDeleteLoading(true);
            
            const formData = new FormData();
            formData.append('csrf_token', window.csrfToken);
            formData.append('action', 'delete_project');
            formData.append('project_id', this.currentProjectId);
            
            console.log('Deleting project:', this.currentProjectId);
            console.log('CSRF Token:', window.csrfToken);
            
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });
            
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('HTTP Error Response:', errorText);
                throw new Error(`HTTP error! status: ${response.status}, body: ${errorText}`);
            }
            
            const responseText = await response.text();
            console.log('Response text:', responseText);
            
            let data;
            try {
                data = JSON.parse(responseText);
                console.log('Parsed response data:', data);
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                console.error('Response text that failed to parse:', responseText);
                throw new Error('Invalid JSON response from server');
            }
            
            if (data.success) {
                this.showSuccess(data.message);
                
                // Store project ID from response or currentProjectId
                const projectIdToRemove = data.project_id || this.currentProjectId;
                
                if (!projectIdToRemove) {
                    console.error('Project ID is missing in response');
                    this.showError('Project ID is missing');
                    this.closeDeleteModal();
                    return;
                }
                
                this.closeDeleteModal();
                
                // Remove project card with animation
                this.removeProjectCard(projectIdToRemove);
                
                // Update project count
                this.updateProjectCount();
            } else {
                console.error('Delete failed:', data.message);
                this.showError(data.message || 'Failed to delete project');
            }
        } catch (error) {
            console.error('Delete error details:', error);
            console.error('Error name:', error.name);
            console.error('Error message:', error.message);
            console.error('Error stack:', error.stack);
            this.showError('An error occurred. Please try again. Error: ' + error.message);
        } finally {
            this.setDeleteLoading(false);
        }
    },
    
    // Remove project card with animation
    removeProjectCard(projectId) {
        if (!projectId) {
            console.error('Project ID is null or undefined');
            return;
        }
        
        const projectCard = document.querySelector(`[data-project-id="${projectId}"]`);
        if (projectCard) {
            projectCard.style.transition = 'all 0.5s ease';
            projectCard.style.opacity = '0';
            projectCard.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                projectCard.remove();
            }, 500);
        } else {
            console.error('Project card not found for ID:', projectId);
        }
    },
    
    // Set delete loading state
    setDeleteLoading(loading) {
        const confirmButton = document.querySelector('[onclick="confirmDelete()"]');
        if (confirmButton) {
            if (loading) {
                confirmButton.disabled = true;
                confirmButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...';
            } else {
                confirmButton.disabled = false;
                confirmButton.innerHTML = 'Delete Project';
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
    }
};

// Initialize delete project manager
document.addEventListener('DOMContentLoaded', function() {
    DeleteProjectManager.init();
});

// Make DeleteProjectManager globally available
window.DeleteProjectManager = DeleteProjectManager;
