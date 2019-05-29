<?php

namespace Amber\Framework\Http\Message;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Amber\Framework\Http\Message\Utils\StatusCodeInterface;
use Amber\Framework\Container\ContainerAwareClass;
use Carbon\Carbon;

class ResponseFactory extends ContainerAwareClass implements ResponseFactoryInterface, StatusCodeInterface
{
    /**
     * Create a new response.
     *
     * @param int $code HTTP status code; defaults to 200
     * @param string $reasonPhrase Reason phrase to associate with status code
     *
     * @return ResponseInterface
     */
    public function createResponse(int $code = self::STATUS_OK, string $reasonPhrase = ''): ResponseInterface
    {
        $response = static::getContainer()->get(ResponseInterface::class);

        return $response->withHeader('Content-Type', ['text/html', 'charset=UTF-8'])
            ->withHeader('Cache-Control', ['no-cache', 'private'])
            ->withHeader('Date', gmdate('D, d M Y H:i:s T'))
            ->withStatus($code, $reasonPhrase)
        ;
    }

    /**
     *
     */
    public function redirect($to = '/'): ResponseInterface
    {
        return $this->createResponse(302)->withHeader('Location', $to);
    }

    /**
     * The 400 status code, or Bad Request error, means the HTTP request that was sent to the server has invalid syntax.
     */
    public function badRequest(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(400, $reasonPhrase);
    }

    /**
     * The 401 status code, or an Unauthorized error, means that the user trying to access the resource has not been
     * authenticated or has not been authenticated correctly.
     */
    public function unauthorized(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(401, $reasonPhrase);
    }

    /**
     * The 403 status code, or a Forbidden error, means that the user made a valid request but the server is refusing to
     * serve the request, due to a lack of permission to access the requested resource.
     */
    public function forbidden(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(403, $reasonPhrase);
    }

    /**
     * The 404 status code, or a Not Found error, means that the user is able to communicate with the server but it is
     * unable to locate the requested file or resource.
     */
    public function notFound(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(404, $reasonPhrase);
    }

    /**
     * The 500 status code, or Internal Server Error, means that server cannot process the request for an unknown
     * reason. Sometimes this code will appear when more specific 5xx errors are more appropriate.
     */
    public function internalServerError(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(500, $reasonPhrase);
    }

    /**
     * The 502 status code, or Bad Gateway error, means that the server is a gateway or proxy server, and it is not
     * receiving a valid response from the backend servers that should actually fulfill the request.
     */
    public function badGateway(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(502, $reasonPhrase);
    }

    /**
     * The 503 status code, or Service Unavailable error, means that the server is overloaded or under maintenance.
     * This error implies that the service should become available at some point.
     */
    public function serviceUnavailable(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(503, $reasonPhrase);
    }

    /**
     * The 504 status code, or Gateway Timeout error, means that the server is a gateway or proxy server, and it is not
     * receiving a response from the backend servers within the allowed time period.
     */
    public function gatewayTimeout(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(504, $reasonPhrase);
    }
}
