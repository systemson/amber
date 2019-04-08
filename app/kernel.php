<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Amber\Container\Container;
use Amber\Framework\Application;
use Amber\Framework\Container\ContainerFacade;
use Amber\Framework\Providers\ServiceProvider;

$app = new Container();
$app->bind(Container::class, $app);

ContainerFacade::setContainer($app);
ServiceProvider::setContainer($app);

return Application::getInstance();
