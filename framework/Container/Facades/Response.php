<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Psr\Http\Message\ResponseFactoryInterface as Accessor;
use Amber\Utils\Traits\SingletonTrait;

class Response extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;
}
