<?php

namespace Amber\Model\Resource;

use Amber\Collection\Collection;
use Amber\Validator\Validator;
use Amber\Model\Traits\AttributesTrait;
use Amber\Collection\Implementations\{
    IteratorAggregateTrait,
    ArrayAccessTrait,
    PropertyAccessTrait,
    SerializableTrait,
    CountableTrait
};

class Resource implements ResourceInterface
{
    use IteratorAggregateTrait,
        PropertyAccessTrait,
        SerializableTrait,
        AttributesTrait,
        CountableTrait
    ;

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

    public function isNew(): bool
    {
        return empty($this->getStoredValues());
    }

    public function setValues(iterable $values = []): ResourceInterface
    {
        foreach ($values as $name => $value) {
            if ($this->hasAttribute($name)) {
                $this->getAttribute($name)->setValue($value);
            }
        }

        return $this;
    }

    public function getValues(iterable $values = []): Collection
    {
        return $this->getAttributes()->map(function($attr) {
            return $attr->getValue();
        });
    }

    public function setStoredValues(array $stored): ResourceInterface
    {
        $this->_metadata['stored'] = $stored;

        return $this;
    }

    public function getStoredValues(): Collection
    {
        return $this->getAttributes()->map(function($attr) {
            return $attr->getStoredValue();
        });
    }

    public function setId(string $id): ResourceInterface
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setName(string $name = ''): ResourceInterface
    {
        $this->name  = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributesNames(): Collection
    {
        return $this->attributes->keys();
    }

    public function setErrors(array $errors = []): ResourceInterface
    {
        $this->errors = $errors;

        return $this;
    }

    public function getErrors(): Collection
    {
        return $this->errors ?? [];
    }

    public function validate(): Collection
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

    public function sync(array $values): ResourceInterface
    {
        foreach ($values as $key => $value) {
            if ($this->get($key) !== $value) {
                $this->set($key, $value);
            }

            return $this;
        }
    }

    public function updatable(): Collection
    {
        return $this->getValues()->diff($this->getStoredValues()->toArray());
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

    public function insertable(): Collection
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

    public function offsetSet($offset, $value)
    {
        if (!$this->hasAttribute($offset)) {
            return;
        }

        $this->getAttribute($offset)->setValue($value);
    }

    public function offsetExists($offset)
    {
        return $this->hasAttribute($offset)
            && $this->getAttribute($offset)->getValue() !== null
        ;
    }

    public function offsetUnset($offset)
    {
        if (!$this->hasAttribute($offset)) {
            return;
        }

        $this->getAttribute($offset)->setValue(null);
    }

    public function &offsetGet($offset)
    {
        $value = null;

        if ($this->hasAttribute($offset)) {
            $value = $this->getAttribute($offset)->getValue();
            $ret =& $value;

            return $ret;
        }

        return $value;
    }

    public function toArray(): array
    {
        return $this->getValues()->toArray();
    }
}
