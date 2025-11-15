<?php
/**
 * Imagick stub for IDEs/static analysis when imagick extension is missing.
 *
 * This file is not included at runtime, it only exists so tools like
 * Intelephense/Psalm/PHPStan can understand the Imagick API without the
 * native extension being installed locally.
 */

if (!class_exists('Imagick')) {
    /**
     * @internal Stub class, real implementation lives in the imagick extension.
     * @codeCoverageIgnore
     */
    class Imagick
    {
        public const FILTER_LANCZOS = 1;

        public function __construct(?string $filename = null) {}

        public function getImageWidth(): int
        {
            return 0;
        }

        public function getImageHeight(): int
        {
            return 0;
        }

        public function resizeImage(
            int $columns,
            int $rows,
            int $filter,
            float $blur,
            bool $bestfit = false
        ): bool {
            return false;
        }

        public function setImageFormat(string $format): bool
        {
            return false;
        }

        public function setImageCompressionQuality(int $quality): bool
        {
            return false;
        }

        public function stripImage(): bool
        {
            return false;
        }

        public function optimizeImageLayers(): bool
        {
            return false;
        }

        public function writeImage(string $filename): bool
        {
            return false;
        }

        public function destroy(): bool
        {
            return false;
        }
    }
}

