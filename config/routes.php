<?php

use Amber\Route\Route;

Route::get('/', 'App\Controllers\HomeController::index');
Route::get('/home', 'App\Controllers\HomeController::index');
