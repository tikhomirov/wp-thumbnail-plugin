<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

define('ABSPATH', dirname(__DIR__, 4) . '/');
define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
define('WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins');

if (! function_exists('wp_normalize_path')) {
    function wp_normalize_path($path)
    {
        return str_replace('\\', '/', $path);
    }
}

if (! function_exists('plugin_dir_url')) {
    function plugin_dir_url($file)
    {
        return 'http://example.com/wp-content/plugins/wp-thumbnail-plugin/';
    }
}

if (! function_exists('plugin_dir_path')) {
    function plugin_dir_path($file)
    {
        return dirname(__DIR__) . '/';
    }
}

if (! function_exists('get_template_directory')) {
    function get_template_directory()
    {
        return ABSPATH . 'wp-content/themes/default';
    }
}

if (! function_exists('get_template_directory_uri')) {
    function get_template_directory_uri()
    {
        return 'http://example.com/wp-content/themes/default';
    }
}

if (! function_exists('content_url')) {
    function content_url()
    {
        return 'http://example.com/wp-content';
    }
}

if (! function_exists('get_option')) {
    function get_option($option, $default = false)
    {
        return $default;
    }
}

if (! function_exists('get_site_option')) {
    function get_site_option($option, $default = false)
    {
        return $default;
    }
}

if (! function_exists('is_multisite')) {
    function is_multisite()
    {
        return false;
    }
}

if (! function_exists('is_admin')) {
    function is_admin()
    {
        return false;
    }
}

if (! function_exists('wp_doing_ajax')) {
    function wp_doing_ajax()
    {
        return false;
    }
}

if (! function_exists('add_action')) {
    function add_action($hook, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        return true;
    }
}

if (! function_exists('add_filter')) {
    function add_filter($hook, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        return true;
    }
}

if (! function_exists('do_action')) {
    function do_action($hook, ...$args)
    {
        return null;
    }
}

if (! function_exists('apply_filters')) {
    function apply_filters($hook, $value, ...$args)
    {
        return $value;
    }
}

if (! function_exists('has_filter')) {
    function has_filter($hook, $function_to_check = false)
    {
        return false;
    }
}

if (! function_exists('load_plugin_textdomain')) {
    function load_plugin_textdomain($domain, $deprecated = false, $plugin_rel_path = false)
    {
        return true;
    }
}

if (! function_exists('__')) {
    function __($text, $domain = 'default')
    {
        return $text;
    }
}

if (! function_exists('esc_url')) {
    function esc_url($url)
    {
        return $url;
    }
}

if (! function_exists('esc_attr')) {
    function esc_attr($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (! function_exists('sanitize_key')) {
    function sanitize_key($key)
    {
        return mb_strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', $key));
    }
}

if (! function_exists('wp_get_attachment_url')) {
    function wp_get_attachment_url($attachment_id)
    {
        return 'http://example.com/wp-content/uploads/image.jpg';
    }
}

if (! function_exists('get_post_meta')) {
    function get_post_meta($post_id, $meta_key = '', $single = false)
    {
        return $single ? '' : [];
    }
}

if (! function_exists('update_post_meta')) {
    function update_post_meta($post_id, $meta_key, $meta_value, $prev_value = '')
    {
        return true;
    }
}

if (! function_exists('get_children')) {
    function get_children($args = [], $output = 'objects')
    {
        return [];
    }
}

if (! function_exists('get_the_ID')) {
    function get_the_ID()
    {
        return 0;
    }
}

if (! function_exists('wp_parse_args')) {
    function wp_parse_args($args, $defaults = '')
    {
        if (is_object($args)) {
            $r = get_object_vars($args);
        } elseif (is_array($args)) {
            $r = &$args;
        } else {
            return $defaults;
        }

        if (is_array($defaults)) {
            return array_merge($defaults, $r);
        }

        return $r;
    }
}

if (! function_exists('untrailingslashit')) {
    function untrailingslashit($string)
    {
        return mb_rtrim($string, '/\\');
    }
}

uses(TestCase::class)->in('Unit', 'Integration');
