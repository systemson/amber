<?php

namespace Amber\Framework;

use Amber\Utils\Implementations\AbstractWrapper;
use Amber\Framework\Application;

abstract class AbstractContainerFacade extends AbstractWrapper
{
    /**
     * @var string The class accessor.
     */
    protected static $accessor;

    /**
     * @var mixed The instance of the accessor.
     */
    protected static $instance;

    /**
     * To expose publicy a method it should be declared protected.
     *
     * @var array The method(s) that should be publicly exposed.
     */
    protected static $passthru = [];

    /**
     * @var The DI container.
     */
    protected static $container;

    public static function getInstance()
    {
        $accessor = static::getAccessor();
        if (!static::$instance instanceof $accessor) {
            static::$instance = Application::get($accessor);
        }
        return static::$instance;
    }
}
