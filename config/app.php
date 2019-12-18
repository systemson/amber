<?php

return [

    'locale' => 'es',

    'fallback_locale' => 'en',

    'faker_locale' => 'es_ES',

    /*
     * Registers the dependency injection container services.
     *
     * Feel free to add as much services as you need since they are lazy loaded.
     */
    'binds' => [
        Amber\Auth\AuthClass::class,
        App\Models\UserProvider::class,
        Amber\Sketch\Template\Template::class,
    ],

    /*
     * Registers the service providers for the dependency injection container.
     *
     * The service providers are loaded in this order.
     */
    'providers' => [
        Amber\Container\Providers\DotenvServiceProvider::class,
        Amber\Container\Providers\ModelServiceProvider::class,
        Amber\Container\Providers\DataMapperServiceProvider::class,
        Amber\Container\Providers\CacheServiceProvider::class,
        Amber\Container\Providers\LoggerServiceProvider::class,
        Amber\Container\Providers\HttpServiceProvider::class,
        Amber\Container\Providers\FilesystemServiceProvider::class,
        Amber\Container\Providers\AmberSuiteServiceProvider::class,
        Amber\Container\Providers\ViewServiceProvider::class,
        Amber\Container\Providers\LocalizationServiceProvider::class,
    ],

    /*
     * The app default middlewares.
     *
     * This are the default and mandatory middlewares for every single server request.
     * If your middleware is route dependent add the middleware(s) as a route option.
     */
    'middlewares' => [
        Amber\Http\Server\Middleware\ErrorHandlerMiddleware::class,
        Amber\Http\Server\Middleware\InitTestsiddleware::class,
        Amber\Http\Server\Middleware\RequestMethodHandlerMiddleware::class,
        Amber\Http\Server\Middleware\ParseBodyContentMiddleware::class,
        Amber\Http\Server\Middleware\RouteHandlerMiddleware::class,
        Amber\Http\Server\Middleware\ClientIpHandlerMiddleware::class,
    ],

    /*
     * The command line interface commands registration.
     */
    'cli_commands' => [
        Amber\Commands\MigrateUpCommand::class,
        Amber\Commands\MigrateDownCommand::class,
        Amber\Commands\MigrateDropCommand::class,
        Amber\Commands\MigrateSeedsCommand::class,
        Amber\Commands\MigrateRestartCommand::class,
        Amber\Commands\AppCacheCommand::class,
        Amber\Commands\ServerCommand::class,
        Amber\Commands\MakeMigrationCommand::class,
        Amber\Commands\MakeKeyCommand::class,
    ],
];
