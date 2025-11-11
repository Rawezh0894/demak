# Dark Mode Implementation Guide

## Overview
This project uses a **unified dark mode system** that works consistently across all browsers (Chrome, Edge, Firefox, Safari).

## Files

### Core Files
1. **`assets/js/dark-mode-unified.js`** - Main dark mode handler
2. **`includes/optimized-head.php`** - Inline script in `<head>` to prevent flash
3. **`includes/navbar.php`** - Navbar integration with unified script

## How It Works

### 1. Immediate Application (Prevents Flash)
- Inline script in `<head>` applies dark mode **before** page render
- Checks `localStorage` first, then system preference
- Adds `dark` class to `<html>` element immediately

### 2. Full Initialization
- Unified script loads and initializes dark mode
- Applies both `dark` class (for Tailwind CSS) and `dark-mode` class (for custom styles)
- Updates icons and UI elements
- Listens for system preference changes

### 3. Browser Compatibility
- **Chrome/Edge**: Full support with `matchMedia` and `addEventListener`
- **Firefox**: Full support
- **Safari**: Full support
- **Older browsers**: Fallback to `addListener` for Edge/older Chrome

## Usage

### Basic Toggle
```javascript
// Toggle dark mode
window.toggleDarkMode();
```

### Check Current State
```javascript
// Returns true if dark mode is active
const isDark = window.getDarkModeState();
```

### Listen for Changes
```javascript
document.addEventListener('darkModeChanged', function(e) {
    const isDark = e.detail.isDark;
    // Handle dark mode change
});
```

## Classes Applied

- **`<html class="dark">`** - For Tailwind CSS dark mode
- **`<body class="dark-mode">`** - For custom dark mode styles

## Storage

- **Key**: `darkMode`
- **Values**: `'true'` or `'false'`
- **Location**: `localStorage`

## Priority Order

1. **User preference** (from `localStorage`)
2. **System preference** (from `prefers-color-scheme`)
3. **Default**: Light mode

## Integration

### In PHP Files
```php
<!-- Include in <head> -->
<?php require_once 'includes/optimized-head.php'; ?>

<!-- Include before </body> -->
<?php require_once 'includes/navbar.php'; ?>
```

### In HTML Files
```html
<!-- In <head> -->
<script src="assets/js/dark-mode-unified.js"></script>

<!-- Toggle button -->
<button onclick="toggleDarkMode()">Toggle</button>
```

## Troubleshooting

### Dark mode not working in Edge
- Ensure `dark-mode-unified.js` is loaded
- Check browser console for errors
- Verify `localStorage` is enabled

### Flash of light content
- Ensure inline script in `<head>` is present
- Script must run before page render

### Icons not updating
- Ensure icon elements have correct IDs:
  - `lightModeIcon`
  - `darkModeIcon`
- Listen for `darkModeChanged` event

## Migration from Old System

If you have old dark mode code, replace it with:

```javascript
// Old code
function toggleDarkMode() {
    body.classList.toggle('dark-mode');
}

// New code (unified)
// Just use window.toggleDarkMode() - it's already available
```

The unified script handles everything automatically!

