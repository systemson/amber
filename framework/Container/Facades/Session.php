<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Amber\Http\Session\Session as Accessor;
use Amber\Utils\Traits\SingletonTrait;

class Session extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;

    public static function cache()
    {
        return static::getContainer()->get('_session_cache');
    }
}
