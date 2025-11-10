// Image Preview Functions

// Initialize image preview functionality
function initializeImagePreview() {
    const mainImage = document.getElementById('mainImage');
    const additionalImages = document.getElementById('additionalImages');
    
    if (mainImage) {
        mainImage.addEventListener('change', handleMainImageChange);
    }
    
    if (additionalImages) {
        additionalImages.addEventListener('change', handleAdditionalImagesChange);
    }
}

// Handle main image change
function handleMainImageChange(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const mainImagePreviewImg = document.getElementById('mainImagePreviewImg');
            const mainImagePreview = document.getElementById('mainImagePreview');
            
            if (mainImagePreviewImg) {
                mainImagePreviewImg.src = e.target.result;
            }
            if (mainImagePreview) {
                mainImagePreview.classList.remove('hidden');
            }
        };
        reader.readAsDataURL(file);
    }
}

// Handle additional images change
function handleAdditionalImagesChange(e) {
    const files = e.target.files;
    const preview = document.getElementById('additionalImagesPreview');
    
    if (!preview) return;
    
    preview.innerHTML = '';
    
    if (files.length > 0) {
        preview.classList.remove('hidden');
        
        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const thumbItem = document.createElement('div');
                thumbItem.className = 'thumb-item relative';
                thumbItem.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-20 object-cover rounded-lg">
                    <button type="button" onclick="removeAdditionalImage(${index})" class="absolute -top-1 -right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                preview.appendChild(thumbItem);
            };
            reader.readAsDataURL(file);
        });
    } else {
        preview.classList.add('hidden');
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeImagePreview();
});

// Override global functions if needed
window.initializeImagePreview = initializeImagePreview;

