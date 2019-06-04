<?php

namespace Amber\Framework\Http\Server;

use Amber\Container\Container;
use Amber\Collection\Collection;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class RequestHandler implements RequestHandlerInterface
{
    protected $responseFactory;
    protected $middlewares = [];
    protected $container;
    protected $index;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        array $middlewares = [],
        Container $container = null
    ) {
        $this->responseFactory = $responseFactory;
        $this->middlewares = new Collection($middlewares);
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
        $id = $this->next();

        if (isset($this->middlewares[$id])) {
            $response =  $this->getMiddleware($id)->process($request, $this);
        } else {
            $response = $this->default();
        }

        return $this->setResponseBody($request, $response);
    }

    public function next(): int
    {
        if (is_null($this->index)) {
            return $this->index = 0;
        }

        return $this->index++;
    }

    public function default()
    {
        return $this->newResponse();
    }

    public function addMiddleware(string $middleware)
    {
        $this->middlewares->append($middleware);
    }

    public function hasMiddleware(string $middleware)
    {
        $this->middlewares->contains($middleware);
    }

    protected function getMiddleware($index)
    {
        $middleware = $this->middlewares->get($index);

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        } elseif ($this->container instanceof Container) {
            return $this->container->make($middleware);
        } else {
            throw new \Exception('Middleware is invalid');
        }
    }

    public function addMiddlewares(iterable $middlewares)
    {
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }
    }

    public function getMiddlewares()
    {
        $middlewares = [];

        foreach ($this->middlewares as $index => $value) {
            $middlewares[] = $this->getMiddleware($index);
        }

        return $middlewares;
    }

    protected function setResponseBody(Request $request, Response $response): Response
    {
        if ($response->getStatusCode() <= 400) {
            return $response;
        }

        $reason = $response->reasonPhrase;

        /* Check if the request wants a json response */
        if (strpos($request->getHeader('Accept'), 'application/json') !== false) {
            $body = json_encode(['message' => $reason]);
        } else {
            $body = $reason;
        }


        $streamFactory = $this->container->get(StreamFactoryInterface::class);

        return $response->withBody($streamFactory->createStream($body));
    }
}
