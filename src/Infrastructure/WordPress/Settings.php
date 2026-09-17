<?php

namespace KamaThumb\Infrastructure\WordPress;

final class Settings
{
    public const OPTION = 'kama_thumbnail';

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'no_photo_url'    => '',
            'quality'         => 90,
            'webp'            => false,
            'no_stub'         => false,
            'allow_hosts'     => '',
            'rise_small'      => true,
            'auto_clear'      => false,
            'auto_clear_days' => 7,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function get(): array
    {
        $options = get_option(self::OPTION, []);

        if (! is_array($options)) {
            $options = [];
        }

        return array_merge(self::defaults(), $options);
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public static function save(array $input): void
    {
        $defaults = self::defaults();
        $noPhoto = $input['no_photo_url'] ?? '';

        if (is_numeric($noPhoto)) {
            $noPhoto = (string) (int) $noPhoto;
        } else {
            $noPhoto = esc_url_raw(trim((string) $noPhoto));
        }

        $options = [
            'no_photo_url'    => $noPhoto,
            'quality'         => max(0, min(100, (int) ($input['quality'] ?? $defaults['quality']))),
            'webp'            => ! empty($input['webp']),
            'no_stub'         => ! empty($input['no_stub']),
            'allow_hosts'     => sanitize_text_field((string) ($input['allow_hosts'] ?? '')),
            'rise_small'      => ! empty($input['rise_small']),
            'auto_clear'      => ! empty($input['auto_clear']),
            'auto_clear_days' => max(1, (int) ($input['auto_clear_days'] ?? $defaults['auto_clear_days'])),
        ];

        update_option(self::OPTION, $options);
    }

    public static function resolveNoPhotoUrl(?string $value = null): string
    {
        if ($value === null) {
            $value = (string) (self::get()['no_photo_url'] ?? '');
        }

        if ($value === '') {
            return '';
        }

        if (is_numeric($value) && function_exists('wp_get_attachment_url')) {
            $url = wp_get_attachment_url((int) $value);

            return $url ?: '';
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, mixed>
     */
    public static function mergeDefaults(array $args): array
    {
        $options = self::get();

        if (! isset($args['quality'])) {
            $args['quality'] = (int) $options['quality'];
        }

        if (! isset($args['format']) && ! empty($options['webp'])) {
            $args['format'] = 'webp';
        }

        return $args;
    }
}
