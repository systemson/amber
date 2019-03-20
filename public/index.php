<?php

define('INIT_TIME', microtime(true));

/**
 * Load the composer autoload.
 */
require __DIR__.'/../vendor/autoload.php';

use Amber\Framework\Application as App;
use Symfony\Component\HttpFoundation\Request;

/**
 * Get the request handler
 */
$handler = App::get(Amber\Framework\Dispatch::class);

/**
 * Get the response.
 */
$response = $handler->response();

/**
 * Send the response.
 */
$response->prepare(App::get(Request::class));
$response->send();
