<?php

namespace Amber\Model\Provider;

use Amber\Model\Contracts\Mediator;
use Aura\SqlQuery\QueryFactory;
use Amber\Model\QueryBuilder\QueryBuilder;
use Amber\Model\Resource\Resource;
use Aura\SqlQuery\AbstractQuery;
use Amber\Container\Facades\Gemstone;
use Amber\Collection\Contracts\CollectionInterface;

abstract class AbstractProvider
{
    protected $id = 'id';

    protected $name;

    protected $attributes = [];

    protected $relations = [];

    protected $mediator;

    protected $query;

    public function __construct()
    {
        $this->mediator = env('DB_DRIVER', 'pgsql');
    }

    public function resource(): Resource
    {
        return $this->bootResource(new Resource());
    }

    public function bootResource(Resource $resource = null): ?Resource
    {
        if (is_null($resource)) {
            return null;
        }

        return $resource
            ->init()
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

    protected function query()
    {
        $factory = new QueryBuilder(getenv('DB_DRIVER', 'pgsql'));

        $factory->setLastInsertIdNames([
            $this->getName() . '.' . $this->getId() => $this->getName() . '_' . $this->getId() . '_seq',
        ]);

        return $factory;
    }

    public function select(array $columns = [])
    {
        $this->query = $this->query()
            ->newSelect()
            ->from($this->getName())
        ;

        if (!empty($columns)) {
            $this->query->cols($columns);
        } else {
            $this->query->cols(['*']);
        }

        return $this;
    }

    public function all()
    {
        return $this->select()
            ->get()
        ;
    }

    public function first()
    {
        return $this->select()
            ->limit(1)
            ->get()
        ;
    }

    public function where(string $column, string $operator, $value)
    {
        if (is_null($this->query)) {
            $this->select();
        }
        
        $this->query
            ->where("{$column} {$operator} ?", $value)
        ;

        return $this;
    }

    public function find($id)
    {
        return $this->bootResource($this->select()
            ->where("$this->id = ?", $id)
            ->limit(1)
            ->get());
    }

    public function insert(array $columns)
    {
        $query = $this->query()
            ->newInsert()
            ->into($this->getName())
        ;

        $query->cols($columns);

        $id = Gemstone::execute($query);

        return $this->find($id);
    }

    public function update(Resource $resource)
    {
        $values = $resource->updatable();

        if (!empty($values)) {
            $query = $this->query()
                ->newUpdate()
                ->table($this->getName())
                ->cols($values)
                ->where($this->getId() . ' = ?', $resource->{$this->getId()})
            ;

            $resource->init();
            return Gemstone::execute($query);
        }

        return false;
    }

    public function delete(Resource $resource)
    {
        $values = $resource->updatable();

        if (!empty($values)) {
            $query = $this->query()
                ->newDelete()
                ->from($this->getName())
                ->where($this->getId() . ' = ?', $resource->{$this->getId()})
            ;

            return Gemstone::execute($query);
        }

        return false;
    }

    public function get()
    {
        $query = $this->query;
        $this->query = null;

        $result = Gemstone::execute($query);

        if ($result instanceof Resource) {
            return $this->bootResource($result);
        } elseif ($result instanceof CollectionInterface && $result->isNotEmpty()) {
            return $result->map(function ($resource) {
                return $this->bootResource($resource);
            });
        }

        return $result;
    }

    public function __call($method, $args = [])
    {
        if (!$this->query instanceof AbstractQuery) {
            throw new \Exception("Error Processing Request", 1);
        }

        if (!in_array($method, get_class_methods($this->query))) {
            throw new \Exception("Error Processing Request", 1);
        }

        $this->query = call_user_func_array([$this->query, $method], $args);

        return $this;
    }
}
