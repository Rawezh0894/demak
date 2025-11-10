/**
 * Design Reconstruction Select/Filter Projects
 * 
 * Handles project selection and filtering functionality
 */

// Project selection and filtering functionality
const ProjectSelectManager = {
    // Initialize project selection functionality
    init() {
        this.bindEvents();
        this.initializeFilters();
    },
    
    // Bind event listeners
    bindEvents() {
        // Search input
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', this.handleSearch.bind(this));
        }
        
        // Category filter
        const categoryFilter = document.getElementById('categoryFilter');
        if (categoryFilter) {
            categoryFilter.addEventListener('change', this.handleCategoryFilter.bind(this));
        }
        
        // Sort select
        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) {
            sortSelect.addEventListener('change', this.handleSort.bind(this));
        }
        
        // Clear filters button
        const clearFiltersBtn = document.getElementById('clearFilters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', this.clearFilters.bind(this));
        }
    },
    
    // Initialize filters
    initializeFilters() {
        // Get all project cards
        this.projectCards = Array.from(document.querySelectorAll('.project-card'));
        this.originalOrder = [...this.projectCards];
        
        // Store original display state
        this.projectCards.forEach(card => {
            card.dataset.originalDisplay = card.style.display || 'block';
        });
    },
    
    // Handle search
    handleSearch(event) {
        const searchTerm = event.target.value.toLowerCase().trim();
        this.filterProjects(searchTerm, this.getCurrentCategoryFilter(), this.getCurrentSort());
    },
    
    // Handle category filter
    handleCategoryFilter(event) {
        const category = event.target.value;
        this.filterProjects(this.getCurrentSearchTerm(), category, this.getCurrentSort());
    },
    
    // Handle sort
    handleSort(event) {
        const sortValue = event.target.value;
        this.filterProjects(this.getCurrentSearchTerm(), this.getCurrentCategoryFilter(), sortValue);
    },
    
    // Get current search term
    getCurrentSearchTerm() {
        const searchInput = document.getElementById('searchInput');
        return searchInput ? searchInput.value.toLowerCase().trim() : '';
    },
    
    // Get current category filter
    getCurrentCategoryFilter() {
        const categoryFilter = document.getElementById('categoryFilter');
        return categoryFilter ? categoryFilter.value : '';
    },
    
    // Get current sort
    getCurrentSort() {
        const sortSelect = document.getElementById('sortSelect');
        return sortSelect ? sortSelect.value : 'name_asc';
    },
    
    // Filter projects
    filterProjects(searchTerm, categoryFilter, sortValue) {
        let filteredCards = [...this.projectCards];
        
        // Apply search filter
        if (searchTerm) {
            filteredCards = filteredCards.filter(card => {
                const projectName = (card.dataset.name || '').toLowerCase();
                const projectDescription = card.querySelector('.project-description')?.textContent.toLowerCase() || '';
                return projectName.includes(searchTerm) || projectDescription.includes(searchTerm);
            });
        }
        
        // Apply category filter
        if (categoryFilter) {
            filteredCards = filteredCards.filter(card => {
                return card.dataset.category === categoryFilter;
            });
        }
        
        // Apply sorting
        filteredCards = this.sortCards(filteredCards, sortValue);
        
        // Update display
        this.updateProjectDisplay(filteredCards);
        
        // Update results count
        this.updateResultsCount(filteredCards.length);
    },
    
    // Sort cards
    sortCards(cards, sortValue) {
        return cards.sort((a, b) => {
            switch (sortValue) {
                case 'name_asc':
                    return (a.dataset.name || '').localeCompare(b.dataset.name || '');
                case 'name_desc':
                    return (b.dataset.name || '').localeCompare(a.dataset.name || '');
                case 'price_asc':
                    return this.parsePrice(a.dataset.price || '0') - this.parsePrice(b.dataset.price || '0');
                case 'price_desc':
                    return this.parsePrice(b.dataset.price || '0') - this.parsePrice(a.dataset.price || '0');
                case 'date_created':
                    return new Date(b.dataset.createdAt || '') - new Date(a.dataset.createdAt || '');
                default:
                    return 0;
            }
        });
    },
    
    // Parse price for sorting
    parsePrice(priceStr) {
        // Remove currency symbols and commas, then parse
        const cleaned = priceStr.replace(/[$,]/g, '');
        return parseFloat(cleaned) || 0;
    },
    
    // Update project display
    updateProjectDisplay(filteredCards) {
        const projectsGrid = document.getElementById('projectsGrid');
        if (!projectsGrid) return;
        
        // Hide all cards first
        this.projectCards.forEach(card => {
            card.style.display = 'none';
        });
        
        // Show filtered cards
        filteredCards.forEach(card => {
            card.style.display = card.dataset.originalDisplay || 'block';
        });
        
        // Reorder cards in DOM
        filteredCards.forEach(card => {
            projectsGrid.appendChild(card);
        });
        
        // Show/hide no results message
        this.toggleNoResultsMessage(filteredCards.length === 0);
    },
    
    // Update results count
    updateResultsCount(count) {
        const resultsCount = document.getElementById('resultsCount');
        if (resultsCount) {
            resultsCount.textContent = `${count} project${count !== 1 ? 's' : ''} found`;
        }
    },
    
    // Toggle no results message
    toggleNoResultsMessage(show) {
        const noProjectsFound = document.getElementById('noProjectsFound');
        if (noProjectsFound) {
            if (show) {
                noProjectsFound.classList.remove('hidden');
            } else {
                noProjectsFound.classList.add('hidden');
            }
        }
    },
    
    // Clear all filters
    clearFilters() {
        // Reset search input
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.value = '';
        }
        
        // Reset category filter
        const categoryFilter = document.getElementById('categoryFilter');
        if (categoryFilter) {
            categoryFilter.value = '';
        }
        
        // Reset sort
        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) {
            sortSelect.value = 'name_asc';
        }
        
        // Reset display
        this.filterProjects('', '', 'name_asc');
    },
    
    // Get project by ID
    getProjectById(projectId) {
        return this.projectCards.find(card => card.dataset.projectId === projectId.toString());
    },
    
    // Highlight project
    highlightProject(projectId) {
        const projectCard = this.getProjectById(projectId);
        if (projectCard) {
            projectCard.classList.add('ring-2', 'ring-purple-500');
            
            // Scroll to project
            projectCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Remove highlight after 3 seconds
            setTimeout(() => {
                projectCard.classList.remove('ring-2', 'ring-purple-500');
            }, 3000);
        }
    },
    
    // Export filtered projects
    exportProjects() {
        const visibleCards = this.projectCards.filter(card => 
            card.style.display !== 'none'
        );
        
        const projectsData = visibleCards.map(card => ({
            name: card.dataset.name,
            category: card.dataset.category,
            price: card.dataset.price
        }));
        
        // Create CSV content
        const csvContent = this.createCSV(projectsData);
        
        // Download CSV
        this.downloadCSV(csvContent, 'design_reconstruction_projects.csv');
    },
    
    // Create CSV content
    createCSV(data) {
        const headers = ['Name', 'Category', 'Price'];
        const csvRows = [headers.join(',')];
        
        data.forEach(row => {
            const values = [
                `"${row.name}"`,
                `"${row.category}"`,
                `"${row.price}"`
            ];
            csvRows.push(values.join(','));
        });
        
        return csvRows.join('\n');
    },
    
    // Download CSV
    downloadCSV(content, filename) {
        const blob = new Blob([content], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        link.click();
        window.URL.revokeObjectURL(url);
    }
};

// Initialize project select manager
document.addEventListener('DOMContentLoaded', function() {
    ProjectSelectManager.init();
});

// Make ProjectSelectManager globally available
window.ProjectSelectManager = ProjectSelectManager;
