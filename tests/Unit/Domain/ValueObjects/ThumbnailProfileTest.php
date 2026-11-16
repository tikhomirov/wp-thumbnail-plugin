<?php

use KamaThumb\Domain\ValueObjects\ThumbnailProfile;

it('creates thumbnail profile with default values', function () {
    $profile = new ThumbnailProfile(300, 200);

    expect($profile->getWidth())->toBe(300)
        ->and($profile->getHeight())->toBe(200)
        ->and($profile->getCrop())->toBeTrue()
        ->and($profile->getQuality())->toBe(90)
        ->and($profile->getFormat())->toBeNull();
});

it('creates thumbnail profile with custom values', function () {
    $profile = new ThumbnailProfile(
        width: 400,
        height: 300,
        crop: false,
        quality: 85,
        format: 'webp',
    );

    expect($profile->getWidth())->toBe(400)
        ->and($profile->getHeight())->toBe(300)
        ->and($profile->getCrop())->toBeFalse()
        ->and($profile->getQuality())->toBe(85)
        ->and($profile->getFormat())->toBe('webp');
});

it('converts profile to array', function () {
    $profile = new ThumbnailProfile(300, 200, true, 90, 'jpg');
    $array = $profile->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKey('width')
        ->and($array)->toHaveKey('height')
        ->and($array)->toHaveKey('crop')
        ->and($array)->toHaveKey('quality')
        ->and($array)->toHaveKey('format')
        ->and($array['width'])->toBe(300)
        ->and($array['height'])->toBe(200);
});

it('creates profile from array', function () {
    $data = [
        'width'   => 500,
        'height'  => 400,
        'crop'    => false,
        'quality' => 80,
        'format'  => 'png',
    ];

    $profile = ThumbnailProfile::fromArray($data);

    expect($profile->getWidth())->toBe(500)
        ->and($profile->getHeight())->toBe(400)
        ->and($profile->getCrop())->toBeFalse()
        ->and($profile->getQuality())->toBe(80)
        ->and($profile->getFormat())->toBe('png');
});

it('creates profile from partial array with defaults', function () {
    $data = ['width' => 300, 'height' => 200];
    $profile = ThumbnailProfile::fromArray($data);

    expect($profile->getWidth())->toBe(300)
        ->and($profile->getHeight())->toBe(200)
        ->and($profile->getCrop())->toBeTrue()
        ->and($profile->getQuality())->toBe(90)
        ->and($profile->getFormat())->toBeNull();
});
