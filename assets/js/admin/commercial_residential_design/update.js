// Update Project Functionality

// Update project functions
function updateProject(projectId) {
    // Load project data for editing
    loadProjectDataForUpdate(projectId);
}

// Load project data for update
function loadProjectDataForUpdate(projectId) {
    console.log('Loading project data for update, ID:', projectId);
    
    fetch(`../../process/commercial_residential_design/get_project.php?project_id=${projectId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateUpdateForm(data);
            } else {
                throw new Error(data.message || 'Failed to load project data');
            }
        })
        .catch(error => {
            console.error('Error loading project data:', error);
            if (typeof showErrorMessage === 'function') {
                showErrorMessage('هەڵەیەک ڕوویدا لە بارکردنی داتای پڕۆژە: ' + error.message);
            }
        });
}

// Populate update form
function populateUpdateForm(projectData) {
    const project = projectData.project;
    
    const projectName = document.getElementById('projectName');
    const projectCategory = document.getElementById('projectCategory');
    const projectPrice = document.getElementById('projectPrice');
    const projectDuration = document.getElementById('projectDuration');
    const projectArea = document.getElementById('projectArea');
    const projectFloors = document.getElementById('projectFloors');
    const projectDescription = document.getElementById('projectDescription');
    
    if (projectName) projectName.value = project.name || '';
    if (projectCategory) projectCategory.value = project.category_key || '';
    if (projectPrice) projectPrice.value = project.price || '';
    if (projectDuration) projectDuration.value = project.duration || '';
    if (projectArea) projectArea.value = project.area || '';
    if (projectFloors) projectFloors.value = project.floors || '';
    if (projectDescription) projectDescription.value = project.description || '';
    
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
                   value="${feature.replace(/"/g, '&quot;')}"
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
    const additionalImagesPreview = document.getElementById('additionalImagesPreview');
    if (!additionalImagesPreview) return;
    
    additionalImagesPreview.innerHTML = '';
    
    if (images.length > 0) {
        additionalImagesPreview.classList.remove('hidden');
        
        images.forEach((image, index) => {
            const thumbItem = document.createElement('div');
            thumbItem.className = 'thumb-item relative';
            thumbItem.innerHTML = `
                <img src="../../${image.path}" class="w-full h-20 object-cover rounded-lg">
                <button type="button" onclick="removeAdditionalImage(${index})" class="absolute -top-1 -right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            `;
            additionalImagesPreview.appendChild(thumbItem);
        });
    } else {
        additionalImagesPreview.classList.add('hidden');
    }
}

// Form validation for update
function validateUpdateForm() {
    const projectName = document.getElementById('projectName');
    const projectCategory = document.getElementById('projectCategory');
    const projectPrice = document.getElementById('projectPrice');
    const projectDuration = document.getElementById('projectDuration');
    const projectArea = document.getElementById('projectArea');
    const projectFloors = document.getElementById('projectFloors');
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
    
    if (!projectArea || !projectArea.value.trim()) {
        errorMessage += 'Project area is required.\n';
        isValid = false;
    }
    
    if (!projectFloors || projectFloors.value <= 0) {
        errorMessage += 'Project floors must be greater than 0.\n';
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
window.updateProject = updateProject;
window.loadProjectDataForUpdate = loadProjectDataForUpdate;
window.populateUpdateForm = populateUpdateForm;
window.loadProjectFeaturesForUpdate = loadProjectFeaturesForUpdate;
window.loadProjectImagesForUpdate = loadProjectImagesForUpdate;
window.validateUpdateForm = validateUpdateForm;

