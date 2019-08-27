<?php

return [

    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [

        'mysql' => [
            'driver'   => 'mysql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'amber'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', 'root'),
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'amber'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', 'postgres'),
        ],

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => realpath(path(env('DB_DATABASE', 'database/sqlite.db'))),
        ],

        'oracle' => [
            'driver'   => 'oracle',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '1521'),
            'database' => env('DB_DATABASE', 'amber'),
            'username' => env('DB_USERNAME', 'admin'),
            'password' => env('DB_PASSWORD', 'secret'),
        ],
    ]

];
