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
     *     in generated response; if none is provided implementations MAY use
     *     the defaults as suggested in the HTTP specification.
     *
     * @return ResponseInterface
     */
    public function createResponse(int $code = self::STATUS_OK, string $reasonPhrase = ''): ResponseInterface
    {
        $response = static::getContainer()->get(ResponseInterface::class);

        return $response->withHeader('Content-Type', 'text/html')
            ->withHeader('Cache-Control', ['no-cache', 'private'])
            ->withHeader('Date', gmdate('D, d M Y H:i:s T'))
            ->withStatus($code, $reasonPhrase)
        ;
    }

    public function forbidden(string $reasonPhrase = ''): ResponseInterface
    {
        return $this->createResponse(403, $reasonPhrase);
    }
}
