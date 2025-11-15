// Modal Functions for Infrastructure Management

// Modal functions
function openAddProjectModal() {
    const modalTitle = document.getElementById('modalTitle');
    const formAction = document.getElementById('formAction');
    const projectId = document.getElementById('projectId');
    const projectForm = document.getElementById('projectForm');
    const mainImagePreview = document.getElementById('mainImagePreview');
    const additionalImagesPreview = document.getElementById('additionalImagesPreview');
    const projectModal = document.getElementById('projectModal');
    
    if (modalTitle) modalTitle.textContent = 'Add New Project';
    if (formAction) formAction.value = 'add_project';
    if (projectId) projectId.value = '';
    if (projectForm) projectForm.reset();
    if (mainImagePreview) mainImagePreview.classList.add('hidden');
    if (additionalImagesPreview) additionalImagesPreview.classList.add('hidden');
    if (projectModal) projectModal.classList.remove('hidden');
}

function editProject(projectId) {
    // Use the editProject function from edit.js if available
    // Otherwise, use the basic modal opening
    if (typeof window.editProjectFromEdit !== 'undefined') {
        window.editProjectFromEdit(projectId);
        return;
    }
    
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
    if (mainImagePreview) mainImagePreview.classList.add('hidden');
    if (additionalImagesPreview) additionalImagesPreview.classList.add('hidden');
    if (projectModal) projectModal.classList.remove('hidden');
    
    // Load project data via AJAX
    if (typeof loadProjectData === 'function') {
        loadProjectData(projectId);
    }
}

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
        console.log('🗑️ Submitting delete request...');
        console.log('🗑️ Delete project ID:', window.currentProjectId);
        console.log('🗑️ Form data entries:', Array.from(formData.entries()));
        console.log('🗑️ Current URL:', window.location.href);
        console.log('🗑️ Full fetch URL:', new URL(window.location.href).href);
        console.log('🗑️ Relative path:', window.location.href);
        console.log('🗑️ Base URL:', window.location.origin + window.location.pathname);
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                console.log('📡 Delete response status:', response.status);
                console.log('📡 Delete response headers:', response.headers.get('content-type'));
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If not JSON, get text to see what we received
                    return response.text().then(text => {
                        console.error('❌ Server returned HTML instead of JSON:');
                        console.error('Response text:', text.substring(0, 500) + '...');
                        throw new Error('Server returned HTML instead of JSON. Check server configuration.');
                    });
                }
            })
        .then(data => {
            console.log('🗑️ Delete response:', data);
            if (data.success) {
                console.log('✅ Delete successful');
                // Success - show message and update UI dynamically
                showSuccessMessage(data.message);
                
                // Close delete modal
                closeDeleteModal();
                
                // Update projects list dynamically - reset to page 1 after delete
                console.log('🔄 Calling updateProjectsList from delete...');
                if (typeof updateProjectsList === 'function') {
                    updateProjectsList(1, window.currentCategory || '', window.currentSearch || '');
                } else {
                    console.error('❌ updateProjectsList function not found');
                }
            } else {
                console.error('❌ Delete failed:', data.message);
                throw new Error(data.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('❌ Delete error:', error);
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

function closeProjectModal() {
    const projectModal = document.getElementById('projectModal');
    if (projectModal) {
        projectModal.classList.add('hidden');
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
window.openAddProjectModal = openAddProjectModal;
window.editProject = editProject;
window.deleteProject = deleteProject;
window.confirmDelete = confirmDelete;
window.closeProjectModal = closeProjectModal;
window.closeDeleteModal = closeDeleteModal;
