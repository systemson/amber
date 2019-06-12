<?php

namespace Amber\Framework\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class ErrorHandlerMiddleware extends RequestMiddleware
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, Handler $handler): Response
    {
        if (getenv('APP_ENV') == 'dev') {

            $whoops = new \Whoops\Run();

            if ($request->acceptJson()) {
                $whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler());
            /*} elseif ($request->acceptXml()) {
                $whoops->pushHandler(new \Whoops\Handler\XmlResponseHandler());*/
            } else {
                $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
            }

            $whoops->register();

        } else {

            set_exception_handler(function ($e) use ($request) {
                dd($e, $request);
            });
        }

        return $handler->handle($request);
    }
}
