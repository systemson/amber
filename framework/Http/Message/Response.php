<?php

namespace Amber\Framework\Http\Message;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use Amber\Framework\Http\Message\Traits\MessageTrait;
use Amber\Framework\Http\Message\Utils\StatusCodeInterface;
use Amber\Framework\Http\Message\Utils\RequestMethodInterface;
use Amber\Collection\Collection;

/**
 * Representation of an outgoing, server-side response.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - Status code and reason phrase
 * - Headers
 * - Message body
 *
 * Responses are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */
class Response implements ResponseInterface
{
    use MessageTrait;

    public $protocol = '1.1';
    public $code;
    public $reasonPhrase;
    public $body;

    public $headers;

    public function __construct(
        $code = StatusCodeInterface::STATUS_OK,
        $reasonPhrase = '',
        $headers = [],
        StreamInterface $body = null
    ) {
        $this->code = $code;
        $this->reasonPhrase = $reasonPhrase == '' ? StatusCodeInterface::REASON_PHRASE[$code] : $reasonPhrase;
        $this->headers = new Collection($headers);
        $this->body = $body;
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
        return $this->code;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $new = $this->clone();

        $new->code = $code;
        $new->reasonPhrase = $reasonPhrase == '' ? StatusCodeInterface::REASON_PHRASE[$code] : $reasonPhrase;

        return $new;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }
}
