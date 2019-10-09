<?php

namespace Amber\Helpers\ClassMaker;

use Amber\Phraser\Phraser;

class Property
{
    protected $name;
    protected $description;
    protected $visibility;
    protected $typehint;

    public function __construct(
        string $name,
        string $visibility = 'public',
        string $typehint = null
    ) {
        $this->name = $name;
        $this->visibility = $visibility;
        $this->typehint = $ctypehintode;
    }

    public function toString(): string
    {
        return Phraser::make($this->name)
            ->prepend($this->visibility)
            ->prepend('    ')
        ;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
