<?php

namespace Amber\Framework\Http\Message\Traits;

trait ClonableTrait
{
    public function clone(): self
    {
        return clone $this;
    }
}
