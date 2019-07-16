<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Amber\Model\Gemstone as Accessor;
use Amber\Utils\Traits\SingletonTrait;

class Gemstone extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;
}
