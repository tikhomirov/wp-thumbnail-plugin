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
