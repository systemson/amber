<?php

namespace Amber\Framework\Http\Server;

use Amber\Container\Container;
use Amber\Container\Application;
use Amber\Framework\Middleware\MiddlewareCollection;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Psr\Http\Message\ResponseFactoryInterface;
use Amber\Framework\Container\ContainerAwareClass;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Sunrise\Stream\StreamFactory;

class RequestHandler extends ContainerAwareClass implements RequestHandlerInterface
{
    protected $middlewares = [];
    protected $response;
    protected $responseFactory;

    protected $locked = false;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->setResponse($this->newResponse());
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function newResponse(int $code = 200, string $reasonPhrase = ''): Response
    {
        return $this->responseFactory->createResponse($code, $reasonPhrase);
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(Request $request): Response
    {
        try {
            $defaults = $this->match($request);
        } catch (ResourceNotFoundException $e) {
            $response = $this->responseFactory->notFound($e->getMessage());

            if (strtolower($request->getHeader('Accept')) != 'application/json') {
                $response->body = $e->getMessage();
            }

            return $response;
        }

        $middlewares = $defaults['_middlewares'] ?? [];

        $response = $this->middlewares($request, $middlewares);

        if ($this->isLocked()) {
            return $response;
        }

        $return = $this->handleController($defaults);

        if ($return instanceof Response) {
            return $return;
        }

        return $response->withBody((new StreamFactory())->createStream($return));
    }

    protected function match(Request $request)
    {
        $matcher = static::getContainer()->get(UrlMatcher::class);
        $uri = $request->getUri();

        return $matcher->match($uri->getpath());
    }

    protected function middlewares(Request $request, $middlewares = [])
    {
        foreach ($middlewares as $class) {
            $middleware = static::getContainer()->make($class);

            $this->setResponse($middleware->process($request, $this));

            if ($this->locked) {
                break;
            }
        }
        
        return $this->getResponse();
    }

    protected function handleController($default)
    {
        $callback = static::getContainer()->getClosureFor($default['_controller'], $default['_action']);

        return $callback();
    }

    public function lockResponse()
    {
        $this->locked = true;
    }

    public function isLocked()
    {
        return $this->locked;
    }
}
