<?php

namespace Amber\Framework\Http\Routing;

use Symfony\Component\Routing\RouteCollection as SymfonyRoute;

class RouteCollection //extends SymfonyRoute
{
    /**
     * Return the controller resource name.
     */
    private function getResource(Str $default): Str
    {
        return $default->removeAll(['App\Controllers\\' , 'Controller'])
            ->fromCamelCase()
            ->toSnakeCase()
        ;
    }

    /**
     * Return the controller action name.
     */
    private function getAction(Str $default): Str
    {
        return $default->fromCamelCase()
            ->toSnakeCase()
        ;
    }

    /**
     * Adds a new route to the route collection.
     */
    private function map(string $method, string $url, $default): SymfonyRoute
    {
        $default = static::handleDefault($default);

        $route = static::routeFactory($method, $url, $default);

        $name = static::getName(array_values($default));

        static::add($name, $route);

        return $route;
    }

    private function handleDefault($default)
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
    private function getControllerToActionArray($default)
    {
        return  Phraser::make($default)
            ->explode('::')
        ;
    }

    /**
     * Return a new Route Instance.
     */
    private function routeFactory(string $method, string $url, array $default): SymfonyRoute
    {
        return (new AmberRoute($url))
            ->setMethods(strtoupper($method))
            ->setDefaults($default)
        ;
    }

    private function middlewares()
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
    private function getName(array $default)
    {
        $resource = static::getResource($default[0]);
        $action = static::getAction($default[1]);

        return "{$resource}_{$action}";
    }

    public function get(string $url, $default)
    {
        return static::map('GET', $url, $default);
    }

    public function post(string $url, $default)
    {
        return static::map('POST', $url, $default);
    }

    public function patch(string $url, $default)
    {
        return static::map('PATCH', $url, $default);
    }

    public function put(string $url, $default)
    {
        return static::map('PUT', $url, $default);
    }

    public function delete(string $url, $default)
    {
        return static::map('DELETE', $url, $default);
    }

    public function group(\Closure $callback)
    {
        $collection = new RouteCollection();

        dump($collection);

        $callback($collection);

        dump($collection);

        static::addCollection($collection);
    }
}
