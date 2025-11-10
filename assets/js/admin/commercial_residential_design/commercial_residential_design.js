// Commercial Residential Design Management JavaScript

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
                if (projectName.includes(searchTerm)) {
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

// Category filter functionality
function initializeFilters() {
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            const selectedCategory = this.value;
            const projectCards = document.querySelectorAll('.project-card');
            let visibleCount = 0;
            
            projectCards.forEach(card => {
                if (selectedCategory === '' || card.dataset.category === selectedCategory) {
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
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>درێژەپێدە...';
            }
            
            // Prepare form data
            const formData = new FormData(this);
            
            // Submit via AJAX
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Check if response is JSON
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
                    // Success - show message and update UI dynamically
                    showSuccessMessage(data.message);
                    
                    // Close modal
                    closeProjectModal();
                    
                    // Update projects list dynamically
                    updateProjectsList();
                } else {
                    throw new Error(data.message || 'Unknown error');
                }
            })
            .catch(error => {
                showErrorMessage(error.message || 'هەڵەیەک ڕوویدا لە پاشەکەوتکردنی پڕۆژە');
            })
            .finally(() => {
                // Always re-enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>پاشەکەوتکردن';
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
}

// Global pagination state
let currentPage = 1;
let totalPages = 1;
let currentCategory = '';
let currentSearch = '';

// Update projects list dynamically
function updateProjectsList(page = 1, category = '', search = '') {
    currentPage = page;
    currentCategory = category || '';
    currentSearch = search || '';
    
    // Build query string
    const params = new URLSearchParams();
    if (page > 1) params.append('page', page);
    if (category) params.append('category', category);
    if (search) params.append('search', search);
    
    const url = '../../process/commercial_residential_design/get_projects_list.php' + 
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
            // Fallback to page reload if dynamic update fails
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        });
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

// Render projects list
function renderProjectsList(projects) {
    const projectsGrid = document.getElementById('projectsGrid');
    if (!projectsGrid) {
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
    
    const newHTML = projects.map(project => `
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden project-card transition-all duration-300 hover:shadow-2xl hover:-translate-y-2" 
             data-category="${project.category_key}" 
             data-name="${project.name.toLowerCase()}"
             data-price="${project.price || 0}">
            <!-- Project Image -->
            <div class="relative h-56 bg-gray-200 dark:bg-gray-700">
                ${project.main_image ? 
                    `<img src="../../${project.main_image}" alt="${project.name}" class="w-full h-full object-cover">` :
                    `<div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-5xl"></i>
                    </div>`
                }
                <div class="absolute top-4 right-4">
                    <span class="bg-blue-600 text-white px-3 py-2 rounded-xl text-sm font-medium shadow-lg">
                        ${project.category_title}
                    </span>
                </div>
            </div>
            
            <!-- Project Content -->
            <div class="p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                    ${project.name}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-base mb-6 line-clamp-2">
                    ${project.description || ''}
                </p>
                
                <!-- Project Info -->
                <div class="space-y-2 mb-6">
                    <div class="flex items-center justify-between text-base">
                        <div class="flex items-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-ruler-combined mr-2 text-green-600"></i>
                            <span class="font-semibold">ڕووبەر: ${project.area ? project.area.replace(/[^\d.,]/g, '') : ''} م²</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-base">
                        <div class="flex items-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-layer-group mr-2 text-blue-600"></i>
                            <span class="font-semibold">نهۆم: ${project.floors || 0}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-base">
                        <div class="flex items-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-dollar-sign mr-2 text-green-600"></i>
                            <span class="font-semibold">${project.price || ''}</span>
                        </div>
                        <div class="flex items-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-clock mr-2 text-blue-600"></i>
                            <span class="font-semibold">${project.duration || ''}</span>
                        </div>
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
}

// Make functions globally available
window.currentProjectId = currentProjectId;
window.updateProjectsList = updateProjectsList;
window.renderProjectsList = renderProjectsList;
window.showSuccessMessage = showSuccessMessage;
window.showErrorMessage = showErrorMessage;

