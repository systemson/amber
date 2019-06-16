<?php

namespace Amber\Framework\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Amber\Framework\Container\Facades\Response as ResponseFacade;
use Carbon\Factory;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class ThrottleRequestMiddleware extends RequestMiddleware
{
    public function __construct()
    {
        $this->maxAttempts = 60;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, Handler $handler): Response
    {
        $throttle = $this->getRequestThrottle($request);

        if ($throttle->remain >= 0) {

            return $handler->handle($request)
                // Request limit per hour.
                ->withHeader('X-RateLimit-Limit', $throttle->max)
                // The number of requests left for the time window.
                ->withHeader('X-RateLimit-Remaining', $throttle->remain)
                // The remaining window before the rate limit resets in UTC epoch seconds.
                ->withHeader('X-RateLimit-Reset', $throttle->reset_at)
            ;

        } else {

            return $handler->handle($request)
                // 
                ->withHeader('Retry-After', $throttle->retry_at)
            ;
        }
    }

    protected function getRequestIdentifier(Request $request): string
    {
        return '';
    }

    protected function getRequestThrottle(Request $request)
    {
        $id = $this->getRequestIdentifier($request);
        $currentAttempts = 0;

        return (object) [
            'max' => $this->maxAttempts,
            'remain' => $this->maxAttempts - $currentAttempts,
            'reset_at' => carbon()->now()->addMinute()->timestamp,
            'retry_at' => null,
        ];
    }
}
