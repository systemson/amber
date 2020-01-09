<?php

namespace Amber\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Amber\Http\Server\Middleware\Middleware as RequestMiddleware;

abstract class AbstractController extends RequestMiddleware
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     *
     * @return Response
     */
    public function process(Request $request, Handler $handler): Response
    {
        $defaults = $request->getAttribute('defaults');

        $method = $defaults['_action']->toString();

        $args = $this->geMethodtArguments($method, $this->getArgumentsFromDefaults($defaults));
        $args[Request::class] = $request;

        return $this->handlerControllerReturn(
            call_user_func_array([$this, $method], $args),
            $handler->handle($request)
        );
    }

    protected function getArgumentsFromDefaults(array $defaults): array
    {
        return array_filter($defaults, function ($key) {
            return substr($key, 0, 1) != "_";
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function geMethodtArguments(string $method, array $args = []): array
    {
        $service = $this->container->locate(get_class($this));
        $service->setArguments($method, $args);
        return $this->container->getArguments($service, $method);
    }

    protected function handlerControllerReturn($return, Response $response): Response
    {
        if ($return instanceof Response) {
            return $return
                ->withHeaders($response->getHeaders())
            ;
        }

        if (is_string($return)) {
            $response->getBody()->write($return);

            return $response;
        }

        if ($return instanceof StreamInterface) {
            return $response
                ->withBody($body)
            ;
        }

        if (is_null($return)) {
            return $response;
        }

        $message = sprintf(
            "Return value of [%s::%s()] must be of the type string, null, an instance of [%s] or [%s], %s returned.",
            $defaults['_controller'],
            $defaults['_action'],
            Response::class,
            StreamInterface::class,
            gettype($ret)
        );

        throw new \TypeError($message);
    }
}
