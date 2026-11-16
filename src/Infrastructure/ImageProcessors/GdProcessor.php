<?php

namespace KamaThumb\Infrastructure\ImageProcessors;

use KamaThumb\Domain\Contracts\ImageProcessorInterface;

final class GdProcessor implements ImageProcessorInterface
{
    public function process(array $params): bool
    {
        if (! $this->isAvailable()) {
            return false;
        }

        $sourcePath = $params['source_path'];
        $destPath = $params['destination_path'];
        $width = $params['width'];
        $height = $params['height'];
        $crop = $params['crop'];
        $quality = $params['quality'];

        $imageInfo = getimagesize($sourcePath);
        if (! $imageInfo) {
            return false;
        }

        [$originalWidth, $originalHeight, $imageType] = $imageInfo;

        $sourceImage = $this->createImageFromFile($sourcePath, $imageType);
        if (! $sourceImage) {
            return false;
        }

        if ($crop) {
            $destImage = $this->cropAndResize($sourceImage, $originalWidth, $originalHeight, $width, $height, $crop);
        } else {
            $destImage = $this->resize($sourceImage, $originalWidth, $originalHeight, $width, $height);
        }

        if (! $destImage) {
            imagedestroy($sourceImage);

            return false;
        }

        $dir = dirname($destPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $format = $params['format'] ?? null;
        $success = $this->saveImage($destImage, $destPath, $format, $quality);

        imagedestroy($sourceImage);
        imagedestroy($destImage);

        return $success;
    }

    public function isAvailable(): bool
    {
        return function_exists('gd_info');
    }

    public function getSupportedFormats(): array
    {
        $formats = ['jpg', 'jpeg', 'png', 'gif'];

        if (function_exists('imagewebp')) {
            $formats[] = 'webp';
        }

        return $formats;
    }

    protected function createImageFromFile(string $path, int $imageType): \GdImage|false
    {
        return match ($imageType) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG  => imagecreatefrompng($path),
            IMAGETYPE_GIF  => imagecreatefromgif($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false,
            default        => false,
        };
    }

    protected function resize(
        \GdImage $source,
        int $originalWidth,
        int $originalHeight,
        int $targetWidth,
        int $targetHeight,
    ): \GdImage|false {
        $ratio = min($targetWidth / $originalWidth, $targetHeight / $originalHeight);
        $newWidth = (int) ($originalWidth * $ratio);
        $newHeight = (int) ($originalHeight * $ratio);

        $dest = imagecreatetruecolor($newWidth, $newHeight);
        if (! $dest) {
            return false;
        }

        $this->preserveTransparency($dest);

        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        return $dest;
    }

    protected function cropAndResize(
        \GdImage $source,
        int $originalWidth,
        int $originalHeight,
        int $targetWidth,
        int $targetHeight,
        bool|array $crop,
    ): \GdImage|false {
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

        $dest = imagecreatetruecolor($targetWidth, $targetHeight);
        if (! $dest) {
            return false;
        }

        $this->preserveTransparency($dest);

        imagecopyresampled($dest, $source, 0, 0, $x, $y, $targetWidth, $targetHeight, $newWidth, $newHeight);

        return $dest;
    }

    protected function preserveTransparency(\GdImage $image): void
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);
    }

    protected function saveImage(\GdImage $image, string $path, ?string $format, int $quality): bool
    {
        $extension = $format ?? mb_strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg' => imagejpeg($image, $path, $quality),
            'png'         => imagepng($image, $path, (int) (9 - ($quality / 10))),
            'gif'         => imagegif($image, $path),
            'webp'        => function_exists('imagewebp') ? imagewebp($image, $path, $quality) : false,
            default       => false,
        };
    }
}
