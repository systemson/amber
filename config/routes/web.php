<?php

$routes->get('/', 'App\Controllers\HomeController::index');

$routes->group(function ($routes) {
    $routes->get('/login', 'App\Controllers\Auth\AuthController::loginForm');
    $routes->post('/login', 'App\Controllers\Auth\AuthController::login');
}, [
    'middlewares' => [
        'Amber\Framework\Http\Server\Middleware\AuthenticatedMiddleware',
    ]
]);

$routes->post('/logout', 'App\Controllers\Auth\AuthController::logout');
