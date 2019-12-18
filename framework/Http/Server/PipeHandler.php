<?php

namespace Amber\Http\Server;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

class PipeHandler extends RequestHandler
{
    protected $container;

    public function __construct(
        array $middlewares = [],
        ContainerInterface $container = null
    ) {
        parent::__construct($middlewares);

        $this->container = $container;
    }

    protected function getMiddleware($index): MiddlewareInterface
    {
        $middleware = $this->middlewares->get($index);

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        } elseif ($this->container instanceof ContainerInterface) {
            if ($this->container->has($middleware)) {
                return $this->container->get($middleware);
            }
            return $this->container->make($middleware);
        } else {
            throw new \Exception('Middleware is invalid');
        }
    }
}
