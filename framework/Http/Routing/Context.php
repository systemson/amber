<?php

namespace Amber\Framework\Http\Routing;

use Symfony\Component\Routing\RequestContext;
use Amber\Framework\Http\Message\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Amber\Framework\Http\Message\Uri;

/**
 * UrlMatcher matches URL based on a set of routes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Context extends RequestContext
{
    /**
     * Updates the RequestContext information based on a PRS ServerRequestInterface.
     *
     * @return $this
     */
    public function fromPrsRequest(ServerRequestInterface $request)
    {
        $uri = $request->getUri();

        $this->setBaseUrl($uri->getAuthority());
        $this->setPathInfo($uri->getPath());
        $this->setMethod($request->getMethod());
        $this->setHost($uri->getHost());
        $this->setScheme($scheme = $uri->getScheme());
        $this->setHttpPort($scheme == 'https' ? Uri::DEFAULT_PORT['http'] : $uri->getPort());
        $this->setHttpsPort($scheme == 'https' ? $uri->getPort() : Uri::DEFAULT_PORT['https']);
        $this->setQueryString($uri->getQuery());

        return $this;
    }
}
