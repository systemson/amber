<?php

return [

    'default' => 'web',

    'routes' => [

        'web' => [
            'path' => CONFIG_DIR . '/routes/web.php',
            'middlewares' => [
                Amber\Framework\Http\Server\Middleware\SessionMiddleware::class,
                Amber\Framework\Http\Server\Middleware\AuthMiddleware::class,
                Amber\Framework\Http\Server\Middleware\CsfrMiddleware::class,
            ],
            'prefix' => '',
            'namespace' => 'App\Http\Controllers',
        ],


        'api' => [
            'path' => CONFIG_DIR . '/routes/api.php',
            'middlewares' => [
                Amber\Framework\Http\Server\Middleware\ThrottleRequestMiddleware::class,
            ],
            'prefix' => 'api',
            'namespace' => 'App\Http\Controllers\Api',
        ],

    ],
];
