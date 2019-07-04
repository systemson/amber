<?php

namespace Amber\Model\Provider;

use Amber\Model\Contracts\Mediator;
use Aura\SqlQuery\QueryFactory;
use Amber\Model\QueryBuilder\QueryBuilder;
use Amber\Model\Resource\Resource;
use Psr\Log\LoggerInterface;

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

    public function resource(): Resource
    {
        return $this->bootResource(new Resource());
    }

    public function bootResource(Resource $resource)
    {
        return $resource
            ->setName($this->getName())
            ->setId($this->getId())
            ->setAttributes($this->getAttributes())
        ;
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
        $factory = new QueryBuilder(getenv('DB_DRIVER', 'pgsql'));

        $factory->setLastInsertIdNames([
            $this->getName() . '.' . $this->getId() => $this->getName() . '_' . $this->getId() . '_seq',
        ]);

        return $factory;
    }

    public function select(array $columns = [])
    {
        $query = $this->query()
            ->newSelect()
            ->from($this->getName())
        ;

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
        $query = $this->query()
            ->newInsert()
            ->into($this->getName())
        ;

        $query->provider = $this;
        $query->id = $this->getId();

        $query->cols($columns);

        return $query;
    }

    public function insertAll(array $items)
    {
        $query = $this->query()->newInsert()->into($this->getName());

        $query->provider = $this;

        foreach ($items as $columns) {
            $query->addRow($columns);
        }

        return $query;
    }
}
