<?php

namespace Amber\Model\Provider;

use Amber\Model\Contracts\Mediator;
use Aura\SqlQuery\QueryFactory;
use Amber\Model\QueryBuilder\QueryBuilder;
use Amber\Model\Resource\Resource;
use Aura\SqlQuery\AbstractQuery;
use Amber\Container\Facades\Gemstone;
use Amber\Collection\Contracts\CollectionInterface;

/**
 * @todo MUST implement method save(), it should decide to insert or update the resource in the storage.
 * @todo Method save must accept a single resource or an Array/Collection of resources.
 */
abstract class AbstractProvider
{
    use Insertable, Selectable, Updatable, Deletable;

    protected $id = 'id';

    protected $name;

    protected $attributes = [];

    protected $relations = [];

    protected $mediator;

    protected $query;

    const QUERY_CLASSES = [
        'insert' => 'Aura\SqlQuery\Pgsql\Insert',
        'select' => 'Aura\SqlQuery\Pgsql\Select',
        'update' => 'Aura\SqlQuery\Pgsql\Update',
        'delete' => 'Aura\SqlQuery\Pgsql\Delete',
    ];

    public function __construct()
    {
        $this->mediator = env('DB_DRIVER', 'pgsql');
    }

    public function new(): Resource
    {
        return $this->bootResource(new Resource());
    }

    public function bootResource(Resource $resource = null): ?Resource
    {
        if (is_null($resource)) {
            return null;
        }

        return $resource
            ->boot()
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

    public function query(string $type = 'select')
    {
        $class = self::QUERY_CLASSES[$type];

        if (!is_null($this->query) && $this->query instanceof $class) {
            return $this->query;
        }

        $factory = new QueryBuilder(env('DB_DRIVER', 'pgsql'));

        $factory->setLastInsertIdNames([
            $this->getName() . '.' . $this->getId() => $this->getName() . '_' . $this->getId() . '_seq',
        ]);

        switch ($type) {
            case 'select':
                return $this->query = $factory->newSelect();
                break;

            case 'insert':
                return $this->query = $factory->newInsert();
                break;

            case 'update':
                return $this->query = $factory->newUpdate();
                break;

            case 'delete':
                return $this->query = $factory->newDelete();
                break;
            
            default:
                throw new \Exception("Wrong statement type.");
                break;
        }
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
