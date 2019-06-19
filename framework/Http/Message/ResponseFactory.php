<?php

namespace Amber\Framework\Http\Message;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Amber\Framework\Http\Message\Utils\StatusCodeInterface;
use Amber\Framework\Container\ContainerAwareClass;
use Carbon\Carbon;
use Psr\Http\Message\StreamFactoryInterface;

class ResponseFactory extends ContainerAwareClass implements ResponseFactoryInterface, StatusCodeInterface
{
    /**
     * Create a new response.
     *
     * @param int    $code         HTTP status code; defaults to 200.
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function createResponse(
        int $code = self::STATUS_OK,
        string $reasonPhrase = ''
    ): ResponseInterface {
        $response = static::getContainer()->get(ResponseInterface::class);

        return $response
            ->withHeader('Cache-Control', ['no-cache', 'private'])
            ->withStatus($code, $reasonPhrase)
        ;
    }

    /**
     * Create a new json response.
     *
     * @param mixed  $content      The content that must be parsed to json and returned.
     * @param int    $code         HTTP status code; defaults to 200.
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function json(
        $content = null,
        int $code = self::STATUS_OK,
        string $reasonPhrase = ''
    ): ResponseInterface {
        $factory = static::getContainer()->get(StreamFactoryInterface::class);

        $body = $factory->createStream(json_encode($content));

        return $this->createResponse($code, $reasonPhrase)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($body)
        ;
    }

    /**
     * A 303 redirect is meant to redirect a POST, PUT, PATCH, DELETE request to a GET resource.
     *
     * @param string $to The url to redirect to.
     *
     * @return ResponseInterface
     */
    public function redirect(string $to = '/'): ResponseInterface
    {
        return $this->createResponse(self::STATUS_SEE_OTHER)->withHeader('Location', $to);
    }

    /**
     * The 400 status code, or Bad Request error, means the HTTP request that was sent to the server has invalid syntax.
     *
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function badRequest(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(self::STATUS_BAD_REQUEST, $reasonPhrase);
    }

    /**
     * The 401 status code, or an Unauthorized error, means that the user trying to access the resource has not been
     * authenticated or has not been authenticated correctly.
     *
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function unauthorized(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(self::STATUS_UNAUTHORIZED, $reasonPhrase);
    }

    /**
     * The 403 status code, or a Forbidden error, means that the user made a valid request but the server is refusing to
     * serve the request, due to a lack of permission to access the requested resource.
     *
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function forbidden(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(self::STATUS_FORBIDDEN, $reasonPhrase);
    }

    /**
     * The 404 status code, or a Not Found error, means that the user is able to communicate with the server but it is
     * unable to locate the requested file or resource.
     *
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function notFound(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(self::STATUS_NOT_FOUND, $reasonPhrase);
    }

    /**
     * The 405 status code, or a Method Not Allowed error, indicates that the request method is known by the server but
     * is not supported by the target resource.
     *
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function methodNotAllowed(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(self::STATUS_METHOD_NOT_ALLOWED, $reasonPhrase);
    }

    /**
     * The 429 status code, or a Too Many Requests response status code indicates the user has sent too many requests in
     * a given amount of time ("rate limiting").
     *
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function tooManyRequest(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(self::STATUS_TOO_MANY_REQUESTS, $reasonPhrase);
    }

    /**
     * The 500 status code, or Internal Server Error, means that server cannot process the request for an unknown
     * reason. Sometimes this code will appear when more specific 5xx errors are more appropriate.
     *
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function internalServerError(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(self::STATUS_INTERNAL_SERVER_ERROR, $reasonPhrase);
    }

    /**
     * The 502 status code, or Bad Gateway error, means that the server is a gateway or proxy server, and it is not
     * receiving a valid response from the backend servers that should actually fulfill the request.
     *
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function badGateway(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(self::STATUS_BAD_GATEWAY, $reasonPhrase);
    }

    /**
     * The 503 status code, or Service Unavailable error, means that the server is overloaded or under maintenance.
     * This error implies that the service should become available at some point.
     *
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function serviceUnavailable(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(self::STATUS_SERVICE_UNAVAILABLE, $reasonPhrase);
    }

    /**
     * The 504 status code, or Gateway Timeout error, means that the server is a gateway or proxy server, and it is not
     * receiving a response from the backend servers within the allowed time period.
     *
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function gatewayTimeout(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(self::STATUS_GATEWAY_TIMEOUT, $reasonPhrase);
    }
}
