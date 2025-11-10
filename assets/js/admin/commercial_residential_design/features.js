// Feature Management Functions

// Feature management
function addFeature() {
    const container = document.getElementById('featuresContainer');
    if (!container) return;
    
    const featureDiv = document.createElement('div');
    featureDiv.className = 'flex items-center space-x-2 mb-2';
    featureDiv.innerHTML = `
        <input type="text" 
               name="project_features[]" 
               placeholder="Add feature"
               class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
        <button type="button" 
                onclick="removeFeature(this)"
                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(featureDiv);
}

function removeFeature(button) {
    if (button && button.parentElement) {
        button.parentElement.remove();
    }
}

// Override global functions
window.addFeature = addFeature;
window.removeFeature = removeFeature;

