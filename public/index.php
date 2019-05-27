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
 * Get the request handler.
 */
$handlerPSR = $app->get(Psr\Http\Message\RequestHandlerInterface::class);


/**
 * Get the request.
 */
$requestPSR = $app->get(Psr\Http\Message\ServerRequestInterface::class);


/**
 * Get the response.
 */
$responsePSR = $handlerPSR->handle($requestPSR);


/**
 * Send the response.
 */
$app->get(Amber\Framework\Http\Server\ResponseDispatcher::class)->send($responsePSR);


/*$app->get(Psr\Log\LoggerInterface::class)->info('Sistem report', [
    'Memory - ' . memory_get_peak_usage(true)/1000/1000,
    'Execution - ' . number_format(microtime(true) - INIT_TIME, 6),
    '_GET - ' . json_encode($request->query->all()),
    '_POST - ' . json_encode($request->request->all()),
]);*/
