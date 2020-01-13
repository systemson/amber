<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Psr\Container\ContainerInterface as Accessor;
use Amber\Utils\Traits\SingletonTrait;

class Application extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;
}
