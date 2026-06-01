<?php
/**
 * Simple PSR-4 Autoloader
 * Namespace: UrbanTaxi\BookSeatWidget\
 */

if (!defined('ABSPATH')) {
    exit;
}

spl_autoload_register(function ($class) {

    $prefix = 'UrbanTaxi\\BookSeatWidget\\';

    $base_dir = dirname(__DIR__) . '/app/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace namespace separators with directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Load file if exists
    if (file_exists($file)) {
        require_once $file;
    }
});

