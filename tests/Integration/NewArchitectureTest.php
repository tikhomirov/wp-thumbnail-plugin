<?php

use KamaThumb\Infrastructure\WordPress\ServiceContainer;

it('initializes service container', function () {
    $container = ServiceContainer::getInstance();
    expect($container)->toBeInstanceOf(ServiceContainer::class);
});

it('provides image processor', function () {
    $container = ServiceContainer::getInstance();
    $processor = $container->getImageProcessor();

    expect($processor)->toBeInstanceOf(KamaThumb\Domain\Contracts\ImageProcessorInterface::class)
        ->and($processor->isAvailable())->toBeTrue();
});

it('provides storage', function () {
    $container = ServiceContainer::getInstance();
    $storage = $container->getStorage();

    expect($storage)->toBeInstanceOf(KamaThumb\Domain\Contracts\StorageInterface::class);
});

it('provides thumbnail generator', function () {
    $container = ServiceContainer::getInstance();
    $generator = $container->getThumbnailGenerator();

    expect($generator)->toBeInstanceOf(KamaThumb\Domain\Services\ThumbnailGenerator::class);
});

it('provides thumbnail service', function () {
    $container = ServiceContainer::getInstance();
    $service = $container->getThumbnailService();

    expect($service)->toBeInstanceOf(KamaThumb\Application\ThumbnailService::class);
});

it('registers new template functions', function () {
    KamaThumb\Infrastructure\WordPress\TemplateFunctions::register();

    expect(function_exists('thumb_src'))->toBeTrue()
        ->and(function_exists('thumb_img'))->toBeTrue()
        ->and(function_exists('thumb_a_img'))->toBeTrue();
});
