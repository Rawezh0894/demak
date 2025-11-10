// Infrastructure Management JavaScript

// Global variables
let currentProjectId = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeFilters();
    initializeSort();
    initializeFormHandling();
    initializeBrowserHistory();
    // initializeImagePreview will be called by image-preview.js
});

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
                updateProjectsList(1, currentCategory, searchTerm);
            }, 500);
        });
    }
}

// Category filter functionality with pagination
function initializeFilters() {
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            const selectedCategory = this.value;
            currentCategory = selectedCategory;
            updateProjectsList(1, selectedCategory, currentSearch);
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
                        return a.dataset.name.localeCompare(b.dataset.name);
                    case 'name_desc':
                        return b.dataset.name.localeCompare(a.dataset.name);
                    case 'price_asc':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'price_desc':
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
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
            e.preventDefault(); // Prevent default form submission
            
            // Disable submit button to prevent double submission
            const submitBtn = this.querySelector('button[type="submit"]');
            const formAction = this.querySelector('#formAction').value;
            console.log('🔘 Submit button found:', !!submitBtn);
            console.log('🔘 Form action:', formAction);
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>درێژەپێدە...';
                console.log('🔘 Submit button disabled and spinner added');
            }
            
            // Prepare form data
            const formData = new FormData(this);
            
        // Submit via AJAX
        console.log('📤 Submitting form data...');
        console.log('📤 Form action:', this.querySelector('#formAction').value);
        console.log('📤 Form data entries:', Array.from(formData.entries()));
        console.log('📤 Current URL:', window.location.href);
        console.log('📤 Full fetch URL:', new URL(window.location.href).href);
        console.log('📤 Relative path:', window.location.href);
        console.log('📤 Base URL:', window.location.origin + window.location.pathname);
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                console.log('📡 Form response status:', response.status);
                console.log('📡 Form response headers:', response.headers.get('content-type'));
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
                console.log('📊 Form submission response:', data);
                if (data.success) {
                    console.log('✅ Form submission successful');
                    // Success - show message and update UI dynamically
                    showSuccessMessage(data.message);
                    
                    // Close modal
                    closeProjectModal();
                    
                    // Update projects list dynamically
                    console.log('🔄 Calling updateProjectsList...');
                    updateProjectsList(1, currentCategory, currentSearch);
                } else {
                    console.error('❌ Form submission failed:', data.message);
                    throw new Error(data.message || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('❌ Form submission error:', error);
                showErrorMessage(error.message || 'هەڵەیەک ڕوویدا لە پاشەکەوتکردنی پڕۆژە');
            })
            .finally(() => {
                // Always re-enable submit button
                console.log('🔄 Finally block executed - re-enabling submit button');
                const submitBtn = projectForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>پاشەکەوتکردن';
                    console.log('✅ Submit button re-enabled');
                } else {
                    console.error('❌ Submit button not found in finally block');
                }
            });
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

// Browser history management
function initializeBrowserHistory() {
    // Clear browser history to prevent POST data warning
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
    
    // Clear form data on page load
    window.addEventListener('load', function() {
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
        // Clear any duplicate action parameters
        if (window.location.search.includes('duplicate_action=1')) {
            const url = new URL(window.location);
            url.searchParams.delete('duplicate_action');
            url.searchParams.delete('t');
            window.history.replaceState(null, null, url.toString());
        }
    });
    
    // Prevent back button from resubmitting forms
    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.preventResubmit) {
            window.location.reload();
        }
    });
}

// Modal functions (placeholder - will be overridden by modal.js)
function openAddProjectModal() {
    console.log('openAddProjectModal called - should be overridden by modal.js');
}

function editProject(projectId) {
    console.log('editProject called - should be overridden by modal.js');
}

function deleteProject(projectId) {
    console.log('deleteProject called - should be overridden by modal.js');
}

function confirmDelete() {
    console.log('confirmDelete called - should be overridden by modal.js');
}

function closeProjectModal() {
    console.log('closeProjectModal called - should be overridden by modal.js');
}

function closeDeleteModal() {
    console.log('closeDeleteModal called - should be overridden by modal.js');
}

function addFeature() {
    console.log('addFeature called - should be overridden by features.js');
}

function removeFeature(button) {
    console.log('removeFeature called - should be overridden by features.js');
}

// Global pagination state
let currentPage = 1;
let totalPages = 1;
let currentCategory = '';
let currentSearch = '';

// Update projects list dynamically
function updateProjectsList(page = 1, category = '', search = '') {
    console.log('🔄 updateProjectsList called', { page, category, search });
    
    currentPage = page;
    currentCategory = category || '';
    currentSearch = search || '';
    
    // Build query string
    const params = new URLSearchParams();
    if (page > 1) params.append('page', page);
    if (category) params.append('category', category);
    if (search) params.append('search', search);
    
    const url = '../../process/infrastructure_management/get_projects_list.php' + 
                (params.toString() ? '?' + params.toString() : '');
    
    console.log('🌐 Fetching from:', url);
    
    fetch(url)
        .then(response => {
            console.log('📡 get_projects_list response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return response.text().then(text => {
                    console.error('❌ Server returned HTML instead of JSON:');
                    console.error('Response text:', text.substring(0, 500) + '...');
                    throw new Error('Server returned HTML instead of JSON. Check server configuration.');
                });
            }
        })
        .then(data => {
            console.log('📊 Data received:', data);
            if (data.success) {
                console.log('✅ Success - rendering projects:', data.projects.length);
                renderProjectsList(data.projects);
                if (data.pagination) {
                    totalPages = data.pagination.total_pages;
                    renderPagination(data.pagination);
                }
            } else {
                console.error('❌ API Error:', data.message);
            }
        })
        .catch(error => {
            console.error('❌ get_projects_list Fetch Error:', error);
            // Fallback to page reload if dynamic update fails
            setTimeout(() => {
                console.log('🔄 Fallback: Reloading page');
                window.location.reload();
            }, 2000);
        });
}

// Render projects list
function renderProjectsList(projects) {
    console.log('🎨 renderProjectsList called with:', projects.length, 'projects');
    
    const projectsGrid = document.getElementById('projectsGrid');
    if (!projectsGrid) {
        console.error('❌ projectsGrid element not found');
        return;
    }
    
    console.log('📋 Current projectsGrid content:', projectsGrid.innerHTML.length, 'characters');
    
    if (projects.length === 0) {
        console.log('📭 No projects - showing empty state');
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
    
    console.log('🔨 Rendering', projects.length, 'projects');
    
    const newHTML = projects.map(project => `
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden project-card" 
             data-category="${project.category_key}" 
             data-name="${project.name.toLowerCase()}"
             data-price="${project.price}">
            <!-- Project Image -->
            <div class="relative h-48 bg-gray-200 dark:bg-gray-700">
                ${project.main_image ? 
                    `<img src="../../${project.main_image}" alt="${project.name}" class="w-full h-full object-cover">` :
                    `<div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                    </div>`
                }
                <div class="absolute top-4 right-4">
                    <span class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs font-medium">
                        ${project.category_title}
                    </span>
                </div>
            </div>
            
            <!-- Project Content -->
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    ${project.name}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                    ${project.description}
                </p>
                
                <!-- Project Info -->
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-dollar-sign mr-1"></i>
                        <span>${project.price}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-1"></i>
                        <span>${project.duration}</span>
                    </div>
                </div>
                
                <!-- Actions -->
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
    `).join('');
    
    projectsGrid.innerHTML = newHTML;
    console.log('✅ Projects rendered successfully');
}

// Render pagination controls
function renderPagination(pagination) {
    const paginationContainer = document.getElementById('paginationContainer');
    if (!paginationContainer) {
        console.warn('⚠️ Pagination container not found');
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
            <button onclick="updateProjectsList(${current_page - 1}, '${currentCategory}', '${currentSearch}')" 
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
            <button onclick="updateProjectsList(1, '${currentCategory}', '${currentSearch}')" 
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
                <button onclick="updateProjectsList(${i}, '${currentCategory}', '${currentSearch}')" 
                        class="pagination-number">${i}</button>
            `;
        }
    }
    
    if (endPage < total_pages) {
        if (endPage < total_pages - 1) {
            paginationHTML += `<span class="pagination-ellipsis">...</span>`;
        }
        paginationHTML += `
            <button onclick="updateProjectsList(${total_pages}, '${currentCategory}', '${currentSearch}')" 
                    class="pagination-number">${total_pages}</button>
        `;
    }
    
    // Next button
    if (has_next) {
        paginationHTML += `
            <button onclick="updateProjectsList(${current_page + 1}, '${currentCategory}', '${currentSearch}')" 
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

// Close project modal function
function closeProjectModal() {
    const projectModal = document.getElementById('projectModal');
    if (projectModal) {
        projectModal.classList.add('hidden');
    }
}

// Make functions globally available
window.currentProjectId = currentProjectId;
window.openAddProjectModal = openAddProjectModal;
window.editProject = editProject;
window.deleteProject = deleteProject;
window.confirmDelete = confirmDelete;
window.closeProjectModal = closeProjectModal;
window.closeDeleteModal = closeDeleteModal;
window.addFeature = addFeature;
window.removeFeature = removeFeature;
// Expose functions and variables globally
window.updateProjectsList = updateProjectsList;
window.renderProjectsList = renderProjectsList;
window.currentCategory = currentCategory;
window.currentSearch = currentSearch;
