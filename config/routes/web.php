<?php

$routes->get('/', 'App\Controllers\HomeController::index');

$routes->group(function ($routes) {
    $routes->get('/login', 'App\Controllers\Auth\AccessController::loginForm');
    $routes->post('/login', 'App\Controllers\Auth\AccessController::login');
}, [
    'middlewares' => [
        'Amber\Http\Server\Middleware\AuthenticatedMiddleware',
    ]
]);

$routes->post('/logout', 'App\Controllers\Auth\AccessController::logout');
