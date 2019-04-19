<?php

use Amber\Framework\Container\Facades\Route;

$middlewares = [
    'Amber\Framework\Middleware\SessionMiddleware',
    'Amber\Framework\Middleware\CsfrMiddleware',
];

Route::get('/', 'App\Controllers\HomeController::index')->setDefault('_middlewares', $middlewares);

Route::get('/login', 'App\Controllers\Auth\AuthController::loginForm')->setDefault('_middlewares', $middlewares);
Route::post('/login', 'App\Controllers\Auth\AuthController::login');
Route::get('/logout', 'App\Controllers\Auth\AuthController::logout')->setDefault('_middlewares', $middlewares);
