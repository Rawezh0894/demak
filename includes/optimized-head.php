<!-- Optimized Head Section for Better Performance -->

<!-- DNS Prefetch بۆ CDN-ەکان -->
<link rel="dns-prefetch" href="//cdn.tailwindcss.com">
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="//images.unsplash.com">

<!-- Preconnect بۆ خێراتر لۆد کردن -->
<link rel="preconnect" href="https://cdn.tailwindcss.com">
<link rel="preconnect" href="https://cdnjs.cloudflare.com">
<link rel="preconnect" href="https://images.unsplash.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<!-- Preload بۆ فایلە گرنگەکان -->
<?php
$base_path = isset($base_path) ? $base_path : '';
?>
<link rel="preload" href="<?php echo $base_path; ?>assets/fonts/Rabar_021.ttf" as="font" type="font/ttf" crossorigin>
<link rel="preload" href="<?php echo $base_path; ?>assets/css/main.css" as="style">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lalezar&display=swap">

<!-- Tailwind CSS: Prefer compiled CSS in production, fallback to CDN in dev -->
<?php
$tailwindLocal = ($base_path ?? '') . 'assets/css/tailwind.min.css';
if (file_exists(__DIR__ . '/../' . str_replace('../', '', $tailwindLocal))) {
?>
<link rel="stylesheet" href="<?php echo $tailwindLocal; ?>">
<?php } else { ?>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    'rabar': ['Rabar', 'sans-serif'],
                }
            }
        }
    }
    // Note: Using CDN in development; build tailwind.min.css for production
</script>
<?php } ?>

<!-- Font Awesome - تەنها ئەو ئایکۆنانەی پێویستە -->
<!-- 
    تێبینی: دەتوانین ئەمە بگۆڕین بە فایلی icons.css-مان بۆ باشترین performance
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/icons.css">
-->
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

<!-- Performance Optimization Meta Tags -->
<meta http-equiv="x-dns-prefetch-control" content="on">

<!-- تێبینیەکان بۆ باشترکردنی زیاتر:
1. بەکارهێنانی icons.css لە جیاتی Font Awesome (کەمکردنەوەی 200KB)
2. دروستکردنی Tailwind build-ێکی کەمتر (کەمکردنەوەی 400KB)
3. بەکارهێنانی WebP بۆ وێنەکان (کەمکردنەوەی 50-80%)
4. زیادکردنی Service Worker بۆ offline caching
-->

