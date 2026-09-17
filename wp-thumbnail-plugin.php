<?php

/**
 * Plugin Name:     Thumbnails
 * Plugin URL:      https://rwsite.ru
 * Description:     Modern thumbnail generation plugin with WebP support, PSR-4 architecture, and comprehensive API. Automatically generates and caches thumbnails from featured images, post content, or attachments. Supports both legacy (<code>kama_thumb_*</code>) and modern (<code>thumb_*</code>) API functions with flexible configuration.
 * Version:         1.0.2
 * Text Domain:     thumbnail
 * Domain Path:     /languages
 * Author:          Aleksey Tikhomirov <alex@rwsite.ru>
 * Author URI:      https://rwsite.ru
 *
 * Tags:            thumbnail, webp, image, resize, cache
 * Requires at least: 5.6
 * Tested up to:     6.9
 * Requires PHP:     7.4
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

define('KT_MAIN_FILE', __FILE__);
define('KT_PATH', wp_normalize_path(dirname(__FILE__).'/'));

$is_plugin = false;
if (defined('WP_PLUGIN_DIR') && strpos(KT_PATH, wp_normalize_path(WP_PLUGIN_DIR))) {
    $is_plugin = true;
} elseif (defined('WPMU_PLUGIN_DIR') && strpos(KT_PATH, wp_normalize_path(WPMU_PLUGIN_DIR))) {
    $is_plugin = true;
}

if ($is_plugin) {
    define('KT_URL', plugin_dir_url(__FILE__));
} else {
    define('KT_URL', strtr(KT_PATH, [wp_normalize_path(get_template_directory()) => get_template_directory_uri()]));
}

require_once __DIR__.'/autoload.php';

add_action('init', function () {
    if (! defined('DOING_AJAX')) {
        load_plugin_textdomain('thumbnail', false, basename(KT_PATH).'/languages');
    }

    \KamaThumb\Infrastructure\WordPress\Plugin::getInstance();

    if (is_admin() && ! wp_doing_ajax()) {
        require_once __DIR__.'/upgrade.php';
        kama_thumb_upgrade();
    }
});

require_once 'functions.php';
