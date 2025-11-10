// Edit Project Functionality

// Edit project functions
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
    loadProjectData(projectId);
}

// Load project data for editing
function loadProjectData(projectId) {
    // Show loading state
    const modalTitle = document.getElementById('modalTitle');
    if (modalTitle) {
        modalTitle.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>بارکردنی داتا...';
    }
    
    // AJAX call to load project data
    fetch(`../../process/infrastructure_management/get_project.php?project_id=${projectId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateForm(data);
                // Restore modal title
                if (modalTitle) {
                    modalTitle.textContent = 'Edit Project';
                }
            } else {
                throw new Error(data.message || 'Failed to load project data');
            }
        })
        .catch(error => {
            console.error('Error loading project data:', error);
            showErrorMessage('هەڵەیەک ڕوویدا لە بارکردنی داتای پڕۆژە: ' + error.message);
            
            // Restore modal title
            if (modalTitle) {
                modalTitle.textContent = 'Edit Project';
            }
        });
}

// Populate form with project data
function populateForm(data) {
    const project = data.project;
    
    const projectName = document.getElementById('projectName');
    const projectCategory = document.getElementById('projectCategory');
    const projectPrice = document.getElementById('projectPrice');
    const projectDuration = document.getElementById('projectDuration');
    const projectDescription = document.getElementById('projectDescription');
    
    if (projectName) projectName.value = project.name || '';
    if (projectCategory) projectCategory.value = project.category_key || '';
    if (projectPrice) projectPrice.value = project.price || '';
    if (projectDuration) projectDuration.value = project.duration || '';
    if (projectDescription) projectDescription.value = project.description || '';
    
    // Load features
    if (data.features && data.features.length > 0) {
        loadProjectFeatures(data.features);
    }
    
    // Load images
    if (data.images && data.images.length > 0) {
        loadProjectImages(data.images);
    }
    
    // Show main image if exists
    if (project.main_image) {
        const mainImagePreview = document.getElementById('mainImagePreview');
        const mainImagePreviewImg = document.getElementById('mainImagePreviewImg');
        if (mainImagePreview && mainImagePreviewImg) {
            mainImagePreviewImg.src = '../../' + project.main_image;
            mainImagePreview.classList.remove('hidden');
        }
    }
}

// Load project features
function loadProjectFeatures(features) {
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

// Load project images
function loadProjectImages(images) {
    const additionalImagesPreview = document.getElementById('additionalImagesPreview');
    if (!additionalImagesPreview) return;
    
    // Clear existing preview
    additionalImagesPreview.innerHTML = '';
    
    if (images.length > 0) {
        additionalImagesPreview.classList.remove('hidden');
        
        images.forEach(image => {
            const img = document.createElement('img');
            img.src = '../../' + image.path;
            img.className = 'w-full h-20 object-cover rounded-lg';
            img.alt = 'Project image';
            additionalImagesPreview.appendChild(img);
        });
    } else {
        additionalImagesPreview.classList.add('hidden');
    }
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

// Override global functions
window.editProject = editProject;
window.loadProjectData = loadProjectData;
window.populateForm = populateForm;
window.loadProjectFeatures = loadProjectFeatures;
window.loadProjectImages = loadProjectImages;
