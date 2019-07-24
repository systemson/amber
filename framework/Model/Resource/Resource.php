<?php

namespace Amber\Model\Resource;

use Amber\Collection\Collection;
use Amber\Validator\Validator;
use Amber\Model\Traits\AttributesTrait;

class Resource extends Collection
{
    use AttributesTrait;

    private $_metadata = [];

    public function __construct(
        array $array = [],
        string $id = '',
        string $name = '',
        array $attributes = []
    ) {
        parent::__construct($array);

        $this->setId($id);
        $this->setName($name);

        $this->attributes = new Collection();
        $this->setAttributes($attributes);
    }

    public function boot(): self
    {
        $this->setStoredValues($this->all());

        return $this;
    }

    public function isNew()
    {
        return empty($this->getStoredValues());
    }

    public function setId(string $id = ''): self
    {
        $this->_metadata['id'] = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->_metadata['id'];
    }

    public function setName(string $name = ''): self
    {
        $this->_metadata['name']  = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->_metadata['name'];
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
        $this->_metadata['errors'] = $errors;

        return $this;
    }

    public function getErrors(): array
    {
        return $this->_metadata['errors'] ?? [];
    }

    public function validate()
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

    public function sync(array $values)
    {
        foreach ($values as $key => $value) {
            if ($this->get($key) !== $value) {
                $this->set($key, $value);
            }
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
}
