<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Amber\Http\Session\Session as Accessor;

class Session extends ContainerFacade
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

    public static function cache()
    {
        return static::getContainer()->get('_session_cache');
    }
}
