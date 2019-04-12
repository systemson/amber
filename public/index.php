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
$handlerPSR = $app->get(Psr\Http\Message\RequestHandlerInterface::class);
$handler = $app->get(Amber\Framework\Dispatch\Dispatch::class);


/**
 * Get the request.
 */
$requestPSR = $app->get(Psr\Http\Message\ServerRequestInterface::class);
$request = $app->get(Symfony\Component\HttpFoundation\Request::class);


/**
 * Get the response.
 */
$responsePSR = $handlerPSR->handle($requestPSR);
$response = $handler->response();


/**
 * Send the response.
 */
dump(Amber\Framework\Container\Facades\Session::all());
//$responsePSR->send();
$response->prepare($request)
->send();


$app->get(\Psr\Log\LoggerInterface::class)->info('Sistem report', [
    'Memory - ' . memory_get_peak_usage(true)/1000/1000,
    'Execution - ' . number_format(microtime(true) - INIT_TIME, 6),
]);
