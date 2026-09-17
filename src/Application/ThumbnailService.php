<?php

namespace KamaThumb\Application;

use KamaThumb\Domain\Services\ThumbnailGenerator;
use KamaThumb\Domain\ValueObjects\ImageSource;
use KamaThumb\Domain\ValueObjects\ThumbnailProfile;
use KamaThumb\Infrastructure\WordPress\Settings;

final class ThumbnailService
{
    public function __construct(
        protected ThumbnailGenerator $generator
    ) {}

    /**
     * @param array{
     *     width?: int,
     *     height?: int,
     *     crop?: bool|array,
     *     quality?: int,
     *     format?: string|null,
     *     post_id?: int,
     *     attach_id?: int
     * } $args
     */
    public function generateThumbnail(string|int $source, array $args = []): ?string
    {
        $imageSource = $this->resolveImageSource($source, $args);
        if (! $imageSource) {
            return null;
        }

        $profile = ThumbnailProfile::fromArray(Settings::mergeDefaults($args));

        return $this->generator->generate($imageSource, $profile);
    }

    /**
     * @param  array{post_id?: int, attach_id?: int}  $args
     */
    protected function resolveImageSource(string|int $source, array $args): ?ImageSource
    {
        if (is_numeric($source)) {
            return $this->resolveFromAttachmentId((int) $source);
        }

        if (isset($args['attach_id'])) {
            return $this->resolveFromAttachmentId($args['attach_id']);
        }

        if (isset($args['post_id'])) {
            return $this->resolveFromPostId($args['post_id']);
        }

        return ImageSource::fromUrl($source);
    }

    protected function resolveFromAttachmentId(int $attachmentId): ?ImageSource
    {
        if (! function_exists('wp_get_attachment_url')) {
            return null;
        }

        $url = wp_get_attachment_url($attachmentId);
        if (! $url) {
            return null;
        }

        $path = get_attached_file($attachmentId);

        return ImageSource::fromAttachmentId($attachmentId, $url, $path ?: null);
    }

    protected function resolveFromPostId(int $postId): ?ImageSource
    {
        if (! function_exists('get_post_thumbnail_id')) {
            return null;
        }

        $attachmentId = get_post_thumbnail_id($postId);
        if (! $attachmentId) {
            return null;
        }

        return $this->resolveFromAttachmentId($attachmentId);
    }
}
