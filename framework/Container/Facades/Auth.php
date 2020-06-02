<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Amber\Auth\AuthClass as Accessor;
use Amber\Utils\Traits\SingletonTrait;

class Auth extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;
}
