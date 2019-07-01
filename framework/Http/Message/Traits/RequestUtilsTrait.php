<?php

namespace Amber\Http\Message\Traits;

trait RequestUtilsTrait
{
    public function acceptsHtml()
    {
        return strpos($this->getHeader('Accept'), 'text/html') !== false;
    }

    public function acceptsJson()
    {
        return strpos($this->getHeader('Accept'), 'application/json') !== false;
    }

    public function acceptsXml()
    {
        return strpos($this->getHeader('Accept'), 'application/xml') !== false;
    }
}
