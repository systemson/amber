<?php

namespace Amber\Framework\Http\Server;

use Amber\Container\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class AbstractRequestHandler implements RequestHandlerInterface
{
    protected $responseFactory;
    protected $middlewares = [];
    protected $container;
    protected $index = 0;

    public function __construct(ResponseFactoryInterface $responseFactory, array $middlewares = [], Container $container = null)
    {
        $this->responseFactory = $responseFactory;
        $this->middlewares = $middlewares;
        $this->container = $container;
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
        if (isset($this->middlewares[$this->index])) {
            var_dump($this->index, $this->middlewares[$this->index]);
            return $this->middleware($this->index)->process($request, $this);
        } else {
            return $this->default();
        }
    }

    public function next($request)
    {
        $this->index++;
        return $this->handle($request);
    }

    public function default()
    {
        return $this->newResponse();
    }

    public function pushMiddlewares(array $middlewares)
    {
        $this->middlewares = array_merge($this->middlewares, $middlewares);
    }

    protected function middleware($index)
    {
        $middleware = $this->middlewares[$index];

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        } elseif ($this->container instanceof Container) {
            return $this->container->make($middleware);
        } else {
            throw new \Exception('Middleware is invalid');
        }
    }

    protected function handleController($default)
    {
        $callback = static::getContainer()->getClosureFor($default['_controller'], $default['_action']);

        return $callback();
    }
}
