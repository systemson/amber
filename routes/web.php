<?php

$routes->get('/', 'HomeController::index');

$routes->group(function ($routes) {

    $routes->get('/login', 'Auth\AccessController::loginForm');
    $routes->post('/login', 'Auth\AccessController::login');
}, [
    'middlewares' => [
        'Amber\Http\Server\Middleware\AuthenticatedMiddleware',
    ]
]);

$routes->post('/logout', 'Auth\AccessController::logout');
