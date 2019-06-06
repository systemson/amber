<?php

namespace Amber\Framework\Container\Facades;

use Amber\Framework\Container\ContainerFacade;
use League\Flysystem\FilesystemInterface as Accessor;

class Filesystem extends ContainerFacade
{
    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;

    /**
     * @var mixed The instance of the accessor.
     */
    protected static $instance;

    /**
     * To publicly expose a method it must be public or protected.
     *
     * @var array The method(s) that should be publicly exposed. An empty array means all.
     */
    protected static $passthru = [];
}
