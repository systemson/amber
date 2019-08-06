<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Amber\Phraser\Str as Accessor;
use Amber\Utils\Traits\SingletonTrait;

class Str extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;
}
