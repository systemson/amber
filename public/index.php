<?php

define('INIT_TIME', microtime(true));

use Amber\Container\Injector;
use Amber\Container\Invoker;
use Amber\Route\Route;
use Amber\Common\Utils\Implementatios\AbstractWraper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

use Symfony\Component\HttpFoundation\Response;

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../config/routes.php';

$context = new RequestContext();
$context->fromRequest($request = Request::createFromGlobals());
$routes = Route::getInstance();

$matcher = new UrlMatcher($routes, $context);

$url = $request->getRequestUri();

$response = $matcher->match($url);

$app = new Injector();

$app->bind(Request::class, Request::createFromGlobals());
$app->bind($response['_controller']);

$controller = $app->get($response['_controller']);
$return = $controller->{$response['_action']}();

$response = new Response(
    'Content',
    Response::HTTP_OK,
    ['content-type' => 'text/html']
);
$response->setContent($return);
$response->prepare($request);

$response->send();
