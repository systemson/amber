<?php

namespace Amber\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Amber\Http\Routing\Matcher;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;


use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

use Amber\Http\Message\ServerRequest;
use Amber\Container\Facades\Filesystem;
use Amber\Http\Routing\Router;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class RouteHandlerMiddleware extends Middleware
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, Handler $handler): Response
    {
        try {
            $defaults = $this->match($request);
        } catch (ResourceNotFoundException $e) {
            $response = $this->responseFactory()->notFound();
            $response->getBody()->write($e->getMessage());
            return $response;
        } catch (MethodNotAllowedException $e) {
            return $this->responseFactory()->methodNotAllowed(
                sprintf(
                    "Method [%s] Not Allowed For [%s]",
                    $request->getMethod(),
                    $request->getUri()->getPath()
                )
            );
        } catch (NoConfigurationException $e) {
            return $this->responseFactory()->internalServerError($e->getMessage());
        }

        /* Add the matched route's middlewares */
        $handler->addMiddlewares($defaults['_middlewares']);

        /* Set the route defaults */
        $request = $request->withAttribute('defaults', $defaults);

        return $handler->handle($request);
    }

    protected function match(Request $request)
    {
        $routes = $this->container->get(Router::class);

        $matcher = new UrlMatcher(
            $routes->toSymfonyCollection(),
            (new RequestContext())->fromRequest($this->symfonyRequestFromPsr($request))
        );

        return $matcher->match($request->getUri()->getpath());
    }

    protected function symfonyRequestFromPsr(Request $request): SymfonyRequest
    {
        $bridge = new HttpFoundationFactory();

        return $bridge->createRequest($request);
    }
}
