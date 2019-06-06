<?php

namespace Amber\Framework\Container\Facades;

use Amber\Framework\Http\Security\Csrf as Accessor;
use Amber\Framework\Container\ContainerFacade;

class Csrf extends ContainerFacade
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
