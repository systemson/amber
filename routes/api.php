<?php

$routes->get('/', function () {
    return Amber\Container\Facades\Response::json([
        'message' => \Amber\Container\Facades\Amber::fullname(),
    ]);
});

$routes->apiResource('/users', 'UsersController');
$routes->apiResource('/roles', 'RolesController');
$routes->apiResource('/articles', 'ArticlesController');
$routes->apiResource('/categories', 'CategoriesController');
