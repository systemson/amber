<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define('INIT_TIME', microtime(true));


/**
 * Load the composer autoload.
 */
require __DIR__.'/../vendor/autoload.php';

use Amber\Framework\Container\Application as App;

/**
 * Load the application.
 */
App::boot();


/**
 * Send the response.
 */
App::get(Amber\Framework\Http\Server\ResponseDispatcher::class)->send(
    App::get(Psr\Http\Server\RequestHandlerInterface::class)->handle(
        App::get(Psr\Http\Message\ServerRequestInterface::class
    )
));


/*$app->get(Psr\Log\LoggerInterface::class)->info('Sistem report', [
    'Memory - ' . memory_get_peak_usage(true)/1000/1000,
    'Execution - ' . number_format(microtime(true) - INIT_TIME, 6),
    '_GET - ' . json_encode($request->query->all()),
    '_POST - ' . json_encode($request->request->all()),
]);*/
