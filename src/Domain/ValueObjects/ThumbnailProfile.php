<?php

namespace KamaThumb\Domain\ValueObjects;

final class ThumbnailProfile
{
    public function __construct(
        protected int $width,
        protected int $height,
        protected bool|array $crop = true,
        protected int $quality = 90,
        protected ?string $format = null,
    ) {}

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getCrop(): bool|array
    {
        return $this->crop;
    }

    public function getQuality(): int
    {
        return $this->quality;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @return array{width: int, height: int, crop: bool|array, quality: int, format: string|null}
     */
    public function toArray(): array
    {
        return [
            'width'   => $this->width,
            'height'  => $this->height,
            'crop'    => $this->crop,
            'quality' => $this->quality,
            'format'  => $this->format,
        ];
    }

    /**
     * @param array{width?: int, height?: int, crop?: bool|array, quality?: int, format?: string|null} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['width'] ?? 0,
            $data['height'] ?? 0,
            $data['crop'] ?? true,
            $data['quality'] ?? 90,
            $data['format'] ?? null,
        );
    }
}
