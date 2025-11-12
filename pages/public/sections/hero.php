<?php
// Hero Section
global $languages, $current_lang;
$is_rtl = ($languages[$current_lang]['dir'] ?? 'ltr') === 'rtl';
$heading_alignment = $is_rtl ? 'text-center md:text-right' : 'text-center md:text-left';
$body_alignment = $is_rtl ? 'text-center md:text-right' : 'text-center md:text-left';
$badge_alignment = $is_rtl ? 'justify-center md:justify-end' : 'justify-center md:justify-start';
$brand_highlight_alignment = $is_rtl ? 'md:ml-auto md:mr-0' : 'md:mr-auto md:ml-0';
?>
<!-- Hero Section -->
<section class="construction-hero" dir="<?php echo $is_rtl ? 'rtl' : 'ltr'; ?>">
    <!-- Image Slider -->
    <div class="hero-slider">
        <div class="slider-container">
            <!-- Slide 1: Modern Construction -->
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
    <div class="slide-content relative z-10 px-4 sm:px-6 lg:px-8 py-24">
        <div class="max-w-5xl mx-auto rounded-3xl">
            <div class="px-8 py-12 md:px-14 md:py-16 bg-gradient-to-br from-black/45 via-black/15 to-black/0 rounded-3xl">
                <p class="uppercase tracking-[0.35em] text-xs sm:text-sm md:text-base text-white/70 mb-5 <?php echo $heading_alignment; ?> drop-shadow-md">
                    <?php echo t('welcome_prefix'); ?>
                </p>
                <h1 class="mb-6 leading-tight <?php echo $heading_alignment; ?>">
                    <span class="relative flex w-fit mx-auto md:mx-0 px-6 py-3 md:px-10 md:py-4 rounded-full <?php echo $brand_highlight_alignment; ?> overflow-visible">
                        <span class="pointer-events-none absolute inset-0 rounded-full bg-gradient-to-r from-white/20 via-white/5 to-white/20 opacity-25"></span>
                        <span class="pointer-events-none absolute inset-0 rounded-full border border-white/30 opacity-40"></span>
                        <span class="relative text-5xl md:text-7xl font-black tracking-tight bg-gradient-to-r from-yellow-200 via-amber-400 to-orange-500 text-transparent bg-clip-text drop-shadow-2xl leading-none" style="font-family: 'Lalezar', 'Rabar', sans-serif;">
                            <?php echo t('construction_company'); ?>
                        </span>
                    </span>
                </h1>
                <p class="text-lg md:text-xl text-white/85 mb-10 max-w-3xl mx-auto <?php echo $body_alignment; ?> drop-shadow-lg <?php echo $is_rtl ? 'md:mr-0 md:ml-auto' : 'md:ml-0 md:mr-auto'; ?>">
                    <?php echo t('excellence_in_construction'); ?> · <?php echo t('we_build_dreams'); ?>
                </p>
                <div class="flex flex-col md:flex-row <?php echo $is_rtl ? 'md:items-center' : 'md:items-center'; ?> gap-6 md:gap-10 <?php echo $is_rtl ? 'md:flex-row-reverse' : ''; ?>">
                    <a href="#services" class="inline-flex items-center justify-center rounded-full border border-yellow-400/70 text-yellow-200 text-sm sm:text-base font-semibold tracking-wide py-3 px-10 hover:bg-yellow-400/15 transition-all duration-300 shadow-lg shadow-yellow-500/10 <?php echo $is_rtl ? 'self-center md:self-end' : 'self-center md:self-start'; ?>">
                        <?php echo t('our_services'); ?>
                    </a>
                    <div class="flex flex-wrap <?php echo $badge_alignment; ?> gap-3 text-white/85 text-sm sm:text-base">
                        <span class="px-4 py-2 rounded-full bg-black/30 border border-white/10 shadow-md shadow-black/30">
                            <?php echo t('innovative_design'); ?>
                        </span>
                        <span class="px-4 py-2 rounded-full bg-black/30 border border-white/10 shadow-md shadow-black/30">
                            <?php echo t('precision_engineering'); ?>
                        </span>
                        <span class="px-4 py-2 rounded-full bg-black/30 border border-white/10 shadow-md shadow-black/30">
                            <?php echo t('timely_delivery'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
