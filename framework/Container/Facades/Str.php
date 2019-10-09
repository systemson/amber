<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Amber\Phraser\Phraser as Accessor;
use Amber\Utils\Traits\SingletonTrait;
use Amber\Utils\Implementations\AbstractWrapper;

class Str extends AbstractWrapper
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;

    public static function new(string $string = null)
    {
        return Accessor::make($string);
    }

    /**
     * Returns the instance of the class.
     */
    public static function getInstance()
    {
        $accesor = static::getAccessor();

        //if (!static::$instance instanceof $accesor) {
            static::beforeConstruct();

            $args = static::getArguments();

            $instance = static::make($accesor, $args);

            static::afterConstruct();
        //}

        return $instance;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args = [])
    {
        $first = current($args);

        if ($first !== null && is_string($first) && in_array($method, get_class_methods(static::getAccessor()))) {
            static::setArguments($first);
            unset($args[0]);
        } else {
            static::$arguments = [];
        }

        return parent::__callStatic($method, $args);
    }
}
