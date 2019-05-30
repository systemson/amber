<?php

namespace Amber\Framework\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Amber\Framework\Http\Server\Middleware\ActionHandlerController;
use Psr\Http\Message\StreamFactoryInterface;



use Amber\Framework\Http\Message\ServerRequest;
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
            return $this->sendNotFoundResponse($request, $e->getMessage());
        } catch (MethodNotAllowedException $e) {
            return $this->sendMethodNotAllowedResponse($request, $e->getMessage());
        } catch (NoConfigurationException $e) {
            return $this->sendNoConfigurationResponse($request, $e->getMessage());
        }

        /* Add the matched route's middlewares */
        $handler->addMiddlewares($defaults['_middlewares']);

        /* Set the route defaults */
        $request = $request->withAttribute('defaults', $defaults);

        /* Set the default middleware handler */
        $handler->addMiddleware(ActionHandlerController::class);

        return $handler->next($request);
    }

    protected function match(Request $request)
    {
        $matcher = static::getContainer()->get(UrlMatcher::class);

        return $matcher->match($request->getUri()->getpath());
    }

    protected function sendNotFoundResponse(Request $request, string $reason = '')
    {
        $response = $this->responseFactory->notFound($reason);

        return $this->setBody($request, $response);
    }

    protected function sendMethodNotAllowedResponse(Request $request, string $reason = '')
    {
        $response = $this->responseFactory->forbidden($reason);

        return $this->setBody($request, $response);
    }

    protected function sendNoConfigurationResponse(Request $request, string $reason = '')
    {
        $response = $this->responseFactory->internalServerError($reason);

        return $this->setBody($request, $response);
    }

    protected function setBody(Request $request, Response $response): Response
    {
        $reason = $response->reasonPhrase;

        /* Check if the request wants a json response */
        if (strtolower($request->getHeader('Accept')) == 'application/json') {
            $body = ['message' => $reason];
        } else {
            $body = $reason;
        }

        $streamFactory = static::getContainer()->get(StreamFactoryInterface::class);

        return $response->withBody($streamFactory->createStream($body));
    }
}
