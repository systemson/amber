<?php

namespace Amber\Model\Provider;

use Amber\Model\Contracts\Mediator;
use Aura\SqlQuery\QueryFactory;

abstract class AbstractProvider
{
    protected $id = 'id';

    protected $name;

    protected $attributes = [];

    protected $relations = [];

    protected $mediator;

    public function __construct()
    {
    	$this->mediator = getenv('DB_DRIVER');
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setMediator(string $mediator): self
    {
        $this->mediator = $mediator;

        return $this;
    }

    public function getMediator(): string
    {
        return $this->mediator;
    }

    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        ;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttribute(string $name, array $options = []): self
    {
        $this->attributes[$name] = implode('|', $options);
    }

    public function getAttribute(string $name): ?string
    {
        return $this->attributes[$name] ?? null;
    }

    public function query()
    {
        return new QueryFactory(getenv('DB_DRIVER', 'pgsql'));
    }

    public function select(array $columns = [])
    {
        $query = $this->query()->newSelect()->from($this->getName());

        $query->provider = $this;

        if (!empty($columns)) {
            $query->cols($columns);
        } else {
            $query->cols(['*']);
        }

        return $query;
    }

    public function all()
    {
        return $this->select();
    }

    public function first()
    {
        return $this->select()->limit(1);
    }

    public function find($id)
    {
    	return $this->first()->where("$this->id = ?", $id);
    }

    public function insert(array $columns)
    {
        $query = $this->query()->newInsert()->into($this->getName());

        $query->provider = $this;

        $query->cols($columns);

        return $query;
    }
}
