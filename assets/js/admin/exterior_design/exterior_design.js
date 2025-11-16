// Exterior Design Management JavaScript

// Global variables
let currentProjectId = null;
let projectToDelete = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeSort();
    initializeFormHandling();
    initializeBrowserHistory();
    initializeImagePreview();
});

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const projectCards = document.querySelectorAll('.project-card');
            let visibleCount = 0;
            
            projectCards.forEach(card => {
                const projectName = card.dataset.name;
                if (projectName && projectName.includes(searchTerm)) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            const noProjectsFound = document.getElementById('noProjectsFound');
            if (noProjectsFound) {
                noProjectsFound.classList.toggle('hidden', visibleCount > 0);
            }
        });
    }
}

// Sort functionality
function initializeSort() {
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const sortBy = this.value;
            const projectsGrid = document.getElementById('projectsGrid');
            const projectCards = Array.from(document.querySelectorAll('.project-card'));
            
            projectCards.sort((a, b) => {
                switch(sortBy) {
                    case 'name_asc':
                        return (a.dataset.name || '').localeCompare(b.dataset.name || '');
                    case 'name_desc':
                        return (b.dataset.name || '').localeCompare(a.dataset.name || '');
                    case 'price_asc':
                        return parseFloat(a.dataset.price || 0) - parseFloat(b.dataset.price || 0);
                    case 'price_desc':
                        return parseFloat(b.dataset.price || 0) - parseFloat(a.dataset.price || 0);
                    default:
                        return 0;
                }
            });
            
            projectCards.forEach(card => projectsGrid.appendChild(card));
        });
    }
}

// Form submission handling with AJAX
function initializeFormHandling() {
    const projectForm = document.getElementById('projectForm');
    if (projectForm) {
        projectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const formAction = this.querySelector('#formAction').value;
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>درێژەپێدە...';
            }
            
            const formData = new FormData(this);
            
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
                    console.log(`📤   ${pair[0]}: File - ${pair[1].name} (${pair[1].size} bytes)`);
                } else {
                    console.log(`📤   ${pair[0]}: ${pair[1]}`);
                }
            }
            
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
                        console.error('Server returned HTML instead of JSON');
                        throw new Error('Server returned HTML instead of JSON');
                    });
                }
            })
            .then(data => {
                if (data.success) {
                    showSuccessMessage(data.message || 'پڕۆژە بە سەرکەوتوویی هەڵگیرا');
                    closeProjectModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showErrorMessage(data.message || 'هەڵەیەک ڕوویدا');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i><?php echo t("save_project"); ?>';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('هەڵەیەک ڕوویدا: ' + error.message);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i><?php echo t("save_project"); ?>';
                }
            });
        });
    }
}

// Image preview functionality
function initializeImagePreview() {
    const mainImageInput = document.getElementById('mainImage');
    if (mainImageInput) {
        mainImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImg = document.getElementById('mainImagePreviewImg');
                    const previewDiv = document.getElementById('mainImagePreview');
                    if (previewImg && previewDiv) {
                        previewImg.src = e.target.result;
                        previewDiv.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    const additionalImagesInput = document.getElementById('additionalImages');
    if (additionalImagesInput) {
        additionalImagesInput.addEventListener('change', function(e) {
            const previewContainer = document.getElementById('additionalImagesPreview');
            if (!previewContainer) return;
            
            const mainImageInput = document.getElementById('mainImage');
            const mainImageFile = mainImageInput?.files[0];
            
            previewContainer.innerHTML = '';
            previewContainer.classList.remove('hidden');
            
            // Get main image name to exclude it
            const mainImageName = mainImageFile ? mainImageFile.name : '';
            
            // Filter out the main image and remove duplicates from additional images
            const seenNames = new Set();
            const additionalFiles = Array.from(e.target.files).filter(file => {
                // Skip if it's the main image
                if (file.name === mainImageName) {
                    return false;
                }
                // Skip if we've seen this file name before (duplicate)
                if (seenNames.has(file.name)) {
                    return false;
                }
                seenNames.add(file.name);
                return true;
            });
            
            // Update the file input to exclude main image and duplicates
            if (additionalFiles.length !== e.target.files.length) {
                const dataTransfer = new DataTransfer();
                additionalFiles.forEach(file => dataTransfer.items.add(file));
                e.target.files = dataTransfer.files;
            }
            
            additionalFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const thumbItem = document.createElement('div');
                    thumbItem.className = 'thumb-item';
                    thumbItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview ${index + 1}">
                        <div class="remove-btn" onclick="removeAdditionalImage(${index})">
                            <i class="fas fa-times"></i>
                        </div>
                    `;
                    previewContainer.appendChild(thumbItem);
                };
                reader.readAsDataURL(file);
            });
        });
    }
}

// Modal functions
function openAddProjectModal() {
    const modal = document.getElementById('projectModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('projectForm');
    const formAction = document.getElementById('formAction');
    const projectId = document.getElementById('projectId');
    
    if (modal && form) {
        currentProjectId = null;
        form.reset();
        formAction.value = 'add_project';
        projectId.value = '';
        modalTitle.textContent = 'زیادکردنی پڕۆژەی نوێ';
        
        // Reset previews
        document.getElementById('mainImagePreview')?.classList.add('hidden');
        document.getElementById('additionalImagesPreview')?.classList.add('hidden');
        if (document.getElementById('additionalImagesPreview')) {
            document.getElementById('additionalImagesPreview').innerHTML = '';
        }
        
        modal.classList.remove('hidden');
    }
}

function editProject(projectId) {
    // Fetch project data and populate form
    fetch(`../../process/exterior_design/get_project.php?id=${projectId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.project) {
                const project = data.project;
                const modal = document.getElementById('projectModal');
                const modalTitle = document.getElementById('modalTitle');
                const form = document.getElementById('projectForm');
                const formAction = document.getElementById('formAction');
                const projectIdInput = document.getElementById('projectId');
                
                currentProjectId = projectId;
                formAction.value = 'edit_project';
                projectIdInput.value = projectId;
                modalTitle.textContent = 'دەستکاریکردنی پڕۆژە';
                
                // Populate form fields
                document.getElementById('projectName').value = project.name || '';
                document.getElementById('projectPrice').value = project.price || '';
                document.getElementById('projectDuration').value = project.duration || '';
                document.getElementById('projectDescription').value = project.description || '';
                
                // Show existing main image if available
                if (project.main_image) {
                    const previewImg = document.getElementById('mainImagePreviewImg');
                    const previewDiv = document.getElementById('mainImagePreview');
                    if (previewImg && previewDiv) {
                        previewImg.src = '../../' + project.main_image;
                        previewDiv.classList.remove('hidden');
                    }
                }
                
                modal.classList.remove('hidden');
            } else {
                showErrorMessage('پڕۆژە نەدۆزرایەوە');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('هەڵەیەک ڕوویدا لە بارکردنی پڕۆژە');
        });
}

function closeProjectModal() {
    const modal = document.getElementById('projectModal');
    if (modal) {
        modal.classList.add('hidden');
        const form = document.getElementById('projectForm');
        if (form) {
            form.reset();
        }
        document.getElementById('mainImagePreview')?.classList.add('hidden');
        const additionalPreview = document.getElementById('additionalImagesPreview');
        if (additionalPreview) {
            additionalPreview.classList.add('hidden');
            additionalPreview.innerHTML = '';
        }
    }
}

function deleteProject(projectId) {
    projectToDelete = projectId;
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.classList.remove('hidden');
    }
}

function confirmDelete() {
    if (!projectToDelete) return;
    
    const formData = new FormData();
    formData.append('csrf_token', window.csrfToken || '');
    formData.append('action', 'delete_project');
    formData.append('project_id', projectToDelete);
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.message || 'پڕۆژە بە سەرکەوتوویی سڕایەوە');
            closeDeleteModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showErrorMessage(data.message || 'هەڵەیەک ڕوویدا');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('هەڵەیەک ڕوویدا');
    });
}

function closeDeleteModal() {
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.classList.add('hidden');
        projectToDelete = null;
    }
}

// Helper functions
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
    
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}

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
    
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

function initializeBrowserHistory() {
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
    
    window.addEventListener('load', function() {
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });
}

// Remove image functions
function removeMainImage() {
    const preview = document.getElementById('mainImagePreview');
    const input = document.getElementById('mainImage');
    if (preview) preview.classList.add('hidden');
    if (input) input.value = '';
}

function removeAdditionalImage(index) {
    const previewContainer = document.getElementById('additionalImagesPreview');
    if (!previewContainer) return;
    
    const thumbItems = previewContainer.children;
    if (thumbItems[index]) {
        thumbItems[index].remove();
        
        // Update file input if needed
        const input = document.getElementById('additionalImages');
        if (previewContainer.children.length === 0 && input) {
            previewContainer.classList.add('hidden');
            input.value = '';
        }
    }
}

// Make functions globally available
window.openAddProjectModal = openAddProjectModal;
window.editProject = editProject;
window.deleteProject = deleteProject;
window.confirmDelete = confirmDelete;
window.closeProjectModal = closeProjectModal;
window.closeDeleteModal = closeDeleteModal;
window.removeMainImage = removeMainImage;
window.removeAdditionalImage = removeAdditionalImage;
