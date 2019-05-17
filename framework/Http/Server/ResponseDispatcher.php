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
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        return $this;
    }

    /**
     * Sends the content for the current web response.
     *
     * @return self
     */
    public function sendContent(ResponseInterface $response): self
    {
        echo $response->getBody();

        return $this;
    }
}
