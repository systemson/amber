<?php

use Amber\Framework\Container\Facades\Router;

Router::get('/', 'App\Controllers\HomeController::index');

Router::get('/login', 'App\Controllers\Auth\AuthController::loginForm');
Router::post('/login', 'App\Controllers\Auth\AuthController::login');
Router::get('/logout', 'App\Controllers\Auth\AuthController::logout');
