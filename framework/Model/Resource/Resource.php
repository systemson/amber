<?php

namespace Amber\Model\Resource;

use Amber\Collection\Collection;
use Amber\Validator\Validator;

class Resource extends Collection
{
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
        $this->setAttributes($attributes);
    }

    public function init(): self
    {
        $this->_metadata['stored'] = $this->all();

        return $this;
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

    public function setAttributes(array $attributes = []): self
    {
        $this->_metadata['attributes'] = $attributes;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->_metadata['attributes'];
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
        $errors = Validator::assert(
            $this->getAttributes(),
            $this->all(),
        );

        $this->setErrors($errors->toArray());

        return $errors;
    }

    public function isValid(): bool
    {
        if (!empty($this->validate())) {
            return false;
        }
    }

    public function updatable()
    {
        return array_diff($this->toArray(), $this->_metadata['stored']);
    }
}
