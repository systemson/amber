<?php

define('INIT_TIME', microtime(true));


/**
 * Load the composer autoload.
 */
require __DIR__.'/../vendor/autoload.php';


/**
 * Load the application.
 */
$app = require APP_DIR . '/app/kernel.php';


/**
 * Load the routes.
 */
require CONFIG_DIR . '/routes.php';

/**
 * Get the request handler.
 */
$handler = $app->get(Amber\Framework\Dispatch\Dispatch::class);

/**
 * Get the request.
 */
$request = $app->get(Symfony\Component\HttpFoundation\Request::class);


/**
 * Get and send the resposne.
 */
$handler->response()
->prepare($request)
->send();


$app->get(\Psr\Log\LoggerInterface::class)->info('Sistem report', [
    'Memory - ' . memory_get_peak_usage(true)/1000/1000,
    'Execution - ' . number_format(microtime(true) - INIT_TIME, 6),
]);
