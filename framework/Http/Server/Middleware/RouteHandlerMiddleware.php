<?php

namespace Amber\Framework\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Amber\Framework\Http\Routing\Matcher;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Amber\Framework\Http\Server\Middleware\ActionHandlerMiddleware;
use Psr\Http\Message\StreamFactoryInterface;


use Psr\Http\Server\MiddlewareInterface;


use Amber\Framework\Http\Message\ServerRequest;
use Amber\Framework\Container\Facades\Filesystem;
use Amber\Framework\Http\Routing\Router;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class RouteHandlerMiddleware extends RequestMiddleware
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
            return $this->factory()->notFound($e->getMessage());
        } catch (MethodNotAllowedException $e) {
            return $this->factory()->methodNotAllowed(
                sprintf(
                    "Method [%s] Not Allowed For [%s]",
                    $request->getMethod(),
                    $request->getUri()->getPath()
                )
            );
        } catch (NoConfigurationException $e) {
            return $this->factory()->internalServerError($e->getMessage());
        }

        /* Add the matched route's middlewares */
        $handler->addMiddlewares($defaults['_middlewares']);

        /* Set the route defaults */
        $request = $request->withAttribute('defaults', $defaults);

        return $handler->handle($request);
    }

    protected function match(Request $request)
    {
        $routes = static::getContainer()->get(Router::class);

        $matcher = new Matcher($routes, $request);

        return $matcher->match($request->getUri()->getpath());
    }
}
