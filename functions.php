<?php

/**
 * Legacy functions for backward compatibility
 * These functions wrap the new API to maintain compatibility with old code
 */

use KamaThumb\Infrastructure\WordPress\Settings;

add_filter('jpeg_quality', function () {
    return 100;
});

/**
 * Returns thumbnail URL only (legacy wrapper)
 *
 * @param  array  $args
 * @param  string|int  $src
 * @return string
 */
function kama_thumb_src($args = [], $src = 'notset')
{
    return thumb_src($args, $src);
}

/**
 * Returns thumbnail img tag (legacy wrapper)
 *
 * @param  array  $args
 * @param  string|int  $src
 * @return string
 */
function kama_thumb_img($args = [], $src = 'notset')
{
    return thumb_img($args, $src);
}

/**
 * Returns thumbnail with link to original (legacy wrapper)
 *
 * @param  array  $args
 * @param  string|int  $src
 * @return string
 */
function kama_thumb_a_img($args = [], $src = 'notset')
{
    return thumb_a_img($args, $src);
}

/**
 * Access to last instance properties (legacy compatibility)
 *
 * @param  string  $optname
 * @return mixed|null
 */
function kama_thumb($optname = '')
{
    static $lastArgs = [];

    if (empty($optname)) {
        return null;
    }

    if (isset($lastArgs[$optname])) {
        return $lastArgs[$optname];
    }

    return null;
}

/**
 * Get post thumbnail with custom attributes
 *
 * @param  array|null  $attr
 * @return string|null
 */
function get_post_thumbnail($attr = null)
{
    $_attr = wp_parse_args($attr, [
        'width'            => 480,
        'height'           => 340,
        'crop'             => true,
        'post_id'          => null,
        'show_placeholder' => true,
        'attach_id'        => null,
        'class'            => 'img-fluid rounded',
        'attr'             => '',
    ]);

    $post_id = $_attr['post_id'] ?: get_the_ID();
    $attach_id = $_attr['attach_id'];

    if (! $attach_id && $post_id) {
        $attach_id = get_post_thumbnail_id($post_id);
    }

    if (! $attach_id) {
        if (! $_attr['show_placeholder']) {
            return null;
        }

        $options = Settings::get();
        if (! empty($options['no_stub'])) {
            return null;
        }

        $placeholder = Settings::resolveNoPhotoUrl((string) ($options['no_photo_url'] ?? ''));
        if ($placeholder === '') {
            $placeholder = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
        }

        return sprintf(
            '<img src="%s" width="%d" height="%d" class="%s" alt="" role="presentation" %s />',
            esc_url($placeholder),
            (int) $_attr['width'],
            (int) $_attr['height'],
            esc_attr($_attr['class']),
            $_attr['attr']
        );
    }

    $args = Settings::mergeDefaults([
        'width'  => $_attr['width'],
        'height' => $_attr['height'],
        'crop'   => $_attr['crop'],
        'class'  => $_attr['class'],
        'attr'   => $_attr['attr'],
        'alt'    => get_the_title($post_id),
    ]);

    return thumb_img($args, $attach_id);
}
