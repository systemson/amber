<?php

namespace Amber\Framework\Http\Routing;

use Symfony\Component\Routing\RouteCollection;
use Amber\Phraser\Phraser;
use Amber\Phraser\Str;

/**
 *
 */
class Router
{
    protected $collection;

    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    public function all()
    {
        return $this->collection;
    }

    public function newCollection()
    {
        return new RouteCollection();
    }

    protected function middlewares()
    {
        return [
            'Amber\Framework\Http\Server\Middleware\SessionMiddleware',
            'Amber\Framework\Http\Server\Middleware\CsfrMiddleware',
            //'Amber\Framework\Http\Server\Middleware\AuthenticatedMiddleware',
        ];
    }

    /**
     * Adds a new route to the route collection.
     */
    private function add(string $method, string $url, $defaults): Route
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
                '_middlewares' => $this->middlewares(),
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
        return $this->add('GET', $url, $defaults);
    }

    public function post(string $url, $defaults)
    {
        return $this->add('POST', $url, $defaults);
    }

    public function patch(string $url, $defaults)
    {
        return $this->add('PATCH', $url, $defaults);
    }

    public function put(string $url, $defaults)
    {
        return $this->add('PUT', $url, $defaults);
    }

    public function delete(string $url, $defaults)
    {
        return $this->add('DELETE', $url, $defaults);
    }

    public function group(\Closure $callback)
    {
        $collection = $this->newCollection();

        dump($this);

        $callback($this);

        dump($this);
    }
}
