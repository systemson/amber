<?php

namespace Amber\Container\Facades;

use Amber\Http\Security\Csrf as Accessor;
use Amber\Container\ContainerFacade;
use Amber\Utils\Traits\SingletonTrait;

class Csrf extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;
}
