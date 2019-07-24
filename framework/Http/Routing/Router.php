<?php

namespace Amber\Http\Routing;

use Symfony\Component\Routing\RouteCollection;
use Amber\Phraser\Phraser;
use Amber\Phraser\Str;
use Amber\collection\collection;
use Amber\Http\Server\Middleware\ControllerHandlerMiddleware;
use Amber\Http\Server\Middleware\ClosureHandlerMiddleware;
use Amber\Http\Message\Utils\RequestMethodInterface;

/**
 *
 */
class Router implements RequestMethodInterface
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
    private function addRoute(array $methods, string $url, $defaults): Route
    {
        $defaults = $this->handleDefaults($defaults);

        $route = $this->routeFactory($methods, $url, $defaults);

        $name = $this->getName($url, $defaults);

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
                '_middlewares' => $this->middlewares->append(ControllerHandlerMiddleware::class),
            ];
        } elseif ($defaults instanceof \Closure) {
            return [
                '_callback' => $defaults,
                '_middlewares' => $this->middlewares->append(ClosureHandlerMiddleware::class),
            ];
        }
    }

    /**
     * Returns an array with the controller and the action names.
     */
    private function getControllerToActionArray($defaults)
    {
        return  Phraser::make($defaults)
            ->explode('::');
    }

    /**
     * Returns a new Route Instance.
     */
    protected function routeFactory(array $methods, string $url, array $defaults): Route
    {
        $methods = array_map('strtoupper', $methods);

        return (new Route($url))
            ->setMethods($methods)
            ->setDefaults($defaults)
        ;
    }

    /**
     * Returns the default name of the route.
     */
    protected function getName(string $url, array $defaults): string
    {
        $url = $this->getUrlName($url);
        $resource = $this->getResource($defaults['_controller'] ?? Phraser::make());
        $action = $this->getAction($defaults['_action'] ?? Phraser::make());

        return Phraser::make()
            ->explode('.')
            ->append((string) $url)
            ->append((string) $resource)
            ->append((string) $action)
            ->trim()
            ->toString()
        ;
    }

    /**
     * Returns the controller resource name.
     */
    private function getUrlName(string $url): Str
    {
        return Phraser::make($url)
            ->explode('/')
            ->trim()
            ->toSnakeCase()
        ;
    }

    /**
     * Returns the controller resource name.
     */
    private function getResource(Str $defaults): Str
    {
        if ($defaults->isEmpty()) {
            return $defaults;
        }

        return $defaults->explode('\\')
            ->last()
            ->remove('Controller')
            ->fromCamelCase()
            ->toSnakeCase()
        ;
    }

    /**
     * Returns the controller action name.
     */
    private function getAction(Str $defaults): Str
    {
        return $defaults->fromCamelCase()
            ->toSnakeCase()
        ;
    }

    public function get(string $url, $defaults)
    {
        return $this->addRoute([self::METHOD_GET], $url, $defaults);
    }

    public function post(string $url, $defaults)
    {
        return $this->addRoute([self::METHOD_POST], $url, $defaults);
    }

    public function patch(string $url, $defaults)
    {
        return $this->addRoute([self::METHOD_PATCH], $url, $defaults);
    }

    public function put(string $url, $defaults)
    {
        return $this->addRoute([self::METHOD_PUT], $url, $defaults);
    }

    public function delete(string $url, $defaults)
    {
        return $this->addRoute([self::METHOD_DELETE], $url, $defaults);
    }

    public function update(string $url, $defaults)
    {
        return $this->addRoute([self::METHOD_PUT, self::METHOD_PATCH], $url, $defaults);
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
