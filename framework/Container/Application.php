<?php

namespace Amber\Container;

use Amber\Container\Container;
use Amber\Container\ContainerFacade;
use Amber\Container\ContainerAwareClass;
use Amber\Container\Facades\Router;
use Amber\Http\Server\ResponseDispatcher;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;

class Application extends ContainerFacade
{
    /**
     * @var string The class accessor.
     */
    protected static $accessor = ContainerInterface::class;

    /**
     * @var mixed The instance of the accessor.
     */
    protected static $instance;

    /**
     * To expose publicy a method it should be declared protected.
     *
     * @var array The method(s) that should be publicly exposed.
     */
    protected static $passthru = [
        //
    ];

    protected static $macros;

    /**
     * @var array The service providers.
     */
    private static $providers = [];

    private static $laps = [];

    private static function lap(string $name): void
    {
        if (env('APP_ENV') != 'dev') {
            return;
        }
        if (empty(self::$laps)) {
            self::$laps['Start'] = [
                'name' => 'Start',
                'total' => microtime(true),
                'time' => 0,
            ];
        }

        self::$laps[] = [
            'name' => $name,
            'total' => round($time = microtime(true) - self::$laps['Start']['time'], 6),
            'time' => round($time - end(self::$laps)['time'], 6),
        ];
    }

    /**
     * Prepares the application for running.
     */
    public static function boot(): void
    {
        $app = new Container();
        self::lap('Container instantiation');

        // Binds the container to itself
        $app->register(ContainerInterface::class)
            ->setInstance($app)
        ;
        self::lap('Container self registration');

        // Pass the container to the container aware class
        ContainerAwareClass::setContainer($app);
        self::lap('Container passed to ContainerAwareClass');

        ContainerFacade::setContainer($app);
        self::lap('Container passed to ContainerFacade');

        static::getInstance();
        self::lap('Application instantiation');

        Router::boot();
        self::lap('Router booting');
    }

    /**
     * Runs after the class constructor.
     *
     * @return void
     */
    public static function beforeConstruct(): void
    {
        self::bootProviders();
        self::lap('Providers booting');
    }

    /**
     * Runs after the class constructor.
     *
     * @return void
     */
    public static function afterConstruct(): void
    {
        // Bind from config file
        foreach ((array) config('app.binds') as $service) {
            static::bind($service);
        }
        self::lap('Providers binding');

        self::setUpProviders();
        self::lap('Providers setup');
    }

    public static function respond()
    {
        static::get(ResponseDispatcher::class)->send(
            static::get(RequestHandlerInterface::class)->handle(
                static::get(ServerRequestInterface::class)
            )
        );
        static::lap('Request handled');
    }

    /**
     * Shut downs the application after the response is dispatched.
     */
    public static function shutDown(): void
    {
        self::setDownProviders();
        self::lap('Providers set-down');
    }

    /**
     * Boots the service providers.
     */
    private static function bootProviders(): void
    {
        self::$providers = (array) config('app.providers');

        array_map(
            function ($service) {
                $service::boot();
            },
            self::$providers
        );
    }

    /**
     * Set up the service providers.
     */
    private static function setUpProviders(): void
    {
        array_map(
            function ($service) {
                static::make($service)->setUp();
            },
            self::$providers
        );
    }

    /**
     * Set down the service providers.
     */
    private static function setDownProviders(): void
    {
        array_map(
            function ($service) {
                static::make($service)->setDown();
            },
            self::$providers
        );
    }

    /**
     * Load the console commands.
     */
    private static function setUpCliCommands(): void
    {
        $console = new \Symfony\Component\Console\Application();

        foreach ((array) config('app.cli_commands') as $command) {
            $console->add(new $command);
        }

        $console->run();
    }


    /**
     * Prepares the application for CLI running.
     */
    public static function bootCli(): void
    {
        self::boot();
        self::setUpCliCommands();
    }
}
