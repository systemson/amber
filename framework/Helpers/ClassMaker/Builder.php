<?php

namespace Amber\Helpers\ClassMaker;

use Amber\Phraser\Phraser;

class Builder
{
    protected $namespace;
    protected $name;
    protected $includes = [];
    protected $implements = [];
    protected $traits = [];
    protected $properties = [];
    protected $methods = [];
    protected $parent;

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }


    public function setParent(string $parent): self
    {
        $array = Phraser::make($parent)->explode('\\');

        if ($array->count() > 1) {
            $this->addInclude($array->toString());
        }

        $this->parent = $array->last();

        return $this;
    }

    public function getIncludes(): array
    {
        return $this->includes;
    }

    public function addInclude(string $namespace): self
    {
        $this->includes[] = Phraser::make($namespace)
            ->prepend('use ')
            ->append(';')
            ->toString()
        ;

        return $this;
    }

    public function getImplements(): array
    {
        return $this->implements;
    }

    public function addImplement(string $implement): self
    {
        $array = Phraser::make($implement)->explode('\\');

        if ($array->count() > 1) {
            $this->addInclude($array->toString());
        }

        $this->implements[] = $array->last();

        return $this;
    }


    public function getTraits(): array
    {
        return $this->traits;
    }

    public function addTrait(string $trait): self
    {
        $array = Phraser::make($trait)->explode('\\');

        if ($array->count() > 1) {
            $this->addInclude($array->toString());
        }

        $this->traits[] = $array->last();

        return $this;
    }


    public function getProperties(): array
    {
        return $this->properties;
    }

    public function addProperty(Property $property): self
    {
        $this->properties[] = $property->toString();

        return $this;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function addMethod(Method $method): self
    {
        $this->methods[$method->getName()] = $method->toString();

        return $this;
    }

    public function toString(): string
    {
        $implements = implode(', ', $this->implements);

        $traits = implode(', ', $this->traits);

        $properties = implode(Phraser::make()->eol(2), $this->properties);

        $methods = implode(Phraser::make()->eol(2), $this->methods);

        $includes = implode(Phraser::make()->eol(), $this->includes);

        return Phraser::make('<?php')
            ->eol(2)
            ->append("namespace {$this->namespace};" . Phraser::make()->eol(2), $this->namespace)
            ->append($includes . Phraser::make()->eol(2), $includes)
            ->append("class {$this->name}")
            ->append(" extends {$this->parent}", $this->parent)
            ->append(" implements {$implements}", $implements)
            ->eol()
            ->append('{')
            ->eol()
            ->append("    use {$traits};" . Phraser::make()->eol(2), $traits)
            ->append($properties . Phraser::make()->eol(2), $properties)
            ->append($methods, $methods)
            ->append(Phraser::make()->eol() . '}')
        ;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
