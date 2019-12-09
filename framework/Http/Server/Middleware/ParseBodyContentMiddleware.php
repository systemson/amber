<?php

namespace Amber\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Amber\Container\Facades\Auth;
use Amber\Container\Facades\Response as ResponseFacade;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 *
 * @deprecated
 *
 * @todo This parser MUST be moved to the ServerRequest class.
 */
class ParseBodyContentMiddleware extends Middleware
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
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            if (($input = file_get_contents("php://input")) != null) {
                switch ($request->server->get('CONTENT_TYPE')) {
                    case 'application/json':
                        $data = json_decode($input, true);
                        break;

                    case 'application/x-www-form-urlencoded':
                        parse_str($input, $data);
                        break;
                    
                    default:
                        $data = [];
                        break;
                }
                $request = $request->withParsedBody($data);
            }
        }

        return $handler->handle($request);
    }
}
