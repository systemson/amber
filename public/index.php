<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define('INIT_TIME', microtime(true));
define('BASE_DIR', realpath(getcwd() . DIRECTORY_SEPARATOR . '../'));


/**
 * Load the composer autoload.
 */
require __DIR__.'/../vendor/autoload.php';

use Amber\Container\Application as App;


/**
 * Loads the application.
 */
App::boot();


/**
 * Sends the response.
 */
App::respond();


/**
 * Shuts down the application.
 */
App::shutDown();
