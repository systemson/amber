<?php

namespace Amber\Framework\Http\Server;

use Psr\Http\Message\ResponseInterface;

class ResponseDispatcher
{
    /**
     * Sends HTTP headers and content.
     *
     * @return self
     */
    public function send(ResponseInterface $response): self
    {
        http_response_code($response->getStatusCode());

        $this->sendHeaders($response);
        $this->sendContent($response);

        return $this;
    }

    /**
     * Sends the response HTTP headers.
     *
     * @return self
     */
    public function sendHeaders(ResponseInterface $response): self
    {
        header($this->getStatusLine($response));

        foreach ($response->getHeaders() as $name => $values) {
            header(sprintf('%s: %s', $name, $response->getHeaderLine($name)));
        }

        return $this;
    }

    protected function getStatusLine(ResponseInterface $response)
    {
        $status = sprintf(
            'HTTP/%s %d %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        return trim($status);
    }

    /**
     * Sends the content for the current web response.
     *
     * @return self
     */
    public function sendContent(ResponseInterface $response): self
    {
        echo (string) $response->getBody();

        return $this;
    }
}
