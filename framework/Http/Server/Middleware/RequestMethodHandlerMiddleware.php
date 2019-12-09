<?php

namespace Amber\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class RequestMethodHandlerMiddleware extends Middleware
{
    const METHOD_ATTRIBUTE = '_method';

    const REAL_METHOD_ATTRIBUTE = 'request_real_method';

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, Handler $handler): Response
    {
        if ($this->needsMethodChange($request)) {
            $request = $request->withMethod($this->getNewMethod($request))
                ->withAttribute(
                    static::REAL_METHOD_ATTRIBUTE,
                    $request->getMethod()
                )
            ;
        }

        return $handler->handle($request);
    }

    protected function getNewMethod(Request $request): string
    {
        $method = strtoupper($request->getParsedBody()->get(static::METHOD_ATTRIBUTE));

        if (!in_array($method, ['PUT', 'PATCH', 'HEAD', 'DELETE', 'OPTIONS'])) {
            // MUST change the exception to handle a HTML/JSON response.
            throw new \Exception('Invalid method');
        }

        return strtoupper($method);
    }

    protected function needsMethodChange(Request $request): bool
    {
        return $request->getMethod() == 'POST' && $request->post->has(static::METHOD_ATTRIBUTE);
    }
}
