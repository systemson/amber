<?php

namespace Amber\Framework;

use Amber\Utils\Implementations\AbstractWrapper;
use Amber\Container\Injector as Container;
use Amber\Collection\Collection;

class Application extends AbstractWrapper
{
    /**
     * @var The class accessor.
     */
    protected static $accessor = Container::class;

    /**
     * @var object The instance of the accessor.
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
        'remove',
        'bindMultiple',
        'getMultiple',
        'hasMultiple',
    ];

    /**
     * Runs after the class constructor.
     *
     * @return void
     */
    public static function beforeConstruct(): void
    {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }

    /**
     * Runs after the class constructor.
     *
     * @return void
     */
    public static function afterConstruct(): void
    {
        $binds = new Collection(require __DIR__ . '/../config/app.php');
        $binds->set(Container::class, static::getInstance());

        static::bindMultiple($binds->toArray());
    }
}