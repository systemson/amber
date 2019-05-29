<?php

namespace Amber\Framework\Http\Server;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Psr\Http\Message\ResponseFactoryInterface;
use Amber\Framework\Container\ContainerAwareClass;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Psr\Http\Message\StreamFactoryInterface;

class RouteHandler extends ContainerAwareClass
{
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
    public function handle(Request $request)
    {
        try {

            $defaults = $this->match($request);

        } catch (ResourceNotFoundException $e) {

            $response = $this->responseFactory->notFound($e->getMessage());

            if (strtolower($request->getHeader('Accept')) != 'application/json') {
                $response->body = $e->getMessage();
            }

            return $response;

        } catch (MethodNotAllowedException $e) {
dd($e->getMessage());
            $response = $this->responseFactory->forbidden($e->getMessage());

            if (strtolower($request->getHeader('Accept')) != 'application/json') {
                $response->body = $e->getMessage();
            }

            return $response;
        }

        return $defaults;
    }

    protected function match(Request $request)
    {
        $matcher = static::getContainer()->get(UrlMatcher::class);

        return $matcher->match($request->getUri()->getpath());
    }
}
