<?php

namespace Amber\Http\Server;

use Amber\Collection\Collection;
use Amber\Http\Message\Utils\RequestMethodInterface;
use Amber\Http\Message\Utils\StatusCodeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class RequestHandler implements RequestHandlerInterface, RequestMethodInterface, StatusCodeInterface
{
    protected $responseFactory;
    protected $streamFactory;
    protected $middlewares = [];
    protected $container;
    protected $index;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        array $middlewares = [],
        ContainerInterface $container = null
    ) {
        $this->responseFactory = $responseFactory;
        $this->middlewares = new Collection($middlewares);
        $this->container = $container;
    }

    public function newResponse(
        int $code = self::STATUS_OK,
        string $reasonPhrase = ''
    ): Response {
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

        if ($response->getStatusCode() >= 400 && $response->getBody()->__toString() === '') {
            return $this->alterResponseBody($request, $response);
        }

        return $response;
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
        $this->middlewares = $this->middlewares->append($middleware);
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
        } elseif ($this->container instanceof ContainerInterface) {
            return $this->container->get($middleware);
        } else {
            throw new \Exception('Middleware is invalid');
        }
    }

    public function addMiddlewares(iterable $middlewares = [])
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

    protected function alterResponseBody(
        Request $request,
        Response $response
    ): Response {
        $reason = $response->reasonPhrase;

        /* Check if the request wants a json response */
        if ($request->acceptsjson()) {
            $body = json_encode(['message' => $reason]);

            $response = $response->withHeader('Content-type', 'application/json');
        } else {
            $body = $reason;
        }

        return $response->withBody($this->getStreamFactory()->createStream($body));
    }

    protected function getStreamFactory(): StreamFactoryInterface
    {
        if ($this->streamFactory instanceof StreamFactoryInterface) {
            return $this->streamFactory;
        } elseif ($this->container instanceof ContainerInterface) {
            return $this->streamFactory = $this->container->get(StreamFactoryInterface::class);
        }

        /**
         * @todo Should be a valid stream factory.
         */
        return $this->streamFactory = new StreamFactory();
    }
}
