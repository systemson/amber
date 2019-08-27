<?php

return [

    'default' => [
        'path' => path('tmp', 'cache'),
        'driver' => Amber\Cache\Driver\SimpleCache::class,
    ],

    'session' => [
        'path' => path('tmp', 'framework', 'sessions'),
        'driver' => Amber\Cache\Driver\SimpleCache::class,
    ]

];
