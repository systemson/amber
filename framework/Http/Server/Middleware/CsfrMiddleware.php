<?php

namespace Amber\Framework\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Amber\Framework\Container\Facades\Response as ResponseFacade;
use Amber\Framework\Helpers\Hash;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class CsfrMiddleware extends RequestMiddleware
{
    const TOKEN_NAME = '_csrf';

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, Handler $handler): Response
    {
        $request = $this->setToken($request);

        if (!in_array($request->getMethod(), ['GET', 'HEAD']) && !$this->validate($request)) {
            return $this->responseFactory->forbidden('Invalid CSRF Token');
        }

        return $handler->handle($request);
    }

    protected function setToken($request)
    {
        $session = $request->getAttribute('session');
        $token = Hash::token(64);

        if (!$session->has(static::TOKEN_NAME)) {
            $session->set(static::TOKEN_NAME, $token);
        }
        
        $request->withAttribute(static::TOKEN_NAME, $token);
        $this->getContainer()->bind(static::TOKEN_NAME, $token);

        return $request;
    }

    protected function validate(Request $request): bool
    {
        $session = $request->getAttribute('session');

        $sessionToken = $session->remove(static::TOKEN_NAME);

        $postToken = $request->getParsedBody()->get(static::TOKEN_NAME);

        if (is_null($sessionToken) || $sessionToken !== $postToken) {
            return false;
        }

        return true;
    }
}
