<?php

namespace Amber\Framework\Http\Routing;

use Symfony\Component\Routing\RouteCollection;
use Amber\Phraser\Phraser;
use Amber\Phraser\Str;
use Amber\collection\collection;

/**
 *
 */
class Router
{
    protected $collection;

    protected $middlewares = [];

    public function __construct(array $routes = [])
    {
        $this->collection = new Collection($routes);
        $this->middlewares = new Collection();
    }

    public function toSymfonyCollection()
    {
        $routes = $this->toArray();

        $routeCollection = new RouteCollection();

        foreach ($routes as $name => $route) {
            $routeCollection->add($name, $route);
        }

        return $routeCollection;
    }

    public function toArray()
    {
        return $this->collection->toArray();
    }

    public function newCollection()
    {
        return new Collection();
    }

    public function middlewares($middlewares)
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    public function merge(self $router)
    {
        $this->collection = $this->collection->merge($router->toArray());
    }

    /**
     * Adds a new route to the route collection.
     */
    private function addRoute(string $method, string $url, $defaults): Route
    {
        $defaults = $this->handleDefaults($defaults);

        $route = $this->routeFactory($method, $url, $defaults);

        $name = $this->getName(array_values($defaults));

        $this->collection->add($name, $route);

        return $route;
    }


    private function handleDefaults($defaults)
    {
        if (is_string($defaults)) {
            $defaultArray = $this->getControllerToActionArray($defaults);

            return [
                '_controller'  => $defaultArray->first(),
                '_action'      => $defaultArray->last(),
                '_middlewares' => $this->middlewares,
            ];
        }
    }

    /**
     * Retuns an array with the controller and the action names.
     */
    private function getControllerToActionArray($defaults)
    {
        return  Phraser::make($defaults)
            ->explode('::');
    }

    /**
     * Return a new Route Instance.
     */
    protected function routeFactory(string $method, string $url, array $defaults): Route
    {
        return (new Route($url))
            ->setMethods(strtoupper($method))
            ->setDefaults($defaults)
        ;
    }

    /**
     * Return the default name of the route.
     */
    protected function getName(array $defaults)
    {
        $resource = $this->getResource($defaults[0]);
        $action = $this->getAction($defaults[1]);

        return "{$resource}_{$action}";
    }

    /**
     * Return the controller resource name.
     */
    private function getResource(Str $defaults): Str
    {
        return $defaults->removeAll(['App\Controllers\\' , 'Controller'])
            ->fromCamelCase()
            ->toSnakeCase()
        ;
    }

    /**
     * Return the controller action name.
     */
    private function getAction(Str $defaults): Str
    {
        return $defaults->fromCamelCase()
            ->toSnakeCase()
        ;
    }

    public function get(string $url, $defaults)
    {
        return $this->addRoute('GET', $url, $defaults);
    }

    public function post(string $url, $defaults)
    {
        return $this->addRoute('POST', $url, $defaults);
    }

    public function patch(string $url, $defaults)
    {
        return $this->addRoute('PATCH', $url, $defaults);
    }

    public function put(string $url, $defaults)
    {
        return $this->addRoute('PUT', $url, $defaults);
    }

    public function delete(string $url, $defaults)
    {
        return $this->addRoute('DELETE', $url, $defaults);
    }

    public function group(\Closure $callback, array $options = [])
    {
        $routes = new static();

        $middlewares = $this->middlewares->merge($options['middlewares'] ?? []);

        $routes->middlewares($middlewares);

        $callback->__invoke($routes);

        $this->merge($routes);

        return $this;
    }
}