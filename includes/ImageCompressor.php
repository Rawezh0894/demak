<?php
require_once __DIR__ . '/stubs/ImagickStub.php';
/**
 * Image Compressor Class
 * 
 * Professional image compression with quality preservation
 * Uses ImageMagick if available, falls back to GD
 * 
 * @phpstan-ignore-next-line
 * @psalm-suppress UndefinedClass
 * @phpstan-ignore-next-line
 * @psalm-suppress UndefinedClass
 */
class ImageCompressor {
    /**
     * Check if ImageMagick is available
     */
    public static function hasImageMagick() {
        return extension_loaded('imagick') && class_exists('Imagick');
    }
    
    /**
     * Check if GD is available
     */
    public static function hasGD() {
        return extension_loaded('gd') && function_exists('imagecreatefromjpeg');
    }
    
    /**
     * Compress image using ImageMagick (best quality)
     * 
     * @param string $source Source image path
     * @param string $destination Destination image path
     * @param int $quality Quality (0-100, 85-90 recommended)
     * @param int $maxWidth Maximum width (0 = no resize)
     * @param int $maxHeight Maximum height (0 = no resize)
     * @return bool Success status
     */
    /**
     * @phpstan-ignore-next-line
     * @psalm-suppress UndefinedClass
     */
    public static function compressWithImageMagick($source, $destination, $quality = 85, $maxWidth = 0, $maxHeight = 0) {
        try {
            /** @var \Imagick $image */
            $image = new \Imagick($source);
            
            // Get original dimensions
            $originalWidth = $image->getImageWidth();
            $originalHeight = $image->getImageHeight();
            
            // Resize if needed
            if ($maxWidth > 0 || $maxHeight > 0) {
                $newWidth = $originalWidth;
                $newHeight = $originalHeight;
                
                if ($maxWidth > 0 && $originalWidth > $maxWidth) {
                    $ratio = $maxWidth / $originalWidth;
                    $newWidth = $maxWidth;
                    $newHeight = (int)($originalHeight * $ratio);
                }
                
                if ($maxHeight > 0 && $newHeight > $maxHeight) {
                    $ratio = $maxHeight / $newHeight;
                    $newHeight = $maxHeight;
                    $newWidth = (int)($newWidth * $ratio);
                }
                
                if ($newWidth != $originalWidth || $newHeight != $originalHeight) {
                    /** @phpstan-ignore-next-line */
                    /** @psalm-suppress UndefinedClass */
                    $image->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1, true);
                }
            }
            
            // Determine output format based on source
            $sourceInfo = getimagesize($source);
            $mimeType = $sourceInfo['mime'] ?? 'image/jpeg';
            
            // Use WebP for best compression if supported
            if (function_exists('imagewebp')) {
                $image->setImageFormat('webp');
                $image->setImageCompressionQuality($quality);
            } else {
                // Fallback to JPEG
                $image->setImageFormat('jpeg');
                $image->setImageCompressionQuality($quality);
            }
            
            // Remove metadata to reduce file size
            $image->stripImage();
            
            // Optimize image layers
            $image->optimizeImageLayers();
            
            // Write compressed image
            $image->writeImage($destination);
            $image->destroy();
            
            return true;
        } catch (Exception $e) {
            error_log("ImageMagick compression error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Compress image using GD (fallback)
     * 
     * @param string $source Source image path
     * @param string $destination Destination image path
     * @param int $quality Quality (0-100, 85-90 recommended)
     * @param int $maxWidth Maximum width (0 = no resize)
     * @param int $maxHeight Maximum height (0 = no resize)
     * @return bool Success status
     */
    public static function compressWithGD($source, $destination, $quality = 85, $maxWidth = 0, $maxHeight = 0) {
        try {
            $info = getimagesize($source);
            if (!$info) {
                return false;
            }
            
            $mimeType = $info['mime'];
            $originalWidth = $info[0];
            $originalHeight = $info[1];
            
            // Create image resource based on type
            switch ($mimeType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($source);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($source);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($source);
                    break;
                case 'image/webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $image = imagecreatefromwebp($source);
                    } else {
                        return false;
                    }
                    break;
                default:
                    return false;
            }
            
            if (!$image) {
                return false;
            }
            
            // Calculate new dimensions if resize needed
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
            
            if ($maxWidth > 0 || $maxHeight > 0) {
                if ($maxWidth > 0 && $originalWidth > $maxWidth) {
                    $ratio = $maxWidth / $originalWidth;
                    $newWidth = $maxWidth;
                    $newHeight = (int)($originalHeight * $ratio);
                }
                
                if ($maxHeight > 0 && $newHeight > $maxHeight) {
                    $ratio = $maxHeight / $newHeight;
                    $newHeight = $maxHeight;
                    $newWidth = (int)($newWidth * $ratio);
                }
                
                // Resize if needed
                if ($newWidth != $originalWidth || $newHeight != $originalHeight) {
                    $resized = imagecreatetruecolor($newWidth, $newHeight);
                    
                    // Preserve transparency for PNG
                    if ($mimeType == 'image/png') {
                        imagealphablending($resized, false);
                        imagesavealpha($resized, true);
                        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                        imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
                    }
                    
                    imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
                    imagedestroy($image);
                    $image = $resized;
                }
            }
            
            // Determine output format and extension
            $destinationExt = strtolower(pathinfo($destination, PATHINFO_EXTENSION));
            
            // Save compressed image
            if ($destinationExt == 'webp' && function_exists('imagewebp')) {
                imagewebp($image, $destination, $quality);
            } elseif ($destinationExt == 'png' || $mimeType == 'image/png') {
                // PNG compression (0-9, 6 is good balance)
                $pngQuality = 9 - round(($quality / 100) * 9);
                imagepng($image, $destination, $pngQuality);
            } else {
                // JPEG
                imagejpeg($image, $destination, $quality);
            }
            
            imagedestroy($image);
            
            return true;
        } catch (Exception $e) {
            error_log("GD compression error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Compress image (auto-detect best method)
     * 
     * @param string $source Source image path
     * @param string $destination Destination image path (optional, if null overwrites source)
     * @param int $quality Quality (0-100, 85-90 recommended)
     * @param int $maxWidth Maximum width (0 = no resize, recommended: 1920 for main, 1200 for gallery)
     * @param int $maxHeight Maximum height (0 = no resize, recommended: 1080 for main, 800 for gallery)
     * @return bool Success status
     */
    public static function compress($source, $destination = null, $quality = 85, $maxWidth = 0, $maxHeight = 0) {
        if (!file_exists($source)) {
            error_log("ImageCompressor: Source file not found: " . $source);
            return false;
        }
        
        // If no destination specified, overwrite source
        if ($destination === null) {
            $destination = $source;
        }
        
        // Ensure destination directory exists
        $destDir = dirname($destination);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        
        // Try ImageMagick first (best quality)
        if (self::hasImageMagick()) {
            if (self::compressWithImageMagick($source, $destination, $quality, $maxWidth, $maxHeight)) {
                return true;
            }
        }
        
        // Fallback to GD
        if (self::hasGD()) {
            return self::compressWithGD($source, $destination, $quality, $maxWidth, $maxHeight);
        }
        
        // If neither is available, just copy the file
        error_log("ImageCompressor: Neither ImageMagick nor GD is available. Copying file without compression.");
        return copy($source, $destination);
    }
    
    /**
     * Compress and convert to WebP (best compression)
     * 
     * @param string $source Source image path
     * @param string $destination Destination image path (will be .webp)
     * @param int $quality Quality (0-100, 85-90 recommended)
     * @param int $maxWidth Maximum width
     * @param int $maxHeight Maximum height
     * @return bool Success status
     * @phpstan-ignore-next-line
     * @psalm-suppress UndefinedClass
     */
    public static function compressToWebP($source, $destination, $quality = 85, $maxWidth = 0, $maxHeight = 0) {
        // Ensure destination has .webp extension
        $destination = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $destination);
        
        if (self::hasImageMagick()) {
            try {
                /** @var \Imagick $image */
                /** @phpstan-ignore-next-line */
                $image = new \Imagick($source);
                
                // Resize if needed
                if ($maxWidth > 0 || $maxHeight > 0) {
                    $originalWidth = $image->getImageWidth();
                    $originalHeight = $image->getImageHeight();
                    
                    $newWidth = $originalWidth;
                    $newHeight = $originalHeight;
                    
                    if ($maxWidth > 0 && $originalWidth > $maxWidth) {
                        $ratio = $maxWidth / $originalWidth;
                        $newWidth = $maxWidth;
                        $newHeight = (int)($originalHeight * $ratio);
                    }
                    
                    if ($maxHeight > 0 && $newHeight > $maxHeight) {
                        $ratio = $maxHeight / $newHeight;
                        $newHeight = $maxHeight;
                        $newWidth = (int)($newWidth * $ratio);
                    }
                    
                    if ($newWidth != $originalWidth || $newHeight != $originalHeight) {
                        /** @phpstan-ignore-next-line */
                        /** @psalm-suppress UndefinedClass */
                        $image->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1, true);
                    }
                }
                
                $image->setImageFormat('webp');
                $image->setImageCompressionQuality($quality);
                $image->stripImage();
                $image->optimizeImageLayers();
                $image->writeImage($destination);
                $image->destroy();
                
                return true;
            } catch (Exception $e) {
                error_log("ImageMagick WebP conversion error: " . $e->getMessage());
            }
        }
        
        // Fallback to GD
        if (self::hasGD() && function_exists('imagewebp')) {
            return self::compressWithGD($source, $destination, $quality, $maxWidth, $maxHeight);
        }
        
        return false;
    }
}
?>

