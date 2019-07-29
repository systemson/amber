<?php

namespace Amber\Http\Message;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Amber\Http\Message\Utils\StatusCodeInterface;

class ResponseFactory implements ResponseFactoryInterface, StatusCodeInterface
{
    private $container;

    /**
     * Creates a new instance of the class.
     *
     * @param ContainerInterface $container An instance of a PSR Container.
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

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
        $response = $this->ok();

        return $response
            ->withHeader('Cache-Control', ['no-cache', 'private'])
            ->withStatus($code, $reasonPhrase)
        ;
    }

    /**
     * A 200 status code, or OK, indicates that the request has succeeded.
     *
     * A 200 response is cacheable by default.
     *
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function ok()
    {
        if ($this->container instanceof ContainerInterface && $this->container->has(ResponseInterface::class)) {
            return $this->container->get(ResponseInterface::class);
        }

        return new Response();
    }

    /**
     * A 201 status code, or Created, indicates that the request has succeeded and has led to the creation of
     * a resource.
     *
     * The new resource must be returned in the body of the message, and its location being either the URL of
     * the request, or the content of the Location header.
     * The common use case of this status code is as the result of a POST request.
     *
     * @param string $reasonPhrase Reason phrase to associate with status code.
     *
     * @return ResponseInterface
     */
    public function created(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(self::STATUS_CREATED, $reasonPhrase);
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
        $response = $this->createResponse($code, $reasonPhrase);

        $response->getBody()
            ->write(json_encode($content))
        ;

        return $response
            ->withHeader('Content-Type', 'application/json')
        ;
    }

    /**
     * A 303 status code, or Redirect, is meant to redirect a POST, PUT, PATCH, DELETE request to a GET resource.
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
     * A 303 status code, or Redirect, is meant to redirect a POST, PUT, PATCH, DELETE request to a GET resource.
     *
     * @return ResponseInterface
     */
    public function redirectBack(): ResponseInterface
    {
        return $this->redirect($_SERVER['HTTP_REFERER']);
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
