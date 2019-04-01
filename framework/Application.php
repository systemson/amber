<?php

namespace Amber\Framework;

use Amber\Utils\Implementations\AbstractWrapper;
use Amber\Container\Container;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Database\Capsule\Manager as Eloquent;

class Application extends AbstractWrapper
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

        static::bindMultiple(config('app')->singleton);

        static::bind(Container::class, static::getInstance());

        static::register(RequestContext::class)
        ->afterConstruct('fromRequest', static::get(Request::class));

        static::register(\Monolog\Logger::class, \Psr\Log\LoggerInterface::class)
        ->afterConstruct('pushHandler', new \Monolog\Handler\StreamHandler(
            config('logger')->path,
            \Monolog\Logger::DEBUG
        ));

        $local = new \League\Flysystem\Adapter\Local(config('filesystem')->main['path']);
        static::register(\League\Flysystem\Filesystem::class, \League\Flysystem\FilesystemInterface::class)
        ->setArgument(\League\Flysystem\AdapterInterface::class, $local);

        static::register(\Amber\Sketch\Sketch::class)
        ->afterConstruct('setViewsFolder', 'assets/views')
        ->afterConstruct('setCacheFolder', 'tmp/cache/views')
        ->afterConstruct('setTemplate', function() {
            return static::get(\Amber\Sketch\Template\Template::class);
        });

        static::register(\Amber\Sketch\Template\Template::class)
        ->setArgument('path', 'home_index.php');

        $eloquent = new Eloquent();

        $eloquent->addConnection([
           "driver" => "pgsql",
           "host" => "127.0.0.1",
           "port" =>"5432",
           "database" => "api",
           "username" => "deivi",
           "password" => "deivi"
        ]);

        //Make this Capsule instance available globally.
        $eloquent->setAsGlobal();

        // Setup the Eloquent ORM.
        $eloquent->bootEloquent();
    }
}
