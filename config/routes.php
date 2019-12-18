<?php

return [

    'default' => 'web',

    'routes' => [

        'web' => [
            'path' => path('routes', 'web.php'),
            'middlewares' => [
                Amber\Http\Server\Middleware\SessionMiddleware::class,
                Amber\Http\Server\Middleware\AuthMiddleware::class,
                Amber\Http\Server\Middleware\CsfrMiddleware::class,
            ],
            'prefix' => '',
            'namespace' => 'App\Http\Controllers',
        ],


        'api' => [
            'path' => path('routes', 'api.php'),
            'middlewares' => [
                Amber\Http\Server\Middleware\ThrottleRequestMiddleware::class,
                Amber\Http\Server\Middleware\CorsMiddleware::class,
            ],
            'prefix' => 'api',
            'namespace' => 'App\Http\Controllers\Api',
        ],

    ],
];
