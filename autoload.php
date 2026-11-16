<?php

/**
 * PSR-4 Autoloader for production
 * Loads only KamaThumb namespace classes without dev dependencies
 */
spl_autoload_register(function ($class) {
    $prefix = 'KamaThumb\\';
    $base_dir = __DIR__ . '/src/';

    $len = mb_strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = mb_substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
