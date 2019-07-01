<?php

namespace Amber\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Amber\Container\Facades\Response as ResponseFacade;
use Carbon\Factory;
use Psr\SimpleCache\CacheInterface;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class ThrottleRequestMiddleware extends RequestMiddleware
{
    private $maxAttempts = 60;
    private $secondsToReset = 60;
    // private $retryAfter;

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
                // Request limit per minute.
                ->withHeader('X-RateLimit-Limit', $this->maxAttempts)
                // The number of requests left for the time window.
                ->withHeader('X-RateLimit-Remaining', $throttle->remain)
                // The remaining window before the rate limit resets in UTC epoch seconds.
                ->withHeader('X-RateLimit-Reset', $throttle->reset_at)
                // For testing purpose.
                ->withHeader('Date-Timestamp', $this->now()->timestamp)
            ;
        } else {
            return ResponseFacade::tooManyRequest()
                // The api request timeout.
                ->withHeader('Retry-After', $throttle->reset_at - $this->now()->timestamp)
            ;
        }
    }

    protected function now()
    {
        return carbon()->now();
    }

    protected function getRequestIdentifier(Request $request): string
    {
        if (!is_null($user = $request->getAttribute('user'))) {
            $id =  $user->id;
        } elseif ($ip = $request->getAttribute('client_ip')) {
            $id = $ip;
        }

        return sha1($id);
    }

    protected function getCacheHandler(): CacheInterface
    {
        return $this->getContainer()->get('_session_cache');
    }

    protected function loadRequestThrottle(string $id)
    {
        $cache = $this->getCacheHandler();

        if ($this->validateThrottle($id)) {
            $throttle = $cache->get($id);
        } else {
            $throttle = [
                'created_at' => $this->now()->timestamp,
                'reset_at' => $this->now()->addSeconds($this->secondsToReset)->timestamp,
                'attempts' => 0,
            ];
        }

        $throttle['id'] = $id;

        return $throttle;
    }

    public function validateThrottle($id)
    {
        $cache = $this->getCacheHandler();

        if ($cache->has($id) && !is_null($throttle = $cache->get($id))) {
            if ($throttle['reset_at'] >= $this->now()->timestamp) {
                return true;
            }
        }
        return false;
    }

    protected function updateRequestThrottleCache(array $data)
    {
        $cache = $this->getCacheHandler();

        $throttle = [
            'created_at' => $data['created_at'],
            'reset_at' => $data['reset_at'],
            'attempts' => $currentAttempts = $data['attempts'] + 1,
        ];

        $cache->set(
            $data['id'],
            $throttle
        );

        return $throttle;
    }

    protected function getRequestThrottle(Request $request)
    {
        $id = $this->getRequestIdentifier($request);

        return (object) $this->handleRequestThrottleFromCache($id);
    }

    protected function handleRequestThrottleFromCache($id)
    {
        $cache = $this->loadRequestThrottle($id);

        $cache = $this->updateRequestThrottleCache($cache);

        return [
            'remain' => $this->maxAttempts - $cache['attempts'],
            'reset_at' => $cache['reset_at'],
        ];
    }
}
