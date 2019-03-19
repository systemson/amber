<?php

define('INIT_TIME', microtime(true));

use Amber\Framework\Application;

/**
 * Load the composer autoload.
 */
require __DIR__.'/../vendor/autoload.php';

/**
 * Load the declared routes.
 */
require __DIR__.'/../config/routes.php';

/**
 * Get the request handler
 */
$handler = Application::get('_dispatch');

/**
 * Get the response.
 */
$response = $handler->response();

/**
 * Send the response.
 */
$response->send();

