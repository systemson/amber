<?php

namespace Amber\Framework\Http\Routing;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Amber\Framework\Http\Routing\Router;

/**
 * UrlMatcher matches URL based on a set of routes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Matcher extends UrlMatcher
{
    public function __construct(Router $routes, RequestContext $context)
    {
        $this->routes = $routes->toSymfonyCollection();
        $this->context = $context;
    }
}
