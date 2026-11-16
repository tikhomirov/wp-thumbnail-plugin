<?php

namespace KamaThumb\Domain\ValueObjects;

final class ImageSource
{
    public function __construct(
        protected string $url,
        protected ?string $path = null,
        protected ?int $attachmentId = null,
    ) {}

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getAttachmentId(): ?int
    {
        return $this->attachmentId;
    }

    public function isLocal(): bool
    {
        return $this->path !== null;
    }

    public function isExternal(): bool
    {
        return $this->path === null;
    }

    public static function fromUrl(string $url): self
    {
        return new self($url);
    }

    public static function fromAttachmentId(int $attachmentId, string $url, ?string $path = null): self
    {
        return new self($url, $path, $attachmentId);
    }
}
