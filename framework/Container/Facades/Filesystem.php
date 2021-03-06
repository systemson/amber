<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use League\Flysystem\FilesystemInterface as Accessor;
use Amber\Utils\Traits\SingletonTrait;

class Filesystem extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;
}
