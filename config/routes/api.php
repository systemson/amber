<?php

$routes->get('/', function () {
    $version = Amber\Container\Facades\Amber::version();

    return Amber\Container\Facades\Response::json([
        'message' => $version,
    ]);
});

$routes->apiResource('/users', 'UsersController');
$routes->apiResource('/roles', 'RolesController');
$routes->apiResource('/articles', 'ArticlesController');
$routes->apiResource('/categories', 'CategoriesController');
