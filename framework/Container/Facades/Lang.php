<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Amber\Helpers\Localization\Lang as Accessor;
use Amber\Utils\Traits\SingletonTrait;

class Lang extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;
}
