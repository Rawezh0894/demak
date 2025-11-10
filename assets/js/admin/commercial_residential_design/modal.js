// Modal Functions for Commercial Residential Design Management

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
            if (typeof showErrorMessage === 'function') {
                showErrorMessage(error.message || 'هەڵەیەک ڕوویدا لە سڕینەوەی پڕۆژە');
            }
            if (deleteBtn) {
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = 'سڕینەوەی پڕۆژە';
            }
        });
    }
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

