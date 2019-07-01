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
class ControllerHandlerMiddleware extends RequestMiddleware
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

        $callback = $this->getControllerCallback($defaults['_controller'], $defaults['_action']);

        $ret = $callback->__invoke();

        if ($ret instanceof Response) {
            return $ret;
        } elseif (is_string($ret)) {
            $streamFactory = static::getContainer()->get(StreamFactoryInterface::class);

            $body = $streamFactory->createStream($ret);

            return $this->createResponse()->withBody($body);
        } elseif ($ret instanceof StreamInterface) {
            return $this->createResponse()->withBody($ret);
        }

        return $handler->handle($request);
    }

    protected function getControllerCallback(string $contoller, string $action = '__invoke')
    {
        return static::getContainer()->getClosureFor($contoller, $action);
    }
}
