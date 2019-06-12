<?php

namespace Amber\Framework\Http\Message\Traits;

trait RequestUtilsTrait
{
    public function acceptHtml()
    {
        return strpos($this->getHeader('Accept'), 'text/html') !== false;
    }

    public function acceptJson()
    {
        return strpos($this->getHeader('Accept'), 'application/json') !== false;
    }

    public function acceptXml()
    {
        return strpos($this->getHeader('Accept'), 'application/xml') !== false;
    }
}
