<?php

namespace Amber\Model\Provider;

use Amber\Model\Contracts\Mediator;
use Aura\SqlQuery\QueryFactory;
use Amber\Model\QueryBuilder\QueryBuilder;
use Amber\Model\Resource\Resource;
use Aura\SqlQuery\AbstractQuery;
use Amber\Container\Facades\Gemstone;
use Amber\Collection\Contracts\CollectionInterface;
use Amber\Phraser\Phraser;

use Amber\Model\Traits\AttributesTrait;

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
        'insert' => 'Aura\SqlQuery\Common\Insert',
        'select' => 'Aura\SqlQuery\Common\Select',
        'update' => 'Aura\SqlQuery\Common\Update',
        'delete' => 'Aura\SqlQuery\Common\Delete',
    ];

    public function __construct()
    {
        $this->mediator = env('DB_DRIVER', 'pgsql');
    }

    public function new(array $values = []): Resource
    {
        return new Resource(
            $values,
            $this->getId(),
            $this->getName(),
            $this->getAttributes()
        );
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

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function query(string $type = 'select')
    {

        if (!is_null($this->query)) {
            $class = (string) Phraser::make(get_class($this->query))
                ->explode('\\')
                ->last()
                ->toLowerCase()
            ;
            if ($class == $type) {
                return $this->query;
            }
        }

        $factory = new QueryBuilder($this->getMediator());

        $factory->setLastInsertIdNames([
            $this->getName() . '.' . $this->getId() => $this->getName() . '_' . $this->getId() . '_seq',
        ]);

        switch ($type) {
            case 'select':
                return $this->query = $factory->newSelect()
                    ->from($this->getName())
                ;
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

        if ($result instanceof CollectionInterface && $result->isNotEmpty()) {
            return $result->map(function ($values) {
                d($values);
                return $this->new($values);
            });
        } elseif (is_array($result)) {
            return $this->new($result);
        }

        return $result;
    }

    public function __call($method, $args = [])
    {
        if (!$this->query instanceof AbstractQuery) {
            throw new \Exception(sprintf(
                "Class %s doesn't have a method %s",
                get_called_class(),
                $method
            ));
        }

        if (!in_array($method, get_class_methods($this->query))) {
            throw new \Exception("Error Processing Request");
        }

        $this->query = call_user_func_array([$this->query, $method], $args);

        return $this;
    }
}
