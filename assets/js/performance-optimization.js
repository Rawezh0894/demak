/**
 * Performance Optimization JavaScript
 * باشترکردنی خێرایی لۆدکردن و performance
 */

// 1. Lazy Loading بۆ وێنەکان
function initLazyLoading() {
    // بەکارهێنانی Intersection Observer بۆ lazy loading
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                
                // زیادکردنی skeleton loading
                img.classList.add('skeleton-loading');
                
                // گۆڕینی src
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
                if (img.dataset.srcset) {
                    img.srcset = img.dataset.srcset;
                }
                
                // سڕینەوەی skeleton کاتێک لۆد بوو
                img.addEventListener('load', function() {
                    img.classList.remove('skeleton-loading');
                    img.classList.add('loaded');
                });
                
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: '50px' // لۆدکردن 50px پێش گەیشتن بە viewport
    });
    
    // تەماشاکردنی هەموو وێنەکان
    document.querySelectorAll('img[data-src], img[loading="lazy"]').forEach(img => {
        imageObserver.observe(img);
    });
}

// 2. Debounce بۆ Scroll Events
function debounce(func, wait = 20, immediate = false) {
    let timeout;
    return function executedFunction() {
        const context = this;
        const args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// 3. Throttle بۆ Resize Events
function throttle(func, limit = 100) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// 4. Fade In On Scroll بە Intersection Observer
function initScrollAnimations() {
    const animateOnScroll = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    document.querySelectorAll('.fade-in-on-scroll').forEach(el => {
        animateOnScroll.observe(el);
    });
}

// 5. Preload Next Page Resources
function preloadNextPageResources() {
    // Prefetch دەکات بۆ ئەو پەڕانەی ئەگەری زۆرە بەکاربێنرێن
    const links = document.querySelectorAll('a[href^="/"], a[href^="./"]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const link = entry.target;
                const href = link.getAttribute('href');
                if (href && !link.dataset.prefetched) {
                    const prefetchLink = document.createElement('link');
                    prefetchLink.rel = 'prefetch';
                    prefetchLink.href = href;
                    document.head.appendChild(prefetchLink);
                    link.dataset.prefetched = 'true';
                }
            }
        });
    });
    
    links.forEach(link => observer.observe(link));
}

// 6. کاتژمێرکردنی Performance
function measurePerformance() {
    if ('performance' in window) {
        window.addEventListener('load', () => {
            const perfData = performance.timing;
            const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
            const connectTime = perfData.responseEnd - perfData.requestStart;
            const renderTime = perfData.domComplete - perfData.domLoading;
            
            console.log('📊 Performance Metrics:');
            console.log(`Total Page Load: ${pageLoadTime}ms`);
            console.log(`Server Connection: ${connectTime}ms`);
            console.log(`DOM Rendering: ${renderTime}ms`);
            
            // دەتوانین ئەمانە بنێرین بۆ analytics
            if (pageLoadTime > 3000) {
                console.warn('⚠️ Page load time is over 3 seconds!');
            }
        });
    }
}

// 7. کەمکردنەوەی Reflow/Repaint
function batchDOMUpdates(updates) {
    requestAnimationFrame(() => {
        updates.forEach(update => update());
    });
}

// 8. Image Placeholder بۆ کاتی لۆدکردن
function createImagePlaceholder(img) {
    const placeholder = document.createElement('div');
    placeholder.className = 'image-placeholder skeleton-loading';
    placeholder.style.width = img.width + 'px';
    placeholder.style.height = img.height + 'px';
    return placeholder;
}

// 9. Check Connection Speed
function checkConnectionSpeed() {
    if ('connection' in navigator) {
        const connection = navigator.connection;
        const effectiveType = connection.effectiveType;
        
        // کەمکردنەوەی کوالێتی بۆ کەنەکشنی خاو
        if (effectiveType === 'slow-2g' || effectiveType === '2g') {
            console.log('🐌 Slow connection detected - reducing quality');
            document.body.classList.add('low-bandwidth');
            // کەمکردنەوەی ئەنیمەیشنەکان
            document.querySelectorAll('.floating-shapes').forEach(el => {
                el.style.display = 'none';
            });
        }
    }
}

// 10. Service Worker Registration (بۆ Offline Caching)
function registerServiceWorker() {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/service-worker.js')
                .then(registration => {
                    console.log('✅ Service Worker registered:', registration);
                })
                .catch(error => {
                    console.log('❌ Service Worker registration failed:', error);
                });
        });
    }
}

// 11. Critical CSS Loading
function loadNonCriticalCSS(href) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    link.media = 'print'; // لۆد دەکات بەبێ block کردنی render
    link.onload = function() {
        this.media = 'all'; // گۆڕینی بۆ all کاتێک لۆد بوو
    };
    document.head.appendChild(link);
}

// 12. Defer Non-Critical JavaScript
function loadDeferredScripts() {
    const scripts = document.querySelectorAll('script[data-defer]');
    scripts.forEach(script => {
        if (script.dataset.defer === 'true') {
            const newScript = document.createElement('script');
            newScript.src = script.dataset.src;
            newScript.async = true;
            document.body.appendChild(newScript);
        }
    });
}

// Initialize All Optimizations
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initializing Performance Optimizations...');
    
    // پێویستەکان یەکەم
    initLazyLoading();
    checkConnectionSpeed();
    
    // ئەوانەی کەم گرنگترن دواتر
    setTimeout(() => {
        initScrollAnimations();
        preloadNextPageResources();
        loadDeferredScripts();
    }, 1000);
    
    // Measurements
    if (window.location.search.includes('debug')) {
        measurePerformance();
    }
});

// Export functions for global use
window.performanceOptimization = {
    initLazyLoading,
    debounce,
    throttle,
    checkConnectionSpeed,
    measurePerformance
};

console.log('✅ Performance Optimization Module Loaded');

