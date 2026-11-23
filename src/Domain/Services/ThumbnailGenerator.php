<?php

namespace KamaThumb\Domain\Services;

use KamaThumb\Domain\Contracts\ImageProcessorInterface;
use KamaThumb\Domain\Contracts\StorageInterface;
use KamaThumb\Domain\ValueObjects\ImageSource;
use KamaThumb\Domain\ValueObjects\ThumbnailProfile;

final class ThumbnailGenerator
{
    public function __construct(
        protected ImageProcessorInterface $processor,
        protected StorageInterface $storage,
    ) {}

    public function generate(ImageSource $source, ThumbnailProfile $profile): ?string
    {
        $profile = $this->withSupportedFormat($source, $profile);

        $cacheKey = $this->generateCacheKey($source, $profile);
        $cachePath = $this->storage->path($cacheKey);

        if ($this->storage->exists($cacheKey)) {
            return $this->storage->url($cacheKey);
        }

        if (! $source->isLocal()) {
            return null;
        }

        $sourcePath = $source->getPath();
        if (! $sourcePath || ! file_exists($sourcePath)) {
            return null;
        }

        $success = $this->processor->process([
            'source_path'      => $sourcePath,
            'destination_path' => $cachePath,
            'width'            => $profile->getWidth(),
            'height'           => $profile->getHeight(),
            'crop'             => $profile->getCrop(),
            'quality'          => $profile->getQuality(),
            'format'           => $profile->getFormat(),
        ]);

        if (! $success) {
            return null;
        }

        return $this->storage->url($cacheKey);
    }

    /**
     * Falls back to a format the processor can actually write.
     *
     * A requested webp on a GD build without libwebp would otherwise make every
     * cache miss fail and return null, which callers render as an empty thumbnail.
     */
    protected function withSupportedFormat(ImageSource $source, ThumbnailProfile $profile): ThumbnailProfile
    {
        $format = $profile->getFormat();
        if ($format === null) {
            return $profile;
        }

        $supported = array_map('strtolower', $this->processor->getSupportedFormats());
        if (in_array(strtolower($format), $supported, true)) {
            return $profile;
        }

        $fallback = $this->resolveFallbackFormat($source, $supported);
        if ($fallback === null) {
            return $profile;
        }

        return new ThumbnailProfile(
            $profile->getWidth(),
            $profile->getHeight(),
            $profile->getCrop(),
            $profile->getQuality(),
            $fallback,
        );
    }

    /**
     * @param string[] $supported
     */
    protected function resolveFallbackFormat(ImageSource $source, array $supported): ?string
    {
        $imageType = null;
        $sourcePath = $source->getPath();
        if ($sourcePath && file_exists($sourcePath) && function_exists('getimagesize')) {
            $info = getimagesize($sourcePath);
            $imageType = $info[2] ?? null;
        }

        $preferred = ['png', 'jpg', 'jpeg', 'gif'];
        if ($imageType === IMAGETYPE_JPEG) {
            $preferred = ['jpg', 'jpeg', 'png', 'gif'];
        } elseif ($imageType === IMAGETYPE_GIF) {
            $preferred = ['gif', 'png', 'jpg', 'jpeg'];
        }

        foreach ($preferred as $candidate) {
            if (in_array($candidate, $supported, true)) {
                return $candidate;
            }
        }

        return null;
    }

    protected function generateCacheKey(ImageSource $source, ThumbnailProfile $profile): string
    {
        $parts = [
            md5($source->getUrl()),
            $profile->getWidth(),
            $profile->getHeight(),
            is_array($profile->getCrop()) ? implode('-', $profile->getCrop()) : (int) $profile->getCrop(),
            $profile->getQuality(),
        ];

        $filename = implode('_', $parts);
        $extension = $profile->getFormat() ?? pathinfo($source->getUrl(), PATHINFO_EXTENSION);

        return $filename . '.' . $extension;
    }
}
