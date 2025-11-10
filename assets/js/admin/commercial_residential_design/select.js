// Select and Filter Functionality

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
function initializeCategoryFilter() {
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

// Initialize select functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeCategoryFilter();
    initializeSort();
});

