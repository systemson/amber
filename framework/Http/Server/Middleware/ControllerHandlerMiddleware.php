<?php

namespace Amber\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class ControllerHandlerMiddleware extends Middleware
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
        $defaults = $request->getAttribute('defaults');

        $args = array_filter($defaults, function ($key) {
            return substr($key, 0, 1) != "_";
        }, ARRAY_FILTER_USE_KEY);

        $args[Request::class] = $request;

        $callback = $this->getControllerCallback($defaults['_controller'], $defaults['_action'], $args);

        $ret = $callback->__invoke();

        $response = $handler->handle($request);

        if ($ret instanceof Response) {
            return $ret
                ->withHeaders($response->getHeaders())
            ;
        } elseif (is_string($ret)) {
            $streamFactory = $this->container->get(StreamFactoryInterface::class);

            $body = $streamFactory->createStream($ret);

            return $response
                ->withBody($body)
            ;
        } elseif ($ret instanceof StreamInterface) {
            return $response
                ->withBody($body)
            ;
        } elseif (is_null($ret)) {
            return $response;
        }

        $message = sprintf(
            "Return value of [%s::%s()] must be of the type string, null, an instance of [%s] or [%s], %s returned.",
            $defaults['_controller'],
            $defaults['_action'],
            Response::class,
            StreamInterface::class,
            gettype($ret)
        );

        throw new \TypeError($message);
    }

    protected function getControllerCallback(string $contoller, string $action = '__invoke', array $args = [])
    {
        return $this->container->getClosureFor($contoller, $action, $args);
    }
}
