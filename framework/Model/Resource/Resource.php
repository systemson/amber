<?php

namespace Amber\Model\Resource;

use Amber\Collection\Collection;

class Resource extends Collection
{
    private $_metadata = [];

    public function setId($id = null): self
    {
        $this->_metadata['id'] = $id;

        return $this;
    }

    public function getId()
    {
        return $this->_metadata['id'] ;
    }

    public function setName(string $name = null): self
    {
        $this->_metadata['name']  = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->_metadata['name'] ;
    }

    public function setAttributes(array $attributes): self
    {
        $this->_metadata['attributes'] = $attributes;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->_metadata['attributes'];
    }
}
