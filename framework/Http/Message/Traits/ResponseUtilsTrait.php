<?php

namespace Amber\Http\Message\Traits;

use Psr\Http\Message\ResponseInterface;

trait ResponsetUtilsTrait
{
    /**
     * Create a new json response.
     *
     * @param mixed  $content      The content that must be parsed to json and returned.
     *
     * @return ResponseInterface
     */
    public function json($content = null): ResponseInterface
    {
        $response = $this->clone();

        $response->getBody()
            ->write(json_encode($content))
        ;

        return $response
            ->withHeader('Content-Type', 'application/json')
        ;
    }
}
