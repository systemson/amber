<?php

use Amber\Framework\Route;

Route::get('/', 'App\Controllers\HomeController::index');
Route::get('api/users', 'App\Controllers\HomeController::users');
