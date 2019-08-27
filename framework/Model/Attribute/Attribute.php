<?php

namespace Amber\Model\Attribute;

use Amber\Phraser\Phraser;

class Attribute
{
    private $name;
    private $type;
    private $size;
    private $default;
    private $nullable = false;
    private $rules = [];

    private $value;
    private $stored;

    public function __construct(string $name, string $options = null)
    {
        $this->setName($name);

        list($type, $default, $nullable, $rules) = $this->parseOptions($options);

        $this->setType($type);

        $this->setDefault($default ?? null);
        $this->nullable = $nullable;
        $this->setRules($rules ?? null);
    }

    protected function parseOptions(string $options = null)
    {
        $options = explode('|', $options);

        foreach ($options as $option) {
            if (starts_with($option, 'default')) {
                $default = (string) Phraser::make($option)
                    ->explode(':')
                    ->last()
                ;
                break;
            }

            if ($option == 'optional') {
                $nullable = true;
            }

            if (ends_with('Type', $options)) {
                $type = (string) Phraser::make($option)
                ->fromCamelCase()
                    ->first()
                ;
            } else {
                switch ($option) {
                    case 'numeric':
                        $type = 'numeric';
                        break;
                }
            }

            $rules[] = $option;
        }

        return [
            $type ?? null,
            $default ?? null,
            $nullable ?? false,
            $rules ?? [],
        ];
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setType(string $type = null): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setDefault($default = null): self
    {
        $this->default = $default;

        return $this;
    }

    public function hasDefault(): bool
    {
        return isset($this->default);
    }

    public function getDefault()
    {
        $value = $this->default;

        switch ($this->getType()) {
            case 'string':
                return (string) $value;
                break;

            case 'boolean':
                return (bool) $value;
                break;

            case 'integer':
            case 'numeric':
                return (int) $value;
                break;

            case 'float':
                return (double) $value;
                break;
            
            default:
                return $value;
                break;
        }
    }

    public function setRules(array $rules = []): self
    {
        $this->rules = $rules;

        return $this;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value ?? $this->getDefault();
    }

    public function setStoredValue($value): self
    {
        $this->stored = $value;

        return $this;
    }

    public function getStoredValue()
    {
        return $this->stored;
    }
}
