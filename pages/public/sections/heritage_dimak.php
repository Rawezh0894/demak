<?php
// Heritage Dimak Section - Traditional Kurdish Architecture Element
global $languages, $current_lang;
$is_rtl = ($languages[$current_lang]['dir'] ?? 'ltr') === 'rtl';
$text_alignment = $is_rtl ? 'text-right' : 'text-left';
$flex_direction = $is_rtl ? 'flex-row-reverse' : '';
?>

<!-- Heritage Dimak Section -->
<section id="heritage-dimak" class="py-20 bg-gradient-to-br from-gray-50 via-blue-50/30 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
            <div class="inline-block mb-4">
                <span class="px-4 py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm font-semibold">
                    <?php echo $current_lang === 'ku' ? 'کەرەستەی کۆنی بیناسازی' : ($current_lang === 'ar' ? 'مادة البناء التقليدية' : 'Traditional Building Material'); ?>
                </span>
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                <?php echo $current_lang === 'ku' ? 'دیمەک' : ($current_lang === 'ar' ? 'ديماك' : 'Dimak'); ?>
            </h2>
            <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-cyan-500 mx-auto rounded-full"></div>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto">
            <!-- Text Content Section -->
            <div class="<?php echo $text_alignment; ?>">
                <div class="space-y-6">
                    <!-- Introduction -->
                    <div class="prose prose-lg dark:prose-invert max-w-none">
                        <p class="text-xl md:text-2xl text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                            <?php if ($current_lang === 'ku'): ?>
                                دیمەک کەرەستەیەکی کۆنی بیناسازییە کە زیاتر لە خانووە بەردینەکانی هەوراماندا بەکارهاتووە، کە پارچەدارێکە و شێوەیەکی ئەندازەی پێدراوە و لە نێوان چینە بەردەکاندا بەکارهاتووە.
                            <?php elseif ($current_lang === 'ar'): ?>
                                ديماك مادة بناء تقليدية استُخدمت بشكل رئيسي في المنازل الحجرية في هورامان، وهي قطعة هندسية الشكل تُستخدم بين طبقات الحجارة.
                            <?php else: ?>
                                Dimak is a traditional building material that has been primarily used in the stone houses of Hawraman. It is a geometric piece that has been used between layers of stones.
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Benefits/Features -->
                    <div class="mt-10 space-y-6">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                            <?php echo $current_lang === 'ku' ? 'سودەکانی دیمەک' : ($current_lang === 'ar' ? 'فوائد ديماك' : 'Benefits of Dimak'); ?>
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Benefit 1: Structural Binding -->
                            <div class="group p-6 bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300 hover:shadow-lg">
                                <div class="flex items-start space-x-4 <?php echo $is_rtl ? 'space-x-reverse' : ''; ?>">
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-link text-white text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                            <?php echo $current_lang === 'ku' ? 'بەستنەوەی چینەکان' : ($current_lang === 'ar' ? 'ربط الطبقات' : 'Layer Binding'); ?>
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                            <?php echo $current_lang === 'ku' ? 'بەستنەوەی چینی دیوارە بەردینەکان بە شێوەیەکی بەهێز و بەردەوام' : ($current_lang === 'ar' ? 'ربط طبقات الجدران الحجرية بشكل قوي ومستمر' : 'Strong and continuous binding of stone wall layers'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Benefit 2: Moisture Absorption -->
                            <div class="group p-6 bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300 hover:shadow-lg">
                                <div class="flex items-start space-x-4 <?php echo $is_rtl ? 'space-x-reverse' : ''; ?>">
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-cyan-500 to-teal-500 rounded-xl flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-tint text-white text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                            <?php echo $current_lang === 'ku' ? 'هەڵمژینی شێ' : ($current_lang === 'ar' ? 'امتصاص الرطوبة' : 'Moisture Absorption'); ?>
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                            <?php echo $current_lang === 'ku' ? 'هەڵمژینی شێی ناو خانوەکان و پاراستنی دیوارەکان لە زیان' : ($current_lang === 'ar' ? 'امتصاص الرطوبة داخل المنازل وحماية الجدران من التلف' : 'Absorbing moisture inside houses and protecting walls from damage'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Benefit 3: Architectural Beauty -->
                            <div class="group p-6 bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300 hover:shadow-lg md:col-span-2">
                                <div class="flex items-start space-x-4 <?php echo $is_rtl ? 'space-x-reverse' : ''; ?>">
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-palette text-white text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                            <?php echo $current_lang === 'ku' ? 'جوانی تەلارسازی' : ($current_lang === 'ar' ? 'الجمال المعماري' : 'Architectural Beauty'); ?>
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                            <?php echo $current_lang === 'ku' ? 'شێوەیەکی تەلارسازی جوانی بە خانوەکانیش بەخشیوە و نیشانەیەکی کولتووری و میراتی کوردییە' : ($current_lang === 'ar' ? 'أضف جمالاً معمارياً للمنازل وهو علامة على التراث الثقافي الكردي' : 'Adds architectural beauty to houses and is a symbol of Kurdish cultural heritage'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cultural Significance -->
                    <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-2xl border-l-4 border-blue-500">
                        <div class="flex items-start space-x-4 <?php echo $is_rtl ? 'space-x-reverse' : ''; ?>">
                            <i class="fas fa-landmark text-blue-600 dark:text-blue-400 text-2xl mt-1"></i>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                    <?php echo $current_lang === 'ku' ? 'گرنگیداری کولتووری' : ($current_lang === 'ar' ? 'الأهمية الثقافية' : 'Cultural Significance'); ?>
                                </h4>
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                    <?php echo $current_lang === 'ku' ? 'دیمەک نیشانەیەکی گرنگی میراتی کوردی و تەلارسازی ناوچەی هەورامانە، کە نیشاندەری زیرەکی و داهێنانی پیشەسازانی کوردییە' : ($current_lang === 'ar' ? 'ديماك علامة مهمة على التراث الكردي والهندسة المعمارية لمنطقة هورامان، مما يدل على ذكاء وإبداع الحرفيين الأكراد' : 'Dimak is an important symbol of Kurdish heritage and the architecture of the Hawraman region, representing the intelligence and creativity of Kurdish craftsmen'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

