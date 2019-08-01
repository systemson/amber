<?php

namespace Amber\Http\Routing;

use Psr\Http\Server\MiddlewareInterface;

/**
 *
 */
trait MiddlewareTrait
{
    protected $middlewares = [];

    public function setMiddleware(string $alias, string $middleware = null)
    {
        if (!$this->isMiddleware($middleware)) {
            throw new \Exception("Class [{$middleware}] is not a valid middleware.");
        }

        $this->middlewares->add($alias, $middleware ?? $alias);
    }

    public function getMiddleware(string $alias)
    {
        return $this->middlewares->get($alias);
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    public function setMiddlewares(array $middlewares)
    {
        foreach ($middlewares as $alias => $class) {
            if (is_numeric($alias)) {
                $alias = $class;
            }

            $this->setMiddleware($alias, $class);
        }
    }

    protected function isMiddleware(string $middleware): bool
    {
        return is_a($middleware, MiddlewareInterface::class);
    }
}
