<?php

namespace Amber\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Amber\Auth\UserProviderContract;
use Amber\Container\Facades\Auth;
use Psr\SimpleCache\CacheInterface;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class AuthMiddleware extends Middleware
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
        $session = $request->getAttribute('session');

        if ($session->has('_token')) {
            $cache = $this->container->get('_session_cache');

            $token = $session->get('_token');

            if ($cache->has($token)) {
                $user = $cache->get($token);
            } else {
                $userProvider = $this->container->get(UserProviderContract::class);
                $user = $userProvider->getUserByToken($token);
            }

            $request = $request->withAttribute('user', $user);

            Auth::setRequest($request);
        }

        return $handler->handle($request);
    }
}
