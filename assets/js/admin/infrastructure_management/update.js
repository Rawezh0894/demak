// Update Project Functionality

// Update project functions
function updateProject(projectId) {
    // Load project data for editing
    loadProjectDataForUpdate(projectId);
}

// Load project data for update
function loadProjectDataForUpdate(projectId) {
    // TODO: Implement AJAX call to load project data
    console.log('Loading project data for update, ID:', projectId);
    
    // Example AJAX implementation:
    /*
    fetch(`/api/projects/${projectId}`)
        .then(response => response.json())
        .then(data => {
            populateUpdateForm(data);
        })
        .catch(error => {
            console.error('Error loading project data:', error);
        });
    */
}

// Populate update form
function populateUpdateForm(projectData) {
    const projectName = document.getElementById('projectName');
    const projectCategory = document.getElementById('projectCategory');
    const projectPrice = document.getElementById('projectPrice');
    const projectDuration = document.getElementById('projectDuration');
    const projectDescription = document.getElementById('projectDescription');
    
    if (projectName) projectName.value = projectData.name || '';
    if (projectCategory) projectCategory.value = projectData.category_key || '';
    if (projectPrice) projectPrice.value = projectData.price || '';
    if (projectDuration) projectDuration.value = projectData.duration || '';
    if (projectDescription) projectDescription.value = projectData.description || '';
    
    // Load features
    if (projectData.features && projectData.features.length > 0) {
        loadProjectFeaturesForUpdate(projectData.features);
    }
    
    // Load images
    if (projectData.images && projectData.images.length > 0) {
        loadProjectImagesForUpdate(projectData.images);
    }
}

// Load project features for update
function loadProjectFeaturesForUpdate(features) {
    const featuresContainer = document.getElementById('featuresContainer');
    if (!featuresContainer) return;
    
    // Clear existing features
    featuresContainer.innerHTML = '';
    
    features.forEach((feature, index) => {
        const featureDiv = document.createElement('div');
        featureDiv.className = 'flex items-center space-x-2 mb-2';
        featureDiv.innerHTML = `
            <input type="text" 
                   name="project_features[]" 
                   value="${feature}"
                   placeholder="Add feature"
                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <button type="button" 
                    onclick="removeFeature(this)"
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg">
                <i class="fas fa-trash"></i>
            </button>
        `;
        featuresContainer.appendChild(featureDiv);
    });
}

// Load project images for update
function loadProjectImagesForUpdate(images) {
    // TODO: Implement image loading for update mode
    console.log('Loading project images for update:', images);
}

// Form validation for update
function validateUpdateForm() {
    const projectName = document.getElementById('projectName');
    const projectCategory = document.getElementById('projectCategory');
    const projectPrice = document.getElementById('projectPrice');
    const projectDuration = document.getElementById('projectDuration');
    const projectDescription = document.getElementById('projectDescription');
    
    let isValid = true;
    let errorMessage = '';
    
    // Validate required fields
    if (!projectName || !projectName.value.trim()) {
        errorMessage += 'Project name is required.\n';
        isValid = false;
    }
    
    if (!projectCategory || !projectCategory.value) {
        errorMessage += 'Project category is required.\n';
        isValid = false;
    }
    
    if (!projectPrice || !projectPrice.value.trim()) {
        errorMessage += 'Project price is required.\n';
        isValid = false;
    }
    
    if (!projectDuration || !projectDuration.value.trim()) {
        errorMessage += 'Project duration is required.\n';
        isValid = false;
    }
    
    if (!projectDescription || !projectDescription.value.trim()) {
        errorMessage += 'Project description is required.\n';
        isValid = false;
    }
    
    if (!isValid) {
        alert(errorMessage);
    }
    
    return isValid;
}

// Override global functions
window.editProject = editProject;
window.loadProjectData = loadProjectData;
window.populateForm = populateForm;
window.loadProjectFeatures = loadProjectFeatures;
window.loadProjectImages = loadProjectImages;
window.validateUpdateForm = validateUpdateForm;
