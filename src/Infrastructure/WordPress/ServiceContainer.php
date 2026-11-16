<?php

namespace KamaThumb\Infrastructure\WordPress;

use KamaThumb\Application\ThumbnailService;
use KamaThumb\Domain\Contracts\ImageProcessorInterface;
use KamaThumb\Domain\Contracts\StorageInterface;
use KamaThumb\Domain\Services\ThumbnailGenerator;
use KamaThumb\Infrastructure\ImageProcessors\GdProcessor;
use KamaThumb\Infrastructure\ImageProcessors\ImagickProcessor;
use KamaThumb\Infrastructure\Storage\FileSystemStorage;

final class ServiceContainer
{
    protected static ?self $instance = null;

    protected ?ImageProcessorInterface $imageProcessor = null;

    protected ?StorageInterface $storage = null;

    protected ?ThumbnailGenerator $generator = null;

    protected ?ThumbnailService $thumbnailService = null;

    protected function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function getImageProcessor(): ImageProcessorInterface
    {
        if ($this->imageProcessor === null) {
            $imagick = new ImagickProcessor;
            $gd = new GdProcessor;

            $this->imageProcessor = $imagick->isAvailable() ? $imagick : $gd;
        }

        return $this->imageProcessor;
    }

    public function getStorage(): StorageInterface
    {
        if ($this->storage === null) {
            $basePath = defined('WP_CONTENT_DIR')
                ? WP_CONTENT_DIR . '/cache/thumb'
                : '';

            $baseUrl = function_exists('content_url')
                ? content_url() . '/cache/thumb'
                : '';

            $this->storage = new FileSystemStorage($basePath, $baseUrl);
        }

        return $this->storage;
    }

    public function getThumbnailGenerator(): ThumbnailGenerator
    {
        if ($this->generator === null) {
            $this->generator = new ThumbnailGenerator(
                $this->getImageProcessor(),
                $this->getStorage(),
            );
        }

        return $this->generator;
    }

    public function getThumbnailService(): ThumbnailService
    {
        if ($this->thumbnailService === null) {
            $this->thumbnailService = new ThumbnailService(
                $this->getThumbnailGenerator(),
            );
        }

        return $this->thumbnailService;
    }
}
