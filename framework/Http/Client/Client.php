<?php

namespace Amber\Http\Client;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Amber\Http\Message\Utils\Curl;
use Sunrise\Stream\StreamFactory;
use Amber\Http\Message\Response;

class Client implements ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $curl = new Curl();

        try {
            $response = $curl
                ->setUrl((string) $request->getUri())
                ->setMethod($request->getMethod())
                ->setHeaders($request->getHeaders()->toArray())
                ->exec()
            ;
        } catch (\Throwable $e) {
            throw new NetworkException($request, $e->getMessage());
        }

        $code = $response['info']['http_code'];

        return new Response(
            $code,
            Response::REASON_PHRASE[$code],
            (new StreamFactory())->createStream($response['body']),
            $response['headers']
        );
    }
}
