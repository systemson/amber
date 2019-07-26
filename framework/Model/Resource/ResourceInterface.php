<?php

namespace Amber\Model\Resource;

use Amber\Collection\Collection;

interface ResourceInterface
{
    public function isNew(): bool;

    public function setId(string $id): self;

    public function getId(): string;

    public function setName(string $name): self;

    public function getName(): string;

    public function setAttributes(array $attributes): self;

    public function getAttributes(): Collection;

    public function setAttribute(string $name, array $options = []): self;

    public function hasAttribute(string $name): bool;

    public function getAttribute(string $name): ?string;

    public function setErrors(array $errors = []): self;

    public function getErrors(): Collection;

    public function validate(): Collection;

    public function isValid(): bool;

    public function sync(array $values): self;

    public function updatable(): Collection;

    public function insertable(): Collection;
}
