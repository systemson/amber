<?php

namespace Amber\Model\Attribute;

use Amber\Utils\Implementations\AbstractNullObject;

class NullableAttribute extends AbstractNullObject
{
    public function __call($method, $args)
    {
        return;
    }
}
