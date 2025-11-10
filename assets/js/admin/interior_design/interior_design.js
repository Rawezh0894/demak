// Interior Design Management JavaScript

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

// Global pagination state
let currentPage = 1;
let totalPages = 1;
let currentSearch = '';

// Search functionality with pagination
let searchTimeout;
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim();
            clearTimeout(searchTimeout);
            
            // Debounce search to avoid too many requests
            searchTimeout = setTimeout(() => {
                currentSearch = searchTerm;
                updateProjectsList(1, searchTerm);
            }, 500);
        });
    }
}

// Update projects list dynamically
function updateProjectsList(page = 1, search = '') {
    currentPage = page;
    currentSearch = search || '';
    
    // Build query string
    const params = new URLSearchParams();
    if (page > 1) params.append('page', page);
    if (search) params.append('search', search);
    
    const url = '../../process/interior_design/get_projects_list.php' + 
                (params.toString() ? '?' + params.toString() : '');
    
    fetch(url)
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
                renderProjectsList(data.projects);
                if (data.pagination) {
                    totalPages = data.pagination.total_pages;
                    renderPagination(data.pagination);
                }
            } else {
                console.error('API Error:', data.message);
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        });
}

// Render projects list
function renderProjectsList(projects) {
    const projectsGrid = document.getElementById('projectsGrid');
    if (!projectsGrid) {
        console.error('projectsGrid element not found');
        return;
    }
    
    if (projects.length === 0) {
        projectsGrid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <i class="fas fa-folder-open text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    هیچ پڕۆژەیەک نەدۆزرایەوە
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    هێشتا هیچ پڕۆژەیەک زیاد نەکراوە
                </p>
                <button onclick="openAddProjectModal()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                    <i class="fas fa-plus mr-2"></i>
                    زیادکردنی پڕۆژەی نوێ
                </button>
            </div>
        `;
        return;
    }
    
    const newHTML = projects.map(project => {
        const mainImage = project.main_image || (project.images && project.images.length > 0 ? project.images[0].image_path : null);
        return `
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden project-card" 
             data-name="${(project.name || '').toLowerCase()}">
            <div class="relative h-48 bg-gray-200 dark:bg-gray-700">
                ${mainImage ? 
                    `<img src="../../${mainImage}" alt="${project.name}" class="w-full h-full object-cover">` :
                    `<div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                    </div>`
                }
            </div>
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    ${project.name || ''}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                    ${project.description || ''}
                </p>
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-dollar-sign mr-1"></i>
                        <span>${project.price || ''}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-1"></i>
                        <span>${project.duration || ''}</span>
                    </div>
                </div>
                <div class="flex gap-3">
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
    }).join('');
    
    projectsGrid.innerHTML = newHTML;
}

// Render pagination controls
function renderPagination(pagination) {
    const paginationContainer = document.getElementById('paginationContainer');
    if (!paginationContainer) {
        console.warn('Pagination container not found');
        return;
    }
    
    if (pagination.total_pages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    const { current_page, total_pages, total_projects, has_prev, has_next } = pagination;
    
    let paginationHTML = `
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium">${total_projects}</span> پڕۆژە لە کۆی 
                <span class="font-medium">${total_pages}</span> پەڕە
            </div>
            <div class="flex items-center gap-2">
    `;
    
    // Previous button
    if (has_prev) {
        paginationHTML += `
            <button onclick="updateProjectsList(${current_page - 1}, '${currentSearch}')" 
                    class="pagination-btn pagination-btn-prev">
                <i class="fas fa-chevron-right"></i>
                <span>پێشوو</span>
            </button>
        `;
    } else {
        paginationHTML += `
            <button disabled class="pagination-btn pagination-btn-disabled">
                <i class="fas fa-chevron-right"></i>
                <span>پێشوو</span>
            </button>
        `;
    }
    
    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, current_page - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(total_pages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage < maxVisiblePages - 1) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    if (startPage > 1) {
        paginationHTML += `
            <button onclick="updateProjectsList(1, '${currentSearch}')" 
                    class="pagination-number">1</button>
        `;
        if (startPage > 2) {
            paginationHTML += `<span class="pagination-ellipsis">...</span>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === current_page) {
            paginationHTML += `
                <button class="pagination-number pagination-number-active">${i}</button>
            `;
        } else {
            paginationHTML += `
                <button onclick="updateProjectsList(${i}, '${currentSearch}')" 
                        class="pagination-number">${i}</button>
            `;
        }
    }
    
    if (endPage < total_pages) {
        if (endPage < total_pages - 1) {
            paginationHTML += `<span class="pagination-ellipsis">...</span>`;
        }
        paginationHTML += `
            <button onclick="updateProjectsList(${total_pages}, '${currentSearch}')" 
                    class="pagination-number">${total_pages}</button>
        `;
    }
    
    // Next button
    if (has_next) {
        paginationHTML += `
            <button onclick="updateProjectsList(${current_page + 1}, '${currentSearch}')" 
                    class="pagination-btn pagination-btn-next">
                <span>دواتر</span>
                <i class="fas fa-chevron-left"></i>
            </button>
        `;
    } else {
        paginationHTML += `
            <button disabled class="pagination-btn pagination-btn-disabled">
                <span>دواتر</span>
                <i class="fas fa-chevron-left"></i>
            </button>
        `;
    }
    
    paginationHTML += `
            </div>
        </div>
    `;
    
    paginationContainer.innerHTML = paginationHTML;
}

// Expose functions globally
window.updateProjectsList = updateProjectsList;
window.currentSearch = currentSearch;

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
                        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>پاشەکەوتکردن';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('هەڵەیەک ڕوویدا: ' + error.message);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>پاشەکەوتکردن';
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
            
            previewContainer.innerHTML = '';
            previewContainer.classList.remove('hidden');
            
            Array.from(e.target.files).forEach((file, index) => {
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
    fetch(`../../process/interior_design/get_project.php?id=${projectId}`)
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
