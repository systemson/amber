<?php

use Amber\Framework\Container\Facades\Route;

Route::get('/', 'App\Controllers\HomeController::index');

Route::get('/login', 'App\Controllers\Auth\AuthController::loginForm')->setDefault('_middlewares', [
    'Amber\Framework\Middleware\SessionMiddleware',
    'Amber\Framework\Middleware\CsfrMiddleware',
]);
Route::post('/login', 'App\Controllers\Auth\AuthController::login');
Route::get('/logout', 'App\Controllers\Auth\AuthController::logout');

Route::get('api/users', 'App\Controllers\HomeController::users');
