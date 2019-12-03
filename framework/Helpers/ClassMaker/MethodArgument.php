<?php

namespace Amber\Helpers\ClassMaker;

use Amber\Phraser\Phraser;

class MethodArgument
{
    protected $name;
    protected $typehint;
    protected $default;

    public function __construct(
        string $name,
        string $typehint = null,
        string $default = null
    ) {
        $this->name = $name;
        $this->typehint = $typehint;
        $this->default = $default;
    }

    public function toString(): string
    {
        return Phraser::make("\${$this->name}")
            ->prepend("{$this->typehint} ", $this->typehint)
            ->append(" = {$this->default}", $this->default)
        ;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
