<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Amber\Sketch\Sketch as Accessor;
use Amber\Utils\Traits\SingletonTrait;

class View extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;

    public static function view(string $view)
    {
        return static::getTemplate()->setView($view);
    }
}
