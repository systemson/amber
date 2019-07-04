<?php

namespace Amber\Model\Attribute;

class Attribute
{
    private $name;
    private $type;
    private $default;
    private $rules;

    private $current_value;
    private $stored_value;

    public function __construct(string $name, string $type, array $options = [])
    {
        extract($options);

        $this->setName($name);
        $this->setType($type);

        $this->setDefault($default ?? null);
        $this->setRules($rules ?? null);
    }
}
