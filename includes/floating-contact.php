<?php
// Include shared translations
require_once __DIR__ . '/translations.php';

// Get current language direction
$page_dir = $languages[$current_lang]['dir'];
$is_rtl = ($page_dir === 'rtl');

// Contact information (can be moved to database later)
$contact_phone = '+964 750 123 4567';
$contact_email = 'info@demak.com';
$contact_facebook = 'https://www.facebook.com/demak'; // Replace with actual Facebook page URL

// Set position based on direction
$contact_position = $is_rtl ? 'left-0' : 'right-0';
$contact_transform = $is_rtl ? '-translate-x-full' : 'translate-x-full';
$contact_expand_transform = $is_rtl ? 'translate-x-0' : 'translate-x-0';
?>

<!-- Floating Contact Sidebar -->
<div id="floatingContact" class="fixed z-40 draggable-floating-contact" data-direction="<?php echo $page_dir; ?>" style="cursor: move;">
    <!-- Toggle Button -->
    <button id="floatingContactToggle" class="relative w-14 h-14 bg-gradient-to-br from-blue-600 via-blue-500 to-green-500 text-white rounded-full shadow-2xl hover:shadow-blue-500/50 transition-all duration-500 flex items-center justify-center group hover:scale-110 hover:rotate-12 ring-4 ring-blue-500/20 hover:ring-blue-500/40" onmousedown="handleDragStart(event)" ontouchstart="handleDragStart(event)">
        <!-- Animated Background -->
        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-blue-400 to-green-400 opacity-0 group-hover:opacity-100 transition-opacity duration-500 animate-pulse"></div>
        <!-- Icon -->
        <svg class="w-7 h-7 relative z-10 transition-all duration-500 group-hover:scale-125 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
        </svg>
        <!-- Ripple Effect -->
        <span class="absolute inset-0 rounded-full bg-white opacity-0 group-active:opacity-20 group-active:scale-150 transition-all duration-500"></span>
        <!-- Contact Text -->
        <span class="absolute <?php echo $is_rtl ? '-right-20' : '-left-20'; ?> top-1/2 -translate-y-1/2 text-xs font-bold text-gray-800 dark:text-gray-100 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-500 transform <?php echo $is_rtl ? 'translate-x-2' : '-translate-x-2'; ?> group-hover:translate-x-0 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 pointer-events-none">
            <?php echo t('contact'); ?>
        </span>
    </button>
    
    <!-- Contact Panel -->
    <div id="floatingContactPanel" class="fixed max-w-[calc(100vw-20px)] bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 hidden transform transition-all duration-500 ease-out" style="z-index: 39; width: 288px;">
        <!-- Gradient Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 via-transparent to-green-50/50 dark:from-blue-900/10 dark:via-transparent dark:to-green-900/10 rounded-2xl pointer-events-none"></div>
        
        <div class="relative p-5">
            <!-- Header -->
            <div class="flex items-center justify-between mb-5 pb-4 border-b-2 border-gradient-to-r from-blue-500 to-green-500 border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-green-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold bg-gradient-to-r from-blue-600 to-green-600 bg-clip-text text-transparent dark:from-blue-400 dark:to-green-400"><?php echo t('contact_us'); ?></h3>
                </div>
                <button onclick="toggleFloatingContact(event)" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-red-100 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-400 transition-all duration-300 flex items-center justify-center group" id="closeContactPanelBtn">
                    <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Contact Options -->
            <div class="space-y-3">
                <!-- Call -->
                <a href="tel:<?php echo str_replace(' ', '', $contact_phone); ?>" class="floating-contact-item floating-contact-call flex items-center space-x-4 rtl:space-x-reverse p-4 rounded-xl transition-all duration-300 group border hover:shadow-lg hover:-translate-y-0.5">
                    <div class="relative w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center text-white group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg shadow-green-500/30 icon-container-call">
                        <i class="fas fa-phone-alt text-lg relative z-10"></i>
                        <span class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wide mb-1"><?php echo t('phone'); ?></p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate"><?php echo $contact_phone; ?></p>
                    </div>
                    <svg class="w-5 h-5 text-green-500 opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                
                <!-- Gmail -->
                <a href="mailto:<?php echo $contact_email; ?>" class="floating-contact-item floating-contact-gmail flex items-center space-x-4 rtl:space-x-reverse p-4 rounded-xl transition-all duration-300 group border hover:shadow-lg hover:-translate-y-0.5">
                    <div class="relative w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl flex items-center justify-center text-white group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg shadow-red-500/30 icon-container-gmail">
                        <i class="fab fa-google text-lg relative z-10"></i>
                        <span class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wide mb-1">Gmail</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate"><?php echo $contact_email; ?></p>
                    </div>
                    <svg class="w-5 h-5 text-red-500 opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                
                <!-- Facebook -->
                <a href="<?php echo $contact_facebook; ?>" target="_blank" rel="noopener noreferrer" class="floating-contact-item floating-contact-facebook flex items-center space-x-4 rtl:space-x-reverse p-4 rounded-xl transition-all duration-300 group border hover:shadow-lg hover:-translate-y-0.5">
                    <div class="relative w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center text-white group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg shadow-blue-500/30 icon-container-facebook">
                        <i class="fab fa-facebook-f text-lg relative z-10"></i>
                        <span class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-1">Facebook</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white"><?php echo t('follow_us'); ?></p>
                    </div>
                    <svg class="w-5 h-5 text-blue-500 opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Draggable Floating Contact Variables
let isDragging = false;
let dragStartX = 0;
let dragStartY = 0;
let initialX = 0;
let initialY = 0;
let currentX = 0;
let currentY = 0;

// Initialize Floating Contact Position
function initializeFloatingContactPosition() {
    const floatingContact = document.getElementById('floatingContact');
    if (!floatingContact) return;
    
    // Get initial X position (fixed based on direction)
    currentX = getInitialXPosition();
    
    // Get saved Y position from localStorage
    const savedPosition = localStorage.getItem('floatingContactPosition');
    
    if (savedPosition) {
        const { y } = JSON.parse(savedPosition);
        currentY = y;
        floatingContact.style.left = currentX + 'px';
        floatingContact.style.top = y + 'px';
        floatingContact.style.right = 'auto';
    } else {
        // Default position - center vertically
        const screenHeight = window.innerHeight;
        const buttonSize = 56; // 14 * 4 (w-14 = 3.5rem = 56px)
        
        currentY = screenHeight / 2 - buttonSize / 2; // Center vertically
        
        floatingContact.style.left = currentX + 'px';
        floatingContact.style.top = currentY + 'px';
        floatingContact.style.right = 'auto';
    }
    
    // Update panel position
    updatePanelPosition();
}

// Update Panel Position based on button position
function updatePanelPosition() {
    const floatingContact = document.getElementById('floatingContact');
    const panel = document.getElementById('floatingContactPanel');
    if (!floatingContact || !panel) return;
    
    const direction = floatingContact.getAttribute('data-direction') || 'ltr';
    const isRTL = direction === 'rtl';
    const buttonSize = 56;
    const fixedOffset = 10; // Fixed horizontal distance from button (10px)
    const screenWidth = window.innerWidth;
    const screenHeight = window.innerHeight;
    const panelWidth = 288; // w-72 = 18rem = 288px
    const panelHeight = panel.offsetHeight || 400;
    const minPadding = 10; // Minimum padding from screen edges
    
    // Get button position from style (more reliable than getBoundingClientRect during drag)
    const buttonTop = parseFloat(floatingContact.style.top) || 0;
    const buttonLeft = parseFloat(floatingContact.style.left) || 0;
    
    // Check if screen is too small - adjust panel width for mobile
    const isSmallScreen = screenWidth < 640; // sm breakpoint
    const actualPanelWidth = isSmallScreen ? Math.min(panelWidth, screenWidth - minPadding * 2) : panelWidth;
    
    // Calculate panel position horizontally
    if (isRTL) {
        // For RTL: panel appears to the right of button
        let panelLeft = buttonLeft + buttonSize + fixedOffset;
        
        // Ensure panel doesn't go off screen to the right
        if (panelLeft + actualPanelWidth > screenWidth - minPadding) {
            panelLeft = screenWidth - actualPanelWidth - minPadding;
        }
        
        // Ensure panel doesn't go off screen to the left
        if (panelLeft < minPadding) {
            panelLeft = minPadding;
        }
        
        panel.style.left = panelLeft + 'px';
        panel.style.right = 'auto';
        panel.style.width = actualPanelWidth + 'px';
    } else {
        // For LTR: panel appears to the left of button
        let panelRight = screenWidth - buttonLeft - buttonSize - fixedOffset;
        
        // Calculate panel left position
        let panelLeft = screenWidth - panelRight - actualPanelWidth;
        
        // Ensure panel doesn't go off screen to the left
        if (panelLeft < minPadding) {
            panelLeft = minPadding;
            panelRight = screenWidth - panelLeft - actualPanelWidth;
        }
        
        // Ensure panel doesn't go off screen to the right
        if (panelLeft + actualPanelWidth > screenWidth - minPadding) {
            panelRight = minPadding;
            panelLeft = screenWidth - actualPanelWidth - minPadding;
        }
        
        panel.style.left = panelLeft + 'px';
        panel.style.right = 'auto';
        panel.style.width = actualPanelWidth + 'px';
    }
    
    // Calculate panel position vertically - center it with button
    const buttonCenter = buttonTop + buttonSize / 2;
    let panelTop = buttonCenter - panelHeight / 2;
    
    // Keep panel within viewport vertically
    const maxTop = screenHeight - panelHeight;
    const minTop = minPadding;
    
    // If panel would go above viewport, align top with minimum top
    if (panelTop < minTop) {
        panelTop = minTop;
    }
    // If panel would go below viewport, align bottom with viewport bottom
    else if (panelTop > maxTop) {
        panelTop = Math.max(minTop, maxTop);
    }
    
    panel.style.top = panelTop + 'px';
}

// Track if user actually dragged (for click detection)
let userActuallyDragged = false;

// Handle Drag Start
function handleDragStart(e) {
    const floatingContact = document.getElementById('floatingContact');
    if (!floatingContact) return;
    
    // Reset drag tracking
    userActuallyDragged = false;
    
    // Only check vertical movement (Y axis) for drag detection
    const clickThreshold = 5; // pixels
    const startEvent = e.touches ? e.touches[0] : e;
    dragStartX = startEvent.clientX;
    dragStartY = startEvent.clientY;
    initialX = currentX; // Keep X position fixed
    initialY = currentY;
    
    // Don't prevent default immediately - allow click to work
    let hasMoved = false;
    
    // Set dragging flag after a small delay to distinguish click from drag
    let dragTimer = setTimeout(() => {
        if (!hasMoved) {
            isDragging = true;
            floatingContact.style.transition = 'none';
            document.body.style.userSelect = 'none';
            document.body.style.cursor = 'ns-resize'; // Vertical resize cursor
            
            // Add dragging class for visual feedback
            floatingContact.classList.add('dragging');
        }
    }, 100); // Reduced delay for better responsiveness
    
    // Handle mouse/touch move
    const handleMove = (moveEvent) => {
        const moveClientX = moveEvent.touches ? moveEvent.touches[0].clientX : moveEvent.clientX;
        const moveClientY = moveEvent.touches ? moveEvent.touches[0].clientY : moveEvent.clientY;
        
        // Only check vertical movement (Y axis) for drag detection
        const deltaY = Math.abs(moveClientY - dragStartY);
        
        if (deltaY > clickThreshold) {
            hasMoved = true;
            userActuallyDragged = true; // Mark that user actually dragged
            clearTimeout(dragTimer);
            isDragging = true;
            e.preventDefault(); // Prevent default only when actually dragging
            floatingContact.style.transition = 'none';
            document.body.style.userSelect = 'none';
            document.body.style.cursor = 'ns-resize'; // Vertical resize cursor
            floatingContact.classList.add('dragging');
        }
        
        if (isDragging) {
            // Only use Y offset - X position stays fixed
            const offsetY = moveClientY - dragStartY;
            
            // Calculate new position (only Y changes, X stays fixed)
            let newY = initialY + offsetY;
            
            // Boundary constraints (only for Y axis)
            const buttonSize = 56;
            const screenHeight = window.innerHeight;
            
            // Keep X position fixed at initialX
            newY = Math.max(0, Math.min(newY, screenHeight - buttonSize));
            
            currentY = newY;
            
            // Only update Y position, X stays at initialX
            floatingContact.style.left = initialX + 'px';
            floatingContact.style.top = newY + 'px';
            floatingContact.style.right = 'auto';
            
            // Update panel position immediately during drag
            requestAnimationFrame(() => {
                updatePanelPosition();
            });
        }
    };
    
    // Handle mouse/touch end
    const handleEnd = (endEvent) => {
        // Clear the drag timer
        clearTimeout(dragTimer);
        
        // Check if it was a click (no movement) or a drag
        const endEventX = endEvent.touches ? (endEvent.touches[0] ? endEvent.touches[0].clientX : dragStartX) : endEvent.clientX;
        const endEventY = endEvent.touches ? (endEvent.touches[0] ? endEvent.touches[0].clientY : dragStartY) : endEvent.clientY;
        const totalDeltaY = Math.abs(endEventY - dragStartY);
        
        // If it was a drag, save position
        if (userActuallyDragged && totalDeltaY > clickThreshold) {
            saveFloatingContactPosition();
        }
        
        // If it was a click (not a drag), toggle the panel immediately
        if (!userActuallyDragged && totalDeltaY <= clickThreshold) {
            // Use requestAnimationFrame for immediate execution without blocking
            requestAnimationFrame(() => {
                toggleFloatingContact(endEvent);
            });
        }
        
        // Reset dragging state
        isDragging = false;
        hasMoved = false;
        userActuallyDragged = false;
        floatingContact.style.transition = '';
        document.body.style.userSelect = '';
        document.body.style.cursor = '';
        floatingContact.classList.remove('dragging');
        
        // Remove event listeners
        document.removeEventListener('mousemove', handleMove);
        document.removeEventListener('mouseup', handleEnd);
        document.removeEventListener('touchmove', handleMove);
        document.removeEventListener('touchend', handleEnd);
    };
    
    // Add event listeners
    document.addEventListener('mousemove', handleMove);
    document.addEventListener('mouseup', handleEnd);
    document.addEventListener('touchmove', handleMove);
    document.addEventListener('touchend', handleEnd);
}

// Save Floating Contact Position
function saveFloatingContactPosition() {
    localStorage.setItem('floatingContactPosition', JSON.stringify({
        x: currentX,
        y: currentY
    }));
}

// Get Initial X Position (fixed position based on direction)
function getInitialXPosition() {
    const floatingContact = document.getElementById('floatingContact');
    if (!floatingContact) return 0;
    
    const direction = floatingContact.getAttribute('data-direction') || 'ltr';
    const isRTL = direction === 'rtl';
    const screenWidth = window.innerWidth;
    const buttonSize = 56;
    
    if (isRTL) {
        return 20; // 20px from left
    } else {
        return screenWidth - buttonSize - 20; // 20px from right
    }
}

// Floating Contact Toggle Function
function toggleFloatingContact(e) {
    const floatingContact = document.getElementById('floatingContact');
    const panel = document.getElementById('floatingContactPanel');
    const toggle = document.getElementById('floatingContactToggle');
    
    if (!panel || !toggle || !floatingContact) return;
    
    // Prevent event propagation if event is provided
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    // Get direction from data attribute
    const direction = floatingContact.getAttribute('data-direction') || 'ltr';
    const isRTL = direction === 'rtl';
    const hideClass = isRTL ? '-translate-x-full' : 'translate-x-full';
    const showClass = 'translate-x-0';
    
    const isHidden = panel.classList.contains('hidden');
    
    if (isHidden) {
        // Update panel position before showing
        updatePanelPosition();
        
        // Show panel with animation
        panel.classList.remove('hidden');
        // Remove all transform classes
        panel.classList.remove('-translate-x-full', 'translate-x-full', 'translate-x-0', 'opacity-0', 'scale-95');
        // Add initial state
        panel.classList.add('opacity-0', 'scale-95');
        // Trigger animation
        requestAnimationFrame(() => {
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add(showClass, 'opacity-100', 'scale-100');
        });
    } else {
        // Hide panel with animation
        panel.classList.remove(showClass, 'opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95', hideClass);
        // Hide after animation
        setTimeout(() => {
            panel.classList.add('hidden');
            panel.classList.remove('opacity-0', 'scale-95');
        }, 500);
    }
}

// Close panel when clicking outside
document.addEventListener('click', function(event) {
    const floatingContact = document.getElementById('floatingContact');
    const panel = document.getElementById('floatingContactPanel');
    const toggle = document.getElementById('floatingContactToggle');
    const closeBtn = document.getElementById('closeContactPanelBtn');
    
    if (!floatingContact || !panel || !toggle) return;
    
    // Don't close if clicking on toggle button, close button, or inside panel
    if (toggle.contains(event.target) || panel.contains(event.target) || (closeBtn && closeBtn.contains(event.target))) {
        return;
    }
    
    // Don't close if currently dragging
    if (isDragging) return;
    
    // Close if clicking outside
    if (!panel.classList.contains('hidden')) {
        toggleFloatingContact(event);
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeFloatingContactPosition();
    
    // Update position on window resize
    window.addEventListener('resize', function() {
        const floatingContact = document.getElementById('floatingContact');
        if (!floatingContact) return;
        
        // Update X position based on direction (fixed)
        currentX = getInitialXPosition();
        
        // Ensure Y position is within bounds
        const buttonSize = 56;
        const screenHeight = window.innerHeight;
        
        currentY = Math.max(0, Math.min(currentY, screenHeight - buttonSize));
        
        floatingContact.style.left = currentX + 'px';
        floatingContact.style.top = currentY + 'px';
        
        updatePanelPosition();
    });
});
</script>

