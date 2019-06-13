<?php

namespace Amber\Framework\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Amber\Framework\Container\Facades\Response as ResponseFacade;
use Amber\Framework\Auth\UserProvider;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class ApiTokenMiddleware extends RequestMiddleware
{
    const API_TOKEN_NAME = 'api_token';

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, Handler $handler): Response
    {
        $provider = $this->getContainer()
            ->get(UserProvider::class)
        ;

        $token = $this->getToken($request);

        if (is_null($token) || !$provider->hasUserByToken($token)) {
            return ResponseFacade::unauthorized();
        }

        return $handler->handle($request);
    }

    public function getToken(Request $request)
    {
        if ($request->getQueryParams()->has(static::API_TOKEN_NAME)) {
            return $request->getQueryParams()->get(static::API_TOKEN_NAME);
        }

        if ($request->getParsedBody()->has(static::API_TOKEN_NAME)) {
            return $request->getParsedBody()->get(static::API_TOKEN_NAME);
        }

        if ($request->getHeaders()->has(static::API_TOKEN_NAME)) {
            return $request->getHeaders()->get(static::API_TOKEN_NAME);
        }

        return;
    }
}
