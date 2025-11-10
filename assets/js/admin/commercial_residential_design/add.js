// Add Project Functionality

// Add project functions
function openAddProjectModal() {
    const modalTitle = document.getElementById('modalTitle');
    const formAction = document.getElementById('formAction');
    const projectId = document.getElementById('projectId');
    const projectForm = document.getElementById('projectForm');
    const mainImagePreview = document.getElementById('mainImagePreview');
    const additionalImagesPreview = document.getElementById('additionalImagesPreview');
    const projectModal = document.getElementById('projectModal');
    
    if (modalTitle) modalTitle.textContent = 'Add New Project';
    if (formAction) formAction.value = 'add_project';
    if (projectId) projectId.value = '';
    if (projectForm) projectForm.reset();
    if (mainImagePreview) mainImagePreview.classList.add('hidden');
    if (additionalImagesPreview) additionalImagesPreview.classList.add('hidden');
    if (projectModal) projectModal.classList.remove('hidden');
}

// Form validation for add project
function validateAddProjectForm() {
    const projectName = document.getElementById('projectName');
    const projectCategory = document.getElementById('projectCategory');
    const projectPrice = document.getElementById('projectPrice');
    const projectDuration = document.getElementById('projectDuration');
    const projectArea = document.getElementById('projectArea');
    const projectFloors = document.getElementById('projectFloors');
    const projectDescription = document.getElementById('projectDescription');
    const mainImage = document.getElementById('mainImage');
    
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
    
    if (!mainImage || !mainImage.files || mainImage.files.length === 0) {
        errorMessage += 'Main image is required.\n';
        isValid = false;
    }
    
    if (!isValid) {
        alert(errorMessage);
    }
    
    return isValid;
}

// Override global functions
window.openAddProjectModal = openAddProjectModal;
window.validateAddProjectForm = validateAddProjectForm;

