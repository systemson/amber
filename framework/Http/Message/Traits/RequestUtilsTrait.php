<?php

namespace Amber\Http\Message\Traits;

trait RequestUtilsTrait
{
    public function acceptsHtml()
    {
        return strpos($this->getHeaderLine('Accept'), 'text/html') !== false;
    }

    public function acceptsJson()
    {
        return strpos($this->getHeaderLine('Accept'), 'application/json') !== false;
    }

    public function acceptsXml()
    {
        return strpos($this->getHeaderLine('Accept'), 'application/xml') !== false;
    }
}
