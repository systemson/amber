<?php

namespace Amber\Framework\Http\Server\Middleware;

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
class ClosureHandlerMiddleware extends RequestMiddleware
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

        $return = $defaults['_callback']->__invoke();

        if ($return instanceof Response) {
            return $return;
        } elseif (is_string($return)) {
            $streamFactory = static::getContainer()->get(StreamFactoryInterface::class);

            $body = $streamFactory->createStream($return);

            return $this->createResponse()->withBody($body);
        } elseif ($return instanceof StreamInterface) {
            return $this->createResponse()->withBody($return);
        }

        return $handler->next($request);
    }
}
