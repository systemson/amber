<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use Amber\Container\Container;
use Amber\Framework\Container\Application;
use Amber\Framework\Container\ContainerFacade;
use Amber\Framework\Container\ContainerAwareClass;
use Amber\Framework\Container\Facades\Route;

$app = new Container();
$app->register(Container::class)
->setInstance($app);

Application::boot();

ContainerAwareClass::setContainer($app);
ContainerFacade::setContainer($app);

$app = Application::getInstance();
Route::boot();

return $app;