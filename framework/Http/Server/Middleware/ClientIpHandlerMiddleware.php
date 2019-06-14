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
class ClientIpHandlerMiddleware extends RequestMiddleware
{
    const CLIENT_IP_ATTRIBUTE = 'client_ip';

    const PROXY_FALLBACK = 'REMOTE_ADDR';

    protected $proxy = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
    ];

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, Handler $handler): Response
    {
        return $handler->handle(
            $request->withAttribute(
                static::CLIENT_IP_ATTRIBUTE,
                $this->getRealClientIp($request)
            )
        );
    }

    public function getRealClientIp(Request $request): string
    {
        $headers = $request->getServerParams();

        foreach ((array) $this->proxy as $key) {
            if ($headers->has($key)) {
                $raw = $headers->get($key);
                $array = explode(',', $raw);

                $userIp = trim(end($array));
                break;
            }
        }

        return $userIp ?? $headers->get(static::PROXY_FALLBACK);
    }
}
