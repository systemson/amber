<?php

namespace Amber\Http\Routing;

use Symfony\Component\Routing\Route as SymfonyRoute;

class Route extends SymfonyRoute
{
    public function controller(string $class, string $method = null)
    {
        $this->setDefault(
            '_controller',
            [
                'class' => $class,
                'method' => $method ?? '__invoke',
            ]
        );

        return $this;
    }

    public function middleware(string ... $middlewares)
    {
        $current = $this->getDefault('_middleware') ?? [];
    
        $this->setDefault('_middleware', array_merge($current, $middlewares));

        return $this;
    }
}
