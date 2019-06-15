<?php

return [

    'default' => 'web',

    'routes' => [

        'web' => [
            'path' => CONFIG_DIR . '/routes/web.php',
            'middlewares' => [
                'Amber\Framework\Http\Server\Middleware\SessionMiddleware',
                'Amber\Framework\Http\Server\Middleware\AuthMiddleware',
                'Amber\Framework\Http\Server\Middleware\CsfrMiddleware',
            ],
            'prefix' => '',
            'namespace' => 'App\Http\Controllers',
        ],


        'api' => [
            'path' => CONFIG_DIR . '/routes/api.php',
            'middlewares' => [
                //'Amber\Framework\Http\Server\Middleware\ApiTokenMiddleware',
            ],
            'prefix' => 'api',
            'namespace' => 'App\Http\Controllers\Api',
        ],

    ],
];
