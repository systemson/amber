<?php

namespace Amber\Model\Resource;

use Amber\Collection\Collection;

interface ResourceInterface extends
    \IteratorAggregate,
    \ArrayAccess,
    \Serializable,
    \JsonSerializable,
    \Countable
{
    public function isNew(): bool;

    public function setId(string $id): ResourceInterface;

    public function getId(): string;

    public function setName(string $name): ResourceInterface;

    public function getName():string;

    public function setAttributes(array $attributes): ResourceInterface;

    public function getAttributes(): Collection;

    public function setAttribute(string $name, array $options = []): ResourceInterface;

    public function hasAttribute(string $name): bool;

    public function getAttribute(string $name);

    public function setErrors(array $errors = []): ResourceInterface;

    public function getErrors(): Collection;

    public function validate(): Collection;

    public function isValid(): bool;

    public function sync(array $values): ResourceInterface;

    public function updatable(): Collection;

    public function insertable(): Collection;
}
