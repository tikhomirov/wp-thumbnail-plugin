<?php

use KamaThumb\Domain\Contracts\ImageProcessorInterface;
use KamaThumb\Domain\Contracts\StorageInterface;
use KamaThumb\Domain\Services\ThumbnailGenerator;
use KamaThumb\Domain\ValueObjects\ImageSource;
use KamaThumb\Domain\ValueObjects\ThumbnailProfile;

/**
 * 1x1 transparent PNG, enough for getimagesize() to detect the source type.
 */
function thumbnail_test_png(): string
{
    $base = tempnam(sys_get_temp_dir(), 'thumb-test-');
    $path = $base . '.png';
    file_put_contents($path, base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
    ));
    @unlink($base);

    return $path;
}

function thumbnail_test_processor(array $formats): ImageProcessorInterface
{
    return new class($formats) implements ImageProcessorInterface
    {
        public array $calls = [];

        public function __construct(protected array $formats) {}

        public function process(array $params): bool
        {
            $this->calls[] = $params;

            return true;
        }

        public function isAvailable(): bool
        {
            return true;
        }

        public function getSupportedFormats(): array
        {
            return $this->formats;
        }
    };
}

function thumbnail_test_storage(): StorageInterface
{
    return new class implements StorageInterface
    {
        public function exists(string $path): bool
        {
            return false;
        }

        public function put(string $path, string $contents): bool
        {
            return true;
        }

        public function get(string $path): string
        {
            return '';
        }

        public function delete(string $path): bool
        {
            return true;
        }

        public function url(string $path): string
        {
            return 'http://example.com/cache/' . $path;
        }

        public function path(string $relativePath): string
        {
            return sys_get_temp_dir() . '/' . $relativePath;
        }

        public function clearCache(): int
        {
            return 0;
        }
    };
}

it('falls back to png when the processor cannot write webp', function () {
    $processor = thumbnail_test_processor(['jpg', 'jpeg', 'png', 'gif']);
    $generator = new ThumbnailGenerator($processor, thumbnail_test_storage());
    $sourcePath = thumbnail_test_png();

    $url = $generator->generate(
        ImageSource::fromAttachmentId(1, 'http://example.com/image.png', $sourcePath),
        new ThumbnailProfile(100, 80, true, 90, 'webp'),
    );

    @unlink($sourcePath);

    expect($url)->toEndWith('.png')
        ->and($processor->calls[0]['format'])->toBe('png');
});

it('keeps webp when the processor supports it', function () {
    $processor = thumbnail_test_processor(['jpg', 'jpeg', 'png', 'gif', 'webp']);
    $generator = new ThumbnailGenerator($processor, thumbnail_test_storage());
    $sourcePath = thumbnail_test_png();

    $url = $generator->generate(
        ImageSource::fromAttachmentId(1, 'http://example.com/image.png', $sourcePath),
        new ThumbnailProfile(100, 80, true, 90, 'webp'),
    );

    @unlink($sourcePath);

    expect($url)->toEndWith('.webp')
        ->and($processor->calls[0]['format'])->toBe('webp');
});

it('does not rewrite a format the processor supports', function () {
    $processor = thumbnail_test_processor(['jpg', 'jpeg', 'png', 'gif']);
    $generator = new ThumbnailGenerator($processor, thumbnail_test_storage());
    $sourcePath = thumbnail_test_png();

    $url = $generator->generate(
        ImageSource::fromAttachmentId(1, 'http://example.com/image.png', $sourcePath),
        new ThumbnailProfile(100, 80, true, 90, 'jpg'),
    );

    @unlink($sourcePath);

    expect($url)->toEndWith('.jpg')
        ->and($processor->calls[0]['format'])->toBe('jpg');
});
