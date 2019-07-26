<?php

namespace Amber\Model\Resource;

use Amber\Collection\Collection;
use Amber\Validator\Validator;
use Amber\Model\Traits\AttributesTrait;
use Amber\Collection\Implementations\IteratorAggregateTrait;
use Amber\Collection\Implementations\ArrayAccessTrait;
use Amber\Collection\Implementations\PropertyAccessTrait;
use Amber\Collection\Implementations\SerializableTrait;
use Amber\Collection\Implementations\CountableTrait;

class Resource implements ResourceInterface
{
    use IteratorAggregateTrait,
        PropertyAccessTrait,
        SerializableTrait,
        AttributesTrait,
        CountableTrait
    ;

    private $_name;
    private $_id;
    private $_metadata = [];

    public function __construct(
        array $values = [],
        string $id = '',
        string $name = '',
        array $attributes = [],
        bool $isStored = false
    ) {
        $this->setAttributes($attributes);

        $this->setId($id);
        $this->setName($name);
        
        $this->setValues($values);
        if ($isStored) {
            $this->setStoredValues($values);
        }
    }

    public function isNew(): bool
    {
        return $this->getStoredValues()->isEmpty();
    }

    public function setValues(iterable $values = []): ResourceInterface
    {
        foreach ($values as $name => $value) {
            $this->getAttribute($name)->setValue($value);
        }

        return $this;
    }

    public function getValues(): Collection
    {
        return $this->getAttributes()->map(function ($attr) {
            return $attr->getValue();
        });
    }

    public function setStoredValues(array $values): ResourceInterface
    {
        foreach ($values as $name => $value) {
            $this->getAttribute($name)->setStoredValue($value);
        }

        return $this;
    }

    public function getStoredValues(): Collection
    {
        return $this->getAttributes()->map(function ($attr) {
            return $attr->getStoredValue();
        });
    }

    public function setId(string $id): ResourceInterface
    {
        $this->_id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->_id;
    }

    public function setName(string $name = ''): ResourceInterface
    {
        $this->_name  = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function getAttributesNames(): Collection
    {
        return $this->attributes->keys();
    }

    public function setErrors(Collection $errors): ResourceInterface
    {
        $this->errors = $errors;

        return $this;
    }

    public function getErrors(): Collection
    {
        return $this->errors ?? new Collection();
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
                ->only($this->updatable()->keys())
                ->map(function ($value) {
                    return implode('|', $value->getRules());
                })
            ;

            $values = $this->updatable();
        }

        $validation = Validator::assert(
            $ruleSet->filter(function ($value) {
                return isset($value) && !empty($value);
            })->toArray(),
            $values
        );

        if ($validation !== true) {
            $errors = new Collection($validation->toArray());

            $this->setErrors($errors);
        }

        return $errors ?? new Collection();
    }

    public function isValid(): bool
    {
        return empty($this->validate());
    }

    public function sync(ResourceInterface $resource): ResourceInterface
    {
        foreach ($resource->getAttributes() as $name => $attr) {
            $attribute = $this->getAttribute($name);

            $attribute->setValue($attr->getValue());
            $attribute->setStoredValue($attr->getStoredValue());
        }
 
        return $this;
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
        return $this->getValues()->filter(function ($value) {
            return isset($value) && !empty($value);
        });
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
