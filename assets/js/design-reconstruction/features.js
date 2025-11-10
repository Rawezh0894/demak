/**
 * Design Reconstruction Features Management
 * 
 * Handles dynamic feature input management
 */

// Features management functions
const FeaturesManager = {
    // Add new feature input
    addFeature() {
        const featuresContainer = document.getElementById('featuresContainer');
        if (!featuresContainer) return;
        
        const newFeature = document.createElement('div');
        newFeature.className = 'flex items-center space-x-2 mb-2';
        newFeature.innerHTML = `
            <input type="text" 
                   name="project_features[]" 
                   placeholder="${translations.addFeature}"
                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <button type="button" 
                    onclick="removeFeature(this)"
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg transition-colors duration-200">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        featuresContainer.appendChild(newFeature);
        
        // Focus on the new input
        const newInput = newFeature.querySelector('input');
        if (newInput) {
            newInput.focus();
        }
        
        // Add animation
        newFeature.style.opacity = '0';
        newFeature.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            newFeature.style.transition = 'all 0.3s ease';
            newFeature.style.opacity = '1';
            newFeature.style.transform = 'translateY(0)';
        }, 10);
    },
    
    // Remove feature input
    removeFeature(button) {
        const featureDiv = button.parentElement;
        const featuresContainer = document.getElementById('featuresContainer');
        
        // Don't remove if it's the last feature
        if (featuresContainer.children.length <= 1) {
            // Just clear the input instead of removing
            const input = featureDiv.querySelector('input');
            if (input) {
                input.value = '';
                input.focus();
            }
            return;
        }
        
        // Animate out
        featureDiv.style.transition = 'all 0.3s ease';
        featureDiv.style.opacity = '0';
        featureDiv.style.transform = 'translateX(100%)';
        
        setTimeout(() => {
            featureDiv.remove();
        }, 300);
    },
    
    // Get all feature values
    getFeatures() {
        const featuresContainer = document.getElementById('featuresContainer');
        if (!featuresContainer) return [];
        
        const inputs = featuresContainer.querySelectorAll('input[name="project_features[]"]');
        const features = [];
        
        inputs.forEach(input => {
            const value = input.value.trim();
            if (value) {
                features.push(value);
            }
        });
        
        return features;
    },
    
    // Set features
    setFeatures(features) {
        const featuresContainer = document.getElementById('featuresContainer');
        if (!featuresContainer) return;
        
        // Clear existing features
        featuresContainer.innerHTML = '';
        
        if (features.length === 0) {
            // Add one empty feature input
            this.addFeature();
            return;
        }
        
        // Add features
        features.forEach((feature, index) => {
            const featureDiv = document.createElement('div');
            featureDiv.className = 'flex items-center space-x-2 mb-2';
            featureDiv.innerHTML = `
                <input type="text" 
                       name="project_features[]" 
                       value="${feature}"
                       placeholder="${translations.addFeature}"
                       class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <button type="button" 
                        onclick="removeFeature(this)"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            featuresContainer.appendChild(featureDiv);
        });
    },
    
    // Validate features
    validateFeatures() {
        const features = this.getFeatures();
        
        // Check for duplicates
        const uniqueFeatures = [...new Set(features)];
        if (features.length !== uniqueFeatures.length) {
            return {
                valid: false,
                message: 'Duplicate features are not allowed'
            };
        }
        
        // Check for empty features
        if (features.some(feature => !feature.trim())) {
            return {
                valid: false,
                message: 'All features must have content'
            };
        }
        
        return {
            valid: true,
            message: 'Features are valid'
        };
    },
    
    // Add feature with Enter key support
    addEnterKeySupport() {
        const featuresContainer = document.getElementById('featuresContainer');
        if (!featuresContainer) return;
        
        featuresContainer.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
                e.preventDefault();
                this.addFeature();
            }
        });
    }
};

// Initialize features management
document.addEventListener('DOMContentLoaded', function() {
    FeaturesManager.addEnterKeySupport();
});

// Make FeaturesManager globally available
window.FeaturesManager = FeaturesManager;
window.addFeature = FeaturesManager.addFeature.bind(FeaturesManager);
window.removeFeature = FeaturesManager.removeFeature.bind(FeaturesManager);
