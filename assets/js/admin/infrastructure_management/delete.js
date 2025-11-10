// Delete Project Functionality

// Use global currentProjectId from infrastructure_management.js
// let currentProjectId = null; // Removed - using global variable

// Delete project functions
function deleteProject(projectId) {
    window.currentProjectId = projectId;
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.classList.remove('hidden');
    }
}

function confirmDelete() {
    if (window.currentProjectId) {
        // Show loading state
        const deleteBtn = document.querySelector('#deleteModal button[onclick="confirmDelete()"]');
        if (deleteBtn) {
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>سڕینەوە...';
        }
        
        // Prepare form data
        const formData = new FormData();
        formData.append('csrf_token', getCSRFToken());
        formData.append('action', 'delete_project');
        formData.append('project_id', window.currentProjectId);
        
        // Submit via AJAX
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success - show message and update UI dynamically
                showSuccessMessage(data.message);
                
                // Close delete modal
                closeDeleteModal();
                
                // Update projects list dynamically - reset to page 1 after delete
                if (typeof updateProjectsList === 'function') {
                    updateProjectsList(1, window.currentCategory || '', window.currentSearch || '');
                }
            } else {
                throw new Error(data.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage(error.message || 'هەڵەیەک ڕوویدا لە سڕینەوەی پڕۆژە');
            
            // Re-enable delete button
            if (deleteBtn) {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = 'سڕینەوەی پڕۆژە';
            }
        });
    }
}

// Success message display
function showSuccessMessage(message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    messageDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(messageDiv);
    
    // Remove message after 3 seconds
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
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

function closeDeleteModal() {
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.classList.add('hidden');
    }
    window.currentProjectId = null;
}

// Helper function to get CSRF token
function getCSRFToken() {
    const csrfInput = document.querySelector('input[name="csrf_token"]');
    return csrfInput ? csrfInput.value : '';
}

// Delete confirmation with additional validation
function confirmDeleteWithValidation() {
    if (!window.currentProjectId) {
        console.error('No project ID selected for deletion');
        return;
    }
    
    // Show confirmation dialog
    const confirmed = confirm('Are you sure you want to delete this project? This action cannot be undone.');
    
    if (confirmed) {
        confirmDelete();
    }
}

// Initialize delete functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for delete buttons
    const deleteButtons = document.querySelectorAll('[onclick*="deleteProject"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const projectId = this.getAttribute('data-project-id') || 
                             this.onclick.toString().match(/deleteProject\((\d+)\)/)?.[1];
            if (projectId) {
                deleteProject(projectId);
            }
        });
    });
});

// Override global functions
window.deleteProject = deleteProject;
window.confirmDelete = confirmDelete;
window.closeDeleteModal = closeDeleteModal;
