<?php

namespace Amber\Framework;

use Amber\Utils\Implementations\AbstractWrapper;
use Amber\Framework\Application;
use Amber\Sketch\Sketch;
use Amber\Sketch\Template\Template;

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

    protected static $template;

    public static function getInstance()
    {
        $accessor = static::getAccessor();
        if (!static::$instance instanceof $accessor) {
            static::$instance = Application::get($accessor );
        }
        return static::$instance;
    }

    public static function template(): Template
    {
        if (!static::$template instanceof Template) {
            static::$template = Application::get(Template::class);
            static::setTemplate(static::$template);
        }
        return static::$template;
    }

    public static function view(string $view)
    {
        static::template()->setView($view);
        return static::template();
    }
}
