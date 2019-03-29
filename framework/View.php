<?php

namespace Amber\Framework;

use Amber\Utils\Implementations\AbstractWrapper;
use Amber\Framework\Application;
use Amber\Sketch\Sketch;

class View extends AbstractWrapper
{
    /**
     * @var string The class accessor.
     */
    protected static $accessor = Sketch::class;

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
     * @todo MUST be moved to a ContainerAwareTrait
     *
     * @var The DI container.
     */
    protected static $container;

    public static function getInstance()
    {
        return Application::get(static::getAccessor());
    }
}
