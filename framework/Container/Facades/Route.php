<?php

namespace Amber\Framework\Container\Facades;

use Symfony\Component\Routing\Route as SymfonyRoute;
use Amber\Framework\Container\ContainerFacade;
use Amber\Framework\Application;
use Symfony\Component\Routing\RouteCollection;
use Amber\Phraser\Phraser;
use Amber\Phraser\Str;

class Route extends ContainerFacade
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
     * To expose publicy a method it should be declared public or protected.
     *
     * @var array The method(s) that should be publicly exposed.
     */
    protected static $passthru = [];

    public static function boot(): void
    {
        include CONFIG_DIR . '/routes.php';
    }

    /**
     * Return the controller resource name.
     */
    private static function getResource(Str $default): Str
    {
        return $default->removeAll(['App\Controllers\\' , 'Controller'])
            ->fromCamelCase()
            ->toSnakeCase();
    }

    /**
     * Return the controller action name.
     */
    private static function getAction(Str $default): Str
    {
        return $default->fromCamelCase()
            ->toSnakeCase();
    }

    /**
     * Adds a new route to the route collection.
     */
    private static function map(string $method, string $uri, $default): SymfonyRoute
    {
        $default = static::handleDefault($default);

        $route = static::routeFactory($method, $uri, $default);

        $name = static::getName(array_values($default));

        static::add($name, $route);

        return $route;
    }

    private static function handleDefault($default)
    {
        if (is_string($default)) {
            $defaultArray = static::getControllerToActionArray($default);

            return [
                '_controller'  => $defaultArray->first(),
                '_action'      => $defaultArray->last(),
                '_middlewares' => self::middlewares(),
            ];
        }
    }

    /**
     * Retuns an array with the controller and the action names.
     */
    private static function getControllerToActionArray($default)
    {
        return  Phraser::make($default)
        ->explode('::');
    }

    /**
     * Return a new Route Instance.
     */
    private static function routeFactory(string $method, string $uri, array $default): SymfonyRoute
    {
        $route = new SymfonyRoute($uri);
        $route->setMethods(strtoupper($method));
        $route->setDefaults($default);

        return $route;
    }

    private static function middlewares()
    {
        return [
            'Amber\Framework\Http\Server\Middleware\SessionMiddleware',
            'Amber\Framework\Http\Server\Middleware\CsfrMiddleware',
            //'Amber\Framework\Http\Server\Middleware\AuthenticatedMiddleware',
        ];
    }

    /**
     * Return the default name of the route.
     */
    private static function getName(array $default)
    {
        $resource = static::getResource($default[0]);
        $action = static::getAction($default[1]);

        return "{$resource}_{$action}";
    }

    public static function get(string $uri, $default)
    {
        return static::map('GET', $uri, $default);
    }

    public static function post(string $uri, $default)
    {
        return static::map('POST', $uri, $default);
    }

    public static function patch(string $uri, $default)
    {
        return static::map('PATCH', $uri, $default);
    }

    public static function put(string $uri, $default)
    {
        return static::map('PUT', $uri, $default);
    }

    public static function delete(string $uri, $default)
    {
        return static::map('DELETE', $uri, $default);
    }
}
