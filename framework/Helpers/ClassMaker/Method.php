<?php

namespace Amber\Helpers\ClassMaker;

use Amber\Phraser\Phraser;

class Method
{
    protected $name;
    protected $arguments;
    protected $visibility;
    protected $returnTypehint;
    protected $code;

    public function __construct(
        string $name,
        array $arguments = [],
        string $visibility = 'public',
        string $returnTypehint = null,
        string $code = null
    ) {
        $this->name = $name;
        $this->arguments = $arguments;
        $this->visibility = $visibility;
        $this->returnTypehint = $returnTypehint;
        $this->code = $code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function public(): self
    {
        $this->visibility = 'public';

        return $this;
    }

    public function private(): self
    {
        $this->visibility = 'private';

        return $this;
    }

    public function protected(): self
    {
        $this->visibility = 'protected';

        return $this;
    }

    public function addArgument(MethodArgument $argument)
    {
        $this->arguments[] = $argument->toString();
    }

    public function toString(): string
    {
        $arguments = implode(', ', $this->arguments);

        return Phraser::make("    {$this->visibility} function {$this->name}({$arguments})")
        ->append(": {$this->returnTypehint}", $this->returnTypehint)
        ->eol()
        ->append('    {')
        ->eol()
        ->append("{$this->code}", $this->code)
        ->eol()
        ->append('    }')
        ;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
