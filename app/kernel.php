<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use Amber\Container\Container;
use Amber\Framework\Container\Application;
use Amber\Framework\Container\ContainerFacade;
use Amber\Framework\Container\ContainerAwareClass;
use Amber\Framework\Container\Facades\Router;

$array['Start'] = microtime(true) - INIT_TIME;

$app = new Container();
$array['Container instantiation'] = microtime(true) - INIT_TIME;

$app->register(Container::class)
->setInstance($app);
$array['Container self-registration'] = microtime(true) - INIT_TIME;

ContainerAwareClass::setContainer($app);
$array['ContainerAwareClass setContainer'] = microtime(true) - INIT_TIME;

ContainerFacade::setContainer($app);
$array['ContainerFacade setContainer'] = microtime(true) - INIT_TIME;

$app = Application::getInstance();
$array['Application init'] = microtime(true) - INIT_TIME;

Router::boot();
$array['Routes boot'] = microtime(true) - INIT_TIME;

//dd($array);
spl_autoload_register(function () {

    include getcwd() . '/../vendor/amber/assets/src/Provider.php';
});
return $app;
