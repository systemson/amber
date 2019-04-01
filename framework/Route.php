<?php

namespace Amber\Framework;

use Symfony\Component\Routing\Route as SymfonyRoute;
use Amber\Utils\Implementations\AbstractWrapper;
use Amber\Framework\Application;
use Symfony\Component\Routing\RouteCollection;
use Amber\Phraser\Phraser;

class Route extends AbstractWrapper
{
    /**
     * @var string The class accessor.
     */
    protected static $accessor = RouteCollection::class;

    /**
     * @var mixed The instance of the accessor.
     */
    protected static $instance;

    /**
     * To expose publicy a method it should be declared protected.
     *
     * @var array The method(s) that should be publicly exposed.
     */
    protected static $passthru = [];

    /**
     * @todo MUST be moved to a ContainerAwareTrait
     *
     * @var The DI container.
     */
    protected static $container;

    private static function name(array $default) {
        
    }

    private static function action(array $default) {
        
    }

    public static function get(string $uri, $default)
    {
        $route = new SymfonyRoute($uri);
        $route->setMethods('GET');

        // Returns StringArray
        $defaultArray = Phraser::make($default)->explode('::');

        $resource = $defaultArray->first()
        ->removeAll(['App\Controllers\\' , 'Controller'])
        ->fromCamelCase()
        ->toSnakeCase();

        $action = $defaultArray->last()
        ->fromCamelCase()
        ->toSnakeCase();

        $route->setDefaults(
            [
                '_controller' => $defaultArray[0],
                '_action' => $defaultArray[1] ?? null,
            ]
        );
        $route = static::add($resource . '_' .$action, $route);
    }
}
