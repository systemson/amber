<?php

$routes->get('/', function () {
    $version = Amber\Container\Facades\Amber::version();

    return Amber\Container\Facades\Response::json([
        'message' => $version,
    ]);
});

$routes->group(function ($routes) {
    $routes->get('/', 'UsersController::list');
    //$routes->get('/create', 'UsersController::form');
    $routes->post('/', 'UsersController::create');
    $routes->get('/{id}', 'UsersController::read');
    //$routes->get('/{id}/edit', 'UsersController::form');
    $routes->update('/{id}', 'UsersController::update');
    $routes->delete('/{id}', 'UsersController::delete');
}, [
    'prefix' => '/users',
]);
