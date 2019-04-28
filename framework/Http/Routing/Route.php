<?php

namespace Amber\Framework\Http\Routing;

use Symfony\Component\Routing\Route as SymfonyRoute;

class Route extends SymfonyRoute
{
    public function middleware($middleware): self
    {
        $middlewares = array_merge($this->getDefault('_middlewares'), (array) $middleware);

        $this->setDefault('_middlewares', $middlewares);

        return $this;
    }
}
