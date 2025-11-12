<?php
// Hero Section
?>
<!-- Hero Section -->
<section class="construction-hero">
    <!-- Image Slider -->
    <div class="hero-slider">
        <div class="slider-container">
            <!-- Slide 1: Modern Construction -->
           <h1>Hello</h1>
            <div class="slide active" style="background-image: url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')"></div>
            
            <!-- Slide 2: Building Construction -->
            <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')"></div>
            
            <!-- Slide 3: Industrial Construction -->
            <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')"></div>
            
            <!-- Slide 4: Residential Construction -->
            <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')"></div>
            
            <!-- Slide 5: Commercial Building -->
            <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1541971875076-8f970d573be6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')"></div>
        </div>
        
        <!-- Slider Navigation -->
        <div class="slider-nav">
            <div class="slider-dot active"></div>
            <div class="slider-dot"></div>
            <div class="slider-dot"></div>
            <div class="slider-dot"></div>
            <div class="slider-dot"></div>
        </div>
        
        <!-- Slider Arrows -->
        <button class="slider-arrow prev">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="slider-arrow next">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    
    <!-- Hero Content -->
    <div class="slide-content relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 fade-in">
                <?php echo t('welcome'); ?> <br>
                <span class="text-yellow-400"><?php echo t('construction_company'); ?></span>
            </h1>
            <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                <?php echo t('excellence_in_construction'); ?> - <?php echo t('we_build_dreams'); ?>
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#projects" class="btn btn-primary text-lg px-8 py-3">
                    <?php echo t('our_projects'); ?>
                </a>
                <a href="#contact" class="btn bg-white text-blue-600 hover:bg-gray-100 text-lg px-8 py-3">
                    <?php echo t('get_quote'); ?>
                </a>
            </div>
        </div>
    </div>
</section>
