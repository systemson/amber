<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use Amber\Container\Container;
use Amber\Framework\Container\Application;
use Amber\Framework\Container\ContainerFacade;
use Amber\Framework\Container\ContainerAwareClass;
use Amber\Framework\Providers\ServiceProvider;

$app = new Container();
$app->bind(Container::class, $app);

ContainerAwareClass::setContainer($app);
ContainerFacade::setContainer($app);
ServiceProvider::setContainer($app);

Application::boot();

return Application::getInstance();
