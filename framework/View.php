<?php

namespace Amber\Framework;

use Amber\Framework\Application;
use Amber\Sketch\Sketch;
use Amber\Sketch\Template\Template;

class View extends AbstractContainerFacade
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

    protected static $template;

    public static function view(string $view)
    {
        return static::getTemplate()->setView($view);
    }
}
