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
    
    // Clear previews but don't reset form yet - wait until data is loaded
    if (mainImagePreview) {
        mainImagePreview.classList.add('hidden');
        const mainImagePreviewImg = document.getElementById('mainImagePreviewImg');
        if (mainImagePreviewImg) mainImagePreviewImg.src = '';
    }
    if (additionalImagesPreview) {
        additionalImagesPreview.classList.add('hidden');
        additionalImagesPreview.innerHTML = '';
    }
    
    // Clear file inputs
    const mainImageInput = document.getElementById('mainImage');
    const additionalImagesInput = document.getElementById('additionalImages');
    if (mainImageInput) mainImageInput.value = '';
    if (additionalImagesInput) additionalImagesInput.value = '';
    
    // Clear features container
    const featuresContainer = document.getElementById('featuresContainer');
    if (featuresContainer) {
        featuresContainer.innerHTML = `
            <div class="flex items-center space-x-2 mb-2">
                <input type="text" 
                       name="project_features[]" 
                       placeholder="Add feature"
                       class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <button type="button" 
                        onclick="removeFeature(this)"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    }
    
    if (projectModal) projectModal.classList.remove('hidden');
    
    // Load project data via AJAX - will populate form with data
    loadProjectData(projectId);
}

// Load project data for editing
function loadProjectData(projectId) {
    // Show loading state
    const modalTitle = document.getElementById('modalTitle');
    if (modalTitle) {
        modalTitle.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>بارکردنی داتا...';
    }
    
    // Wait a bit to ensure modal is fully visible and DOM is ready
    setTimeout(() => {
        // AJAX call to load project data
        fetch(`../../process/commercial_residential_design/get_project.php?project_id=${projectId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Wait a bit more to ensure all input fields are accessible
                    setTimeout(() => {
                        populateForm(data);
                        // Restore modal title
                        if (modalTitle) {
                            modalTitle.textContent = 'Edit Project';
                        }
                    }, 50);
                } else {
                    throw new Error(data.message || 'Failed to load project data');
                }
            })
            .catch(error => {
                console.error('Error loading project data:', error);
                if (typeof showErrorMessage === 'function') {
                    showErrorMessage('هەڵەیەک ڕوویدا لە بارکردنی داتای پڕۆژە: ' + error.message);
                }
                
                // Restore modal title
                if (modalTitle) {
                    modalTitle.textContent = 'Edit Project';
                }
            });
    }, 100);
}

// Populate form with project data
function populateForm(data) {
    const project = data.project;
    
    console.log('Populating form with project data:', project);
    console.log('Area value from API:', project.area, typeof project.area);
    console.log('Floors value from API:', project.floors, typeof project.floors);
    
    // Get all input elements
    const projectName = document.getElementById('projectName');
    const projectCategory = document.getElementById('projectCategory');
    const projectPrice = document.getElementById('projectPrice');
    const projectDuration = document.getElementById('projectDuration');
    const projectArea = document.getElementById('projectArea');
    const projectFloors = document.getElementById('projectFloors');
    const projectDescription = document.getElementById('projectDescription');
    
    // Log if elements are found
    console.log('projectArea element:', projectArea);
    console.log('projectFloors element:', projectFloors);
    
    // Set all field values
    if (projectName) {
        projectName.value = project.name || '';
    }
    
    if (projectCategory) {
        projectCategory.value = project.category_key || '';
    }
    
    if (projectPrice) {
        projectPrice.value = project.price || '';
    }
    
    if (projectDuration) {
        projectDuration.value = project.duration || '';
    }
    
    // Set area - extract only numbers (remove "م²" or any text)
    if (projectArea) {
        let areaValue = '';
        if (project.area !== null && project.area !== undefined) {
            // Extract only numbers from the area string (e.g., "200 م²" -> "200")
            const areaStr = String(project.area);
            // Remove all non-numeric characters except decimal point and comma
            areaValue = areaStr.replace(/[^\d.,]/g, '').replace(/,/g, '');
        }
        projectArea.value = areaValue;
        console.log('Area input value set to:', projectArea.value);
        // Force update
        projectArea.setAttribute('value', areaValue);
    } else {
        console.error('❌ projectArea input element not found!');
        // Try to find it again after a short delay
        setTimeout(() => {
            const retryArea = document.getElementById('projectArea');
            if (retryArea) {
                let areaValue = '';
                if (project.area !== null && project.area !== undefined) {
                    const areaStr = String(project.area);
                    areaValue = areaStr.replace(/[^\d.,]/g, '').replace(/,/g, '');
                }
                retryArea.value = areaValue;
                console.log('Area set on retry:', retryArea.value);
            }
        }, 200);
    }
    
    // Set floors - ensure it's a number
    if (projectFloors) {
        const floorsValue = project.floors !== null && project.floors !== undefined ? String(project.floors) : '';
        projectFloors.value = floorsValue;
        console.log('Floors input value set to:', projectFloors.value);
        // Force update
        projectFloors.setAttribute('value', floorsValue);
    } else {
        console.error('❌ projectFloors input element not found!');
        // Try to find it again after a short delay
        setTimeout(() => {
            const retryFloors = document.getElementById('projectFloors');
            if (retryFloors) {
                retryFloors.value = project.floors !== null && project.floors !== undefined ? String(project.floors) : '';
                console.log('Floors set on retry:', retryFloors.value);
            }
        }, 200);
    }
    
    if (projectDescription) {
        projectDescription.value = project.description || '';
    }
    
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

// Load project images
function loadProjectImages(images) {
    const additionalImagesPreview = document.getElementById('additionalImagesPreview');
    if (!additionalImagesPreview) return;
    
    // Clear existing preview
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

// Override global functions
window.editProject = editProject;
window.loadProjectData = loadProjectData;
window.populateForm = populateForm;
window.loadProjectFeatures = loadProjectFeatures;
window.loadProjectImages = loadProjectImages;

