<?php

namespace Amber\Http\Message;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Amber\Http\Message\Traits\RequestTrait;
use Amber\Http\Message\Traits\RequestUtilsTrait;
use Amber\Collection\Collection;
use Amber\Http\Message\Utils\RequestMethodInterface;

/**
 * Representation of an outgoing, client-side request.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - HTTP method
 * - URI
 * - Headers
 * - Message body
 *
 * During construction, implementations MUST attempt to set the Host header from
 * a provided URI if no Host header is provided.
 *
 * Requests are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */
class Request implements RequestInterface, RequestMethodInterface
{
    use RequestTrait,
        RequestUtilsTrait
    ;

    protected $version;
    protected $method;
    protected $uri;
    protected $body;

    public $headers;

    const PROTOCOL_VERSION = '1.1';

    public function __construct(
        string $uri = '/',
        string $method = self::METHOD_GET,
        StreamInterface $body = null,
        array $headers = [],
        string $version = self::PROTOCOL_VERSION
    ) {
        $this->headers = new Collection($headers);

        $this->version = $version;
        $this->method = strtoupper($method);
        $this->uri = Uri::fromString($uri);

        $this->body = $body;
    }

    /**
     * Creates a new instance from super global vars.
     *
     * @return UriInterface
     */
    public static function fromGlobals()
    {
        $new = new static(
            Uri::fromGlobals(),
            $this->server->get('REQUEST_METHOD'),
            null,
            getallheaders(),
            explode('/', $this->server->get('SERVER_PROTOCOL'))[1]
        );

        return $new;
    }
}
