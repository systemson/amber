<?php

namespace Amber\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Amber\Container\Facades\Filesystem;
use Amber\Helpers\ClassMaker\Maker;
use Amber\Http\Session\Session;
use Amber\Helpers\Assets\Loader;
use Amber\Http\Message\Uri;
use Amber\Container\Facades\Gemstone;
use App\Models\UserProvider;
use Amber\Phraser\Phraser;
use Amber\Helpers\Hash;
use Amber\Container\Facades\Str;
use Amber\Helpers\ClassMaker\ClassBlueprint;

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
        return $handler->handle($request);
    }
}
