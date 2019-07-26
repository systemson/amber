<?php

namespace Amber\Model\Traits;

use Amber\Model\Attribute\Attribute;
use Amber\Collection\Collection;

trait AttributesTrait
{
    protected $attributes = [];

    protected function initAttributes(): void
    {
        if (!$this->attributes instanceof Collection) {
            $this->attributes = new Collection($this->attributes);
        }
    }

    public function setAttributes(iterable $attributes): self
    {
        $this->initAttributes();

        foreach ($attributes as $name => $options) {
            if (is_string($name)) {
                $this->setAttribute($name, $options);
                continue;
            }

            $this->setAttribute($options);
        }

        return $this;
    }

    public function getAttributes(): iterable
    {
        $this->initAttributes();

        return $this->attributes;
    }

    public function setAttribute(string $name, $options = null): self
    {
        $this->initAttributes();

        $this->attributes->set($name, new Attribute($name, $options));

        return $this;
    }

    public function hasAttribute(string $name): bool
    {
        $this->initAttributes();

        return $this->attributes->has($name);
    }

    public function getAttribute(string $name)
    {
        $this->initAttributes();

        return $this->attributes->get($name);
    }
}
