<?php

namespace Amber\Framework;

use Amber\Framework\Container\ContainerFacade;
use Amber\Container\Container;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Database\Capsule\Manager as Eloquent;

class Application extends ContainerFacade
{
    /**
     * @var string The class accessor.
     */
    protected static $accessor = Container::class;

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
        'bind',
        'get',
        'has',
        'unbind',
        'bindMultiple',
        'getMultiple',
        'hasMultiple',
        'unbindMultiple',
        'register',
        'make',
    ];

    private static $providers = [];

    /**
     * Runs after the class constructor.
     *
     * @return void
     */
    public static function beforeConstruct(): void
    {
        $whoops = new \Whoops\Run();
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        $whoops->register();

        $dotenv = \Dotenv\Dotenv::create(APP_DIR);
        $dotenv->load();
    }

    /**
     * Runs after the class constructor.
     *
     * @return void
     */
    public static function afterConstruct(): void
    {
        // Bind from config file
        foreach (config('app')->binds as $service) {
            // TODO should validate if the class exists
            static::bind($service);
        }

        foreach (config('app')->providers as $provider) {
            // TODO should validate if the class exists
            self::$providers[] = $provider;
            static::bind($provider);
        }

        self::setUpProviders();

        static::bind(
        	\Symfony\Component\Routing\RouteCollection::class,
        	new \Symfony\Component\Routing\RouteCollection()
        );



        static::bind(\Symfony\Component\HttpFoundation\Request::class, function () {
            return \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        });


        static::register(\Symfony\Component\Routing\RequestContext::class)
        ->afterConstruct('fromRequest', function () {
            return static::get(Request::class);
        });


        static::register(\Monolog\Logger::class, \Psr\Log\LoggerInterface::class)
        ->setArgument('name', 'AmberFramework')
        ->afterConstruct('pushHandler', function () {
            switch (config('logger')->driver) {
                case 'simple':
                    return new \Monolog\Handler\StreamHandler(config('logger')->path);
                    break;

                case 'daily':
                    return new \Monolog\Handler\RotatingFileHandler(config('logger')->path, config('logger')->maxFiles);
                    break;
                
                default:
                    return new \Monolog\Handler\StreamHandler(config('logger')->path);
                    break;
            }
        });


        static::register(\League\Flysystem\Filesystem::class, \League\Flysystem\FilesystemInterface::class)
        ->setArgument(\League\Flysystem\AdapterInterface::class, function () {
            return new \League\Flysystem\Adapter\Local(config('filesystem')->main['path']);
        });


        static::register(\Amber\Sketch\Sketch::class)
        ->afterConstruct('setViewsFolder', 'assets/views')
        ->afterConstruct('setCacheFolder', 'tmp/cache/views')
        ->afterConstruct('setTemplate', function () {
            return static::get(\Amber\Sketch\Template\Template::class);
        });


        $eloquent = new Eloquent();
        $eloquent->addConnection(config('database')->pgsql);
        $eloquent->setAsGlobal();
        $eloquent->bootEloquent();
    }

    private static function setUpProviders(): void
    {
    	array_map(function ($value) {
    		static::get($value)->setUp();
    	}, self::$providers);
    }
}
