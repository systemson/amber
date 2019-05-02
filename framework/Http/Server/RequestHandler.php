<?php

namespace Amber\Framework\Http\Server;

use Amber\Container\Container;
use Amber\Framework\Middleware\MiddlewareCollection;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Psr\Http\Message\ResponseFactoryInterface;
use Amber\Framework\Container\ContainerAwareClass;

class RequestHandler extends ContainerAwareClass implements RequestHandlerInterface
{
    protected $middlewares = [];
    protected $response;
    protected $responseFactory;

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
        $defaults = $this->match($request);
        $middlewares = $defaults['_middlewares'] ?? [];

        if (!is_null($response = $this->middlewares($request, $middlewares))) {
            return $response;
        }

        return $this->getResponse();
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

            $response = $middleware->process($request, $this);

            if ($response !== $this->getResponse()) {
                return $response;
                break;
            }
        }
    }
}
