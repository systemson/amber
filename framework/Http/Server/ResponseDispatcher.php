<?php

namespace Amber\Framework\Http\Server;

use Psr\Http\Message\ResponseInterface;

class ResponseDispatcher
{
    /**
     * Sends HTTP headers and content.
     *
     * @return $this
     */
    public function send(ResponseInterface $response)
    {
        $this->sendHeaders($response);
        $this->sendContent($response);
    }

    /**
     * Sends HTTP headers.
     *
     * @return $this
     */
    public function sendHeaders(ResponseInterface $response)
    {
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }
    }

    /**
     * Sends content for the current web response.
     *
     * @return $this
     */
    public function sendContent(ResponseInterface $response)
    {
        echo $response->getBody();

        return $this;
    }
}
