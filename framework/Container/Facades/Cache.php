<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Psr\SimpleCache\CacheInterface as Accessor;
use Amber\Utils\Traits\SingletonTrait;

class Cache extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;
}
