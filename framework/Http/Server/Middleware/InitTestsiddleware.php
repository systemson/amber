<?php

namespace Amber\Framework\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Amber\Framework\Container\Facades\Filesystem;
use Amber\Framework\Helpers\ClassMaker\Maker;
use Amber\Framework\Http\Session\Session;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class InitTestsiddleware extends RequestMiddleware
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
        //$this->testClassMaker();
        //$this->testSession();

        return $handler->handle($request);
    }

    protected function testClassMaker()
    {
        $maker = new Maker();

        d($maker->getImplementingClass('App\Controllers\UsersController', Handler::class));
        d($maker->getExtendingClass('App\Http\Middleware\TestMiddleware', RequestMiddleware::class));
        dd($maker->getExtendingClass(
            'App\Http\Middleware\TestMiddleware',
            RequestMiddleware::class,
            Middleware::class
        ));
    }

    protected function testSession()
    {
        $session = new Session();

        dd(
            $session,
            $session->metadata()->all(),
            $session->metadata()->created_at,
            $session->metadata()->updated_at,
            $session->metadata()->clear(),
            $_SESSION
        );
    }
}
