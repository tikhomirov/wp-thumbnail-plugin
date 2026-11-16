<?php

namespace KamaThumb\Infrastructure\ImageProcessors;

use KamaThumb\Domain\Contracts\ImageProcessorInterface;

final class ImagickProcessor implements ImageProcessorInterface
{
    public function process(array $params): bool
    {
        if (! $this->isAvailable()) {
            return false;
        }

        try {
            $imagick = new \Imagick($params['source_path']);

            $originalWidth = $imagick->getImageWidth();
            $originalHeight = $imagick->getImageHeight();

            $width = $params['width'];
            $height = $params['height'];
            $crop = $params['crop'];

            if ($crop) {
                $this->cropImage($imagick, $originalWidth, $originalHeight, $width, $height, $crop);
            } else {
                $imagick->thumbnailImage($width, $height, true);
            }

            $imagick->setImageCompressionQuality($params['quality']);

            if (isset($params['format'])) {
                $imagick->setImageFormat($params['format']);
            }

            $imagick->stripImage();

            $dir = dirname($params['destination_path']);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $imagick->writeImage($params['destination_path']);
            $imagick->clear();
            $imagick->destroy();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isAvailable(): bool
    {
        return extension_loaded('imagick');
    }

    public function getSupportedFormats(): array
    {
        return ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    }

    protected function cropImage(
        \Imagick $imagick,
        int $originalWidth,
        int $originalHeight,
        int $targetWidth,
        int $targetHeight,
        bool|array $crop,
    ): void {
        $sourceRatio = $originalWidth / $originalHeight;
        $targetRatio = $targetWidth / $targetHeight;

        if ($sourceRatio > $targetRatio) {
            $newWidth = (int) ($originalHeight * $targetRatio);
            $newHeight = $originalHeight;
            $x = (int) (($originalWidth - $newWidth) / 2);
            $y = 0;
        } else {
            $newWidth = $originalWidth;
            $newHeight = (int) ($originalWidth / $targetRatio);
            $x = 0;
            $y = (int) (($originalHeight - $newHeight) / 2);
        }

        if (is_array($crop)) {
            $x = isset($crop[0]) ? (int) ($crop[0] * $originalWidth / 100) : $x;
            $y = isset($crop[1]) ? (int) ($crop[1] * $originalHeight / 100) : $y;
        }

        $imagick->cropImage($newWidth, $newHeight, $x, $y);
        $imagick->thumbnailImage($targetWidth, $targetHeight, false);
    }
}
