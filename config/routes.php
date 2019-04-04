<?php

use Amber\Framework\Route;

Route::get('/', 'App\Controllers\HomeController::index');

Route::get('/login', 'App\Controllers\Auth\AuthController::loginForm');

Route::get('api/users', 'App\Controllers\HomeController::users');
