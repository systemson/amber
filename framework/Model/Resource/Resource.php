<?php

namespace Amber\Model\Resource;

use Amber\Collection\Collection;
use Amber\Validator\Validator;
use Amber\Model\Traits\AttributesTrait;

class Resource //extends Collection //implements ResourceInterface
{
    use AttributesTrait;

    private $_metadata = [];

    public function __construct(
        array $values = [],
        string $id = '',
        string $name = '',
        array $attributes = []
    ) {
        $this->setAttributes($attributes);

        $this->setId($id);
        $this->setName($name);
        
        $this->setValues($values);
    }

    public function isNew()
    {
        return empty($this->getStoredValues());
    }

    public function setValues(iterable $values = []): self
    {
        foreach ($values as $name => $value) {
            if ($this->hasAttribute($name)) {
                $this->getAttribute($name)->setValue($value);
            }
        }

        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setName(string $name = ''): self
    {
        $this->name  = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStoredValues(): array
    {
        return $this->_metadata['stored'] ?? [];
    }

    public function setStoredValues(array $stored): self
    {
        $this->_metadata['stored'] = $stored;

        return $this;
    }

    public function getAttributesNames(): array
    {
        return $this->attributes->keys();
    }

    public function setErrors(array $errors = []): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors ?? [];
    }

    public function validate(): array
    {
        if ($this->isNew()) {
            $ruleSet = $this->getAttributes()->map(function ($value) {
                return implode('|', $value->getRules());
            });

            $values = $this->insertable();
        } else {
            $ruleSet = $this->getAttributes()
                ->only(array_keys($this->updatable()))
                ->map(function ($value) {
                    return implode('|', $value->getRules());
                })
            ;

            $values = $this->updatable();
        }

        $validation = Validator::assert(
            $ruleSet->toArray(),
            $values
        );

        if ($validation !== true) {
            $errors = $validation->toArray();

            $this->setErrors($errors);

            return $errors;
        }

        return [];
    }

    public function isValid(): bool
    {
        return empty($this->validate());
    }

    public function sync(array $values): self
    {
        foreach ($values as $key => $value) {
            if ($this->get($key) !== $value) {
                $this->set($key, $value);
            }

            return $this;
        }
    }

    public function updatable()
    {
        return array_diff($this->all(), $this->getStoredValues());
    }

    public function hasDefault(string $attribute)
    {
        return $this->getAttributes()[$attribute]->hasDefault();
    }

    public function getDefault(string $name)
    {
        $attribute = $this->getAttribute($name);

        if (is_null($attribute) || !$attribute->hasDefault()) {
            return null;
        }

        $value = $attribute->getDefault();

        switch ($value) {
            case 'null':
                return null;
                break;

            case 'true':
                return true;
                break;

            case 'false':
                return false;
                break;
            
            default:
                return $value;
                break;
        }
    }

    public function insertable()
    {
        foreach ($this->getAttributes() as $name => $attr) {
            if ($this->has($name)) {
                $array[$name] = $this->get($name);
            } elseif ($attr->hasDefault()) {
                $array[$name] = $attr->getDefault();
            }
        }

        return $array;
    }

    public function __set($name, $value)
    {
        if (!$this->hasAttribute($name)) {
            return;
        }

        $this->getAttribute($name)->setValue($value);
    }

    public function __isset($name)
    {
        return $this->hasAttribute($name)
            && $this->getAttribute($name)->getValue() !== null
        ;
    }

    public function __unset($name)
    {
        if (!$this->hasAttribute($name)) {
            return;
        }

        $this->getAttribute($name)->setValue(null);
    }

    public function __get($name)
    {
        if (!$this->hasAttribute($name)) {
            return;
        }

        return $this->getAttribute($name)->getValue();
    }
}
