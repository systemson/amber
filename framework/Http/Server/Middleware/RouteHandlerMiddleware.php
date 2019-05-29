<?php

namespace Amber\Framework\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

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

            $reason = $e->getMessage();

            $response = $this->responseFactory->notFound($reason);

            if (strtolower($request->getHeader('Accept')) != 'application/json') {
                $response->body = $reason;
            }

            return $response;

        } catch (MethodNotAllowedException $e) {

            $reason = 'Method not allowed.';

            $response = $this->responseFactory->forbidden($reason);

            if (strtolower($request->getHeader('Accept')) != 'application/json') {
                $response->body = $reason;
            }

            return $response;

        } catch (NoConfigurationException $e) {

        }

        $handler->pushMiddlewares($defaults['_middlewares']);

        return $handler->next($request);
    }

    protected function match(Request $request)
    {
        $matcher = static::getContainer()->get(UrlMatcher::class);

        return $matcher->match($request->getUri()->getpath());
    }
}
