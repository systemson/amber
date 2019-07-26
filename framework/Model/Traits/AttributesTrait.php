<?php

namespace Amber\Model\Traits;

use Amber\Model\Attribute\Attribute;
use Amber\Model\Resource\ResourceInterface;
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

    public function setAttributes(array $attributes): ResourceInterface
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

    public function getAttributes(): Collection
    {
        $this->initAttributes();

        return $this->attributes;
    }

    public function setAttribute(string $name, $options = null): ResourceInterface
    {
        $this->initAttributes();

        $this->attributes->set($name, new Attribute($name, $options));

        return $this;
    }

    public function hasAttribute(string $name = null): bool
    {
        $this->initAttributes();

        return $name !== null && $this->attributes->has($name);
    }

    public function getAttribute(string $name)
    {
        $this->initAttributes();

        return $this->attributes->get($name);
    }
}
