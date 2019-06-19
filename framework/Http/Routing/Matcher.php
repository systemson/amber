<?php

namespace Amber\Framework\Http\Routing;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Amber\Framework\Http\Routing\Router;
use Symfony\Component\Routing\RequestContext;
use Psr\Http\Message\ServerRequestInterface;

/**
 * UrlMatcher matches URL based on a set of routes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Matcher extends UrlMatcher
{
    public function __construct(Router $routes, ServerRequestInterface $request)
    {
        $this->routes = $routes->toSymfonyCollection();

        $this->context = $this->fromPsrRequest($request);
    }

    /**
     * Updates the RequestContext information based on a PSR Request.
     *
     * @return $this
     */
    public function fromPsrRequest(ServerRequestInterface $request)
    {
        $context = new RequestContext();

        $uri = $request->getUri();

        $context
        	->setBaseUrl($uri->getAuthority())
        	->setPathInfo($uri->getPath())
        	->setMethod($request->getMethod())
        	->setHost($uri->getHost())
        	->setScheme($scheme = $uri->getScheme())
        	->setHttpPort($uri->getPort() ?? $uri::DEFAULT_PORT['http'])
        	->setHttpsPort($uri->getPort() ?? $uri::DEFAULT_PORT['http'])
        	->setQueryString($request->getServerParams()->get('QUERY_STRING') ?? '')
        ;

        return $context;
    }
}
