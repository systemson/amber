<?php

namespace Amber\Helpers\ClassMaker;

use Amber\Phraser\Phraser;

class ClassBlueprint
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
        $this->includes[] = $namespace;

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

    public function addProperty(string $name, $visibility = 'public', $typehint = null): self
    {
        $this->properties[] = (object) [
            'name' => $name,
            'visibility' => $visibility,
            'typehint' => $typehint,
        ];

        return $this;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function addMethod(string $name, array $arguments = [], $visibility = 'public', string $returnTypehint = null, string $code = null): self
    {
        $this->methods[$name] = (object) [
            'name' => $name,
            'arguments' => $this->formatArguments($arguments),
            'visibility' => $visibility,
            'returnTypehint' => $returnTypehint,
            'code' => $code,
        ];

        return $this;
    }

    protected function formatArguments(array $arguments): array
    {
        foreach ($arguments as $key => $value) {

            $name = is_numeric($key) ? $value : $key;
            $typehint = $value['type'] ?? null;
            $default = $value['default'] ?? null;

            $args[] =  Phraser::make('$' . $name)
                ->prepend("{$typehint} ", $typehint)
                ->append(" = {$default}", $default)
            ;
        }

        return $args ?? [];
    }

    public function toString(): string
    {
        $implements = implode(', ', $this->implements);
        $traits = implode(', ', $this->traits);

        $properties =[];
        foreach ($this->properties as $property) {
            $properties[] = Phraser::make("\${$property->name};")
                ->prepend("{$property->visibility} ", $property->visibility)
                ->prepend('    ')
            ;
        }

        $includes = [];
        foreach ($this->includes as $include) {
            $includes[] = Phraser::make($include)
                ->prepend('use ')
                ->append(';')
            ;
        }

        $methods = [];
        foreach ($this->methods as $method) {
            $arguments = implode(', ', $method->arguments);

            $methods[] = Phraser::make("    {$method->visibility} function {$method->name}({$arguments})")
                ->append(": {$method->returnTypehint}", $method->returnTypehint)
                ->eol()
                ->append('    {')
                ->eol()
                ->append("{$method->code}", $method->code)
                ->eol()
                ->append('    }')
            ;
        }

        return Phraser::make('<?php')
            ->eol(2)
            ->append("namespace {$this->namespace};" . Phraser::make()->eol(2), $this->namespace)
            ->append(implode(Phraser::make()->eol(), $includes) . Phraser::make()->eol(2), $this->includes)
            ->append("class {$this->name}")
            ->append(" extends {$this->parent}", $this->parent)
            ->append(" implements {$implements}", $implements)
            ->eol()
            ->append('{')
            ->eol()
            ->append("    use {$traits};" . Phraser::make()->eol(2), $traits)
            ->append(implode(Phraser::make()->eol(2), $properties) . Phraser::make()->eol(2), $properties)
            ->append(implode(Phraser::make()->eol(2), $methods), $methods)
            ->append(Phraser::make()->eol() . '}')
        ;
    }
}
