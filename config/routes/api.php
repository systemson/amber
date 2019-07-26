<?php

$routes->get('/api', function () {
    $version = Amber\Container\Facades\Amber::version();

    return Amber\Container\Facades\Response::json([
        'message' => $version,
    ]);
});

$routes->get('/api/users', 'App\Controllers\Api\UsersController::list');
$routes->post('/api/users', 'App\Controllers\Api\UsersController::create');
$routes->get('/api/users/{id}', 'App\Controllers\Api\UsersController::read');
$routes->update('/api/users/{id}', 'App\Controllers\Api\UsersController::update');
$routes->delete('/api/users/{id}', 'App\Controllers\Api\UsersController::delete');
