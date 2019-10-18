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
use Amber\Model\Provider\AbstractProvider;

class Resource implements ResourceInterface
{
    use IteratorAggregateTrait,
        PropertyAccessTrait,
        SerializableTrait,
        AttributesTrait,
        CountableTrait // It's not compatible.
    ;

    private $_name;
    private $_id;
    private $_errors = [];
    private $_metadata = [];
    private $_relations = [];

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
            if ($this->hasAttribute($name) || in_array($name, [AbstractProvider::CREATED_AT, AbstractProvider::EDITED_AT, $this->getId()])) {
                if ($name == AbstractProvider::CREATED_AT) {
                    $this->setAttribute(AbstractProvider::CREATED_AT, 'date');
                } elseif ($name == AbstractProvider::EDITED_AT) {
                    $this->setAttribute(AbstractProvider::EDITED_AT, 'date');
                } elseif ($name == $this->getId()) {
                    $this->setAttribute($this->getId(), 'numeric');
                }

                $this->getAttribute($name)->setValue($value);
            }
        }

        return $this;
    }

    public function getValues(): Collection
    {
        $array = $this->getAttributes()->map(function ($attr) {
            if ($attr) {
                return $attr->getValue();
            }
        });

        foreach ($this->_relations as $name => $values) {
            if (is_object($values) && in_array('toArray', get_class_methods($values))) {
                $array[$name] = $values->toArray();
            } else {
                $array[$name] = $values;
            }
        }

        return $array;
    }

    public function getRawValues(): Collection
    {
        $array = $this->getAttributes()->map(function ($attr) {
            return $attr->getValue();
        });

        return $array;
    }

    public function setStoredValues(array $values): ResourceInterface
    {
        foreach ($values as $name => $value) {
            if ($this->hasAttribute($name)) {
                $this->getAttribute($name)->setStoredValue($value);
            }
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

    public function getMetadata(string $name)
    {
        return $this->_metadata[$name] ?? null;
    }

    public function setMetadata(string $name, $value)
    {
        $this->_metadata[$name] = $value;

        return $this;
    }

    public function getAttributesNames(): array
    {
        return $this->attributes->keys();
    }

    public function setErrors(Collection $errors): ResourceInterface
    {
        $this->_errors = $errors;

        return $this;
    }

    public function getErrors(): Collection
    {
        if (is_array($this->_errors)) {
            return new Collection();
        }

        return $this->_errors;
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
            $values->toArray()
        );

        if ($validation !== true) {
            $errors = new Collection($validation->toArray());

            $this->setErrors($errors);
        }

        return $errors ?? new Collection();
    }

    public function isValid(): bool
    {
        return $this->validate()->isEmpty();
    }

    public function fill(array $values): ResourceInterface
    {
        $attribute->setValue($value);
 
        return $this;
    }

    public function update(array $values): ResourceInterface
    {
        $this->setValues($values);
        $this->setStoredValues($values);

        return $this;
    }

    public function replace(ResourceInterface $resource): ResourceInterface
    {
        $this->setValues($resource->toArray());
        $this->setRelations($resource->getRelations());

        return $this;
    }

    public function updatable(): Collection
    {
        $array1 = $this->getRawValues()->toArray();
        $array2 = $this->getStoredValues()->toArray();

        return new Collection(array_diff_assoc($array1, $array2));
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

    public function delete()
    {
        $this->_metadata['delete'] = true;
    }

    public function isDeleted(): bool
    {
        return $this->_metadata['delete'] ?? false;
    }

    public function clear()
    {
        unset($this->attributes);
        unset($this->_name);
        unset($this->_id);
        unset($this->_errors);
        unset($this->_metadata);
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
        } elseif (isset($this->_relations[$offset])) {
            $ret =& $this->_relations[$offset];
            return $ret;
        }

        return $value;
    }

    public function setRelation($name, $values): self
    {
        $this->_relations[$name] = $values;

        return $this;
    }

    public function getRelation($name)
    {
        return $this->_relations[$name] ?? null;
    }

    public function setRelations(array $relations): self
    {
        foreach ($relations as $name => $values) {
            $this->setRelation($name, $values);
        }

        return $this;
    }

    public function getRelations()
    {
        return $this->_relations;
    }

    public function toArray(): array
    {
        return $this->getValues()->toArray();
    }

    public function join($array, string $name, string $fkey, string $pkey, bool $multiple = false): self
    {
        $values = $multiple ? [] : null;

        foreach ($array as $value) {
            if ($this->{$fkey} === $value->getMetadata($name)[$pkey]) {
                if (!$multiple) {
                    $values = $value;
                    break;
                }

                $values[] = $value;
            }
        }

        $this->setRelation($name, $values);

        return $this;
    }
}
