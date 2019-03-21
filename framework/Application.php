<?php

namespace Amber\Framework;

use Amber\Utils\Implementations\AbstractWrapper;
use Amber\Container\Injector as Container;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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
    }

    /**
     * Runs after the class constructor.
     *
     * @return void
     */
    public static function afterConstruct(): void
    {
        foreach (config('app')->binds as $service) {
        	// TODO should validate if the class exists
        	static::bind($service);
        }

    	// Bind from config file
        static::bindMultiple(config('app')->singleton);


        // Bind the container to the app
        static::bind(Container::class, static::getInstance());


        // Bind the request context
        $context = new RequestContext();
        $context->fromRequest(static::get(Request::class));
        static::bind(RequestContext::class, $context);


		// create a log channel
		$log = new Logger('Amber');
		$log->pushHandler(new StreamHandler(config('logger')->path, Logger::DEBUG));
        static::bind(LoggerInterface::class, $log);
    }
}