<?php

use KamaThumb\Domain\ValueObjects\ImageSource;

it('creates image source from url', function () {
    $source = ImageSource::fromUrl('https://example.com/image.jpg');

    expect($source->getUrl())->toBe('https://example.com/image.jpg')
        ->and($source->getPath())->toBeNull()
        ->and($source->getAttachmentId())->toBeNull()
        ->and($source->isExternal())->toBeTrue()
        ->and($source->isLocal())->toBeFalse();
});

it('creates image source from attachment id', function () {
    $source = ImageSource::fromAttachmentId(
        123,
        'https://example.com/image.jpg',
        '/var/www/uploads/image.jpg',
    );

    expect($source->getUrl())->toBe('https://example.com/image.jpg')
        ->and($source->getPath())->toBe('/var/www/uploads/image.jpg')
        ->and($source->getAttachmentId())->toBe(123)
        ->and($source->isLocal())->toBeTrue()
        ->and($source->isExternal())->toBeFalse();
});

it('detects local source correctly', function () {
    $local = new ImageSource('https://example.com/image.jpg', '/path/to/image.jpg');
    $external = new ImageSource('https://example.com/image.jpg');

    expect($local->isLocal())->toBeTrue()
        ->and($external->isLocal())->toBeFalse();
});

it('detects external source correctly', function () {
    $local = new ImageSource('https://example.com/image.jpg', '/path/to/image.jpg');
    $external = new ImageSource('https://example.com/image.jpg');

    expect($local->isExternal())->toBeFalse()
        ->and($external->isExternal())->toBeTrue();
});
