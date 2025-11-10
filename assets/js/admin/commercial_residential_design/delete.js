// Delete Project Functionality

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
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return response.text().then(text => {
                    throw new Error('Server returned HTML instead of JSON.');
                });
            }
        })
        .then(data => {
            if (data.success) {
                if (typeof showSuccessMessage === 'function') {
                    showSuccessMessage(data.message);
                }
                closeDeleteModal();
                if (typeof updateProjectsList === 'function') {
                    updateProjectsList();
                }
            } else {
                throw new Error(data.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof showErrorMessage === 'function') {
                showErrorMessage(error.message || 'هەڵەیەک ڕوویدا لە سڕینەوەی پڕۆژە');
            }
            
            // Re-enable delete button
            if (deleteBtn) {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = 'سڕینەوەی پڕۆژە';
            }
        });
    }
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

// Override global functions
window.deleteProject = deleteProject;
window.confirmDelete = confirmDelete;
window.closeDeleteModal = closeDeleteModal;

