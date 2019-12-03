<?php

namespace Amber\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Log\LoggerInterface;

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
        $whoops = new \Whoops\Run();
        
        if (getenv('APP_ENV') == 'dev') {
            if ($request->acceptsJson()) {
                $whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler());
            /*} elseif ($request->acceptsXml()) {
                $whoops->pushHandler(new \Whoops\Handler\XmlResponseHandler());*/
            } else {
                $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
            }
        } else {
            // Must be replaced by a production view with a generic error message.
            $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler());
        }

        $whoops->pushHandler(function ($e) {
            $class = get_class($e);

            $message = sprintf("%s in %s on line %s", $e->getMessage(), $e->getFile(), $e->getLine());

            $this->getContainer()->get(LoggerInterface::class)->error("{$class}: {$message}");
        });

        $whoops->register();

        return $handler->handle($request);
    }
}
