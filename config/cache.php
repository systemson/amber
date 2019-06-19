<?php

return [

    'default' => [
        'path' => APP_DIR . 'tmp/cache',
        'driver' => Amber\Cache\Driver\SimpleCache::class,
    ],

    'session' => [
        'path' => APP_DIR . 'tmp/framework/sessions',
        'driver' => Amber\Cache\Driver\SimpleCache::class,
    ]

];
