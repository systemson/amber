<?php

namespace Amber\Model\Traits;

use Amber\Model\Attribute\Attribute;

trait AttributesTrait
{
    protected $attributes = [];

    public function setAttributes(array $attributes): self
    {
        foreach ($attributes as $name => $options) {
            $this->setAttribute($name, $options);
        }

        return $this;
    }

    public function getAttributes(): iterable
    {
        return $this->attributes;
    }

    public function setAttribute(string $name, $options = []): self
    {
        $this->attributes[$name] = new Attribute($name, $options);

        return $this;
    }

    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function getAttribute(string $name): ?string
    {
        return $this->attributes->get($name);
    }
}
