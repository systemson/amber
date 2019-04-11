<?php

namespace Amber\Framework\Middleware;

use Amber\Framework\Container\Facades\Session;
use Amber\Framework\Container\Facades\Auth;
use Amber\Framework\Container\Facades\Cache;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class SessionMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->hasSession()) {
            Session::start();
        }

        if (Session::has('_token')) {
            $token = SessionFacade::get('_token');

            if (Cache::has($token)) {
                $user = Cache::get($token);
            } {
                $userProvider = $container->get(UserProvider::class);
            if (!is_null($token)) {
                $user = $userProvider->getUserByToken($token);
            }
            }

            Auth::setUser($user);
        }
    }
}
