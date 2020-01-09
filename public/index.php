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

use Amber\Container\Bootstrap;


$app = new Bootstrap();
$app->boot();
$app->respond();
$app->shutDown();

die();
