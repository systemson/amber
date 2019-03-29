<?php

define('INIT_TIME', microtime(true));


/**
 * Load the composer autoload.
 */
require __DIR__.'/../vendor/autoload.php';


/**
 * Load the application.
 */
$app = require APP_DIR . '/app/kernell.php';


/**
 * Get the request handler
 */
$handler = $app->get(Amber\Framework\Dispatch::class);



$handler->response() // Get the response.
->prepare($app->get(Symfony\Component\HttpFoundation\Request::class)) // Prepare the response.
->send(); // Send the response.

