<?php

namespace Amber\Http\Routing;

use Psr\Http\Server\MiddlewareInterface;

/**
 *
 */
trait MiddlewareTrait
{
    public function setMiddleware(string $alias, string $middleware = null): self
    {
        if (!$this->isMiddleware($middleware ?? $alias)) {
            throw new \Exception("Class [{$middleware}] is not a valid middleware.");
        }

        $this->options['middlewares'][$alias] = $middleware ?? $alias;

        return $this;
    }

    public function getMiddleware(string $alias)
    {
        return $this->options['middlewares'][$alias] ?? null;
    }

    public function getMiddlewares()
    {
        return $this->options->get('middlewares');
    }

    public function setMiddlewares(array $middlewares): self
    {
        foreach ($middlewares as $alias => $class) {
            if (is_numeric($alias)) {
                $alias = $class;
            }

            $this->setMiddleware($alias, $class);
        }

        return $this;
    }

    /**
     * @todo SHOULD be replace with a Typed Collection.
     */
    protected function isMiddleware(string $middleware): bool
    {
        return in_array(MiddlewareInterface::class, class_implements($middleware));
    }
}
