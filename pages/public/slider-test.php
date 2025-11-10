<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider Test - Debug</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #1e293b;
            padding: 40px;
            font-family: Arial, sans-serif;
        }

        .slider-container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
        }

        .projects-slider {
            display: flex;
            overflow-x: hidden;
            scroll-behavior: smooth;
            gap: 0;
            padding: 1rem 0;
            transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            transform: translateX(0);
            width: 100%;
            flex-wrap: nowrap;
            align-items: stretch;
        }

        .project-slide {
            flex: 0 0 100%;
            flex-shrink: 0;
            flex-grow: 0;
            min-width: 100%;
            max-width: 100%;
            width: 100%;
            min-height: 300px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            background: #334155;
            border: 2px solid rgba(0,0,0,0.15);
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            opacity: 1 !important;
            visibility: visible !important;
            z-index: 1;
            padding: 20px;
        }

        .slide-content {
            color: white;
            font-size: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 3.5rem;
            height: 3.5rem;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.5rem;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slider-prev { left: 2rem; }
        .slider-next { right: 2rem; }

        .slide-counter {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            z-index: 10;
        }

        .slider-dots {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 2rem;
        }

        .slider-dot {
            width: 0.75rem;
            height: 0.75rem;
            background: white;
            opacity: 0.3;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .slider-dot.active {
            opacity: 1;
            width: 2rem;
            border-radius: 1rem;
        }

        .debug-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            color: white;
        }

        .debug-info h3 {
            margin-bottom: 10px;
        }

        .debug-info pre {
            background: rgba(0, 0, 0, 0.3);
            padding: 10px;
            border-radius: 8px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="slider-container">
        <!-- Debug Info -->
        <div class="debug-info" id="debugInfo">
            <h3>🐛 Debug Information</h3>
            <pre id="debugOutput">Initializing...</pre>
        </div>

        <!-- Slide Counter -->
        <div class="slide-counter" id="counter">
            <span class="current-slide">1</span>
            <span>/</span>
            <span class="total-slides">3</span>
        </div>
        
        <button class="slider-arrow slider-prev" onclick="prevSlide()">
            ‹
        </button>
        
        <div class="projects-slider" id="slider">
            <div class="project-slide" data-slide="1">
                <div class="slide-content">
                    <div>
                        <h1>Slide 1</h1>
                        <p style="font-size: 1rem; margin-top: 1rem;">This is the first project</p>
                    </div>
                </div>
            </div>
            
            <div class="project-slide" data-slide="2">
                <div class="slide-content">
                    <div>
                        <h1>Slide 2</h1>
                        <p style="font-size: 1rem; margin-top: 1rem;">This is the second project</p>
                    </div>
                </div>
            </div>
            
            <div class="project-slide" data-slide="3">
                <div class="slide-content">
                    <div>
                        <h1>Slide 3</h1>
                        <p style="font-size: 1rem; margin-top: 1rem;">This is the third project</p>
                    </div>
                </div>
            </div>
        </div>
        
        <button class="slider-arrow slider-next" onclick="nextSlide()">
            ›
        </button>

        <!-- Slider Dots -->
        <div class="slider-dots" id="dots">
            <button class="slider-dot active" onclick="goToSlide(0)"></button>
            <button class="slider-dot" onclick="goToSlide(1)"></button>
            <button class="slider-dot" onclick="goToSlide(2)"></button>
        </div>
    </div>

    <script>
        let currentSlide = 0;
        const totalSlides = 3;

        function updateDebugInfo() {
            const slider = document.getElementById('slider');
            const debugOutput = document.getElementById('debugOutput');
            
            let info = `Current Slide: ${currentSlide + 1} / ${totalSlides}\n`;
            info += `Transform: ${slider.style.transform || 'none'}\n`;
            info += `Slider Width: ${slider.offsetWidth}px\n`;
            info += `Slider Children Count: ${slider.children.length}\n\n`;
            
            Array.from(slider.children).forEach((child, index) => {
                info += `Slide ${index + 1}:\n`;
                info += `  Width: ${child.offsetWidth}px\n`;
                info += `  Flex: ${child.style.flex || 'none'}\n`;
                info += `  Display: ${getComputedStyle(child).display}\n`;
                info += `  Visibility: ${getComputedStyle(child).visibility}\n`;
                info += `  Opacity: ${getComputedStyle(child).opacity}\n`;
                info += `  Left Position: ${child.offsetLeft}px\n\n`;
            });
            
            debugOutput.textContent = info;
        }

        function goToSlide(slideIndex) {
            if (slideIndex < 0 || slideIndex >= totalSlides) return;
            
            currentSlide = slideIndex;
            const slider = document.getElementById('slider');
            const counter = document.getElementById('counter');
            const dots = document.getElementById('dots');
            
            // Update slider position
            const translateX = -slideIndex * 100;
            slider.style.transform = `translateX(${translateX}%)`;
            
            // Update counter
            counter.querySelector('.current-slide').textContent = slideIndex + 1;
            
            // Update dots
            dots.querySelectorAll('.slider-dot').forEach((dot, index) => {
                dot.classList.toggle('active', index === slideIndex);
            });
            
            updateDebugInfo();
        }

        function nextSlide() {
            const next = (currentSlide + 1) % totalSlides;
            goToSlide(next);
        }

        function prevSlide() {
            const prev = currentSlide === 0 ? totalSlides - 1 : currentSlide - 1;
            goToSlide(prev);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateDebugInfo();
            
            // Update debug info every second
            setInterval(updateDebugInfo, 1000);
        });
    </script>
</body>
</html>

