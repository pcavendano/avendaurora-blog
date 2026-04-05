<?php

// Clear OPcache if enabled
if (function_exists('opcache_reset')) {
    opcache_reset();
}

require __DIR__ . '/vendor/autoload.php';

$kirby = new Kirby([
    'roots' => [
        'index'    => __DIR__,
        'content'  => __DIR__ . '/content',
        'site'     => __DIR__ . '/site',
        'storage'  => $storage = __DIR__ . '/storage',
        'accounts' => $storage . '/accounts',
        'cache'    => $storage . '/cache',
        'logs'     => $storage . '/logs',
        'sessions' => $storage . '/sessions',
        'assets'   => __DIR__ . '/assets',
        'media'    => __DIR__ . '/media',
    ]
]);

echo $kirby->render();
