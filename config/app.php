<?php

return [

    // App binds
    'binds' => [
        Amber\Framework\Auth\AuthClass::class,
        Amber\Framework\Auth\UserProvider::class,
        Amber\Sketch\Template\Template::class,
        Amber\Framework\Http\Server\Middleware\InitTestsiddleware::class,
        Amber\Framework\Http\Server\Middleware\SessionMiddleware::class,
        Amber\Framework\Http\Server\Middleware\RouteHandlerMiddleware::class,
        Amber\Framework\Http\Server\Middleware\AuthMiddleware::class,
        Amber\Framework\Http\Server\Middleware\CsfrMiddleware::class,
        Amber\Framework\Http\Server\Middleware\ControllerHandlerMiddleware::class,
        Amber\Framework\Http\Server\Middleware\ClosureHandlerMiddleware::class,
        Amber\Framework\Http\Server\Middleware\AuthenticatedMiddleware::class,
        Amber\Framework\Http\Server\Middleware\ErrorHandlerMiddleware::class,
        Amber\Framework\Http\Server\Middleware\ApiTokenMiddleware::class,
        Amber\Framework\Http\Server\Middleware\ClientIpHandlerMiddleware::class,
        Amber\Framework\Http\Server\Middleware\ThrottleRequestMiddleware::class,
    ],

    'providers' => [
        Amber\Framework\Container\Providers\DotenvServiceProvider::class,
        Amber\Framework\Container\Providers\ModelServiceProvider::class,
        Amber\Framework\Container\Providers\CacheServiceProvider::class,
        Amber\Framework\Container\Providers\HttpServiceProvider::class,
        Amber\Framework\Container\Providers\LoggerServiceProvider::class,
        Amber\Framework\Container\Providers\FilesystemServiceProvider::class,
        Amber\Framework\Container\Providers\ViewServiceProvider::class,
        Amber\Framework\Container\Providers\DataMapperServiceProvider::class,
        Amber\Framework\Container\Providers\AmberSuiteServiceProvider::class,
    ],

    'middlewares' => [
        Amber\Framework\Http\Server\Middleware\ErrorHandlerMiddleware::class,
        Amber\Framework\Http\Server\Middleware\InitTestsiddleware::class,
        Amber\Framework\Http\Server\Middleware\RouteHandlerMiddleware::class,
        Amber\Framework\Http\Server\Middleware\ClientIpHandlerMiddleware::class,
    ],

    'cli_commands' => [
        Amber\Framework\Commands\MigrateUpCommand::class,
        Amber\Framework\Commands\MigrateDownCommand::class,
        Amber\Framework\Commands\MigrateSeedsCommand::class,
        Amber\Framework\Commands\MigrateRestartCommand::class,
        Amber\Framework\Commands\AppCacheCommand::class,
        Amber\Framework\Commands\ServerCommand::class,
    ],
];
