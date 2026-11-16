<?php

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

require_once __DIR__ . '/autoload.php';

use KamaThumb\Infrastructure\WordPress\ServiceContainer;

$container = ServiceContainer::getInstance();
$storage = $container->getStorage();

$deletedCount = $storage->clearCache();

global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'kama_thumb_%'");

delete_option('kama_thumbnail');
delete_option('kama_thumb_version');

$cacheDir = $storage->path('');
if (is_dir($cacheDir)) {
    @rmdir($cacheDir);
}
