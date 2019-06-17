<?php

return [

    /*
     * Registers the dependency injection container services.
     *
     * Feel free to add as much services as you need since they are lazy loaded.
     */
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

    /*
     * Registers the service providers for the dependency injection container.
     *
     * The service providers are loaded in this order.
     */
    'providers' => [
        Amber\Framework\Container\Providers\DotenvServiceProvider::class,
        Amber\Framework\Container\Providers\ModelServiceProvider::class,
        Amber\Framework\Container\Providers\DataMapperServiceProvider::class,
        Amber\Framework\Container\Providers\CacheServiceProvider::class,
        Amber\Framework\Container\Providers\LoggerServiceProvider::class,
        Amber\Framework\Container\Providers\HttpServiceProvider::class,
        Amber\Framework\Container\Providers\FilesystemServiceProvider::class,
        Amber\Framework\Container\Providers\ViewServiceProvider::class,
        Amber\Framework\Container\Providers\AmberSuiteServiceProvider::class,
    ],

    /*
     * The app default middlewares.
     *
     * This are the default and mandatory middlewares for every server request.
     * If your middleware is route dependent add the middleware(s) as a route option(s.
     */
    'middlewares' => [
        Amber\Framework\Http\Server\Middleware\ErrorHandlerMiddleware::class,
        Amber\Framework\Http\Server\Middleware\InitTestsiddleware::class,
        Amber\Framework\Http\Server\Middleware\RouteHandlerMiddleware::class,
        Amber\Framework\Http\Server\Middleware\ClientIpHandlerMiddleware::class,
    ],

    /*
     * The command line interface commands registration.
     */
    'cli_commands' => [
        Amber\Framework\Commands\MigrateUpCommand::class,
        Amber\Framework\Commands\MigrateDownCommand::class,
        Amber\Framework\Commands\MigrateSeedsCommand::class,
        Amber\Framework\Commands\MigrateRestartCommand::class,
        Amber\Framework\Commands\AppCacheCommand::class,
        Amber\Framework\Commands\ServerCommand::class,
    ],
];
