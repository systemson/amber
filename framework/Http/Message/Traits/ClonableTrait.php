<?php

namespace Amber\Http\Message\Traits;

trait ClonableTrait
{
    public function clone(): self
    {
        return clone $this;
    }
}
